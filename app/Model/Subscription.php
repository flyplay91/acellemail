<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log as LaravelLog;

class Subscription extends Model
{
    // Subscription status
    const STATUS_ACTIVE = 'active'; // equiv. to 'queue'
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DISABLED = 'disabled';

    // Subscription state
    const TIME_STATUS_FUTURE = 'future'; // equiv. to 'queue'
    const TIME_STATUS_CURRENT = 'current';
    const TIME_STATUS_PAST = 'past';

    // Paid status
    const PAID_STATUS_TRUE = 'paid';
    const PAID_STATUS_FALSE = 'notpaid';

    // Subscription statuses
    public $statuses = [
        Subscription::STATUS_ACTIVE => [
            Subscription::TIME_STATUS_PAST => 'expired',
            Subscription::TIME_STATUS_CURRENT => 'current_active',
            Subscription::TIME_STATUS_FUTURE => 'pending_active'
        ],
        Subscription::STATUS_INACTIVE => [
            Subscription::TIME_STATUS_PAST => 'expired',
            Subscription::TIME_STATUS_CURRENT => 'current_inactive',
            Subscription::TIME_STATUS_FUTURE => 'pending_inactive'
        ],
        Subscription::STATUS_DISABLED => [
            Subscription::TIME_STATUS_PAST => 'expired',
            Subscription::TIME_STATUS_CURRENT => 'current_disabled',
            Subscription::TIME_STATUS_FUTURE => 'pending_disabled'
        ],
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'start_at',
        'end_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id', 'payment_method_id'
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        $rules = [];

        if(!isset($this->plan_id)) {
            $rules['plan_uid'] = 'required';
        }
        if(!isset($this->customer_id)) {
            $rules['customer_uid'] = 'required';
        }

        if (isset($this->id)) {
            $options = self::defaultOptions();
            foreach ($options as $type => $option) {
                if ($type != 'sending_server_subaccount_uid') {
                    $rules['options.' . $type] = 'required';
                }
            }
        }

        if($this->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_SUBACCOUNT) {
            $rules['options.sending_server_subaccount_uid'] = 'required';
        }

        return $rules;
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
     * Disable a subscription
     *
     * @return void
     */
    public function setDisabled()
    {
        $this->status = self::STATUS_DISABLED;
        $this->save();
    }

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function plan()
    {
        return $this->belongsTo('Acelle\Model\Plan');
    }
    public function customer()
    {
        return $this->belongsTo('Acelle\Model\Customer');
    }
    public function paymentMethod()
    {
        return $this->belongsTo('Acelle\Model\PaymentMethod');
    }
    public function subscriptionsSendingServers()
    {
        return $this->hasMany('Acelle\Model\SubscriptionsSendingServer');
    }
    public function payments()
    {
        return $this->hasMany('Acelle\Model\Payment');
    }
    public function subscriptionsEmailVerificationServers()
    {
        return $this->hasMany('Acelle\Model\SubscriptionsEmailVerificationServer');
    }

    /**
     * Subaccount if any
     */
    public function subAccount()
    {
        // @todo hard-coded for SendGrid
        return $this->belongsTo('Acelle\Model\SubAccountSendGrid');
    }

    /**
     * Get all active subscription sending servers.
     *
     * @return collect
     */
    public function activeSubscriptionsSendingServers()
    {
        return $this->subscriptionsSendingServers()
            ->join('sending_servers', 'sending_servers.id', '=', 'subscriptions_sending_servers.sending_server_id')
            ->where('sending_servers.status', '=', SendingServer::STATUS_ACTIVE);
    }

    /**
     * Get all active subscription email verification servers.
     *
     * @return collect
     */
    public function activeSubscriptionsEmailVerificationServers()
    {
        return $this->subscriptionsEmailVerificationServers()
            ->join('email_verification_servers', 'email_verification_servers.id', '=', 'subscriptions_email_verification_servers.server_id')
            ->where('email_verification_servers.status', '=', EmailVerificationServer::STATUS_ACTIVE);
    }

    public function useSubAccount()
    {
        return $this->getOption('sending_server_option') == \Acelle\Model\Plan::SENDING_SERVER_OPTION_SUBACCOUNT;
    }

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($subscription) {
            // Create new uid
            $uid = uniqid();
            while (Subscription::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $subscription->uid = $uid;
        });

        static::created(function ($subscription) {
            // get sending server from plan.
            foreach($subscription->plan->plansSendingServers as $planServer) {
                $row = new SubscriptionsSendingServer();
                $row->subscription_id = $subscription->id;
                $row->sending_server_id = $planServer->sending_server_id;
                $row->fitness = $planServer->fitness;
                $row->save();
            }

            // get email verification server from plan.
            foreach($subscription->plan->plansEmailVerificationServers as $planServer) {
                $row = new SubscriptionsEmailVerificationServer();
                $row->subscription_id = $subscription->id;
                $row->server_id = $planServer->server_id;
                $row->save();
            }
        });

        static::created(function ($subscription) {
            // get sending server from plan.
            foreach($subscription->plan->plansSendingServers as $planServer) {
                $row = new SubscriptionsSendingServer();
                $row->subscription_id = $subscription->id;
                $row->sending_server_id = $planServer->sending_server_id;
                $row->fitness = $planServer->fitness;
                $row->save();
            }
        });
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return Plan::select('*');
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
        $query = self::select('subscriptions.*')
            ->join('customers', 'customers.id', '=', 'subscriptions.customer_id')
            ->join('users', 'users.id', '=', 'customers.user_id');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('customers.first_name', 'like', '%'.$keyword.'%')
                        ->orwhere('users.email', 'like', '%'.$keyword.'%')
                        ->orwhere('customers.last_name', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;

        if (!empty($filters)) {
            if (!empty($filters['customer_uid'])) {
                $query = $query->where('customers.uid', '=', $filters['customer_uid']);
            }
            if (!empty($filters['plan_uid'])) {
                $plan = \Acelle\Model\Plan::findByUid($filters['plan_uid']);
                $query = $query->where('subscriptions.plan_id', '=', $plan->id);
            }
            if (!empty($filters['status'])) {
                $query = $query->where('subscriptions.status', '=', $filters['status']);
            }
            if (!empty($filters['time_status'])) {
                switch ($filters['time_status']) {
                    case Subscription::TIME_STATUS_PAST:
                        $query = $query->where('subscriptions.end_at', '<', \Carbon\Carbon::now()->startOfDay());
                        break;
                    case Subscription::TIME_STATUS_CURRENT:
                        $query = $query->where('subscriptions.start_at', '<=', \Carbon\Carbon::now()->endOfDay())
                            ->where('subscriptions.end_at', '>=', \Carbon\Carbon::now()->startOfDay());
                        break;
                    case Subscription::TIME_STATUS_FUTURE:
                        $query = $query->where('subscriptions.start_at', '>', \Carbon\Carbon::now()->endOfDay());
                        break;
                }
            }
            if (!empty($filters['paid_status'])) {
                $query = $query->where('subscriptions.paid', '=', $filters['paid_status']);
            }
        }

        // Other filter
        if(!empty($request->customer_id)) {
            $query = $query->where('subscriptions.customer_id', '=', $request->customer_id);
        }

        // Other filter
        if(!empty($request->customer_admin_id)) {
            $query = $query->where('customers.admin_id', '=', $request->customer_admin_id);
        }

        if (!empty($request->customer_list)) {
            $query = $query->where('subscriptions.end_at', '>=', \Carbon\Carbon::now()->startOfDay());
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

        if(isset($request->sort_order)) {
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        } else {
            $query = $query->orderBy('subscriptions.created_at', 'desc');
        }

        return $query;
    }

    /**
     * Fill attributes.
     *
     * @return collect
     */
    public function fillAttributes($params)
    {
        $this->plan_id = $this->plan_id ? $this->plan_id : (!empty($params['plan_uid']) ? \Acelle\Model\Plan::findByUid($params['plan_uid'])->id : null);
        $this->customer_id = $this->customer_id ? $this->customer_id : (isset($params['customer_uid']) ? \Acelle\Model\Customer::findByUid($params['customer_uid'])->id : null);
        $this->payment_method_id = $this->payment_method_id ? $this->payment_method_id : (!empty($params['payment_method_uid']) ? \Acelle\Model\PaymentMethod::findByUid($params['payment_method_uid'])->id : null);
        $this->start_at = $this->start_at ? $this->start_at : $this->customer->getNextScriptionStartAt();

        $plan = $this->plan_id ? Plan::find($this->plan_id) : null;

        $this->fill($params);

        if(isset($this->plan_id)) {
            // Save info from plan
            $this->price = $plan->price;
            $this->plan_name = $plan->name;
            $this->plan_color = $plan->color;
            $this->currency_code = $plan->currency->code;
            $this->currency_format = $plan->currency->format;
            $this->options = json_encode($plan->getOptions());

            switch ($plan->frequency_unit) {
                case 'day':
                    $this->end_at = $this->start_at->addDays($plan->frequency_amount)->subDays(1);
                    break;
                case 'week':
                    $this->end_at = $this->start_at->addWeeks($plan->frequency_amount)->subDays(1);
                    break;
                case 'month':
                    $this->end_at = $this->start_at->addMonths($plan->frequency_amount)->subDays(1);
                    break;
                case 'year':
                    $this->end_at = $this->start_at->addYears($plan->frequency_amount)->subDays(1);
                    break;
                case 'unlimited':
                    $this->start_at = \Acelle\Library\Tool::dateTime(\Carbon\Carbon::now());
                    $this->end_at = NULL;
                    break;
            }
        }
    }

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function frontendRules()
    {
        $rules = array(
            'plan_uid' => 'required',
        );

        return $rules;
    }

    /**
     * Disable subscription.
     *
     * @return boolean
     */
    public function disable()
    {
        $this->status = Subscription::STATUS_DISABLED;
        return $this->save();
    }

    /**
     * Enable subscription.
     *
     * @return boolean
     */
    public function enable()
    {
        $this->status = Subscription::STATUS_ACTIVE;
        if ($this->useSubAccount()) {
            $this->createSubAccount();
        }
        return $this->save();
    }

    /**
     * Check if subscription is free.
     *
     * @return boolean
     */
    public function isFree()
    {
        return $this->price == 0;
    }

    /**
     * Count remaining days.
     *
     * @return integer
     */
    public function daysRemainCount()
    {
        $count = isset($this->end_at) ? $this->end_at->diffInDays(\Carbon\Carbon::now()) : NULL;

        return $count;
    }

    /**
     * Default options for new no plan.
     *
     * @return array
     */
    public static function defaultOptions()
    {
        $options = [
            'email_max' => '0',
            'list_max' => '0',
            'subscriber_max' => '0',
            'subscriber_per_list_max' => '0',
            'segment_per_list_max' => '0',
            'campaign_max' => '0',
            'automation_max' => '0',
            'sending_quota' => '0',
            'sending_quota_time' => '0',
            'sending_quota_time_unit' => 'day',
            'max_process' => '0',
            'all_sending_servers' => 'no',
            'max_size_upload_total' => '0',
            'max_file_size_upload' => '0',
            'unsubscribe_url_required' => 'yes',
            'access_when_offline' => 'no',
            //'create_sending_servers' => 'no',
            'create_sending_domains' => 'no',
            'sending_servers_max' => '0',
            'sending_domains_max' => '0',
            'all_email_verification_servers' => 'no',
            'create_email_verification_servers' => 'no',
            'email_verification_servers_max' => '0',
            'sending_servers_max' => '-1',
            'sending_domains_max' => '-1',
            'list_import' => 'no',
            'list_export' => 'no',
            'all_sending_server_types' => 'yes',
            'sending_server_types' => [],
            'sending_server_option' => \Acelle\Model\Plan::SENDING_SERVER_OPTION_SYSTEM,
            'sending_server_subaccount_uid' => null,
        ];

        // Sending server types
        foreach (\Acelle\Model\SendingServer::types() as $key => $type) {
            $options['sending_server_types'][$key] = 'no';
        }

        return $options;
    }

    /**
     * Default options for new plan.
     *
     * @return array
     */
    public static function defaultUnlimitedOptions()
    {
        $options = [
            'email_max' => '-1',
            'list_max' => '-1',
            'subscriber_max' => '-1',
            'subscriber_per_list_max' => '-1',
            'segment_per_list_max' => '-1',
            'campaign_max' => '-1',
            'automation_max' => '-1',
            'sending_quota' => '-1',
            'sending_quota_time' => '-1',
            'sending_quota_time_unit' => 'day',
            'max_process' => '10',
            'all_sending_servers' => 'yes',
            'max_size_upload_total' => '100000',
            'max_file_size_upload' => '100',
            'unsubscribe_url_required' => 'yes',
            'access_when_offline' => 'yes',
            //'create_sending_servers' => 'yes',
            'create_sending_domains' => 'yes',
            'sending_servers_max' => '-1',
            'sending_domains_max' => '-1',
            'all_email_verification_servers' => 'yes',
            'create_email_verification_servers' => 'yes',
            'email_verification_servers_max' => '-1',
            'list_import' => 'yes',
            'list_export' => 'yes',
            'all_sending_server_types' => 'yes',
            'sending_server_types' => [],
            'sending_server_option' => \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN,
            'sending_server_subaccount_uid' => null,
        ];

        // Sending server types
        foreach (\Acelle\Model\SendingServer::types() as $key => $type) {
            $options['sending_server_types'][$key] = 'yes';
        }

        return $options;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        if (empty($this->options)) {
            return self::defaultOptions();
        } else {
            $defaul_options = self::defaultOptions();
            $saved_options = json_decode($this->options, true);
            foreach($defaul_options as $x => $group) {
                if(isset($saved_options[$x])) {
                    $defaul_options[$x] = $saved_options[$x];
                }
            }
            return $defaul_options;
        }
    }

    /**
     * Get option.
     *
     * @return string
     */
    public function getOption($name)
    {
        return $this->getOptions()[$name];
    }

    /**
     * Display group quota.
     *
     * @return array
     */
    public function displayQuota()
    {
        if ($this->getOption("sending_quota") == -1) {
            return trans('messages.unlimited');
        } elseif ($this->getOption("sending_quota_time") == -1) {
            return $this->getOption("sending_quota");
        } else {
            return strtolower(\Acelle\Library\Tool::format_number($this->getOption("sending_quota")) . " " . trans('messages.' . \Acelle\Library\Tool::getPluralPrase('email', $this->getOption("sending_quota"))) . ' / '.$this->getOption("sending_quota_time").' '.trans('messages.'.\Acelle\Library\Tool::getPluralPrase($this->getOption("sending_quota_time_unit"), $this->getOption("sending_quota"))));
        }
    }

    /**
     * Display total quota.
     *
     * @return array
     */
    public function displayTotalQuota()
    {
        if ($this->getOption("email_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return \Acelle\Library\Tool::format_number($this->getOption("email_max"));
        }
    }

    /**
     * Display max lists.
     *
     * @return array
     */
    public function displayMaxList()
    {
        if ($this->getOption("list_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return \Acelle\Library\Tool::format_number($this->getOption("list_max"));
        }
    }

    /**
     * Display max subscribers.
     *
     * @return array
     */
    public function displayMaxSubscriber()
    {
        if ($this->getOption("subscriber_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return \Acelle\Library\Tool::format_number($this->getOption("subscriber_max"));
        }
    }

    /**
     * Display max campaign.
     *
     * @return array
     */
    public function displayMaxCampaign()
    {
        if ($this->getOption("campaign_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return \Acelle\Library\Tool::format_number($this->getOption("campaign_max"));
        }
    }

    /**
     * Display max campaign.
     *
     * @return array
     */
    public function displayMaxSizeUploadTotal()
    {
        if ($this->getOption("max_size_upload_total") == -1) {
            return trans('messages.unlimited');
        } else {
            return \Acelle\Library\Tool::format_number($this->getOption("max_size_upload_total"));
        }
    }

    /**
     * Display max file size upload.
     *
     * @return array
     */
    public function displayFileSizeUpload()
    {
        if ($this->getOption("max_file_size_upload") == -1) {
            return trans('messages.unlimited');
        } else {
            return \Acelle\Library\Tool::format_number($this->getOption("max_file_size_upload"));
        }
    }

    /**
     * Display sending ervers permission.
     *
     * @return array
     */
    public function displayAllowCreateSendingServer()
    {
        if ($this->getOption("sending_server_option") != \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN) {
            return trans('messages.feature_not_allow');
        }

        if ($this->getOption("sending_servers_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return $this->getOption("sending_servers_max");
        }
    }

    /**
     * Display email verification ervers permission.
     *
     * @return array
     */
    public function displayAllowCreateEmailVerificationServer()
    {
        if ($this->getOption("create_email_verification_servers") == 'no') {
            return trans('messages.feature_not_allow');
        }

        if ($this->getOption("email_verification_servers_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return $this->getOption("email_verification_servers_max");
        }
    }

    /**
     * Display sending domains permission.
     *
     * @return array
     */
    public function displayAllowCreateSendingDomain()
    {
        if ($this->getOption("create_sending_domains") == 'no') {
            return trans('messages.feature_not_allow');
        }

        if ($this->getOption("sending_domains_max") == -1) {
            return trans('messages.unlimited');
        } else {
            return $this->getOption("sending_domains_max");
        }
    }

    /**
     * Check if subscription is active.
     *
     * @return array
     */
    public function isActive()
    {
        return $this->status == Subscription::STATUS_ACTIVE;
    }

    /**
     * Check if subscription is disabled.
     *
     * @return array
     */
    public function isDisabled()
    {
        return $this->status == Subscription::STATUS_DISABLED;
    }

    /**
     * Check if subscription is outdated.
     *
     * @return array
     */
    public function isOld()
    {
        return isset($this->end_at) ? $this->end_at->endOfDay() < \Carbon\Carbon::now() : false;
    }

    /**
     * Check if subscription is started.
     *
     * @return array
     */
    public function isStarted()
    {
        return isset($this->start_at) ? $this->start_at->startOfDay() <= \Carbon\Carbon::now() : true;
    }

    /**
     * Check if subscription is current.
     *
     * @return array
     */
    public function isCurrent()
    {
        if($this->isStarted() &&
           !$this->isOld()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if subscription is current used.
     *
     * @return array
     */
    public function beingUsed()
    {
        if($this->isCurrent() &&
           $this->isActive()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check current state
     *
     * @return array
     */
    public function timeStatus()
    {
        if (!$this->isStarted()) {
            return Subscription::TIME_STATUS_FUTURE;
        } elseif ($this->isOld()) {
            return Subscription::TIME_STATUS_PAST;
        } else {
            return Subscription::TIME_STATUS_CURRENT;
        }
    }

    /**
     * Get subsciption display name
     *
     * @return array
     */
    public function getName() {
        return $this->plan->name;
    }

    /**
     * Check if subscriptioon is paid
     *
     * @return array
     */
    public function isPaid() {
        return $this->paid;
    }

    /**
     * When get status attribute
     *
     * @return array
     */
    public function getStatusAttribute($value) {
        // Auto update status
        if ($this->isOld()) {
            $this->disable();
        }
        return $value;
    }

    /**
     * Update sending servers.
     *
     * @return array
     */
    public function updateSendingServers($servers)
    {
        $this->subscriptionsSendingServers()->delete();
        foreach ($servers as $key => $param) {
            if ($param['check']) {
                $server = SendingServer::findByUid($key);
                $row = new SubscriptionsSendingServer();
                $row->subscription_id = $this->id;
                $row->sending_server_id = $server->id;
                $row->fitness = $param['fitness'];
                $row->save();
            }
        }
    }

    /**
     * Update email verification servers.
     *
     * @return array
     */
    public function updateEmailVerificationServers($servers)
    {
        $this->subscriptionsEmailVerificationServers()->delete();
        foreach ($servers as $key => $param) {
            if ($param['check']) {
                $server = \Acelle\Model\EmailVerificationServer::findByUid($key);
                $row = new \Acelle\Model\SubscriptionsEmailVerificationServer();
                $row->subscription_id = $this->id;
                $row->server_id = $server->id;
                $row->save();
            }
        }
    }

    /**
     * Fill email verification servers.
     *
     * @return void
     */
    public function fillEmailVerificationServers($params)
    {
        $this->subscriptionsEmailVerificationServers = collect([]);
        foreach ($params as $key => $param) {
            if ($param['check']) {
                $server = \Acelle\Model\EmailVerificationServer::findByUid($key);
                $row = new \Acelle\Model\SubscriptionsEmailVerificationServer();
                $row->plan_id = $this->id;
                $row->server_id = $server->id;
                $this->subscriptionsEmailVerificationServers->push($row);
            }
        }
    }

    /**
     * Set paid.
     *
     * @return void
     */
    public function setPaid($params = [])
    {
        $this->paid = true;
        $this->save();

        // description
        if (!empty($params)) {
            $payment = new \Acelle\Model\Payment();
            $payment->subscription_id = $this->id;
            $payment->data = '';
            $payment->description = $params["description"];
            $payment->tax_number = $params["tax_number"];
            $payment->billing_address = $params["billing_address"];
            $payment->payment_method_id = NULL;
            $payment->status = \Acelle\Model\Payment::STATUS_SUCCESS;
            $payment->action = \Acelle\Model\Payment::ACTION_PAID;
            $payment->payment_method_name = '';
            $payment->order_id = $this->getOrderID();
            $payment->save();
        }
    }

    /**
     * Set un-paid.
     *
     * @return void
     */
    public function setUnPaid($description='')
    {
        $this->paid = false;
        $this->save();

        // description
        if (!empty($description)) {
            $payment = new \Acelle\Model\Payment();
            $payment->subscription_id = $this->id;
            $payment->data = '';
            $payment->description = $description;
            $payment->payment_method_id = NULL;
            $payment->status = \Acelle\Model\Payment::STATUS_SUCCESS;
            $payment->action = \Acelle\Model\Payment::ACTION_UNPAID;
            $payment->payment_method_name = '';
            $payment->order_id = $this->getOrderID();
            $payment->save();
        }
    }

    /**
     * Get paid status.
     *
     * @return string
     */
    public function getPaidStatus()
    {
        if ($this->isPaid()) {
            return Subscription::PAID_STATUS_TRUE;
        } else {
            return Subscription::PAID_STATUS_FALSE;
        }
    }

    /**
     * Payments count.
     *
     * @return string
     */
    public function getPayments()
    {
        return $this->payments()->orderBy('created_at', 'desc');
    }

    /**
     * Payments count.
     *
     * @return string
     */
    public function paymentsCount()
    {
        return $this->getPayments()->count();
    }

    /**
     * Get payment order id.
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->uid;
    }

    /**
     * Get long title.
     *
     * @return string
     */
    public function longTitle()
    {
        $strs = [$this->plan_name];
        $strs[] = '(' . \Acelle\Library\Tool::formatDate($this->start_at);
        $strs[] = ' - ' . \Acelle\Library\Tool::formatDate($this->end_at) . ')';
        if ($this->isCurrent()) {
            $strs[] = ' / ' . trans('messages.subscription_time_status_' . $this->timeStatus());
        }
        return implode(' ', $strs);
    }

    /**
     * Subscription status select options.
     *
     * @return array
     */
    public static function statusSelectOptions()
    {
        return [
            ['value' => Subscription::STATUS_ACTIVE, 'text' => trans('messages.subscription_status_' . Subscription::STATUS_ACTIVE)],
            ['value' => Subscription::STATUS_INACTIVE, 'text' => trans('messages.subscription_status_' . Subscription::STATUS_INACTIVE)],
            ['value' => Subscription::STATUS_DISABLED, 'text' => trans('messages.subscription_status_' . Subscription::STATUS_DISABLED)],
        ];
    }

    /**
     * Subscription status select options.
     *
     * @return array
     */
    public static function timeStatusSelectOptions()
    {
        return [
            ['value' => Subscription::TIME_STATUS_PAST, 'text' => trans('messages.subscription_time_status_' . Subscription::TIME_STATUS_PAST)],
            ['value' => Subscription::TIME_STATUS_CURRENT, 'text' => trans('messages.subscription_time_status_' . Subscription::TIME_STATUS_CURRENT)],
            ['value' => Subscription::TIME_STATUS_FUTURE, 'text' => trans('messages.subscription_time_status_' . Subscription::TIME_STATUS_FUTURE)],
        ];
    }

    /**
     * Subscription status select options.
     *
     * @return array
     */
    public static function paidStatusSelectOptions()
    {
        return [
            ['value' => 'true', 'text' => trans('messages.subscription_paid_status_' . Subscription::PAID_STATUS_FALSE)],
            ['value' => 1, 'text' => trans('messages.subscription_paid_status_' . Subscription::PAID_STATUS_TRUE)],
        ];
    }

    /**
     * Check if plan time is unlimited.
     *
     * @return boolean
     */
    public function isTimeUnlimited()
    {
        return !$this->end_at;
    }

    /**
     * Get subscription status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->statuses[$this->status][$this->timeStatus()];
    }

    /**
     * Get stripe price.
     *
     * @return string
     */
    public function stripePrice()
    {
        $currency_rates = [
            'CLP' => 1,
            'DJF' => 1,
            'JPY' => 1,
            'KMF' => 1,
            'RWF' => 1,
            'VUV' => 1,
            'XAF' => 1,
            'XOF' => 1,
            'BIF' => 1,
            'GNF' => 1,
            'KRW' => 1,
            'MGA' => 1,
            'PYG' => 1,
            'VND' => 1,
            'XPF' => 1
        ];
        $rate = isset($currency_rates[$this->currency_code]) ? $currency_rates[$this->currency_code] : 100;
        return $this->price*$rate;
    }

    /**
     * Check if tax billing information is required.
     *
     * @return boolean
     */
    public function isTaxBillingRequired()
    {
        return $this->plan->tax_billing_required;
    }

    /**
     * Check if currency is valid.
     *
     * @return boolean
     */
    public function isValidPaymentMethod($payment_method)
    {
        if (
            in_array($payment_method->type, [\Acelle\Model\PaymentMethod::TYPE_BRAINTREE_PAYPAL,\Acelle\Model\PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD])
            && $this->currency_code != $payment_method->getOption('currencyCode')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Create sending server sub-account.
     *
     * @return void
     */
    public function createSubAccount()
    {
        // just return if subaccount was already setup
        if (!is_null($this->sub_account_id)) {
            return true;
        }

        try {
            // @todo hard-coded here for SendGrid
            $server = \Acelle\Model\SendingServer::findByUid($this->getOption('sending_server_subaccount_uid'));
            $account = \Acelle\Model\SubAccountSendGrid::setup([
                'email' => $this->customer->user->email,
                'customer_id' => $this->customer_id,
                'sending_server_id' => $server->id,
            ]);

            $this->sub_account_id = $account->id;
            $this->save();
        } catch (\Exception $ex) {
            LaravelLog::warning($ex->getMessage());
            $this->last_error = $ex->getMessage();
            $this->setDisabled();
            throw $ex;
        }
    }

    /**
     * Set option.
     *
     * @return void
     */
    public function setOption($name, $value)
    {
        $options = json_decode($this->options, true);
        $options[$name] = $value;

        $this->options = json_encode($options);
        $this->save();
    }

    /**
     * Fill option from request.
     *
     * @return void
     */
    public function fillOptions($options)
    {
        $db_options = json_decode($this->options, true);
        foreach ($options as $key => $value) {
            $db_options[$key] = $value;
        }
        $this->options = json_encode($db_options);
    }
}
