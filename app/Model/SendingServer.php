<?php

/**
 * SendingServer class.
 *
 * An abstract class for different types of sending servers
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
use Acelle\Library\RouletteWheel;
use Acelle\Library\Log as MailLog;
use Acelle\Library\QuotaTrackerFile;
use Carbon\Carbon;
use Acelle\Library\StringHelper;

class SendingServer extends Model
{
    const DELIVERY_STATUS_SENT = 'sent';
    const DELIVERY_STATUS_FAILED = 'failed';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $quotaTracker;
    protected $subAccount;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     * @note important! consider updating the $fillable variable, it will affect some other methods
     *
     */
    protected $fillable = [
        'name', 'type', 'host', 'aws_access_key_id', 'aws_secret_access_key', 'aws_region', 'domain', 'api_key', 'api_secret_key', 'smtp_username',
        'smtp_password', 'smtp_port', 'smtp_protocol', 'quota_value', 'sendmail_path', 'quota_base', 'quota_unit',
        'bounce_handler_id', 'feedback_loop_handler_id', 'status'
    ];

    // Supported server types
    public static $serverMapping = array(
        'amazon-api' => 'SendingServerAmazonApi',
        'amazon-smtp' => 'SendingServerAmazonSmtp',
        'smtp' => 'SendingServerSmtp',
        'sendmail' => 'SendingServerSendmail',
        'php-mail' => 'SendingServerPhpMail',
        'mailgun-api' => 'SendingServerMailgunApi',
        'mailgun-smtp' => 'SendingServerMailgunSmtp',
        'sendgrid-api' => 'SendingServerSendGridApi',
        'sendgrid-smtp' => 'SendingServerSendGridSmtp',
        'elasticemail-api' => 'SendingServerElasticEmailApi',
        'elasticemail-smtp' => 'SendingServerElasticEmailSmtp',
        'sparkpost-api' => 'SendingServerSparkPostApi',
        'sparkpost-smtp' => 'SendingServerSparkPostSmtp',
    );

    /**
     * Tracking logs.
     *
     * @return collection
     */
    public function trackingLogs()
    {
        return $this->hasMany('Acelle\Model\TrackingLog', 'sending_server_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get the bounce handler.
     */
    public function bounceHandler()
    {
        return $this->belongsTo('Acelle\Model\BounceHandler');
    }

    /**
     * Map a server to its class type and retrive an instance from the database
     *
     * @return mixed
     * @param campaign
     */
    public static function mapServerType($server)
    {
        $class_name = '\Acelle\Model\\'.self::$serverMapping[$server->type];

        return $class_name::find($server->id);
    }

    /**
     * Map a server to its class type and initiate an instance.
     *
     * @return object sending server of its particular type
     */
    public static function getInstance($server)
    {
        $class_name = '\Acelle\Model\\'.self::$serverMapping[$server->type];
        $attributes = $server->toArray();
        if (array_key_exists('id', $attributes)) {
            unset($attributes['id']);
        }

        return new $class_name($attributes);
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public function getVerp($recipient)
    {
        if (is_object($this->bounceHandler)) {
            $validator = \Validator::make(
                ['email' => $this->bounceHandler->username],
                ['email' => 'required|email']
            );

            if ($validator->passes()) {
                // @todo disable VERP as it is not supported by all mailbox
                // return str_replace('@', '+'.str_replace('@', '=', $recipient).'@', $this->bounceHandler->username);
                return $this->bounceHandler->username;
            }
        }

        return null;
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return self::where('status', '=', 'active');
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

    public function admin()
    {
        return $this->belongsTo('Acelle\Model\Admin');
    }

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $user = $request->user();
        $query = self::select('sending_servers.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('sending_servers.name', 'like', '%'.$keyword.'%')
                        ->orWhere('sending_servers.type', 'like', '%'.$keyword.'%')
                        ->orWhere('sending_servers.host', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;
        if (!empty($filters)) {
            if (!empty($filters['type'])) {
                $query = $query->where('sending_servers.type', '=', $filters['type']);
            }
        }

        // Other filter
        if(!empty($request->customer_id)) {
            $query = $query->where('sending_servers.customer_id', '=', $request->customer_id);
        }

        if(!empty($request->admin_id)) {
            $query = $query->where('sending_servers.admin_id', '=', $request->admin_id);
        }

        // remove customer sending servers
        if(!empty($request->no_customer)) {
            $query = $query->whereNull('customer_id');
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

        if(!empty($request->sort_order)) {
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        }

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
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

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
            while (SendingServer::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // SendingServer custom order
            SendingServer::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;
        });
    }

    /**
     * Type of server.
     *
     * @return object
     */
    public static function types()
    {
        return [
            'amazon-smtp' => [
                'cols' => [
                    'name' => 'required',
                    'host' => 'required',
                    'aws_access_key_id' => 'required',
                    'aws_secret_access_key' => 'required',
                    'aws_region' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required',
                    'smtp_protocol' => 'required',
                ],
            ],
            'amazon-api' => [
                'cols' => [
                    'name' => 'required',
                    'aws_access_key_id' => 'required',
                    'aws_secret_access_key' => 'required',
                    'aws_region' => 'required',
                ],
            ],
            'sendgrid-smtp' => [
                'cols' => [
                    'name' => 'required',
                    'api_key' => 'required',
                    'host' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required',
                ],
            ],
            'sendgrid-api' => [
                'cols' => [
                    'name' => 'required',
                    'api_key' => 'required',
                ],
            ],
            'mailgun-api' => [
                'cols' => [
                    'name' => 'required',
                    'domain' => 'required',
                    'api_key' => 'required',
                ],
            ],
            'mailgun-smtp' => [
                'cols' => [
                    'name' => 'required',
                    'domain' => 'required',
                    'api_key' => 'required',
                    'host' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required',
                    'smtp_protocol' => 'required',
                ],
            ],
            'elasticemail-api' => [
                'cols' => [
                    'name' => 'required',
                    'api_key' => 'required',
                ],
            ],
            'elasticemail-smtp' => [
                'cols' => [
                    'name' => 'required',
                    'api_key' => 'required',
                    'host' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required',
                ],
            ],
            'sparkpost-api' => [
                'cols' => [
                    'name' => 'required',
                    'api_key' => 'required',
                ],
            ],
            'sparkpost-smtp' => [
                'cols' => [
                    'name' => 'required',
                    'api_key' => 'required',
                    'host' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required',
                    'smtp_protocol' => '',
                ],
            ],
            'smtp' => [
                'cols' => [
                    'name' => 'required',
                    'host' => 'required',
                    'smtp_username' => 'required',
                    'smtp_password' => 'required',
                    'smtp_port' => 'required',
                    'smtp_protocol' => '',
                    'bounce_handler_id' => '',
                    'feedback_loop_handler_id' => '',
                ],
            ],
            'sendmail' => [
                'cols' => [
                    'name' => 'required',
                    'sendmail_path' => 'required',
                    'bounce_handler_id' => '',
                    'feedback_loop_handler_id' => '',
                ],
            ],
            'php-mail' => [
                'cols' => [
                    'name' => 'required',
                    'bounce_handler_id' => '',
                    'feedback_loop_handler_id' => '',
                ],
            ],
        ];
    }

    /**
     * Get select options.
     *
     * @return array
     */
    public static function getSelectOptions()
    {
        $query = self::getAll();
        $options = $query->orderBy('name')->get()->map(function ($item) {
            return ['value' => $item->uid, 'text' => $item->name];
        });

        return $options;
    }

    /**
     * Get sending server's quota.
     *
     * @return string
     */
    public function getSendingQuota()
    {
        return $this->quota_value;
    }

    /**
     * Get sending server's sending quota.
     *
     * @return string
     */
    public function getSendingQuotaUsage()
    {
        $tracker = $this->getQuotaTracker();
        return $tracker->getUsage();
    }

    /**
     * Get rules.
     *
     * @return string
     */
    public static function rules($type)
    {
        $rules = self::types()[$type]['cols'];
        $rules['quota_value'] = 'required|numeric';
        $rules['quota_base'] = 'required|numeric';
        $rules['quota_unit'] = 'required';

        return $rules;
    }

    /**
     * Quota display.
     *
     * @return string
     */
    public function displayQuota()
    {
        if ($this->quota_value == -1) {
            return trans('messages.unlimited');
        }
        return $this->quota_value.' / '.$this->quota_base.' '.trans('messages.'.\Acelle\Library\Tool::getPluralPrase($this->quota_unit, $this->quota_base));
    }

    /**
     * Select options for aws region.
     *
     * @return array
     */
    public static function awsRegionSelectOptions()
    {
        return [
            ['value' => '', 'text' => trans('messages.choose')],
            ['value' => 'us-east-1', 'text' => 'US East (N. Virginia)'],
            ['value' => 'us-west-2', 'text' => 'US West (Oregon)'],
            ['value' => 'ap-southeast-1', 'text' => 'Asia Pacific (Singapore)'],
            ['value' => 'ap-southeast-2', 'text' => 'Asia Pacific (Sydney)'],
            ['value' => 'ap-northeast-1', 'text' => 'Asia Pacific (Tokyo)'],
            ['value' => 'eu-central-1', 'text' => 'EU (Frankfurt)'],
            ['value' => 'eu-west-1', 'text' => 'EU (Ireland)'],
        ];
    }

    /**
     * Disable sending server
     *
     * @return array
     */
    public function disable()
    {
        $this->status = "inactive";
        $this->save();
    }

    /**
     * Enable sending server
     *
     * @return array
     */
    public function enable()
    {
        $this->status = "active";
        $this->save();
    }

    /**
     * Get sending server's QuotaTracker
     *
     * @return array
     */
    public function getQuotaTracker() {
        if(!$this->quotaTracker) {
            $this->initQuotaTracker();
        }

        return $this->quotaTracker;
    }

    /**
     * Initialize the quota tracker
     *
     * @return void
     */
    public function initQuotaTracker() {
        $this->quotaTracker = new QuotaTrackerFile($this->getSendingQuotaLockFile(), ['start' => $this->created_at->timestamp, 'max' => -1], [$this->getQuotaIntervalString() => $this->getSendingQuota()]);
        $this->quotaTracker->cleanupSeries();
        // @note: in case of multi-process, the following command must be issued manually
        //     $this->renewQuotaTracker();
    }

    /**
     * Clean up the quota tracking files to prevent it from growing too large
     *
     * @return void
     */
    function cleanupQuotaTracker()
    {
        // @todo: hard-coded for 1 month
        $this->getQuotaTracker()->cleanupSeries(null, '1 month');
    }

    /**
     * Get sending quota lock file
     *
     * @return string file path
     */
    public function getSendingQuotaLockFile() {
        return storage_path("app/server/quota/{$this->uid}");
    }

    /**
     * Get quota starting time
     *
     * @return string
     */
    public function getQuotaIntervalString() {
        return "{$this->quota_base} {$this->quota_unit}";
    }

    /**
     * Get quota starting time
     *
     * @return array
     */
    public function getQuotaStartingTime() {
        return "{$this->getQuotaIntervalString()} ago";
    }

    /**
     * Increment quota usage
     *
     * @return void
     */
    public function countUsage(Carbon $timePoint = null)
    {
        return $this->getQuotaTracker($timePoint)->add();
    }

    /**
     * Check if user has used up all quota allocated.
     *
     * @return string
     */
    public function overQuota()
    {
        return !$this->getQuotaTracker()->check();
    }

    /**
     * Check if sending server supports custom ReturnPath header (used for bounced/feedback handling)
     *
     * @return boolean
     */
    public function allowCustomReturnPath()
    {
        return ( $this->type == 'smtp' || $this->type == 'sendmail' || $this->type == 'php-mail' );
    }

    /**
     * Get all active items.
     *
     * @return collect
     */
    public static function getAllActive()
    {
        return self::where('status', '=', SendingServer::STATUS_ACTIVE);
    }

    /**
     * Get all active system items.
     *
     * @return collect
     */
    public static function getAllAdminActive()
    {
        return self::getAllActive()->whereNotNull('admin_id');
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
            'type' => 'sending_server',
            'name' => $name,
            'data' => json_encode($data),
        ]);
    }

    /**
     * Send a test email for the sending server
     */
    public function sendTestEmail($params)
    {
        /**
         * Required keys include
         *     + from_email
         *     + to_email
         *     + subject
         *     + plain
         */
        MailLog::info(sprintf("Sending test email to %s for sending server `%s`", $params['to_email'], $this->name));
        $message = \Swift_Message::newInstance();
        $msgId = StringHelper::generateMessageId(StringHelper::getDomainFromEmail($params['from_email']));
        $message->setId($msgId);
        $message->getHeaders()->addTextHeader('X-Acelle-Message-Id', $msgId); // this header is required for SendGrid API sending server
        $message->setContentType('text/plain; charset=utf-8');
        $message->setSubject($params['subject']);
        $message->setFrom($params['from_email']);
        $message->setTo($params['to_email']);
        $message->setReplyTo($params['from_email']);
        $message->setEncoder(\Swift_Encoding::get8bitEncoding());
        $message->addPart($params['plain'], 'text/plain');
        $result = self::getInstance($this)->send($message);

        if (array_key_exists('error', $result)) {
            throw new \Exception($result['error']);
        }

        return true;
    }

    /**
     * Check if the sending server is ElasticEmailAPI or ElasticEmailSmtp
     *
     * @return boolean
     */
    public function isElasticEmailServer()
    {
        return ($this->type == 'elasticemail-api' || $this->type == 'elasticemail-smtp');
    }

    /**
     * Get all sub-account supported sending server types
     *
     * @return array
     */
    public static function getSubAccountTypes()
    {
        return [
            'sendgrid-api',
            'sendgrid-smtp'
        ];
    }

    public function setSubAccount($subAccount)
    {
        $this->subAccount = $subAccount;
    }
}
