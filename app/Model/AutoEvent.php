<?php

/**
 * AutoEvent class.
 *
 * Model for auto events
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Acelle\Library\Log as MailLog;
use Acelle\Model\Subscriber;
use DB;

class AutoEvent extends Model
{
    // event status
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // event type
    const TYPE_SPECIFIC_DATETIME = 'specific-datetime';
    const TYPE_MONTHLY_RECURRING = 'monthly-recurring';
    const TYPE_WEEKLY_RECURRING = 'weekly-recurring';
    const TYPE_LIST_SUBSCRIPTION = 'list-subscription';
    const TYPE_LIST_UNSUBSCRIPTION = 'list-unsubscription';
    const TYPE_SUBSCRIBER_EVENT = 'subscriber-event';
    const TYPE_CUSTOM_CRITERIA = 'custom-criteria';
    const TYPE_API_CALL = 'api-call';
    const TYPE_FOLLOW_UP = 'follow-up';
    const TYPE_FOLLOW_UP_OPENED = 'follow-up-opened';
    const TYPE_FOLLOW_UP_NOT_OPENED = 'follow-up-not-opened';
    const TYPE_FOLLOW_UP_CLICKED = 'follow-up-clicked';
    const TYPE_FOLLOW_UP_NOT_CLICKED = 'follow-up-not-clicked';

    // operators
        const OPERATOR_EQUAL = 'equal';
    const OPERATOR_NOT_EQUAL = 'not_equal';
    const OPERATOR_CONTAINS = 'contains';
    const OPERATOR_NOT_CONTAINS = 'not_contains';
    const OPERATOR_STARTS = 'starts';
    const OPERATOR_ENDS = 'ends';
    const OPERATOR_NOT_STARTS = 'not_starts';
    const OPERATOR_NOT_ENDS = 'not_ends';
    const OPERATOR_GREATOR = 'greater';
    const OPERATOR_LESS = 'less';
    const OPERATOR_BLANK = 'blank';
    const OPERATOR_NOT_BLANK = 'not_blank';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_type'
    ];

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function automation()
    {
        return $this->belongsTo('Acelle\Model\Automation');
    }

    /**
     * Create custom order for the resource.
     *
     * @var void
     */
    public function triggersToFollowUp()
    {
        $relation = $this->hasMany('Acelle\Model\AutoTrigger');
        $relation->getQuery()->whereRaw(sprintf('id NOT IN (SELECT COALESCE(preceded_by, 0) FROM %s)', table('auto_triggers')));
        return $relation;
    }

    public function campaigns()
    {
        return $this->belongsToMany('Acelle\Model\AutomatedCampaign', 'auto_campaigns', 'auto_event_id', 'campaign_id');
    }

    public function originCampaigns()
    {
        return $this->belongsToMany('Acelle\Model\Campaign', 'auto_campaigns');
    }

    /**
     * Get campaign tracking logs.
     *
     * @return mixed
     */
    public function autoTriggers()
    {
        return $this->hasMany('Acelle\Model\AutoTrigger');
    }

    /**
     * Get AutoEvent status
     *
     * @return boolean active or not
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Set uid for resource.
     *
     * @var void
     */
    public function createUid()
    {
        $uid = uniqid();
        while (AutoEvent::where('uid', '=', $uid)->count() > 0) {
            $uid = uniqid();
        }
        $this->uid = $uid;
    }

    /**
     * Create custom order for the resource.
     *
     * @var void
     */
    public function createCustomOrder()
    {
        AutoEvent::select('custom_order')->increment('custom_order', 1);
        $this->custom_order = 0;
    }

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating automation.
        static::creating(function ($event) {
            // Create new uid
            $event->createUid();

            // Update custom order
            $event->createCustomOrder();
        });

        // Create first campaign when auto event created.
        static::created(function ($event) {
            // Create first campaign
            $event->createFirstCampaign();
        });

        // when auto event is deleted
        static::deleting(function ($event) {
            // Update auto campaign series
            $next_event = $event->nextEvent;
            if(is_object($next_event)) {
                $next_event->previous_event_id = $event->previous_event_id;
                $next_event->save();
            }

            // Delete all campaigns
            $event->campaigns()->delete();
        });
    }

    /**
     * Create first campaign for the auto event.
     *
     * @var void
     */
    public function createFirstCampaign()
    {
        if(!$this->campaigns()->count()) {
            $this->addCampaign();
        }
    }

    /**
     * Find item by uid.
     *
     * @return object
     */
    public static function findByUid($uid)
    {
        return self::where('uid', '=', $uid)->first();
    }

    /**
     * The validation rules for automation trigger.
     *
     * @var array
     */
    public function rules($request=NULL)
    {
        $rules = [];
        switch ($this->event_type) {
            case AutoEvent::TYPE_SPECIFIC_DATETIME:
                $rules = [
                    'specific_day' => 'required',
                    'specific_time' => 'required',
                ];
                break;
            case AutoEvent::TYPE_WEEKLY_RECURRING:
                $rules = [
                    'weekly_recurring_weekdays' => 'required',
                    'weekly_recurring_weeks' => 'required',
                    'weekly_recurring_months' => 'required',
                    'weekly_recurring_time' => 'required',
                ];
                break;
            case AutoEvent::TYPE_MONTHLY_RECURRING:
                $rules = [
                    'monthly_recurring_days' => 'required',
                    'monthly_recurring_months' => 'required',
                    'monthly_recurring_time' => 'required',
                ];
                break;
            case AutoEvent::TYPE_SUBSCRIBER_EVENT:
                $rules = [
                    'subscriber_event' => 'required',
                    'delay_type' => 'required',
                    'delay_value' => 'required',
                    'delay_unit' => 'required',
                ];
                break;
            case AutoEvent::TYPE_CUSTOM_CRITERIA:
                if (isset($request->custom_criteria)) {
                    foreach ($request->custom_criteria as $key => $param) {
                        $rules['custom_criteria.'.$key.'.field_uid'] = 'required';
                        $rules['custom_criteria.'.$key.'.operator'] = 'required';
                        if(!in_array($param['operator'], [AutoEvent::OPERATOR_BLANK, AutoEvent::OPERATOR_NOT_BLANK])) {
                            $rules['custom_criteria.'.$key.'.value'] = 'required';
                        }
                    }
                } else {
                    $rules['custom_criteria_empty'] = 'required';
                }
                break;
        }

        return $rules;
    }

    /**
     * Tigger type name select option.
     *
     * @var array
     */
    public static function typeNameSelectOptions()
    {
        return [
            ['value' => self::TYPE_SPECIFIC_DATETIME, 'text' => trans('messages.Immediately_or_on_a_specific_date')],
            ['value' => self::TYPE_WEEKLY_RECURRING, 'text' => trans('messages.weekly_recurring')],
            ['value' => self::TYPE_MONTHLY_RECURRING, 'text' => trans('messages.monthly_recurring')],
            ['value' => self::TYPE_LIST_SUBSCRIPTION, 'text' => trans('messages.Event_based_list_subscription')],
            ['value' => self::TYPE_LIST_UNSUBSCRIPTION, 'text' => trans('messages.Event_based_list_unsubscription')],
            ['value' => self::TYPE_SUBSCRIBER_EVENT, 'text' => trans('messages.Event_based_custom_list_subscriber_event')],
            ['value' => self::TYPE_CUSTOM_CRITERIA, 'text' => trans('messages.Custom_criteria')],
            ['value' => self::TYPE_API_CALL, 'text' => trans('messages.API_call')],
        ];
    }

    /**
     * Fill attributes from array.
     *
     * @var void
     */
    public function fillAttributes($params)
    {
        $this->fill($params);

        $this->data = "[]";

        // set delay time
        switch ($this->event_type) {
            case self::TYPE_LIST_SUBSCRIPTION:
            case self::TYPE_LIST_UNSUBSCRIPTION:
            case self::TYPE_SUBSCRIBER_EVENT:
            case self::TYPE_CUSTOM_CRITERIA:
                // Delay time
                $this->updateData([
                    'delay_type' => isset($params["delay_type"]) ? $params["delay_type"] : 'before',
                    'delay_value' => isset($params["delay_value"]) ? $params["delay_value"] : '0',
                    'delay_unit' => isset($params["delay_unit"]) ? $params["delay_unit"] : 'day',
                    'at' => isset($params["at"]) ? \Acelle\Library\Tool::systemTimeFromString('2017-07-07' . $params["at"], \Auth::user()->timezone)->format('H:i') : '',
                ]);
                break;
        }

        // fill other options
        switch ($this->event_type) {
            case self::TYPE_SPECIFIC_DATETIME:
                // send at specific date
                if(!empty($params["specific_day"]) && !empty($params["specific_time"])) {
                    $send_at = \Acelle\Library\Tool::systemTimeFromString($params["specific_day"].' '.$params["specific_time"], \Auth::user()->timezone);
                    $this->updateData([
                        'datetime' => $send_at->toAtomString()
                    ]);
                }
                break;
            case self::TYPE_WEEKLY_RECURRING:
                if(!empty($params["weekly_recurring_weekdays"])) {
                    $this->updateData([
                        'weekdays' => $params["weekly_recurring_weekdays"]
                    ]);
                }
                if(!empty($params["weekly_recurring_weeks"])) {
                    $this->updateData([
                        'weeks' => $params["weekly_recurring_weeks"]
                    ]);
                }
                if(!empty($params["weekly_recurring_months"])) {
                    $this->updateData([
                        'months' => $params["weekly_recurring_months"]
                    ]);
                }
                if(!empty($params["weekly_recurring_time"])) {
                    $send_at = \Acelle\Library\Tool::systemTimeFromString('2000-01-01 '.$params["weekly_recurring_time"], \Auth::user()->timezone);
                    $this->updateData([
                        'time' => $send_at->toAtomString()
                    ]);
                }
                break;
            case self::TYPE_MONTHLY_RECURRING:
                if(!empty($params["monthly_recurring_days"])) {
                    $this->updateData([
                        'days' => $params["monthly_recurring_days"]
                    ]);
                }
                if(!empty($params["monthly_recurring_months"])) {
                    $this->updateData([
                        'months' => $params["monthly_recurring_months"]
                    ]);
                }
                if(!empty($params["monthly_recurring_time"])) {
                    $send_at = \Acelle\Library\Tool::systemTimeFromString('2000-01-01 '.$params["monthly_recurring_time"], \Auth::user()->timezone);
                    $this->updateData([
                        'time' => $send_at->toAtomString()
                    ]);
                }
                break;
            case self::TYPE_SUBSCRIBER_EVENT:
                $this->updateData([
                    'event' => isset($params["subscriber_event"]) ? $params["subscriber_event"] : ''
                ]);
                break;
            case self::TYPE_CUSTOM_CRITERIA:
                if(!empty($params["custom_criteria"])) {
                    $this->updateData([
                        'criteria' => $params["custom_criteria"]
                    ]);
                }
            case self::TYPE_FOLLOW_UP:
            case self::TYPE_FOLLOW_UP_OPENED:
            case self::TYPE_FOLLOW_UP_NOT_OPENED:
            case self::TYPE_FOLLOW_UP_CLICKED:
            case self::TYPE_FOLLOW_UP_NOT_CLICKED:
                // if(!isset($params["delay_value"]) && !isset($params["delay_unit"])) {
                    $this->updateData([
                        'delay_type' => 'after',
                        'delay_value' => $params["delay_value"],
                        'delay_unit' => $params["delay_unit"]
                    ]);
                // }
                break;
        }
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        return ($this->data) ? json_decode($this->data, true) : [];
    }

    /**
     * Get data value.
     *
     * @return array
     */
    public function getDataValue($name)
    {
        $data = $this->getData();
        return isset($data[$name]) ? $data[$name] : NULL;
    }

    /**
     * Update data json.
     *
     * @return void
     */
    public function updateData($data)
    {
        $json = $this->getData();
        $this->data = json_encode(array_merge($json, $data));
    }

    /**
     * Get operators.
     *
     * @return options
     */
    public static function operators()
    {
        return [
            ['text' => trans('messages.equal'), 'value' => self::OPERATOR_EQUAL],
            ['text' => trans('messages.not_equal'), 'value' => self::OPERATOR_NOT_EQUAL],
            ['text' => trans('messages.contains'), 'value' => self::OPERATOR_CONTAINS],
            ['text' => trans('messages.not_contains'), 'value' => self::OPERATOR_NOT_CONTAINS],
            ['text' => trans('messages.starts'), 'value' => self::OPERATOR_STARTS],
            ['text' => trans('messages.ends'), 'value' => self::OPERATOR_ENDS],
            ['text' => trans('messages.not_starts'), 'value' => self::OPERATOR_NOT_STARTS],
            ['text' => trans('messages.not_ends'), 'value' => self::OPERATOR_NOT_ENDS],
            ['text' => trans('messages.greater'), 'value' => self::OPERATOR_GREATOR],
            ['text' => trans('messages.less'), 'value' => self::OPERATOR_LESS],
            ['text' => trans('messages.blank'), 'value' => self::OPERATOR_BLANK],
            ['text' => trans('messages.not_blank'), 'value' => self::OPERATOR_NOT_BLANK],
        ];
    }

    /**
     * Display message about the auto event.
     *
     * @return array
     */
    public function displayMessage()
    {
        if(empty($this->data)) {
            return trans('messages.event_not_set_yet');
        }

        $msg = '';
        switch ($this->event_type) {
            case self::TYPE_SPECIFIC_DATETIME:
                $msg = trans('messages.on_the_specific_date', [
                    'date' => \Acelle\Library\Tool::dayStringFromTimestamp(\Acelle\Library\Tool::dateTimeFromString($this->getDataValue('datetime'))),
                    'time' => \Acelle\Library\Tool::timeStringFromTimestamp(\Acelle\Library\Tool::dateTimeFromString($this->getDataValue('datetime')))
                ]);
                break;
            case self::TYPE_WEEKLY_RECURRING:
                $msg = trans('messages.weekly_recurring_description', [
                    'weekdays' => implode(', ', \Acelle\Library\Tool::numberArrayToWeekdaysArray($this->getDataValue('weekdays'))),
                    'weeks' => implode(', ', \Acelle\Library\Tool::numberArrayToWeeksArray($this->getDataValue('weeks'))),
                    'months' => implode(', ', \Acelle\Library\Tool::numberArrayToMonthsArray($this->getDataValue('months'))),
                    'time' => \Acelle\Library\Tool::timeStringFromTimestamp(\Acelle\Library\Tool::dateTimeFromString($this->getDataValue('time'))),
                ]);
                break;
            case self::TYPE_MONTHLY_RECURRING:
                $msg = trans('messages.monthly_recurring_description', [
                    'days' => implode(', ', \Acelle\Library\Tool::getDayNamesFromArrayOfNumber($this->getDataValue('days'))),
                    'months' => implode(', ', \Acelle\Library\Tool::numberArrayToMonthsArray($this->getDataValue('months'))),
                    'time' => \Acelle\Library\Tool::timeStringFromTimestamp(\Acelle\Library\Tool::dateTimeFromString($this->getDataValue('time'))),
                ]);
                break;
            case self::TYPE_LIST_SUBSCRIPTION:
                if(!empty($this->getDataValue('delay_value'))) {
                    $msg = trans('messages.event_type_list_subscription_description_with_delay', [
                        'list_name' => $this->automation->defaultMailList->name,
                        'delay' => $this->getDelayMessage()
                    ]);
                } else {
                    $msg = trans('messages.event_type_list_subscription_description', [
                        'list_name' => $this->automation->defaultMailList->name
                    ]);
                }
                break;
            case self::TYPE_LIST_UNSUBSCRIPTION:
                if(!empty($this->getDataValue('delay_value'))) {
                    $msg = trans('messages.event_type_list_unsubscription_description_with_delay', [
                        'list_name' => $this->automation->defaultMailList->name,
                        'delay' => $this->getDelayMessage()
                    ]);
                } else {
                    $msg = trans('messages.event_type_list_unsubscription_description', [
                        'list_name' => $this->automation->defaultMailList->name
                    ]);
                }
                break;
            case self::TYPE_SUBSCRIBER_EVENT:
                $event = $this->getDataValue('event');

                // check if event with field uid
                if($event != 'subscription_date') {
                    $field = \Acelle\Model\Field::findByUid($event);
                    $text = is_object($field) ? strtolower(trans('messages.subscriber_s_field', ['name' => $field->label])) : '';
                } else {
                    $text = strtolower(trans('messages.subscriber_s_field', ['name' => trans('messages.' . $event)]));
                }
                if(!empty($this->getDataValue('delay_value'))) {
                    $msg = trans('messages.event_type_subscriber_event_description_with_delay', [
                        'event' => $text,
                        'delay' => $this->getDelayMessage()
                    ]);
                } else {
                    $msg = trans('messages.event_type_subscriber_event_description', [
                        'event' => $text
                    ]);
                }
                break;
            case self::TYPE_CUSTOM_CRITERIA:
                $strings = [];
                foreach($this->getDataValue('criteria') as $criteria) {
                    $field = Field::findByUid($criteria["field_uid"]);
                    if (is_object($field)) {
                        if(in_array($criteria['operator'], [AutoEvent::OPERATOR_BLANK, AutoEvent::OPERATOR_NOT_BLANK])) {
                            $strings[] = trans('messages.event_type_custom_criteria_description_2', [
                                'field' => $field->label,
                                'operator' => trans('messages.' . $criteria["operator"]),
                                'value' => $criteria["value"],
                            ]);
                        } else {
                            $strings[] = trans('messages.event_type_custom_criteria_description', [
                                'field' => $field->label,
                                'operator' => trans('messages.' . $criteria["operator"]),
                                'value' => $criteria["value"],
                            ]);
                        }
                    }
                }

                if(!empty($this->getDataValue('delay_value'))) {
                    $msg = trans('messages.event_type_custom_criterias_description_with_delay', [
                        'conditions' => implode(' ' . trans('messages.criteria_and') . ' ', $strings),
                        'delay' => $this->getDelayMessage()
                    ]);
                } else {
                    $msg = trans('messages.event_type_custom_criterias_description', [
                        'conditions' => implode(' ' . trans('messages.criteria_and') . ' ', $strings)
                    ]);
                }
                break;
            case self::TYPE_FOLLOW_UP:
            case self::TYPE_FOLLOW_UP_OPENED:
            case self::TYPE_FOLLOW_UP_NOT_OPENED:
            case self::TYPE_FOLLOW_UP_CLICKED:
            case self::TYPE_FOLLOW_UP_NOT_CLICKED:
                if(empty($this->getDataValue('delay_value'))) {
                    $msg = trans('messages.follow_up_send_right_after', [
                        'event' => trans('messages.follow_up_event_' . $this->event_type)
                    ]);
                } else {
                    $msg = trans('messages.follow_up_description', [
                        'value' => $this->getDataValue('delay_value'),
                        'unit' => \Acelle\Library\Tool::getPluralPrase($this->getDataValue('delay_unit'), $this->getDataValue('delay_value')),
                        'event' => trans('messages.follow_up_event_' . $this->event_type)
                    ]);
                }
                break;
            case self::TYPE_API_CALL:
                $msg = trans('messages.event_type_api_description');
                break;
        }

        return $msg;
    }

    /**
     * Get delay message.
     *
     * @return array
     */
    public function getDelayMessage()
    {
        if($this->getDataValue('delay_value') !== NULL) {
            return trans('messages.delay_message', [
                'value' => $this->getDataValue('delay_value'),
                'unit' =>  trans('messages.' . $this->getDataValue('delay_unit')),
                'type' =>  trans('messages.' . $this->getDataValue('delay_type')),
            ]);
        }
    }

    /**
     * Time unit options.
     *
     * @return array
     */
    public static function timeUnitOptions()
    {
        return [
            ['value' => 'hour', 'text' => trans('messages.hour_s')],
            ['value' => 'day', 'text' => trans('messages.day_s')],
            ['value' => 'week', 'text' => trans('messages.week_s')],
            ['value' => 'month', 'text' => trans('messages.month_s')],
            ['value' => 'year', 'text' => trans('messages.year_s')],
        ];
    }

    /**
     * Delay type options.
     *
     * @return array
     */
    public static function delayTypeOptions()
    {
        return [
            ['value' => 'before', 'text' => trans('messages.before')],
            ['value' => 'after', 'text' => trans('messages.after')],
        ];
    }

    /**
     * Email events.
     *
     * @return array
     */
    public static function emailEventOptions()
    {
        return [
            ['value' => self::TYPE_FOLLOW_UP, 'text' => strtolower(trans('messages.sent'))],
            ['value' => self::TYPE_FOLLOW_UP_OPENED, 'text' => strtolower(trans('messages.opened'))],
            ['value' => self::TYPE_FOLLOW_UP_NOT_OPENED, 'text' => strtolower(trans('messages.not_opened'))],
                        ['value' => self::TYPE_FOLLOW_UP_CLICKED, 'text' => strtolower(trans('messages.clicked'))],
            ['value' => self::TYPE_FOLLOW_UP_NOT_CLICKED, 'text' => strtolower(trans('messages.not_clicked'))],
        ];
    }

    /**
     * Add campaign.
     *
     * @return array
     */
    public function addCampaign()
    {
        $campaign = new Campaign([
            'name' => 'Untitled automation email',
            'track_open' => true,
            'track_click' => true,
            'sign_dkim' => true,
        ]);
        $campaign->customer_id = $this->automation->customer_id;
        $campaign->is_auto = true;
        $campaign->type = Campaign::TYPE_REGULAR;
        $campaign->save();

        // Auto Campaign
        $auto_campaign = new AutoCampaign();
        $auto_campaign->campaign_id = $campaign->id;
        $auto_campaign->auto_event_id = $this->id;
        $auto_campaign->save();

        return $campaign;
    }

    /**
     * Next event.
     *
     * @return object
     */
    public function nextEvent()
    {
        return $this->hasOne('Acelle\Model\AutoEvent', 'previous_event_id');
    }

    /**
     * Previous event.
     *
     * @return object
     */
    public function previousEvent()
    {
        return $this->belongsTo('Acelle\Model\AutoEvent', 'previous_event_id');
    }

    /**
     * Check if the critera are met, then trigger the event
     *
     * @return NULL
     */
    public function check()
    {
        $method = $this->camelize('check-for-' . $this->event_type);
        return $this->$method();
    }

    /**
     * Check if the critera are met, then trigger the event
     *
     * @todo move to Helper instead
     */
    public function camelize($str)
    {
        return lcfirst(
          implode('', array_map(
              'ucfirst', array_map(
                  'strtolower', explode(
                  '-', $str))))
        );
    }

    /**
     * Check whether or not another trigger of the same event has occured on the same day
     *
     * @return boolean
     */
    public function hasTriggeredToday()
    {
        $zone = $this->automation->customer->getTimezone();
        $today = Carbon::now($zone)->toDateString();
        foreach($this->autoTriggers as $trigger) {
            if ($trigger->start_at->tz($zone)->toDateString() == $today) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for specific-datetimetime events
     *
     * @return NULL
     */
    public function checkForSpecificDatetime()
    {
        // this is a one-time triggered automation event
        // just abort if it is already triggered
        if (!$this->autoTriggers->isEmpty()) {
            return;
        }

        $now = Carbon::now();
        $event_date = Carbon::parse($this->getData()['datetime']);
        $checked = $now->gte($event_date);

        if ($checked) {
            MailLog::info(sprintf('Trigger sending automation `%s`, event type: %s, event ID: %s', $this->automation->name, $this->event_type, $this->id));
            $this->fire();
        }
    }

    /**
     * Check for anually-recurring events
     *
     * @return NULL
     */
    public function checkForCustomCriteria()
    {
        $subscribers = $this->subscribers()
                            ->whereRaw(sprintf(table('subscribers') . '.id NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('auto_triggers'), $this->id))->get();
        foreach ($subscribers as $subscriber) {
            MailLog::info(sprintf('Trigger sending custom-criteria email for automation `%s`, subscriber ID: %s, event ID: %s', $this->automation->name, $subscriber->id, $this->id));
            $this->fire(collect([$subscriber]), null, $this->getDelayInSeconds());
        }
    }

    /**
     * Check for anually-recurring events
     *
     * @return NULL
     */
    public function checkForAnuallyRecurring()
    {
        // not supported
    }

    /**
     * Check for subscriber-event
     * @note
     *   - Carbon automatically sets db zone to UTC before executing any query, so db zone offset is always +00:00
     *   - Make sure the custom field date format is [yyyy-mm-dd]
     *   - Only take date into consideration, no time, that is why DATE_FORMAT(.., '%m%d') is used
     *   - Do not use convert_tz(DateString, ...) as it may unexpectedly add the default time and change the result date
     *     For example, convert_tz('2016-12-25', '+00:00', '-02:00') will result in '2016-12-24 22:00:00'
     *   - Trigger only one time per year
     *
     * @return NULL
     */
    public function checkForSubscriberEvent()
    {
        $field = $this->getDataValue('event');

        // get the "at" configuration
        $schedule = $this->getData();
        $at = $schedule['at'];
        $userZoneOffset = db_quote(utc_offset($this->automation->customer->getTimezone()));
        $dbZoneOffset = db_quote('+00:00'); // Carbon automatically sets db zone to +00:00 before executing any query
        $delayInterval = $this->getDelayIntervalMySql();

        // get subscribers that match the criteria
        if ($field == 'subscription_date') {
            $subscribers = $this->subscribers()
                         ->whereRaw(sprintf('DATE_FORMAT(CONVERT_TZ(%5$s, %1$s, %2$s) + %3$s, \'%%m%%d\') = DATE_FORMAT(CONVERT_TZ(NOW(), %1$s, %2$s), \'%%m%%d\') AND DATE_FORMAT(CONVERT_TZ(\'2000-01-01 %4$s\', %1$s, %2$s), \'%%H%%i\') <= DATE_FORMAT(CONVERT_TZ(NOW(), %1$s, %2$s), \'%%H%%i\')', $dbZoneOffset, $userZoneOffset, $delayInterval, $at, table('subscribers.created_at')));
        } else {
            $subscribers = $this->subscribers()
                         ->join('subscriber_fields', 'subscribers.id', '=', 'subscriber_fields.subscriber_id')
                         ->join('fields', 'subscriber_fields.field_id', '=', 'fields.id')
                         ->where('fields.uid', $field)
                         ->whereRaw(sprintf('DATE_FORMAT(STR_TO_DATE(%5$s, \'%%Y-%%m-%%d\') + %3$s, \'%%m%%d\') = DATE_FORMAT(CONVERT_TZ(NOW(), %1$s, %2$s), \'%%m%%d\') AND DATE_FORMAT(CONVERT_TZ(\'2000-01-01 %4$s\', %1$s, %2$s), \'%%H%%i\') <= DATE_FORMAT(CONVERT_TZ(NOW(), %1$s, %2$s), \'%%H%%i\')', $dbZoneOffset, $userZoneOffset, $delayInterval, $at, table('subscriber_fields.value')));
        }

        // make sure the event is not yet trigger within the year
        $subscribers = $subscribers->whereRaw(sprintf('%5$s NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %3$s WHERE auto_event_id = %4$s AND EXTRACT(YEAR FROM CONVERT_TZ(NOW(), %1$s, %2$s)) = EXTRACT(YEAR FROM CONVERT_TZ(created_at, %1$s, %2$s)) )', $dbZoneOffset, $userZoneOffset, table('auto_triggers'), $this->id, table('subscribers.id')))->get();

        // actually trigger the event
        foreach ($subscribers as $subscriber) {
            MailLog::info(sprintf('Trigger sending subscriber-event email for automation `%s`, subscriber ID: %s, event ID: %s', $this->automation->name, $subscriber->id, $this->id));
            $this->fire(collect([$subscriber]));
        }
    }

    /**
     * Check for weekly-recurring events
     *
     * @return NULL
     */
    public function checkForWeeklyRecurring()
    {
        if ($this->matchMonth() && $this->matchWeekOfMonth() && $this->matchDayOfWeek() && $this->matchTime() && !$this->hasTriggeredToday()) {
            MailLog::info(sprintf('Trigger sending automation `%s`, event type: %s, event ID: %s', $this->automation->name, $this->event_type, $this->id));
            // @todo: check time as well
            $this->fire();
        }
    }

    /**
     * Check for weekly-recurring events
     *
     * @return NULL
     */
    public function checkForMonthlyRecurring()
    {
        if ($this->matchMonth() && $this->matchDayOfMonth() && $this->matchTime() && !$this->hasTriggeredToday()) {
            MailLog::info(sprintf('Trigger sending automation `%s`, event type: %s, event ID: %s', $this->automation->name, $this->event_type, $this->id));
            // @todo: check time as well
            $this->fire();
        }
    }

    /**
     * Check if the current date matches the scheduled month
     *
     * @return NULL
     */
    public function matchTime()
    {
        $zone = $this->automation->customer->getTimezone();
        $today = Carbon::now($zone)->toTimeString();
        $time = Carbon::parse($this->getDataValue('time'))->tz($zone)->toTimeString();
        return $today >= $time;
    }

    /**
     * Check if the current date matches the scheduled month
     *
     * @return NULL
     */
    public function matchMonth()
    {
        $months = $this->getDataValue('months');
        return in_array(Carbon::now($this->automation->customer->getTimezone())->month, $months, false);
    }

    /**
     * Check if the current date matches the scheduled month
     *
     * @return NULL
     */
    public function matchDayOfMonth()
    {
        $days = $this->getDataValue('days');
        return in_array(Carbon::now($this->automation->customer->getTimezone())->day, $days, false);
    }

    /**
     * Check if the current date matches the scheduled day of week
     *
     * @return NULL
     */
    public function matchDayOfWeek()
    {
        $weekdays = $this->getDataValue('weekdays');
        return in_array(Carbon::now($this->automation->customer->getTimezone())->dayOfWeek, $weekdays, false);
    }

    /**
     * Check if the current date matches the scheduled week of month
     *
     * @return NULL
     */
    public function matchWeekOfMonth()
    {
        $weeks = $this->getDataValue('weeks');
        return in_array(Carbon::now($this->automation->customer->getTimezone())->weekOfMonth, $weeks, false);
    }

    /**
     * Convert the delay setting to a human readable string compatible with Carbon interval
     * For example: "+1 day", "-2 hour"
     *
     * @return NULL
     */
    public function getDelayInterval()
    {
        $delay = $this->getData();
        $modifier = ( $delay['delay_type'] == 'before' ) ? '-' : '+';
        $base = $delay['delay_value'];
        $unit = $delay['delay_unit'];
        return "{$modifier}{$base} {$unit}";
    }

    /**
     * Convert the delay setting to MySQL interval
     * For example: "INTERVAL +1 day", "-2 hour"
     *
     * @return NULL
     */
    public function getDelayIntervalMySql()
    {
        $delay = $this->getData();
        $modifier = ( $delay['delay_type'] == 'before' ) ? '-' : '+';
        $base = $delay['delay_value'];
        $unit = $delay['delay_unit'];
        return "INTERVAL {$modifier}{$base} {$unit}";
    }

    /**
     * Convert the delay setting to interval (seconds)
     *
     * @return int seconds
     */
    public function getDelayInSeconds()
    {
        $now = Carbon::now();
        $delayed = $now->copy()->modify($this->getDelayInterval());
        return $delayed->diffInSeconds(`$now`);
    }

    /**
     * Check for follow-up events
     *
     * @return NULL
     */
    public function checkForFollowUp()
    {
        $now = Carbon::now();
        $preceding = $this->previousEvent;

        if (is_null($preceding)) {
            return;
        }

        // if already followed
        if ($preceding->triggersToFollowUp->isEmpty()) {
            return;
        }

        // One event may be triggered multiple times
        // For example: triggerd by birthday --> make sure to follow all these triggers
        foreach($preceding->triggersToFollowUp as $trigger) {
            if ($now->gte($trigger->start_at->copy()->modify($this->getDelayInterval()))) {
                // for follow-up type of auto-event, need to pass a preceding trigger
                // empty $trigger->subscriber indicates ALL
                MailLog::info(sprintf('Trigger sending follow-up email for automation %s, preceding event ID: %s, event ID: %s', $this->automation->name, $preceding->id, $this->id));
                if ($trigger->subscriber()->exists()) {
                    // follow up individual subscriber
                    $this->fire([$trigger->subscriber], $trigger);
                } else {
                    // follow up the entire list (for the FollowUpSend event)
                    $this->fire(null, $trigger);
                }
            }
        }
    }

    /**
     * Check for follow-up-opened events
     *
     * @return NULL
     */
    public function checkForFollowUpOpened()
    {
        $now = Carbon::now();
        $logs = $this->getOpenedMessagesToFollow();
        foreach($logs as $log) {
            if ($now->gte($log->created_at->copy()->modify($this->getDelayInterval()))) {
                MailLog::info(sprintf('Trigger sending follow-up opened email for automation `%s`, subscriber: %s, event ID: %s', $this->automation->name, $log->subscriber_id, $this->id));
                $this->fire(collect([$log->subscriber]));
                // a more verbal way is: fire(Subscriber::where('id', $log->subscriber_id)
            }
        }
    }

    /**
     * Check for follow-up-clicked events
     *
     * @return NULL
     */
    public function checkForFollowUpClicked()
    {
        $now = Carbon::now();
        $logs = $this->getClickedMessagesToFollow();

        foreach($logs as $log) {
            if ($now->gte($log->created_at->copy()->modify($this->getDelayInterval()))) {
                MailLog::info(sprintf('Trigger sending follow-up clicked email for automation `%s`, subscriber: %s, event ID: %s, message ID: %s', $this->automation->name, $log->subscriber_id, $this->id, $log->message_id));
                $this->fire(collect([$log->subscriber]));
                // a more verbal way is: fire(Subscriber::where('id', $log->subscriber_id)
            }
        }
    }

    /**
     * Check for follow-up-clicked events
     *
     * @return NULL
     */
    public function checkForListUnsubscription()
    {
        $now = Carbon::now();
        $subscribers = $this->getUnsubscribersToFollow();

        foreach ($subscribers as $subscriber) {
            if ($now->gte($subscriber->unsubscribed_at->copy()->modify($this->getDelayInterval()))) {
                MailLog::info(sprintf('Trigger sending follow-up unsubscribed email for automation `%s`, subscriber ID: %s, event ID: %s, message ID: %s', $this->automation->name, $subscriber->id, $this->id, $subscriber->message_id));
                $this->fire(collect([$subscriber]));
                // a more verbal way is: fire(Subscriber::where('id', $log->subscriber_id)
            }
        }
    }

    /**
     * Check for list-subscription events
     *
     * @return NULL
     */
    public function checkForListSubscription()
    {
        $now = Carbon::now();
        $subscribers = $this->getNewSubscribersToFollow();
        foreach($subscribers as $subscriber) {
            if ($now->gte($subscriber->created_at->copy()->modify($this->getDelayInterval()))) {
                $this->fire(collect([$subscriber]));
            }
        }
    }

    /**
     * Check for messages that have not been opened
     *
     * @return null
     */
    public function checkForFollowUpNotOpened()
    {
        $now = Carbon::now();
        $logs = $this->getNotOpenedMessagesToFollow();
        foreach($logs as $log) {
            MailLog::info(sprintf('Trigger sending follow-up not opened email for automation `%s`, subscriber: %s, event ID: %s', $this->automation->name, $log->subscriber_id, $this->id));
            $this->fire(collect([$log->subscriber]));
            // a more verbal way is: fire(Subscriber::where('id', $log->subscriber_id)
        }
    }

    /**
     * Check for messages that have not been clicked
     *
     * @return null
     */
    public function checkForFollowUpNotClicked()
    {
        $now = Carbon::now();
        $logs = $this->getNotClickedMessagesToFollow();
        foreach($logs as $log) {
            MailLog::info(sprintf('Trigger sending follow-up not clicked email for automation `%s`, subscriber: %s, event ID: %s', $this->automation->name, $log->subscriber_id, $this->id));
            $this->fire(collect([$log->subscriber]));
            // a more verbal way is: fire(Subscriber::where('id', $log->subscriber_id)
        }
    }

    /**
     * Actually send automation email
     *
     * @return NULL
     */
    public function fire($subscribers = null, $preceding_trigger = null, $delay = 0)
    {
        // log a trigger record
        $trigger = new AutoTrigger(['start_at' => Carbon::now()]);
        if (!is_null($preceding_trigger)) {
            $trigger->preceded_by = $preceding_trigger->id;
        }

        // @todo: retrieve the first subscriber only, what if there are more than one subscribers?
        if (!empty($subscribers) && !is_null($subscribers)) {
            $trigger->subscriber_id = $subscribers[0]->id;
        }

        $this->autoTriggers()->save($trigger);

        // schedule the corresponding job
        foreach($this->campaigns as $campaign) {
            $campaign->queue($subscribers, $trigger, $delay);
        };
    }

    /**
     * Get previous event's opened messages to follow up.
     *
     * @return collection
     */
    public function getNewSubscribersToFollow()
    {
        return $this->automation->subscribers()
                    ->whereRaw(sprintf(table('subscribers') . '.id NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('auto_triggers'), $this->id))
                    ->where('subscribers.created_at', '>=', $this->created_at)
                    ->whereRaw(sprintf("COALESCE(" . table('subscribers.subscription_type') . ", '') <> %s", db_quote(Subscriber::SUBSCRIPTION_TYPE_IMPORTED)))
                    ->get();
    }

    /**
     * Get previous event's opened messages to follow up.
     *
     * @return collection
     */
    public function getOpenedMessagesToFollow()
    {
        $messages = TrackingLog::select('tracking_logs.*')->join('open_logs', 'tracking_logs.message_id', '=', 'open_logs.message_id')->join('auto_triggers', 'tracking_logs.auto_trigger_id', '=', 'auto_triggers.id')->join('auto_events', 'auto_triggers.auto_event_id', '=', 'auto_events.id')->where('auto_event_id', $this->previousEvent->id)->whereRaw(sprintf(table('tracking_logs') . '.subscriber_id NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('auto_triggers'), $this->id))->get();

        // one message could be opened more than one time
        // @todo: use array_uniq_by() helper function for far better performance thant Collection::uniq()
        $unique = $messages->unique(function ($item) {
            return $item->message_id;
        });

        return $unique;
    }

    /**
     * Get messages that have not been clicked for a specified time
     *
     * @return collection
     */
    public function getNotOpenedMessagesToFollow()
    {
        // no problem if one message is opened more than one time
        $modifier = Carbon::now()->modify("-{$this->getDelayInterval()}");
        $messages = TrackingLog::select('tracking_logs.*')
            ->leftJoin('open_logs', 'tracking_logs.message_id', '=', 'open_logs.message_id')
            ->join('auto_triggers', 'tracking_logs.auto_trigger_id', '=', 'auto_triggers.id')
            ->join('auto_events', 'auto_triggers.auto_event_id', '=', 'auto_events.id')
            ->where('auto_event_id', $this->previousEvent->id)
            ->whereRaw(sprintf('%s NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('tracking_logs.subscriber_id'), table('auto_triggers'), $this->id))
            ->whereRaw(sprintf('%s IS NULL', table('open_logs.id')))
            ->where('tracking_logs.created_at', '<=', $modifier)
            ->get();

        // one message could be opened more than one time
        // @note: use array_uniq_by() helper function for far better performance thant Collection::uniq()
        $unique = array_unique_by($messages, function ($message) {
            return $message->message_id;
        });

        return $unique;
    }

    /**
     * Get messages that have not been opened for a specified time
     *
     * @return collection
     */
    public function getNotClickedMessagesToFollow()
    {
        // no problem if one message is opened more than one time
        $modifier = Carbon::now()->modify("-{$this->getDelayInterval()}");
        $messages = TrackingLog::select('tracking_logs.*')
            ->leftJoin('click_logs', 'tracking_logs.message_id', '=', 'click_logs.message_id')
            ->join('auto_triggers', 'tracking_logs.auto_trigger_id', '=', 'auto_triggers.id')
            ->join('auto_events', 'auto_triggers.auto_event_id', '=', 'auto_events.id')
            ->where('auto_event_id', $this->previousEvent->id)
            ->whereRaw(sprintf('%s NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('tracking_logs.subscriber_id'), table('auto_triggers'), $this->id))
            ->whereRaw(sprintf('%s IS NULL', table('click_logs.id')))
            ->where('tracking_logs.created_at', '<=', $modifier)
            ->get();

        // one message could be clicked more than one time
        // @note: use array_uniq_by() helper function for far better performance thant Collection::uniq()
        $unique = array_unique_by($messages, function ($message) {
            return $message->message_id;
        });

        return $unique;
    }

    /**
     * Get previous event's clicked messages to follow up.
     *
     * @return collection
     */
    public function getClickedMessagesToFollow()
    {
        $messages = TrackingLog::select('tracking_logs.*')->join('click_logs', 'tracking_logs.message_id', '=', 'click_logs.message_id')->join('auto_triggers', 'tracking_logs.auto_trigger_id', '=', 'auto_triggers.id')->join('auto_events', 'auto_triggers.auto_event_id', '=', 'auto_events.id')->where('auto_event_id', $this->previousEvent->id)->whereRaw(sprintf(table('tracking_logs') . '.subscriber_id NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('auto_triggers'), $this->id))->get();

        // one message could be clicked more than one time
        $unique = $messages->unique(function ($item) {
            return $item->message_id;
        });

        return $unique;
    }

    /**
     * Get previous event's unsubscribed messages to follow up.
     *
     * @return collection
     */
    public function getUnsubscribersToFollow()
    {
        return $this->automation->subscribers()
                    ->addSelect('unsubscribe_logs.created_at AS unsubscribed_at')
                    ->addSelect('tracking_logs.message_id')
                    ->join('tracking_logs', 'subscribers.id', '=', 'tracking_logs.subscriber_id')
                    ->join('unsubscribe_logs', 'tracking_logs.message_id', '=', 'unsubscribe_logs.message_id')
                    ->whereRaw(sprintf(table('subscribers') . '.id NOT IN (SELECT COALESCE(subscriber_id, 0) FROM %s WHERE auto_event_id = %s)', table('auto_triggers'), $this->id))
                    ->where('unsubscribe_logs.created_at', '>=', $this->created_at)
                    ->get();
    }

    /**
     * set event status to active.
     *
     * @return void
     */
    public function setActive()
    {
        $this->status = AutoEvent::STATUS_ACTIVE;
        $this->save();
    }

    /**
     * enable event.
     *
     * @return void
     */
    public function enable()
    {
        $this->status = AutoEvent::STATUS_ACTIVE;
        $this->save();
    }

    /**
     * disable event.
     *
     * @return void
     */
    public function disable()
    {
        $this->status = AutoEvent::STATUS_INACTIVE;
        $this->save();
    }

    /**
     * disable event.
     *
     * @return boolean
     */
    public function isValid()
    {
        // return true if it is inactive
        if($this->status == AutoEvent::STATUS_INACTIVE) {
            return true;
        }

        // check auto events emails empty
        if($this->campaigns()->count() <= 0) {
            return false;
        }

        // check if email is not design
        foreach($this->campaigns as $campaign) {
            if(!$campaign->autoCampaignDesigned()) {
                return false;
            }
        }

        return true;
    }

    /**
     * move event up.
     *
     * @return void
     */
    public function moveUp()
    {
        $preivous_id = $this->id;
        $previous_event = $this->previousEvent;

        // move 1st one
        $this->previous_event_id = $previous_event->previous_event_id;
        $this->save();

        // move 2nd
        $previous_event->previous_event_id = $preivous_id;
        $previous_event->save();
    }

    /**
     * move event down.
     *
     * @return void
     */
    public function moveDown()
    {
        $preivous_id = $this->previous_event_id;
        $next_event = $this->nextEvent;

        // move 1st
        $next_event->previous_event_id = $preivous_id;
        $next_event->save();

        // move 2nd
        $this->previous_event_id = $next_event->id;
        $this->save();
    }

    /**
     * Subscribers.
     *
     * @return collect
     * @todo: sanitize the input
     */
    public function subscribers($params = [])
    {
        $query = $this->automation->subscribers();
        return $query;
    }
}
