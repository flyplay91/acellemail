<?php

/**
 * Automation class.
 *
 * Model for automations
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
use Acelle\Library\Log as MailLog;

class Automation extends Model
{
    // Automation status
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active'; // equiv. to 'queue'
    const STATUS_INACTIVE = 'inactive';

    /**
     * Items per page.
     *
     * @var array
     */
    const ITEMS_PER_PAGE = 25;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mail_list_id', 'segment_id'
    ];

    /**
     * has_many association with mailList through automations_lists_segments
     */
    public function mailLists()
    {
        return $this->belongsToMany('Acelle\Model\MailList', 'automations_lists_segments', 'automation_id', 'mail_list_id');
    }

    /**
     * Check if automation is active
     *
     * @return boolean active or not
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Monitor and trigger automation events
     *
     * @return null
     */
    public static function run()
    {
        // @todo: usue a `scoped` instead
        $automations = self::where('status', self::STATUS_ACTIVE)->get();

        foreach ($automations as $automation) {
            MailLog::info(sprintf('Checking automation `%s`', $automation->name));
            foreach ($automation->autoEvents as $event) {
                if ($event->isActive()) {
                    $event->check();
                }
            }
        }
    }

    /**
     * Check if automation is active
     *
     * @return boolean active or not
     */
    public function start()
    {
        if (!$this->isActive()) {
            throw new \Exception('Automation is not ready');
        }

        MailLog::info(sprintf('Automation %s (ID: %s) started', $this->name, $this->id));

        $this->getInitEvent()->fire();

        return true;
    }

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating automation.
        static::creating(function ($automation) {
            // Create new uid
            $uid = uniqid();
            while (Automation::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $automation->uid = $uid;

            // Update custom order
            Automation::select('custom_order')->increment('custom_order', 1);
            $automation->custom_order = 0;
        });
    }

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer');
    }

    public function autoEvents()
    {
        return $this->hasMany('Acelle\Model\AutoEvent');
    }

    public function getCampaigns($request=NULL)
    {
        $query = Campaign::select('campaigns.*')
            ->leftJoin('auto_campaigns', 'auto_campaigns.campaign_id', '=', 'campaigns.id')
            ->leftJoin('auto_events', 'auto_campaigns.auto_event_id', '=', 'auto_events.id')
            ->leftJoin('automations', 'auto_events.automation_id', '=', 'automations.id')
            ->where('automations.id', '=', $this->id);

        // Keyword
        if(isset($request)) {
            if (!empty(trim($request->keyword))) {
                $query = $query->where('campaigns.name', 'like', '%'.$request->keyword.'%');
            }
        }

        $query = $query->orderBy('auto_events.created_at', 'asc');

        return $query;
    }

    /**
     * The validation rules for automation.
     *
     * @var array
     */
    public function rules()
    {
        return [
            'init' => [
                'name' => 'required',
                'mail_list_uid' => 'required',
            ]
        ];
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
     * Fill attributes from array.
     *
     * @var void
     */
    public function fillAttributes($params)
    {
        $this->fill($params);

        // Fill mail list with mail_list_uid
        if(!empty($params["mail_list_uid"])) {
            $this->mail_list_id = MailList::findByUid($params["mail_list_uid"])->id;
        }

        // Fill segment with segment_uid
        if(!empty($params["segment_uid"])) {
            $this->segment_id = Segment::findByUid($params["segment_uid"])->id;
        } elseif(!empty($params)) {
            $this->segment_id = null;
        }
    }

    /**
     * Get the first/init auto event.
     *
     * @var AutoEvent
     */
    public function getInitEvent()
    {
        $auto_event = $this->autoEvents()->where('previous_event_id', '=', NULL)
            ->orderBy('created_at', 'ASC')
            ->first();

        if(!is_object($auto_event)) {
            $auto_event = new AutoEvent([
                'event_type' => AutoEvent::TYPE_SPECIFIC_DATETIME,
            ]);
            $auto_event->status = AutoEvent::STATUS_DRAFT;

            $auto_event->automation_id = $this->id;
        }

        return $auto_event;
    }

    /**
     * Get the first/init auto event.
     *
     * @var AutoEvent
     */
    public function getFollowUpEvents($show_inactive=true)
    {
        $events = collect();
        $event = $this->getInitEvent();
        while(is_object($event->nextEvent)) {
            $event = $event->nextEvent;
            if($show_inactive || $event->status != AutoEvent::STATUS_INACTIVE) {
                $events->push($event);
            }
        }

        return $events;
    }

    /**
     * Get last auto event.
     *
     * @var AutoEvent
     */
    public function getLastEvent()
    {
        $event = $this->getInitEvent();

        while (is_object($event->nextEvent)) {
            $event = $event->nextEvent;
        }

        return $event;
    }

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $query = self::where('customer_id', '=', $user->customer->id);

        // Keyword
        if (!empty(trim($request->keyword))) {
            $query = $query->where('name', 'like', '%'.$request->keyword.'%');
        }

        return $query;
    }

    /**
     * Search items.
     *
     * @return collect
     */
    public static function search($request)
    {
        $query = self::filter($request);

        $query = $query->orderBy($request->sort_order, $request->sort_direction);

        return $query;
    }

    /**
     * Subscribers.
     *
     * @return collect
     */
    public function subscribers($params=[])
    {
        if($this->listsSegments->isEmpty()) {
            // this is a trick for returning an empty builder
            return Subscriber::limit(0);
        }

        $query = Subscriber::select("subscribers.*");
        // Email verification join
        $query = $query->leftJoin('email_verifications', 'email_verifications.subscriber_id', '=', 'subscribers.id');

        // Get subscriber from mailist and segment
        $conditions = [];
        foreach($this->listsSegments as $lists_segment) {
            if(!empty($lists_segment->segment_id)) {
                $conds = $lists_segment->segment->getSubscribersConditions();

                // JOINS...
                if (!empty($conds['joins'])) {
                    foreach ($conds['joins'] as $joining) {
                        $query = $query->leftJoin($joining['table'], function($join) use ($joining)
                        {
                            $join->on($joining['ons'][0][0], '=', $joining['ons'][0][1]);
                            if (isset($joining['ons'][1])) {
                                $join->on($joining['ons'][1][0], '=', $joining['ons'][1][1]);
                            }
                        });
                    }
                }

                // WHERE...
                if (!empty($conds['conditions'])) {
                    $conditions[] = $conds['conditions'];
                }
            } else {
                $conditions[] = '(' . table('subscribers') . '.mail_list_id = ' . $lists_segment->mail_list_id . ')';
            }
        }

        if (!empty($conditions)) {
            $query = $query->whereRaw(implode(' OR ', $conditions));
        }

        return $query;
    }

    /**
     * Create new auto event.
     *
     * @return array
     */
    public function createAutoEvent()
    {
        $event = new AutoEvent([
            'event_type' => AutoEvent::TYPE_FOLLOW_UP
        ]);
        $event->status = AutoEvent::STATUS_DRAFT;

        $event->updateData([
            'delay_type' => 'after',
            'delay_value' => '1',
            'delay_unit' => 'day',
        ]);
        $event->previous_event_id = $this->getLastEvent()->id;

        $this->autoEvents()->save($event);

        return $event;
    }

    /**
     * Delete automation with its events.
     *
     * @return array
     */
    public function doDelete()
    {
        // remove contraints for deleting auto events
        $this->autoEvents()->update([
            'previous_event_id' => NULL
        ]);

        // Delete automation
        $this->delete();
    }

    /**
     * Automation valid.
     *
     * @return array
     */
    public function isValid()
    {
        // check first auto event isset
        $first_event = $this->getInitEvent();
        if(!isset($first_event->id)) { //  ||
            return false;
        }

        // check each auto events is valid
        foreach($this->autoEvents as $auto_event) {
            if(!$auto_event->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * set automation status to active.
     *
     * @return void
     */
    public function setActive()
    {
        $this->status = Automation::STATUS_ACTIVE;

        // set active for all events if they are draft
        foreach($this->autoEvents as $auto_event) {
            if($auto_event->status == AutoEvent::STATUS_DRAFT) {
                $auto_event->setActive();
            }
        }

        $this->save();
    }

    /**
     * Create customer action log.
     *
     * @param string     $cat
     * @param Customer  $customer
     * @param array      $add_datas
     */
    public function log($name, $customer, $add_datas = [])
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        if (is_object($this->defaultMailList)) {
            $data['list_id'] = $this->mail_list_id;
            $data['list_name'] = $this->defaultMailList->name;
        }

        if (is_object($this->segment)) {
            $data['segment_id'] = $this->segment_id;
            $data['segment_name'] = $this->segment->name;
        }

        $data = array_merge($data, $add_datas);

        \Acelle\Model\Log::create([
            'customer_id' => $customer->id,
            'type' => 'automation',
            'name' => $name,
            'data' => json_encode($data),
        ]);
    }

    /**
     * enable automation.
     *
     * @return void
     */
    public function enable()
    {
        $this->status = Automation::STATUS_ACTIVE;
        $this->save();
    }

    /**
     * disable automation.
     *
     * @return void
     */
    public function disable()
    {
        $this->status = Automation::STATUS_INACTIVE;
        $this->save();
    }

    /**
     * Get automation lists segments.
     *
     * @return mixed
     */
    public function getListsSegments()
    {
        $lists_segments = $this->listsSegments;

        if($lists_segments->isEmpty()) {
            $lists_segment = new AutomationsListsSegment();
            $lists_segment->automation_id = $this->id;
            $lists_segment->is_default = true;

            $lists_segments->push($lists_segment);
        }

        return $lists_segments;
    }

    /**
     * Get automation lists segments group by list.
     *
     * @return mixed
     */
    public function getListsSegmentsGroups()
    {
        $lists_segments = $this->getListsSegments();
        $groups = [];

        foreach($lists_segments as $lists_segment) {
            if(!isset($groups[$lists_segment->mail_list_id])) {
                $groups[$lists_segment->mail_list_id] = [];
                $groups[$lists_segment->mail_list_id]['list'] = $lists_segment->mailList;
                if($this->default_mail_list_id == $lists_segment->mail_list_id) {
                    $groups[$lists_segment->mail_list_id]['is_default'] = true;
                } else {
                    $groups[$lists_segment->mail_list_id]['is_default'] = false;
                }
                $groups[$lists_segment->mail_list_id]['segment_uids'] = [];
            }
            if(is_object($lists_segment->segment) && !in_array($lists_segment->segment->uid, $groups[$lists_segment->mail_list_id]['segment_uids'])) {
                $groups[$lists_segment->mail_list_id]['segment_uids'][] = $lists_segment->segment->uid;
            }
        }

        return $groups;
    }

    /**
     * Get automation list segment.
     *
     * @return mixed
     */
    public function listsSegments()
    {
        return $this->hasMany('Acelle\Model\AutomationsListsSegment');
    }

    /**
     * Fill recipients by params.
     *
     * @var void
     */
    public function fillRecipients($params=[])
    {
        if(isset($params['lists_segments'])) {
            foreach ($params['lists_segments'] as $key => $param) {
                if(!empty($param['mail_list_uid'])) {
                    $mail_list = MailList::findByUid($param['mail_list_uid']);

                    // default mail list id
                    if(isset($param['is_default']) && $param['is_default'] == 'true') {
                        $this->default_mail_list_id = $mail_list->id;
                    }

                    if(!empty($param['segment_uids'])) {
                        foreach ($param['segment_uids'] as $segment_uid) {
                            $segment = Segment::findByUid($segment_uid);

                            $lists_segment = new AutomationsListsSegment();
                            $lists_segment->automation_id = $this->id;
                            if(is_object($mail_list)) {
                                $lists_segment->mail_list_id = $mail_list->id;
                            }
                            $lists_segment->segment_id = $segment->id;
                            $this->listsSegments->push($lists_segment);
                        }
                    } else {
                        $lists_segment = new AutomationsListsSegment();
                        $lists_segment->automation_id = $this->id;
                        if(is_object($mail_list)) {
                            $lists_segment->mail_list_id = $mail_list->id;
                        }
                        $this->listsSegments->push($lists_segment);
                    }
                }
            }
        }
    }

    /**
     * Save Recipients.
     *
     * @var void
     */
    public function saveRecipients($params=[])
    {
        // Empty current data
        $this->listsSegments = collect([]);
        // Fill params
        $this->fillRecipients($params);

        $lists_segments_groups = $this->getListsSegmentsGroups();

        $data = [];
        foreach($lists_segments_groups as $lists_segments_group) {
            if(!empty($lists_segments_group['segment_uids'])) {
                foreach($lists_segments_group['segment_uids'] as $segment_uid) {
                    $segment = Segment::findByUid($segment_uid);
                    $data[] = [
                        'automation_id' => $this->id,
                        'mail_list_id' => $lists_segments_group['list']->id,
                        'segment_id' => $segment->id,
                    ];
                }
            } else {
                $data[] = [
                    'automation_id' => $this->id,
                    'mail_list_id' => $lists_segments_group['list']->id,
                    'segment_id' => NULL,
                ];
            }
        }

        // Empty old data
        $this->listsSegments()->delete();

        // Insert Data
        AutomationsListsSegment::insert($data);

        // Save campaign with default list id
        $automation = Automation::find($this->id);
        $automation->default_mail_list_id = $this->default_mail_list_id;
        $automation->save();
    }

    /**
     * Display Recipients.
     *
     * @var array
     */
    public function displayRecipients()
    {
        if(!is_object($this->defaultMailList)) {
            return "";
        }

        $lines = [];
        foreach($this->getListsSegmentsGroups() as $lists_segments_group) {
            $list_name = $lists_segments_group['list']->name;

            $segment_names = [];
            if(!empty($lists_segments_group['segment_uids'])) {
                foreach($lists_segments_group['segment_uids'] as $segment_uid) {
                    $segment = Segment::findByUid($segment_uid);
                    $segment_names[] = $segment->name;
                }
            }

            if(empty($segment_names)) {
                $lines[] = $list_name;
            } else {
                $lines[] = implode(': ', [$list_name, implode(', ', $segment_names)]);
            }
        }

        return implode(' | ', $lines);
    }

    /**
     * Get default mail list.
     *
     * @var array
     */
    public function defaultMailList()
    {
        return $this->belongsTo('Acelle\Model\MailList', 'default_mail_list_id');
    }

    /**
     * The validation rules for automation trigger.
     *
     * @var array
     */
    public function recipientsRules($params=[])
    {
        $rules = [
            'lists_segments' => 'required',
            'name' => 'required'
        ];

        if(isset($params['lists_segments'])) {
            foreach ($params['lists_segments'] as $key => $param) {
                $rules['lists_segments.'.$key.'.mail_list_uid'] = 'required';
            }
        }

        return $rules;
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return self::select('*');
    }

    /**
     * Count subscribers.
     *
     * @return integer
     */
    public function subscribersCount($cache = false)
    {
        if ($cache) {
            return $this->readCache('SubscriberCount');
        }
        return distinctCount($this->subscribers(), 'subscribers.email');
    }

    /**
     * Update Campaign cached data
     *
     * @return void
     */
    public function updateCache($key = null)
    {
        // cache indexes
        $index = [
            'SubscriberCount' => function(&$automation) {
                return $automation->subscribersCount(false);
            }
        ];

        // retrieve cached data
        $cache = json_decode($this->cache, true);
        if (is_null($cache)) {
            $cache = [];
        }

        if (is_null($key)) {
            // update all cache
            foreach($index as $key => $callback) {
                $cache[$key] = $callback($this);
            }
        } else {
            // update specific key
            $callback = $index[$key];
            $cache[$key] = $callback($this);
        }

        // write back to the DB
        $this->cache = json_encode($cache);
        $this->save();
    }

    /**
     * Retrieve Campaign cached data
     *
     * @return mixed
     */
    public function readCache($key, $default = null, $update = false)
    {
        if ($update) {
            $this->updateCacheDelayed();
        }

        $cache = json_decode($this->cache, true);
        if (empty($cache)) {
            return $default;
        } elseif (!array_key_exists($key, $cache)) {
            return $default;
        } else {
            return $cache[$key];
        }
    }

    /**
     * Trigger the AutomationUpdated event to update cached information
     *
     * @return void
     */
    public function updateCacheDelayed()
    {
        event(new \Acelle\Events\AutomationUpdated($this));
    }
}
