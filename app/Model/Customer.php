<?php

/**
 * Customer class.
 *
 * Model class for customer
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

use Acelle\Library\QuotaTrackerFile;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Acelle\Library\Log as MailLog;
use Acelle\Model\MailList;
use Acelle\Model\Subscriber;

class Customer extends Model
{
    protected $quotaTracker;

    // Plan status
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'timezone', 'language_id', 'color_scheme'
    ];

    protected $currentSubscription;

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        $rules = array(
            'email' => 'required|email|unique:users,email,'.$this->user_id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'timezone' => 'required',
            'language_id' => 'required',
        );

        if (isset($this->id)) {
            $rules['password'] = 'confirmed|min:5';
        } else {
            $rules['password'] = 'required|confirmed|min:5';
        }

        return $rules;
    }

    /**
     * Customer email.
     *
     * @return string
     */
    public function email()
    {
        return (is_object($this->user) ? $this->user->email : "" );
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
     * Associations.
     *
     * @var object | collect
     */
    public function contact()
    {
        return $this->belongsTo('Acelle\Model\Contact');
    }

    public function user()
    {
        return $this->belongsTo('Acelle\Model\User');
    }

    public function admin()
    {
        return $this->belongsTo('Acelle\Model\Admin');
    }

    public function lists()
    {
        return $this->hasMany('Acelle\Model\MailList')->orderBy('created_at', 'desc');
    }

    public function language()
    {
        return $this->belongsTo('Acelle\Model\Language');
    }

    public function campaigns()
    {
        return $this->hasMany('Acelle\Model\Campaign')->orderBy('created_at', 'desc');
    }

    public function sentCampaigns()
    {
        return $this->hasMany('Acelle\Model\Campaign')->where('status', '=', 'done')->orderBy('created_at', 'desc');
    }

    public function subscribers()
    {
        return $this->hasManyThrough('Acelle\Model\Subscriber', 'Acelle\Model\MailList');
    }

    public function logs()
    {
        return $this->hasMany('Acelle\Model\Log')->orderBy('created_at', 'desc');
    }

    public function trackingLogs()
    {
        return $this->hasMany('Acelle\Model\TrackingLog')->orderBy('created_at', 'asc');
    }

    public function automations()
    {
        return $this->hasMany('Acelle\Model\Automation');
    }

    public function subscriptions()
    {
        return $this->hasMany('Acelle\Model\Subscription')->orderBy('created_at', 'desc');
    }

    public function sendingDomains()
    {
        return $this->hasMany('Acelle\Model\SendingDomain');
    }

    public function emailVerificationServers()
    {
        return $this->hasMany('Acelle\Model\EmailVerificationServer');
    }

    public function activeEmailVerificationServers()
    {
        return $this->emailVerificationServers()->where("status", "=", EmailVerificationServer::STATUS_ACTIVE);
    }

    public function blacklists()
    {
        return $this->hasMany('Acelle\Model\Blacklist');
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
            while (Customer::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }

    /**
     * Display customer name: first_name last_name.
     *
     * @var string
     */
    public function displayName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Upload and resize avatar.
     *
     * @var void
     */
    public function uploadImage($file)
    {
        $path = 'app/customers/';
        $upload_path = storage_path($path);

        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $filename = 'avatar-'.$this->id.'.'.$file->getClientOriginalExtension();

        // save to server
        $file->move($upload_path, $filename);

        // create thumbnails
        $img = \Image::make($upload_path.$filename);
        $img->fit(120, 120)->save($upload_path.$filename.'.thumb.jpg');

        return $path.$filename;
    }

    /**
     * Get image thumb path.
     *
     * @var string
     */
    public function imagePath()
    {
        if (!empty($this->image) && !empty($this->id)) {
            return storage_path($this->image).'.thumb.jpg';
        } else {
            return '';
        }
    }

    /**
     * Get image thumb path.
     *
     * @var string
     */
    public function removeImage()
    {
        if (!empty($this->image) && !empty($this->id)) {
            $path = storage_path($this->image);
            if (is_file($path)) {
                unlink($path);
            }
            if (is_file($path.'.thumb.jpg')) {
                unlink($path.'.thumb.jpg');
            }
        }
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return Customer::select('customers.*');
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $query = self::select('customers.*')
            ->leftJoin('users', 'users.id', '=', 'customers.user_id');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('customers.first_name', 'like', '%'.$keyword.'%')
                        ->orWhere('customers.last_name', 'like', '%'.$keyword.'%')
                        ->orWhere('users.email', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;
        if (!empty($filters)) {

        }

        // Admin filter
        if(!empty($request->admin_id)) {
            $query = $query->where('customers.admin_id', '=', $request->admin_id);
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
     * Subscribers count by time.
     *
     * @return number
     */
    public static function subscribersCountByTime($begin, $end, $customer_id = null, $list_id = null)
    {
        $query = \Acelle\Model\Subscriber::leftJoin('mail_lists', 'mail_lists.id', '=', 'subscribers.mail_list_id')
                                ->leftJoin('customers', 'customers.id', '=', 'mail_lists.customer_id');

        if (isset($list_id)) {
            $query = $query->where('subscribers.mail_list_id', '=', $list_id);
        }
        if (isset($customer_id)) {
            $query = $query->where('customers.id', '=', $customer_id);
        }

        $query = $query->where('subscribers.created_at', '>=', $begin)
                        ->where('subscribers.created_at', '<=', $end);

        return $query->count();
    }

    /**
     * Get customer options.
     *
     * @return string
     */
    public function getOptions()
    {
        //@todo how to get option from subscription/plan
        $subscription = $this->getCurrentSubscription();
        if(is_object($subscription)) {
            return $subscription->getOptions();
        } else {
            return \Acelle\Model\Subscription::defaultOptions();
        }
    }

    /**
     * Get customer option.
     *
     * @return string
     */
    public function getOption($name)
    {
        $options = $this->getOptions();
        // @todo: chỗ này cần raise 1 cái exception chứ ko phải là trả về rỗng (trả vể rỗng để "dìm" lỗi đi à?)
        return isset($options[$name]) ? $options[$name] : '';
    }

    /**
     * Get max list quota.
     *
     * @return number
     */
    public function maxLists()
    {
        $count = $this->getOption('list_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count customer lists.
     *
     * @return number
     */
    public function listsCount()
    {
        return $this->lists()->count();
    }

    /**
     * Calculate list usage.
     *
     * @return number
     */
    public function listsUsage()
    {
        $max = $this->maxLists();
        $count = $this->listsCount();

        if ($max == '∞') {
            return 0;
        }

        if ($max == 0) {
            return 0;
        }

        if ($count > $max) {
            return 100;
        }


        return round((($count / $max) * 100), 2);
    }

    /**
     * Display calculate list usage.
     *
     * @return number
     */
    public function displayListsUsage()
    {
        if ($this->maxLists() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->listsUsage().'%';
    }

    /**
     * Get campaigns quota.
     *
     * @return number
     */
    public function maxCampaigns()
    {
        $count = $this->getOption('campaign_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count customer's campaigns.
     *
     * @return number
     */
    public function campaignsCount()
    {
        return $this->campaigns()->count();
    }

    /**
     * Calculate campaign usage.
     *
     * @return number
     */
    public function campaignsUsage()
    {
        $max = $this->maxCampaigns();
        $count = $this->campaignsCount();

        if ($max == '∞') {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate campaign usage.
     *
     * @return number
     */
    public function displayCampaignsUsage()
    {
        if ($this->maxCampaigns() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->campaignsUsage().'%';
    }

    /**
     * Get subscriber quota.
     *
     * @return number
     */
    public function maxSubscribers()
    {
        $count = $this->getOption('subscriber_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Count customer's subscribers.
     *
     * @return number
     */
    public function subscribersCount($cache = false)
    {
        if ($cache) {
            return $this->readCache('SubscriberCount');
        }
        return distinctCount($this->subscribers(), 'subscribers.email', 'distinct');
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function subscribersUsage($cache = false)
    {
        $max = $this->maxSubscribers();
        $count = $this->subscribersCount($cache);

        if ($max == '∞') {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function displaySubscribersUsage()
    {
        if ($this->maxSubscribers() == '∞') {
            return trans('messages.unlimited');
        }

        // @todo: avoid using cached value in a function
        //        cache value must be called directly from view only
        return $this->readCache('SubscriberUsage', 0) . '%';
    }

    /**
     * Get customer's quota.
     *
     * @return string
     */
    public function maxQuota()
    {
        $quota = $this->getOption('sending_quota');
        if ($quota == '-1') {
            return '∞';
        } else {
            return $quota;
        }
    }

    /**
     * Check if customer has access to ALL sending servers.
     *
     * @return boolean
     */
    public function allSendingServer()
    {
        $check = $this->getOption('all_sending_servers');
        return ($check == 'yes');
    }

    /**
     * Check if customer has used up all quota allocated.
     *
     * @return string
     */
    public function overQuota()
    {
        return !$this->getQuotaTracker()->check();
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
     * Get customer's sending quota rate.
     *
     * @return string
     */
    public function displaySendingQuotaUsage()
    {
        if ($this->getSendingQuota() == -1) {
            return trans('messages.unlimited');
        }
        // @todo use percentage helper here
        return $this->getSendingQuotaUsagePercentage().'%';
    }

    /**
     * Clean up the quota tracking files to prevent it from growing too large
     *
     * @return void
     */
    function cleanupQuotaTracker()
    {
        $this->getQuotaTracker()->cleanupSeries();
    }

    /**
     * Get customer's color scheme.
     *
     * @return string
     */
    public function getColorScheme()
    {
        if (!empty($this->color_scheme)) {
            return $this->color_scheme;
        } else {
            return \Acelle\Model\Setting::get('frontend_scheme');
        }
    }

    /**
     * Color array.
     *
     * @return array
     */
    public static function colors($default)
    {
        return [
            ['value' => '', 'text' => trans('messages.system_default')],
            ['value' => 'blue', 'text' => trans('messages.blue')],
            ['value' => 'green', 'text' => trans('messages.green')],
            ['value' => 'brown', 'text' => trans('messages.brown')],
            ['value' => 'pink', 'text' => trans('messages.pink')],
            ['value' => 'grey', 'text' => trans('messages.grey')],
            ['value' => 'white', 'text' => trans('messages.white')],
        ];
    }

    /**
     * Disable customer.
     *
     * @return boolean
     */
    public function disable()
    {
        $this->status = 'inactive';
        return $this->save();
    }

    /**
     * Enable customer.
     *
     * @return boolean
     */
    public function enable()
    {
        $this->status = 'active';
        return $this->save();
    }

    /**
     * Get customer's quota.
     *
     * @return string
     */
    public function getSendingQuota()
    {
        // -1 indicate unlimited
        return $this->getOption('email_max');
    }

    /**
     * Get customer's sending quota.
     *
     * @return string
     */
    public function getSendingQuotaUsage()
    {
        $current = $this->getCurrentSubscription();
        if (is_null($current)) {
            return 0;
        }
        $tracker = $this->getQuotaTracker();
        return $tracker->getUsage();
    }

    /**
     * Get customer's sending quota rate.
     *
     * @return string
     */
    public function getSendingQuotaUsagePercentage()
    {
        $current = $this->getCurrentSubscription();
        if (is_null($current)) {
            return 0;
        }

        $max = $this->getSendingQuota();
        $count = $this->getSendingQuotaUsage();

        if ($max == -1) {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Get the quota/limit object
     *
     * @return array
     */
    public function getQuotaHash()
    {
        $current = $this->getCurrentSubscription();

        $quota = [
            'start' => (isset($current->start_at) ? $current->start_at->timestamp : null),
            'max' => $current->getOption('email_max')
        ];

        return $quota;
    }

    /**
     * Initialize the quota tracker
     *
     * @return array
     */
    public function initQuotaTracker() {
        $this->quotaTracker = new QuotaTrackerFile($this->getSendingQuotaLockFile(), $this->getQuotaHash(), $this->getSendingLimits());
        $this->quotaTracker->cleanupSeries();
        // @note: in case of multi-process, the following command must be issued manually
        //     $this->renewQuotaTracker();
    }

    public function getSendingLimits()
    {
        $timeValue = $this->getOption('sending_quota_time');
        if ($timeValue == -1) {
            return []; // no limit
        }
        $timeUnit = $this->getOption('sending_quota_time_unit');
        $limit = $this->getOption('sending_quota');
        return ["{$timeValue} {$timeUnit}" => $limit];
    }

    /**
     * Get sending quota lock file
     *
     * @return string file path
     */
    public function getSendingQuotaLockFile() {
        return storage_path("app/customer/quota/{$this->uid}");
    }

    /**
     * Get customer's QuotaTracker
     *
     * @return array
     */
    public function getQuotaTracker() {
        $current = $this->getCurrentSubscription();

        if (is_null($current)) {
            throw new \Exception("User not subscribed to any active plan");
        }

        if(!$this->quotaTracker) {
            $this->initQuotaTracker();
        }

        return $this->quotaTracker;
    }

    /**
     * Get not auto campaign.
     *
     * @return array
     */
    public function getNormalCampaigns()
    {
        return $this->campaigns()->where('is_auto', '=', false);
    }

    /**
     * Get customer timezone
     *
     * @return string
     */
    public function getTimezone() {
        return $this->timezone;
    }

    /**
     * Get customer language code
     *
     * @return string
     */
    public function getLanguageCode() {
        return (is_object($this->language) ? $this->language->code : null);
    }

    /**
     * Get customer select2 select options
     *
     * @return array
     */
    public static function select2($request) {
        $data = ['items' => [], 'more' => true];

        $query = \Acelle\Model\Customer::getAll()->leftJoin('users', 'users.id', '=', 'customers.user_id');
        if (isset($request->q)) {
            $keyword = $request->q;
            $query = $query->where(function ($q) use ($keyword) {
                $q->orwhere('customers.first_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customers.last_name', 'like', '%'.$keyword.'%')
                    ->orWhere('users.email', 'like', '%'.$keyword.'%');
            });
        }

        // Read all check
        if (!$request->user()->admin->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->where('customers.admin_id', '=', $request->user()->admin->id);
        }

        foreach ($query->limit(20)->get() as $customer) {
            $data['items'][] = ['id' => $customer->uid, 'text' => $customer->displayNameEmailOption()];
        }

        return json_encode($data);
    }

    /**
     * Create/Update customer information
     *
     * @return object
     */
    public function updateInformation($request) {
        // Create user account for customer
        $user = is_object($this->user) ? $this->user : new \Acelle\Model\User();
        $user->email = $request->email;
        // Update password
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        // Save current user info
        if(!$this->id) {
            $this->user_id = $user->id;
            $this->admin_id = is_object($request->user()) ? $request->user()->admin->id : null;
            $this->status = 'active';
        }

        $this->fill($request->all());

        $this->save();

        // Upload and save image
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                // Remove old images
                $this->removeImage();
                $this->image = $this->uploadImage($request->file('image'));
                $this->save();
            }
        }

        // Remove image
        if ($request->_remove_image == 'true') {
            $this->removeImage();
            $this->image = '';
        }
    }

    /**
     * Get customer current subscriptions
     *
     * @return Subscription
     */
    public function getCurrentSubscriptions() {
        return $subsciption = $this->subscriptions()
            ->where(function($query) {
                $query->where('subscriptions.start_at', '<=', \Carbon\Carbon::now()->endOfDay())
                    ->orWhereNull('subscriptions.start_at');
            })->where(function($query) {
                $query->where('subscriptions.end_at', '>=', \Carbon\Carbon::now()->startOfDay())
                    ->orWhereNull('subscriptions.end_at');
            });
    }

    /**
     * Get customer current subscription
     *
     * @return Subscription
     */
    public function getCurrentSubscription() {
        if (!$this->currentSubscription) {
            $this->currentSubscription = $this->getCurrentSubscriptions()
            ->where('subscriptions.status', '=', \Acelle\Model\Subscription::STATUS_ACTIVE)
            ->first();
        }

        return $this->currentSubscription;
    }

    /**
     * Get customer next subscriptions
     *
     * @return Subscription
     */
    public function getNextSubscription() {
        return $subsciption = $this->subscriptions()
            ->where('subscriptions.start_at', '>', \Carbon\Carbon::now()->endOfDay())
            ->first();
    }

    /**
     * Get customer current subscription
     *
     * @return Subscription
     */
    public function getCurrentSubscriptionIgnoreStatus() {
        return $subsciption = $this->getCurrentSubscriptions()
            ->first();
    }

    /**
     * Get customer current pending subscription
     *
     * @return Subscription
     */
    public function getCurrentInactiveSubscription() {
        return $subsciption = $this->subscriptions()
            ->where('status', '=', \Acelle\Model\Subscription::STATUS_INACTIVE)
            ->first();
    }

    /**
     * Get next subscription start at
     *
     * @return Date
     */
    public function getNextScriptionStartAt() {
        $last_subscription = $this->subscriptions()
            ->orderBy('created_at', 'desc')->first();

        if(is_object($last_subscription) && isset($last_subscription->end_at)) {
            return $last_subscription->end_at->addDay(1);
        } else {
            return \Acelle\Library\Tool::customerDateTime(\Carbon\Carbon::now());
        }
    }

    public function sendingServers()
    {
        return $this->hasMany('Acelle\Model\SendingServer');
    }

    public function subAccounts()
    {
        return $this->hasMany('Acelle\Model\SubAccount');
    }

    /**
     * Customers count by time.
     *
     * @return number
     */
    public static function customersCountByTime($begin, $end, $admin = null)
    {
        $query = \Acelle\Model\Customer::select('customers.*');

        if (isset($admin) && !$admin->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->where('customers.admin_id', '=', $admin->id);
        }

        $query = $query->where('customers.created_at', '>=', $begin)
            ->where('customers.created_at', '<=', $end);

        return $query->count();
    }

    /**
     * The rules for validation via api.
     *
     * @var array
     */
    public function apiRules()
    {
        return array(
            'email' => 'required|email|unique:users,email,'.$this->user_id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'timezone' => 'required',
            'language_id' => 'required',
            'password' => 'required|min:5',
        );
    }

    /**
     * The rules for validation via api.
     *
     * @var array
     */
    public function apiUpdateRules($request)
    {
        $arr = [];

        if(isset($request->email)) {
            $arr['email'] = 'required|email|unique:users,email,'.$this->user_id.',id';
        }
        if(isset($request->first_name)) {
            $arr['first_name'] = 'required';
        }
        if(isset($request->last_name)) {
            $arr['last_name'] = 'required';
        }
        if(isset($request->timezone)) {
            $arr['timezone'] = 'required';
        }
        if(isset($request->language_id)) {
            $arr['language_id'] = 'required';
        }
        if(isset($request->password)) {
            $arr['password'] = 'min:5';
        }

        return $arr;
    }

    public function getSubscriberCountByStatus($status)
    {
        // @note: in this particular case, a simple count(distinct) query is much more efficient
        $query = $this->subscribers()->where('subscribers.status', $status)->distinct('subscribers.email');
        return $query->count();
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
            'SubscriberCount' => function(&$customer) {
                return $customer->subscribersCount(false);
            },
            'SubscriberUsage' => function(&$customer) {
                return $customer->subscribersUsage(true);
            },
            'SubscribedCount' => function(&$customer) {
                return $customer->getSubscriberCountByStatus(Subscriber::STATUS_SUBSCRIBED);
            },
            'UnsubscribedCount' => function(&$customer) {
                return $customer->getSubscriberCountByStatus(Subscriber::STATUS_UNSUBSCRIBED);
            },
            'UnconfirmedCount' => function(&$customer) {
                return $customer->getSubscriberCountByStatus(Subscriber::STATUS_UNCONFIRMED);
            },
            'BlacklistedCount' => function(&$customer) {
                return $customer->getSubscriberCountByStatus(Subscriber::STATUS_BLACKLISTED);
            },
            'SpamReportedCount' => function(&$customer) {
                return $customer->getSubscriberCountByStatus(Subscriber::STATUS_SPAM_REPORTED);
            },
            'MailListSelectOptions' => function(&$customer) {
                return $customer->getMailListSelectOptions([], true);
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
     * Sending servers count.
     *
     * @var integer
     */
    public function sendingServersCount()
    {
        return $this->sendingServers()->count();
    }

    /**
     * Sending domains count.
     *
     * @var integer
     */
    public function sendingDomainsCount()
    {
        return $this->sendingDomains()->count();
    }

    /**
     * Get max sending server count.
     *
     * @var integer
     */
    public function maxSendingServers()
    {
        $count = $this->getOption('sending_servers_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Get max email verification server count.
     *
     * @var integer
     */
    public function maxEmailVerificationServers()
    {
        $count = $this->getOption('email_verification_servers_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Calculate email verification server usage.
     *
     * @return number
     */
    public function emailVerificationServersUsage()
    {
        $max = $this->maxEmailVerificationServers();
        $count = $this->emailVerificationServersCount();

        if ($max == '∞') {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate email verigfication servers usage.
     *
     * @return number
     */
    public function displayEmailVerificationServersUsage()
    {
        if ($this->maxEmailVerificationServers() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->emailVerificationServersUsage().'%';
    }

    /**
     * Calculate sending servers usage.
     *
     * @return number
     */
    public function sendingServersUsage()
    {
        $max = $this->maxSendingServers();
        $count = $this->sendingServersCount();

        if ($max == '∞') {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate sending servers usage.
     *
     * @return number
     */
    public function displaySendingServersUsage()
    {
        if ($this->maxSendingServers() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->sendingServersUsage().'%';
    }

    /**
     * Get max sending server count.
     *
     * @var integer
     */
    public function maxSendingDomains()
    {
        $count = $this->getOption('sending_domains_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function sendingDomainsUsage()
    {
        $max = $this->maxSendingDomains();
        $count = $this->sendingDomainsCount();

        if ($max == '∞') {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function displaySendingDomainsUsage()
    {
        if ($this->maxSendingDomains() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->sendingDomainsUsage().'%';
    }

    /**
     * Subscriptions count.
     *
     * @return number
     */
    public function subscriptionsCount()
    {
        return $this->subscriptions()->count();
    }

    /**
     * Check if customer dosen't have any plan.
     *
     * @return boolean
     */
    public function notHaveAnyPlan() {
        return $this->subscriptionsCount() == 0;
    }

    /**
     * Count customer automations.
     *
     * @return number
     */
    public function automationsCount()
    {
        return $this->automations()->count();
    }

    /**
     * Get max automation count.
     *
     * @var integer
     */
    public function maxAutomations()
    {
        $count = $this->getOption('automation_max');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function automationsUsage()
    {
        $max = $this->maxAutomations();
        $count = $this->automationsCount();

        if ($max == '∞') {
            return 0;
        }
        if ($max == 0) {
            return 0;
        }
        if ($count > $max) {
            return 100;
        }

        return round((($count / $max) * 100), 2);
    }

    /**
     * Calculate subscibers usage.
     *
     * @return number
     */
    public function displayAutomationsUsage()
    {
        if ($this->maxAutomations() == '∞') {
            return trans('messages.unlimited');
        }

        return $this->automationsUsage().'%';
    }

    /**
     * Check if customer has admin account
     *
     * @return boolean
     */
    public function hasAdminAccount() {
        return is_object($this->user) && is_object($this->user->admin);
    }

    /**
     * Get all customer active sending servers
     *
     * @return collect
     */
    public function activeSendingServers() {
        return $this->sendingServers()->where('status', '=', \Acelle\Model\SendingServer::STATUS_ACTIVE);
    }

    /**
     * Check if customer is disabled
     *
     * @return boolean
     */
    public function isActive() {
        return $this->status == Customer::STATUS_ACTIVE;
    }

    /**
     * Check if customer is disabled
     *
     * @return boolean
     */
    public function currentPlanName() {
        $current_subscription = $this->getCurrentSubscription();
        return is_object($this->user->admin) ?
            trans('messages.admin_default_plan') :
            (is_object($current_subscription) ?
                $current_subscription->plan_name :
                trans('messages.no_plan')
            );
    }

    /**
     * Get customer subscription not include old one
     *
     * @return Subscription
     */
    public function getNotOutdatedSubscriptions() {
        return $subsciption = $this->subscriptions()
            ->where('subscriptions.end_at', '>=', \Carbon\Carbon::now()->startOfDay());
    }

    /**
     * Get customer current pending subscription not include old one
     *
     * @return Subscription
     */
    public function getNotOutdatedInactiveSubscriptions() {
        return $subsciption = $this->getNotOutdatedSubscriptions()
            ->where('subscriptions.status', '=', \Acelle\Model\Subscription::STATUS_INACTIVE);
    }

    /**
     * Get total file size usage
     *
     * @return number
     */
    public function totalUploadSize() {
        return \Acelle\Library\Tool::getDirectorySize(base_path("public/source/" . $this->user->uid)) / 1048576;
    }

    /**
     * Get max upload size quota.
     *
     * @return number
     */
    public function maxTotalUploadSize()
    {
        $count = $this->getOption('max_size_upload_total');
        if ($count == -1) {
            return '∞';
        } else {
            return $count;
        }
    }

    /**
     * Calculate campaign usage.
     *
     * @return number
     */
    public function totalUploadSizeUsage()
    {
        if ($this->maxTotalUploadSize() == '∞') {
            return 0;
        }
        if ($this->maxTotalUploadSize() == 0) {
            return 100;
        }

        return round((($this->totalUploadSize() / $this->maxTotalUploadSize()) * 100), 2);
    }

    /**
     * Custom can for customer.
     *
     * @return boolean
     */
    public function can($action, $item)
    {
        return $this->user->can($action, [$item, 'customer']);
    }

    /**
     * Custom name + email.
     *
     * @return string
     */
    public function displayNameEmail()
    {
        return $this->displayName() . " (" . $this->user->email . ")";
    }

    /**
     * Get customer contact
     *
     * @return Contact
     */
    public function getContact()
    {
        if (is_object($this->contact)) {
            $contact = $this->contact;
        } else {
            $contact = new \Acelle\Model\Contact([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->user->email,
            ]);
        }

        return $contact;
    }

    /*
     * Custom name + email.
     *
     * @return string
     */
    public function displayNameEmailOption()
    {
        return $this->displayName() . "|||" . $this->user->email;
    }

    /**
     * Email verification servers count.
     *
     * @var integer
     */
    public function emailVerificationServersCount()
    {
        return $this->emailVerificationServers()->count();
    }

    /**
     * Get list of available email verification servers.
     *
     * @var boolean
     */
    public function getEmailVerificaionServers()
    {
        $result = [];

        // Check the customer has permissions using email verification servers and has his own email verification servers
        if ($this->getOption("create_email_verification_servers") == 'yes') {
            $result = $this->activeEmailVerificationServers()->get()->map(function ($server) {
                return $server;
            });
        // If customer dont have permission creating sending servers
        } else {
            $subscription = $this->getCurrentSubscription();

            // Check if has sending servers for current subscription
            if (is_object($subscription)) {
                if ($subscription->getOption("all_email_verification_servers") == 'yes') {
                    $result = \Acelle\Model\EmailVerificationServer::getAllAdminActive()->get()->map(function ($server) {
                        return $server;
                    });
                } else {
                    $result = $subscription->activeSubscriptionsEmailVerificationServers()->get()->map(function ($server) {
                        return $server->emailVerificationServer;
                    });
                }
            }
        }

        return $result;
    }

    /**
     * Get the list of available mail lists, used for populating select box
     *
     * @return array
     */
    public function getMailListSelectOptions($options = [], $cache = false)
    {
        $query = \Acelle\Model\MailList::getAll();
        $query->where('customer_id', '=', $this->id);

        # Other list
        if (isset($options['other_list_of'])) {
            $query->where('id', '!=', $options['other_list_of']);
        }

        $result = $query->orderBy('name')->get()->map(function ($item) use ($cache) {
            return ['id' => $item->id, 'value' => $item->uid, 'text' => $item->name.' ('.$item->subscribersCount($cache).' '.strtolower(trans('messages.subscribers')).')'];
        });

        return $result;
    }

    /**
     * Get email verification servers select options.
     *
     * @return array
     */
    public function emailVerificationServerSelectOptions()
    {
        $servers = $this->getEmailVerificaionServers();
        $options = [];
        foreach ($servers as $server) {
            $options[] = ['text' => $server->name, 'value' => $server->uid];
        }

        return $options;
    }

    /**
     * Get customer's sending servers type.
     *
     * @return array
     */
    public function getSendingServertypes()
    {
        $allTypes = \Acelle\Model\SendingServer::types();
        $types = [];

        foreach ($allTypes as $type => $server) {
            if ($this->isAllowCreateSendingServerType($type)) {
                $types[$type] = $server;
            }
        }

        return $types;
    }

    /**
     * Check customer can create sending servers type.
     *
     * @return boolean
     */
    public function isAllowCreateSendingServerType($type)
    {
        $customerTypes = $this->getOption('sending_server_types');
        if ($this->getOption('all_sending_server_types') == 'yes' ||
            (isset($customerTypes[$type]) && $customerTypes[$type] == 'yes')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get import jobs.
     *
     * @return number
     */
    public function getImportBlacklistJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ImportBlacklistJob")
            ->where("data","like", "%\"customer_id\":" . $this->id . "%");
    }

    /**
     * Get running import jobs.
     *
     * @return number
     */
    public function getActiveImportBlacklistJobs()
    {
        return $this->getImportBlacklistJobs()
            ->where("status","!=", \Acelle\Model\SystemJob::STATUS_DONE)
            ->where("status","!=", \Acelle\Model\SystemJob::STATUS_FAILED)
            ->where("status","!=", \Acelle\Model\SystemJob::STATUS_CANCELLED);
    }

    /**
     * Get last import black list job.
     *
     * @return number
     */
    public function getLastActiveImportBlacklistJob()
    {
        return $this->getActiveImportBlacklistJobs()
            ->orderBy("created_at","DESC")
            ->first();
    }

    /**
     * Add email to customer's blacklist.
     *
     * @return void
     */
    public function addEmaillToBlacklist($email)
    {
        $email = trim(strtolower($email));

        if (\Acelle\Library\Tool::isValidEmail($email)) {
            $exist = $this->blacklists()->where('email','=',$email)->count();
            if (!$exist) {
                $blacklist = new \Acelle\Model\Blacklist();
                $blacklist->customer_id = $this->id;
                $blacklist->email = $email;
                $blacklist->save();
            }
        }
    }
}
