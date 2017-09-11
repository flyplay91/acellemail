<?php

/**
 * MailList class.
 *
 * Model class for log mail list
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
use DB;
use Illuminate\Support\Facades\Validator;
use Acelle\Library\Log as MailLog;
use Acelle\Library\RouletteWheel;
use Acelle\Library\StringHelper;
use Acelle\Model\SendingServer;
use Acelle\Model\EmailVerification;
use Acelle\Model\EmailVerificationServer;
use Acelle\Model\SystemJob;
use Acelle\Exceptions\VerificationProcessCancelledException;

class MailList extends Model
{
    // Subscribers to import every time
    const IMPORT_STATUS_NEW = 'new';
    const IMPORT_STATUS_RUNNING = 'running';
    const IMPORT_STATUS_FAILED = 'failed';
    const IMPORT_STATUS_DONE = 'done';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'default_subject', 'from_email', 'from_name',
        'remind_message', 'send_to', 'email_daily', 'email_subscribe',
        'email_unsubscribe', 'send_welcome_email', 'unsubscribe_notification',
        'subscribe_confirmation', 'all_sending_servers'
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'from_email' => 'required|email',
        'from_name' => 'required',
        //'remind_message' => 'required',
        'contact.company' => 'required',
        'contact.address_1' => 'required',
        'contact.country_id' => 'required',
        'contact.state' => 'required',
        'contact.city' => 'required',
        'contact.zip' => 'required',
        'contact.phone' => 'required',
        'contact.email' => 'required|email',
        'contact.url' => 'url',
        'email_subscribe' => 'regex:"^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$"',
        'email_unsubscribe' => 'regex:"^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$"',
        'email_daily' => 'regex:"^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,4})[\W]*$"',
    );

    // Server pools
    public static $serverPools = array();
    public static $itemsPerPage = 25;
    protected $currentSubscription;
    protected $sendingSevers = null;

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function fields()
    {
        return $this->hasMany('Acelle\Model\Field');
    }

    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer');
    }

    public function segments()
    {
        return $this->hasMany('Acelle\Model\Segment');
    }

    public function pages()
    {
        return $this->hasMany('Acelle\Model\Page');
    }

    public function page($layout)
    {
        return $this->pages()->where('layout_id', $layout->id)->first();
    }

    public function contact()
    {
        return $this->belongsTo('Acelle\Model\Contact');
    }

    public function subscribers()
    {
        return $this->hasMany('Acelle\Model\Subscriber');
    }

    public function campaigns()
    {
        return $this->belongsToMany('Acelle\Model\Campaign', 'campaigns_lists_segments', 'mail_list_id', 'campaign_id');
    }

    /**
     * has_many association with automations through automations_lists_segments
     */
    public function automations()
    {
        return $this->belongsToMany('Acelle\Model\Automation', 'automations_lists_segments', 'mail_list_id', 'automation_id');
    }

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (MailList::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // Update custom order
            MailList::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;
        });

        // Create uid when list created.
        static::created(function ($item) {
            //  Create list default fields
            $item->createDefaultFieds();
        });

        // detele
        static::deleted(function ($item) {
            //  Delete contact when list deleted
            $item->contact->delete();

            // Delete import jobs
            $item->importJobs()->delete();

            // Delete export jobs
            $item->exportJobs()->delete();
        });
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
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $customer = $request->user()->customer;
        $query = self::where('customer_id', '=', $customer->id);

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
     * Find item by uid.
     *
     * @return object
     */
    public static function findByUid($uid)
    {
        return self::where('uid', '=', $uid)->first();
    }

    /**
     * Get all fields.
     *
     * @return object
     */
    public function getFields()
    {
        return $this->fields()->orderBy('custom_order');
    }

    /**
     * Create default fields for list.
     */
    public function createDefaultFieds()
    {
        $this->fields()->create([
                            'mail_list_id' => $this->id,
                            'type' => 'text',
                            'label' => trans('messages.email'),
                            'tag' => 'EMAIL',
                            'required' => true,
                            'visible' => true,
                        ]);

        $this->fields()->create([
                            'mail_list_id' => $this->id,
                            'type' => 'text',
                            'label' => trans('messages.first_name'),
                            'tag' => \Acelle\Model\Field::formatTag(trans('messages.first_name_tag')),
                            'required' => false,
                            'visible' => true,
                        ]);

        $this->fields()->create([
                            'mail_list_id' => $this->id,
                            'type' => 'text',
                            'label' => trans('messages.last_name'),
                            'tag' => \Acelle\Model\Field::formatTag(trans('messages.last_name_tag')),
                            'required' => false,
                            'visible' => true,
                        ]);
    }

    /**
     * Get email field.
     *
     * @return object
     */
    public function getEmailField()
    {
        return $this->getFieldByTag('EMAIL');
    }

    /**
     * Get field by tag.
     *
     * @return object
     */
    public function getFieldByTag($tag)
    {
        return $this->fields()->where('tag', '=', $tag)->first();
    }

    /**
     * Get field by tag.
     *
     * @return object
     */
    public function getActiveSubscribers()
    {
        return $this->subscribers()->where('status', 'active')->get();
    }

    /**
     * Get field rules.
     *
     * @return object
     */
    public function getFieldRules()
    {
        $rules = [];
        foreach ($this->getFields as $field) {
            if ($field->tag == 'EMAIL') {
                $rules[$field->tag] = 'required|email';
            } elseif ($field->required) {
                $rules[$field->tag] = 'required';
            }
        }

        return $rules;
    }

    /**
     * Reset the sending server pool.
     *
     * @return mixed
     */
    public static function resetServerPools()
    {
        self::$serverPools = array();
    }

    /**
     * Check if a email is exsit.
     *
     * @param string the email
     *
     * @return bool
     */
    public function checkExsitEmail($email)
    {
        $valid = !filter_var($email, FILTER_VALIDATE_EMAIL) === false &&
            !empty($email) &&
            $this->subscribers()->where('email', '=', $email)->count() == 0;

        return $valid;
    }

    /**
     * Get segments select options.
     *
     * @return array
     */
    public function getSegmentSelectOptions($cache = false)
    {
        $options = $this->segments->map(function ($item) use ($cache) {
            return ['value' => $item->uid, 'text' => $item->name.' ('.$item->subscribersCount($cache).' '.strtolower(trans('messages.subscribers')).')'];
        });

        return $options;
    }

    /**
     * Count unsubscribe.
     *
     * @return array
     */
    public function unsubscribeCount()
    {
        return distinctCount($this->subscribers()->where('status', '=', 'unsubscribed'), 'subscribers.email');
    }

    /**
     * Unsubscribe rate.
     *
     * @return array
     */
    public function unsubscribeRate($cache = false)
    {
        $count = $this->subscribersCount($cache);
        if ($count == 0) {
            return 0;
        }

        return round($this->unsubscribeCount() / $count, 2);
    }

    /**
     * Count unsubscribe.
     *
     * @return array
     */
    public function subscribeCount()
    {
        return distinctCount($this->subscribers()->where('status', '=', 'subscribed'), 'subscribers.email');
    }

    /**
     * Unsubscribe rate.
     *
     * @return array
     */
    public function subscribeRate($cache = false)
    {
        $count = $this->subscribersCount($cache);
        if ($count == 0) {
            return 0;
        }

        return round($this->subscribeCount() / $count, 2);
    }

    /**
     * Count unsubscribe.
     *
     * @return array
     */
    public function unconfirmedCount()
    {
        return distinctCount($this->subscribers()->where('status', '=', 'unconfirmed'), 'subscribers.email');
    }

    /**
     * Count blacklisted.
     *
     * @return array
     */
    public function blacklistedCount()
    {
        return distinctCount($this->subscribers()->where('status', '=', 'blacklisted'), 'subscribers.email');
    }

    /**
     * Count blacklisted.
     *
     * @return array
     */
    public function spamReportedCount()
    {
        return distinctCount($this->subscribers()->where('status', '=', 'spam-reported'), 'subscribers.email');
    }

    /**
     * Add customer action log.
     */
    public function log($name, $customer, $add_datas = [])
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        $data = array_merge($data, $add_datas);

        Log::create([
            'customer_id' => $customer->id,
            'type' => 'list',
            'name' => $name,
            'data' => json_encode($data),
        ]);
    }

    /**
     * url count.
     */
    public function urlCount()
    {
        $query = CampaignLink::join('campaigns', 'campaigns.id', '=', 'campaign_links.campaign_id')
            ->where('campaigns.default_mail_list_id', '=', $this->id);

        return $query->count();
    }

    /**
     * Open count.
     */
    public function openCount()
    {
        $query = OpenLog::join('tracking_logs', 'tracking_logs.message_id', '=', 'open_logs.message_id')
            ->whereIn('tracking_logs.subscriber_id', function ($query) {
                $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
            });

        return $query->count();
    }

    /**
     * Get list click logs.
     *
     * @return mixed
     */
    public function clickLogs()
    {
        $query = ClickLog::join('tracking_logs', 'tracking_logs.message_id', '=', 'click_logs.message_id')
            ->whereIn('tracking_logs.subscriber_id', function ($query) {
                $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
            });

        return $query;
    }

    /**
     * Open count.
     */
    public function clickCount()
    {
        $query = $this->clickLogs();

        return $query->distinct('url')->count('url');
    }

    /**
     * Open count.
     */
    public function openUniqCount()
    {
        $query = OpenLog::join('tracking_logs', 'tracking_logs.message_id', '=', 'open_logs.message_id')
            ->whereIn('tracking_logs.subscriber_id', function ($query) {
                $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
            });

        return $query->distinct('subscriber_id')->count('subscriber_id');
    }

    /**
     * Tracking count.
     */
    public function trackingCount()
    {
        $query = TrackingLog::whereIn('tracking_logs.subscriber_id', function ($query) {
            $query->select('subscribers.id')
                    ->from('subscribers')
                    ->where('subscribers.mail_list_id', '=', $this->id);
        });

        return $query->count();
    }

    /**
     * Count open rate.
     *
     * @return number
     */
    public function openRate()
    {
        if ($this->trackingCount() == 0) {
            return 0;
        }

        return round(($this->openCount() / $this->trackingCount()) * 100, 2);
    }

    /**
     * Count open uniq rate.
     *
     * @return number
     */
    public function openUniqRate()
    {
        if ($this->trackingCount() == 0) {
            return 0;
        }

        return round(($this->openUniqCount() / $this->trackingCount()) * 100, 2);
    }

    /**
     * Count click rate.
     *
     * @return number
     */
    public function clickRate()
    {
        $open_count = $this->openCount();
        if ($open_count == 0) {
            return 0;
        }

        return round(($this->clickedEmailsCount() / $open_count) * 100, 2);
    }

    /**
     * Count unique clicked opened emails.
     *
     * @return number
     */
    public function clickedEmailsCount()
    {
        $query = $this->clickLogs();

        return $query->distinct('subscriber_id')->count('subscriber_id');
    }

    /**
     * Get other lists.
     *
     * @return number
     */
    public function otherLists()
    {
        return \Auth::user()->customer->lists()->where('id', '!=', $this->id)->get();
    }

    /**
     * Get name with subscrbers count.
     *
     * @return number
     */
    public function longName($cache = false)
    {
        $count = $this->subscribersCount($cache);
        return $this->name.' - '.$count.' '.trans('messages.'.\Acelle\Library\Tool::getPluralPrase('subscriber', $count)).'';
    }

    /**
     * Copy new list.
     */
    public function copy($name)
    {
        $copy = $this->replicate(['cache']);
        $copy->name = $name;
        $copy->created_at = \Carbon\Carbon::now();
        $copy->updated_at = \Carbon\Carbon::now();
        $copy->custom_order = 0;
        $copy->save();

        // Contact
        if (is_object($this->contact)) {
            $new_contact = $this->contact->replicate();
            $new_contact->save();

            // update contact
            $copy->contact_id = $new_contact->id;
            $copy->save();
        }

        // Remove default fields
        $copy->fields()->delete();
        // Fields
        foreach ($this->fields as $field) {
            $new_field = $field->replicate();
            $new_field->mail_list_id = $copy->id;
            $new_field->save();
        }

        // update cache
        $copy->updateCache();
    }

    /**
     * Get import jobs.
     *
     * @return number
     */
    public function importJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ImportSubscribersJob")
            ->where("data","like", "%\"mail_list_uid\":\"" . $this->uid . "\"%");
    }

    /**
     * Get last export job.
     *
     * @return number
     */
    public function getLastImportJob()
    {
        return $this->importJobs()
            ->orderBy("created_at","DESC")
            ->first();
    }

    /**
     * Get export jobs.
     *
     * @return number
     */
    public function exportJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ExportSubscribersJob")
            ->where("data","like", "%\"mail_list_uid\":\"" . $this->uid . "\"%");
    }

    /**
     * Get last export job.
     *
     * @return number
     */
    public function getLastExportJob()
    {
        return $this->exportJobs()
            ->orderBy("created_at","DESC")
            ->first();
    }

    /**
     * Get last export log file.
     *
     * @return string file path
     */
    public function getLastImportLog() {
        $data = json_decode($this->getLastImportJob()->data, true);
        return $data['log'];
    }

    /**
     * Export subscribers.
     *
     * @return void
     */
    public static function export($list, $customer, $job)
    {
        // Info from job
        $systemJob = $job->getSystemJob();
        $directory = $job->getPath();

        $file_path = $directory.'data.csv';

        // Import to database
        $total = $list->subscribersCount(); // no cache
        $success = 0;
        $error = 0;
        $lines_per_second = 1;
        $headers = [];
        foreach ($list->getFields as $key => $field) {
            $headers[] = $field->tag;
        }
        $headers = implode(',', $headers);

        // write csv
        $myfile = file_put_contents($file_path, $headers.PHP_EOL , FILE_APPEND | LOCK_EX);

        $num = 100;
        for($page = 0; $page <= ceil($total/$num); $page++) { // ceil($total/$num)
            $data = [];
            foreach ($list->subscribers()->skip($page*$num)->take($num)->get() as $key => $item) {
                $cols = [];
                foreach ($list->getFields as $key2 => $field) {
                    $value = $item->getValueByField($field);
                    $cols[] = $value;
                }
                $data[] = \Acelle\Library\Tool::arrayToCsv($cols, ',');

                ++$success;
            }

            // write csv
            $myfile = file_put_contents($file_path, implode("\r\n", $data).PHP_EOL , FILE_APPEND | LOCK_EX);

            $content_cache = trans('messages.import_export_statistics_line', [
                'total' => $total,
                'processed' => $success + $error,
                'success' => $success,
                'error' => $error,
            ]);

            // update system job
            $systemJob->data = json_encode([
                "mail_list_uid" => $list->uid,
                "customer_id" => $customer->id,
                "status" => "running",
                "message" => $content_cache,
                "total" => $total,
                "success" => $success,
                "error" => $error,
                "percent" => round((($success + $error) / $total) * 100, 0)
            ]);
            $systemJob->save();
        }

        $content_cache = trans('messages.import_export_statistics_line', [
            'total' => $total,
            'processed' => $success + $error,
            'success' => $success,
            'error' => $error,
        ]);

        // update system job
        $systemJob->data = json_encode([
            "mail_list_uid" => $list->uid,
            "customer_id" => $customer->id,
            "status" => "done",
            "message" => $content_cache,
            "total" => $total,
            "success" => $success,
            "error" => $error,
            "percent" => 100
        ]);
        $systemJob->save();

        // Action Log
        $list->log('export_success', $customer, ['count' => $success, 'error' => $error]);
    }

    /**
     * Send subscription confirmation email to subscriber.
     *
     * @return void
     */
    public function sendSubscriptionConfirmationEmail($subscriber) {
        $list = $this;

        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_confirmation_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $send_page->renderContent(null, $subscriber);
        $this->sendMail($subscriber, $send_page, $send_page->subject);
    }

    /**
     * Send list related email
     *
     * @return void
     */
    function send($message, $params = [])
    {
        $server = $this->pickSendingServer();
        $message->getHeaders()->addTextHeader('X-Acelle-Message-Id', StringHelper::generateMessageId(StringHelper::getDomainFromEmail($this->from_email)));
        return $server->send($message, $params);
    }

    /**
     * Send subscription confirmation email to subscriber.
     *
     * @return void
     */
    public function sendSubscriptionWelcomeEmail($subscriber) {
        $list = $this;

        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_welcome_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $this->sendMail($subscriber, $send_page, $send_page->subject);
    }

    /**
     * Send unsubscription goodbye email to subscriber.
     *
     * @return void
     */
    public function sendUnsubscriptionNotificationEmail($subscriber) {
        $list = $this;

        $layout = \Acelle\Model\Layout::where('alias', 'unsubscribe_goodbye_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $this->sendMail($subscriber, $send_page, $send_page->subject);
    }

    /**
     * Send unsubscription goodbye email to subscriber.
     *
     * @return void
     */
    public function sendProfileUpdateEmail($subscriber) {
        $list = $this;

        $layout = \Acelle\Model\Layout::where('alias', 'profile_update_email')->first();
        $send_page = \Acelle\Model\Page::findPage($list, $layout);
        $this->sendMail($subscriber, $send_page, $send_page->subject);
    }

    /**
     * Get date | datetime fields.
     *
     * @return array
     */
    public function getDateFields()
    {
        return $this->getFields()->whereIn('type', ['date', 'datetime'])->get();
    }

    /**
     * Get subscriber's fields select options.
     *
     * @return array
     */
    public function getSubscriberFieldSelectOptions()
    {
        $options = [];
        $options[] = ['text' => trans('messages.subscriber_subscription_date'), 'value' => 'subscription_date'];
        foreach ($this->getDateFields() as $field) {
            $options[] = ['text' => trans('messages.subscriber_s_field', ['name' => $field->label]), 'value' => $field->uid];
        }

        return $options;
    }

    /**
     * Read a CSV file, returning the meta information
     *
     * @param string file path
     * @return array [$headers, $availableFields, $lineCount, $results]
     */
    public function getRemainingAddSubscribersQuota()
    {
        $max = $this->customer->getOption('subscriber_max');
        $maxPerList = $this->customer->getOption('subscriber_per_list_max');

        $remainingForList = $maxPerList - $this->reload()->subscribers->count();
        $remaining = $max - $this->reload()->customer->subscribersCount(); // no cache

        if ($maxPerList == -1) {
            return ($max == -1) ? -1 : $remaining;
        }

        if ($max == -1) {
            return ($maxPerList == -1) ? -1 : $remainingForList;
        }

        return ($remainingForList > $remaining) ? $remaining : $remainingForList;
    }

    /**
     * Read a CSV file, returning the meta information
     *
     * @param string file path
     * @return array [$headers, $availableFields, $lineCount, $results]
     */
    private function readCsv($file)
    {
        try {
            // Fix the problem with MAC OS's line endings
            if (!ini_get("auto_detect_line_endings")) {
                ini_set("auto_detect_line_endings", '1');
            }

            // Read CSV files
            $lineCount = line_count($file) - 1; // do not count the header
            $reader = \League\Csv\Reader::createFromPath($file);
            $headers = array_map('strtolower', $reader->fetchOne());
            $fields = collect($this->fields)->map(function($field) { return strtolower($field->tag); })->toArray();

            // list's fields found in the input CSV
            $availableFields = array_intersect($headers, $fields);

            // split the entire list into smaller batches
            $results = $reader->fetchAssoc($headers);

            return [$headers, $availableFields, $lineCount, $results];
        } catch (\Exception $ex) {
            // @todo: translation here
            throw new \Exception("Invalid headers. Original error message is: " . $ex->getMessage());
        }
    }

    /**
     * Validate imported file's headers
     *
     * @param headers
     * @return true or throw an exception
     */
    private function validateCsvHeader($headers) {
        // @todo: validation rules required here, currently hard-coded
        $missing = array_diff(['email'], $headers);
        if (!empty($missing)) {
            // @todo: I18n is required here
            throw new \Exception(trans('messages.import_missing_header_field', ['fields' => implode(', ', $missing)]) );
        }
        return true;
    }

    /**
     * Validate imported record
     *
     * @param headers
     * @return boolean whether or not the record is valid
     */
    private function validateCsvRecord($record) {
        //@todo: failed validate should affect the count showing up on the UI (currently, failed is also counted as success)
        $validator = Validator::make(
            $record,
            Subscriber::$rules,
            ['email' => 'invalid email address']
        );

        return [$validator->passes(), $validator->errors()->all()];
    }

    /**
     * Import subscriber from a CSV file
     *
     * @param string original value
     * @return string quoted value
     * @todo: use MySQL escape function to correctly escape string with astrophe
     */
    public function import2($file, $customer, $system_job)
    {
        try {
            $processed_count = 0;
            $logger = $system_job->getLogger();
            $logger->info(trans('messages.Start_importing_for_list_uid', ['uid' => $this->uid]));

            // init the status
            $system_job->updateStatus([
                'status' => self::IMPORT_STATUS_RUNNING,
            ]);

            // Read CSV files
            list($headers, $availableFields, $lineCount, $results) = $this->readCsv($file);

            // validate headers, check for required fields
            // throw an exception in case of error
            $this->validateCsvHeader($availableFields);

            // update status, line count
            $system_job->updateStatus([ 'total' => $lineCount ]);

            // process by batches
            each_batch($results, config('app.import_batch_size'), true, function($batch) use ($logger, $availableFields, &$customer, &$processed_count, &$system_job) {
                // increment count
                $processed_count += sizeof($batch);

                // authorization
                if (!$customer->user->can('addMoreSubscribers', [$this, config('app.import_batch_size')])) {
                    // If use cannot create ANY other subscribers
                    if (!$customer->user->can('addMoreSubscribers', [$this, 1])) {
                        throw new \Exception(trans('messages.error_add_max_quota'));
                    } else {
                        $remaining = $this->getRemainingAddSubscribersQuota();
                        if ($remaining != -1) {
                            $batch = array_slice($batch, 0, $remaining);
                        }
                    }
                }

                // processing for every batch,
                // using transaction to only commit at the end of the batch execution
                DB::beginTransaction();

                // create a temporary table containing the input subscribers
                $tmpTable = table('__tmp_subscribers');
                // @todo: hard-coded charset and COLLATE
                $tmpFields = implode(',', array_map(function($field) { return "`{$field}` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci"; }, $availableFields));
                DB::statement("DROP TEMPORARY TABLE IF EXISTS {$tmpTable};
                               CREATE TEMPORARY TABLE {$tmpTable}({$tmpFields});
                               CREATE INDEX _index_email_{$tmpTable} ON {$tmpTable}(`email`);");

                // Insert subscriber fields from the batch to the temporary table
                // extract only fields whose name matches TAG NAME of MailList
                $data = collect($batch)->map(function($r) use ($availableFields) {
                    $record = array_only($r, $availableFields);
                    if (!is_null($record['email'])) {
                        // replace the non-break space (not a normal space) as well as all other spaces
                        $record['email'] = preg_replace('/[ \s*]*/', '', trim($record['email']));
                    }
                    return $record;
                })->toArray();

                // make the import data table unique by email
                $data = array_unique_by($data, function($r) {
                    return $r['email'];
                });

                // validate amd remove invalid records
                $data = array_where($data, function($key, $value) use ($logger) {
                    list($valid, $errors) = $this->validateCsvRecord($value);
                    if(!$valid) {
                        $logger->warning($value['email'].": ".implode(", ", $errors));
                    }
                    return $valid;
                });
                DB::table('__tmp_subscribers')->insert($data);

                // Insert new subscribers from temp table to the main table
                DB::statement("INSERT INTO " . table('subscribers') . "(uid, mail_list_id, email, status, subscription_type, created_at, updated_at)
                               SELECT UUID_SHORT(), " . $this->id . ", uniq.email, " . db_quote(Subscriber::STATUS_SUBSCRIBED) . ", " . db_quote(Subscriber::SUBSCRIPTION_TYPE_IMPORTED) . ", NOW(), NOW()
                               FROM (SELECT tmp.email FROM {$tmpTable} tmp LEFT JOIN " . table('subscribers') . " main ON (tmp.email = main.email AND main.mail_list_id = {$this->id}) WHERE main.email IS NULL) uniq");

                // Insert subscribers' custom fields to the fields table
                DB::statement("DELETE FROM " . table('subscriber_fields') . " WHERE subscriber_id IN (SELECT main.id FROM " . table('subscribers') . " main JOIN {$tmpTable} tmp ON main.email = tmp.email WHERE mail_list_id = " . $this->id . ")");
                foreach($availableFields as $field) {
                    $sql = "INSERT INTO " . table('subscriber_fields') . "(subscriber_id, field_id, value, created_at, updated_at)
                    SELECT t.subscriber_id, f.id, t.`{$field}`, NOW(), NOW()
                    FROM (SELECT main.id AS subscriber_id, tmp.{$field} FROM " . table('subscribers') . " main JOIN {$tmpTable} tmp ON tmp.email = main.email WHERE main.mail_list_id = " . $this->id . ") t
                    JOIN " . table('fields') . " f ON f.tag = '{$field}' AND f.mail_list_id = " . $this->id;
                    DB::statement($sql);
                }

                // update status, finish one batch
                $system_job->updateStatus([ 'processed' => $processed_count ]);

                // Cleanup
                DB::statement("DROP TEMPORARY TABLE IF EXISTS {$tmpTable};");

                // Actually write to the database
                DB::commit();
            });

            // Update status, finish all batches
            $system_job->updateStatus([ 'status' => self::IMPORT_STATUS_DONE, 'total' => $processed_count ]);

            // Trigger updating related campaigns cache
            $this->updateCachedInfo();

            // Action Log
            $this->log('import_success', $customer, ['count' => $processed_count, 'error' => '']);
            $logger->info(trans('messages.Finish_importing_for_list_uid', ['uid' => $this->uid]));
        } catch (\Exception $e) {
            // finish the transaction
            DB::rollBack();

            $this->updateCachedInfo();

            // update job status
            $system_job->updateStatus([
                'status' => self::IMPORT_STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            // Action Log
            $this->log('import_max_error', $customer, ['count' => $processed_count]);

            // write to job's logger
            $logger->error($e->getMessage());
        }
    }

    /**
     * Import subscriber from a CSV file (beta, for console only)
     *
     * @param string original value
     * @return string quoted value
     * @todo: use MySQL escape function to correctly escape string with astrophe
     */
    public function import3($file)
    {
        try {
            echo "Importing...\n";
            // Read CSV files
            list($headers, $availableFields, $lineCount, $results) = $this->readCsv($file);

            // validate headers, check for required fields
            // throw an exception in case of error
            $this->validateCsvHeader($availableFields);

            // process by batches
            each_batch($results, config('app.import_batch_size'), true, function($batch) use ($availableFields) {

                // authorization
                if (!$this->customer->user->can('addMoreSubscribers', [$this, config('app.import_batch_size')])) {
                    // If use cannot create ANY other subscribers
                    if (!$this->customer->user->can('addMoreSubscribers', [$this, 1])) {
                        throw new \Exception('Quota exceeded');
                    } else {
                        $remaining = $this->getRemainingAddSubscribersQuota();
                        if ($remaining != -1) {
                            $batch = array_slice($batch, 0, $remaining);
                        }
                    }
                }

                // processing for every batch,
                // using transaction to only commit at the end of the batch execution
                DB::beginTransaction();

                // create a temporary table containing the input subscribers
                $tmpTable = table('__tmp_subscribers');
                // @todo: hard-coded charset and COLLATE
                $tmpFields = implode(',', array_map(function($field) { return "`{$field}` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci"; }, $availableFields));
                DB::statement("DROP TEMPORARY TABLE IF EXISTS {$tmpTable};
                               CREATE TEMPORARY TABLE {$tmpTable}({$tmpFields});
                               CREATE INDEX _index_email_{$tmpTable} ON {$tmpTable}(`email`);");

                // Insert subscriber fields from the batch to the temporary table
                // extract only fields whose name matches TAG NAME of MailList
                $data = collect($batch)->map(function($r) use ($availableFields) {
                    $record = array_only($r, $availableFields);
                    if (!is_null($record['email'])) {
                        // replace the non-break space (not a normal space) as well as all other spaces
                        $record['email'] = preg_replace('/[ \s*]*/', '', trim($record['email']));
                    }
                    return $record;
                })->toArray();

                // make the import data table unique by email
                $data = array_unique_by($data, function($r) {
                    return $r['email'];
                });

                // validate amd remove invalid records
                $data = array_where($data, function($key, $value) {
                    list($valid, $errors) = $this->validateCsvRecord($value);
                    if(!$valid) {
                       echo "Warning: " . $value['email'].": ".implode(", ", $errors) . "\n";
                    }
                    return $valid;
                });
                DB::table('__tmp_subscribers')->insert($data);

                // Insert new subscribers from temp table to the main table
                DB::statement("INSERT INTO " . table('subscribers') . "(uid, mail_list_id, email, status, subscription_type, created_at, updated_at)
                               SELECT UUID_SHORT(), " . $this->id . ", uniq.email, " . db_quote(Subscriber::STATUS_SUBSCRIBED) . ", " . db_quote(Subscriber::SUBSCRIPTION_TYPE_IMPORTED) . ", NOW(), NOW()
                               FROM (SELECT tmp.email FROM {$tmpTable} tmp LEFT JOIN " . table('subscribers') . " main ON (tmp.email = main.email AND main.mail_list_id = {$this->id}) WHERE main.email IS NULL) uniq");

                // Insert subscribers' custom fields to the fields table
                DB::statement("DELETE FROM " . table('subscriber_fields') . " WHERE subscriber_id IN (SELECT main.id FROM " . table('subscribers') . " main JOIN {$tmpTable} tmp ON main.email = tmp.email WHERE mail_list_id = " . $this->id . ")");
                foreach($availableFields as $field) {
                    $sql = "INSERT INTO " . table('subscriber_fields') . "(subscriber_id, field_id, value, created_at, updated_at)
                    SELECT t.subscriber_id, f.id, t.`{$field}`, NOW(), NOW()
                    FROM (SELECT main.id AS subscriber_id, tmp.{$field} FROM " . table('subscribers') . " main JOIN {$tmpTable} tmp ON tmp.email = main.email WHERE main.mail_list_id = " . $this->id . ") t
                    JOIN " . table('fields') . " f ON f.tag = '{$field}' AND f.mail_list_id = " . $this->id;
                    DB::statement($sql);
                }

                // Cleanup
                DB::statement("DROP TEMPORARY TABLE IF EXISTS {$tmpTable};");

                // Actually write to the database
                DB::commit();
            });

            // Trigger updating related campaigns cache
            $this->updateCachedInfo();

            // Action Log
            echo "Import complete\n";
        } catch (\Exception $e) {
            // finish the transaction
            DB::rollBack();

            $this->updateCachedInfo();

            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Update List related cache
     *
     * @return void
     */
    public function updateCachedInfo()
    {
        // Update list's cached information
        $this->updateCache();

        // Trigger the CampaignUpdate event to update the campaign cache information
        foreach ($this->campaigns as $campaign) {
            $campaign->updateCache();
        }

        // Trigger the CampaignUpdate event to update the related automations' cache information
        foreach ($this->automations as $automation) {
            $automation->updateCache();
        }

        // Update segments cached information
        foreach ($this->segments as $segment) {
            $segment->updateCache();
        }

        // Update user's cached information
        $this->customer->updateCache();
    }

    /**
     * Reload mail list information
     *
     * @return object mail list model
     * @todo why reload() is needed?
     */
    public function reload() {
        return self::find($this->id);
    }

    public function mailListsSendingServers()
    {
        return $this->hasMany('Acelle\Model\MailListsSendingServer');
    }

    public function activeMailListsSendingServers()
    {
        return $this->mailListsSendingServers()
			->join('sending_servers', 'sending_servers.id', '=', 'mail_lists_sending_servers.sending_server_id')
			->where('sending_servers.status', '=', SendingServer::STATUS_ACTIVE);
    }

    /**
     * Update sending servers.
     *
     * @return array
     */
    public function updateSendingServers($servers)
    {
        $this->mailListsSendingServers()->delete();
        foreach ($servers as $key => $param) {
            if ($param['check']) {
                $server = SendingServer::findByUid($key);
                $row = new MailListsSendingServer();
                $row->mail_list_id = $this->id;
                $row->sending_server_id = $server->id;
                $row->fitness = $param['fitness'];
                $row->save();
            }
        }
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
            // @note: SubscriberCount must come first as its value shall be used by the others
            'SubscriberCount' => function(&$list) {
                return $list->subscribersCount(false);
            },
            'VerifiedSubscriberCount' => function(&$list) {
                return $list->countVerifiedSubscribers();
            },
            'ClickedRate' => function(&$list) {
                return $list->clickRate();
            },
            'UniqOpenRate' => function(&$list) {
                return $list->openUniqRate();
            },
            'SubscribeRate' => function(&$list) {
                return $list->subscribeRate(true);
            },
            'SubscribeCount' => function(&$list) {
                return $list->subscribeCount();
            },
            'UnsubscribeRate' => function(&$list) {
                return $list->unsubscribeRate(true);
            },
            'UnsubscribeCount' => function(&$list) {
                return $list->unsubscribeCount();
            },
            'UnconfirmedCount' => function(&$list) {
                return $list->unconfirmedCount();
            },
            'BlacklistedCount' => function(&$list) {
                return $list->blacklistedCount();
            },
            'SpamReportedCount' => function(&$list) {
                return $list->spamReportedCount();
            },
            'SegmentSelectOptions' => function(&$list) {
                return $list->getSegmentSelectOptions(true);
            },
            'LongName' => function(&$list) {
                return $list->longName(true);
            },
            'VerifiedSubscribersPercentage' => function(&$list) {
                return $list->getVerifiedSubscribersPercentage(true);
            },

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
                if ($key == 'SubscriberCount') {
                    // SubscriberCount cache must always be updated as its value will be used for the others
                    $this->cache = json_encode($cache);
                    $this->save();
                }
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
    public function readCache($key, $default = null)
    {
        $cache = json_decode($this->cache, true);
        if (is_null($cache)) {
            return $default;
        }
        if (array_key_exists($key, $cache)) {
            if (is_null($cache[$key])) {
                return $default;
            } else {
                return $cache[$key];
            }
        } else {
            return $default;
        }
    }

    /**
     * Send mails of list.
     *
     * @param Subscriber $subscriber
     * @param Page       $page
     * @param string     $title
     *
     * @var void
     */
    public function sendMail($subscriber, $page, $title)
    {
        $page->renderContent(null, $subscriber);

        $body = view('pages._email_content', ['page' => $page])->render();

        // Create a message
        $message = \Swift_Message::newInstance($title)
          ->setFrom(array($subscriber->mailList->from_email => $subscriber->mailList->from_name))
          ->setTo(array($subscriber->email, $subscriber->email => trans('messages.to_email_name')))
          ->addPart($body, 'text/html');

        try {
            $this->send($message, [
                'subscriber' => $subscriber
            ]);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            MailLog::error( $error );
            throw new \Exception( $error );
        }
    }

    public function getCurrentSubscription()
    {
        if (empty($this->currentSubscription)) {
            $this->currentSubscription = $this->customer->getCurrentSubscription();
        }

        return $this->currentSubscription;
    }

    /**
     * Pick one sending server associated to the Mail List
     *
     * @return Object SendingServer
     */
    public function pickSendingServer()
    {
        $selection = $this->getSendingServers();

        // raise an exception if no sending servers are available
        if (empty($selection)) {
            throw new \Exception(sprintf("No sending server available for Mail List ID %s", $this->id));
        }

        // do not raise an exception, just wait if sending servers are available but exceeding sending limit
        $blacklisted = [];

        while (true) {
            $id = RouletteWheel::generate($selection);
            if (empty(self::$serverPools[$id])) {
                $server = SendingServer::find($id);
                $server->cleanupQuotaTracker();
                MailLog::info(sprintf('Initialize delivery server `%s` (ID: %s)', $server->name, $id));

                $server = SendingServer::mapServerType($server);

                // flag the server to use sub-account instead
                $subscription = $this->getCurrentSubscription();
                if (!is_null($subscription->sub_account_id)) {
                    $server->setSubAccount($subscription->subAccount);
                }
                self::$serverPools[$id] = $server;
            }

            if (self::$serverPools[$id]->overQuota()) {
                // just wait until it is okie to go
                // log every 60 seconds
                if (!array_key_exists($id, $blacklisted) || time() - $blacklisted[$id] >= 60) {
                    $blacklisted[$id] = time();
                    MailLog::warning(sprintf("Sending server `%s` exceeds sending limit, skipped", self::$serverPools[$id]->name));
                }

                // if all sending servers are blacklisted
                if (sizeof($blacklisted) == sizeof($selection)) {
                    MailLog::warning("All sending servers exceed sending limit, waiting...");
                    sleep(30);
                }

                continue;
            }

            MailLog::info(sprintf('Pick up delivery server `%s` (ID: %s)', self::$serverPools[$id]->name, $id));
            return self::$serverPools[$id];
        }
    }

    /**
     * Check if list can send through it's sending servers.
     *
     * @var boolean
     */
    public function getSendingServers()
    {
        if (!is_null($this->sendingSevers)) {
            return $this->sendingSevers;
        }

        $result = [];
        $subscription = $this->getCurrentSubscription();

        // Check the customer has permissions using sending servers and has his own sending servers
        if ($this->customer->getOption("sending_server_option") == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN) {
            if ($this->all_sending_servers) {
                if ($this->customer->activeSendingServers()->count()) {
                    $result = $this->customer->activeSendingServers()->get()->map(function ($server) {
                        return [ $server->id, '100' ];
                    });
                }
            } elseif ($this->activeMailListsSendingServers()->count() ) {
                $result = $this->activeMailListsSendingServers()->get()->map(function ($server) {
                    return [ $server->sending_server_id, $server->fitness ];
                });
            }
        // If customer dont have permission creating sending servers
        } elseif ($this->customer->getOption("sending_server_option") == \Acelle\Model\Plan::SENDING_SERVER_OPTION_SYSTEM) {
            // Check if has sending servers for current subscription
            if (is_object($subscription)) {
                if ($subscription->getOption("all_sending_servers") == 'yes') {
                    if (\Acelle\Model\SendingServer::getAllAdminActive()->count()) {
                        $result = \Acelle\Model\SendingServer::getAllAdminActive()->get()->map(function ($server) {
                            return [ $server->id, '100' ];
                        });
                    }
                } elseif ($subscription->activeSubscriptionsSendingServers()->count()) {
                    $result = $subscription->activeSubscriptionsSendingServers()->get()->map(function ($server) {
                        return [ $server->sending_server_id, $server->fitness ];
                    });
                }
            }
        } elseif ($subscription->useSubAccount()) {
            $result[] = [$subscription->subAccount->sending_server_id, '100'];
        }

        $assoc = [];
        foreach($result as $server) {
            list($key, $fitness) = $server;
            $assoc[(int) $key] = $fitness;
        }

        $this->sendingSevers = $assoc;
        return $this->sendingSevers;
    }

    /**
     * Queue for list verification
     *
     */
    public function queueForVerification($serverId)
    {
        $job = $this->getRunningVerificationJob();

        if (is_null($job)) {
            $job = (new \Acelle\Jobs\VerifyMailListJob($this->id, $serverId));
            dispatch($job);
        } else {
            MailLog::info(sprintf('Verification process for list `%` already running', $this->id));
        }
    }

    /**
     * Run list verification process, triggered by a daemon
     *
     */
    public function runVerification($serverId)
    {
        try {
            $verifier = EmailVerificationServer::find($serverId);

            if (is_null($verifier)) {
                throw new \Exception(sprintf('Cannot find verification server with such ID: %s', $serverId));
            }
            $index = 1;
            $this->getUnverifiedSubscribers(function($result, $page, $total) use (&$index, $verifier) {
                MailLog::info("Verifying page {$page}");
                foreach ($result->get() as $subscriber) {
                    $job = $this->getRunningVerificationJob();
                    if (is_null($job)) {
                        throw new VerificationProcessCancelledException(sprintf('Mail list `%s`: verification process terminated', $this->id));
                    } elseif ($job->isCancelled()) {
                        // @todo it seems this is never the case, or in case of exception?
                        throw new VerificationProcessCancelledException(sprintf('Mail list `%s`: verification process cancelled', $this->id));
                    }
                    MailLog::info(sprintf("Start verifying %s/%s", $index, $total));
                    $subscriber->verify($verifier);
                    $index += 1;
                }
            });
        } catch (VerificationProcessCancelledException $e) {
            MailLog::warning($e->getMessage());
            // just finish
        }
    }

    /**
     * Stop list verification process (if any)
     *
     */
    public function stopVerification()
    {
        $job = $this->getRunningVerificationJob();
        if (is_null($job)) {
            MailLog::warning(sprintf('Mail list `%s`: verification process already terminated', $this->id));
        } else {
            $job->setCancelled();
            $job->clearJobs();
        }
    }

    /**
     * Reset verification data for list
     *
     */
    public function resetVerification()
    {
        EmailVerification::join('subscribers', 'subscribers.id', '=', 'email_verifications.subscriber_id')
                         ->where('mail_list_id', $this->id)
                         ->delete();
    }

    /**
     * Check if the verification process is running
     *
     */
    public function isVerificationRunning()
    {
        $job = $this->getRunningVerificationJob();
        return !is_null($job);
    }

    /**
     * Get current verification process
     * Note that FAILED is also considered "current"
     *
     */
    public function getRunningVerificationJob()
    {
        $job = SystemJob::where('name', 'Acelle\Jobs\VerifyMailListJob')
                        ->whereIn('status', [SystemJob::STATUS_NEW, SystemJob::STATUS_RUNNING, SystemJob::STATUS_FAILED])
                        ->where('data', $this->id)
                        ->first();
        return $job;
    }

    /**
     * Get unverified subscribers
     *
     */
    public function getUnverifiedSubscribers($callback)
    {
        $builder = $this->subscribers()->whereNotIn('id', function($q){
            $q->select('subscriber_id')->from('email_verifications');
        });

        $total = $builder->count();
        paginate($builder, function($result, $page) use ($callback, $total) {
            $callback($result, $page, $total);
        }, [ 'count' => $total ]);
    }

    /**
     * Count verified subscribers
     *
     */
    public function countVerifiedSubscribers()
    {
        return $this->subscribers()->whereIn('id', function($q){
            $q->select('subscriber_id')->from('email_verifications');
        })->count();
    }

    /**
     * get verified subscribers percentage
     *
     */
    public function getVerifiedSubscribersPercentage($cache = false)
    {
        $count = $this->subscribersCount($cache);
        if ($count == 0) {
            return 0.0;
        } else {
            return (float)$this->countVerifiedSubscribers() / $count;
        }
    }

    /**
     * Subscribers count
     *
     */
    public function subscribersCount($cache = false)
    {
        if ($cache) {
            return $this->readCache('SubscriberCount', 0);
        }
        return $this->subscribers()->count();
    }

    /**
     * Segments count
     *
     */
    public function segmentsCount()
    {
        return $this->segments()->count();
    }
}
