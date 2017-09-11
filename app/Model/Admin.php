<?php

/**
 * Admin class.
 *
 * Model class for admin
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

class Admin extends Model
{
	const STATUS_ACTIVE = 'active';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'timezone', 'language_id', 'color_scheme'
    ];

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
     * Admin email.
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

    public function adminGroup()
    {
        return $this->belongsTo('Acelle\Model\AdminGroup');
    }

    public function customers()
    {
        return $this->hasMany('Acelle\Model\Customer');
    }

    public function templates()
    {
        return $this->hasMany('Acelle\Model\Template');
    }

    public function language()
    {
        return $this->belongsTo('Acelle\Model\Language');
    }

    public function creator()
    {
        return $this->belongsTo('Acelle\Model\User', 'creator_id');
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
            while (Admin::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }

    /**
     * Display admin name: first_name last_name.
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
        $path = 'app/admins/';
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
        return Admin::select('*');
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
        $query = self::select('admins.*')
                        ->leftJoin('admin_groups', 'admin_groups.id', '=', 'admins.admin_group_id');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('admins.first_name', 'like', '%'.$keyword.'%')
                        ->orWhere('admin_groups.name', 'like', '%'.$keyword.'%')
                        ->orWhere('admins.last_name', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;
        if (!empty($filters)) {
            if (!empty($filters['admin_group_id'])) {
                $query = $query->where('admins.admin_group_id', '=', $filters['admin_group_id']);
            }
        }

        if(!empty($request->creator_id)) {
            $query = $query->where('admins.creator_id', '=', $request->creator_id);
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
     * Get admin setting.
     *
     * @return string
     */
    public function getOption($name)
    {
        return $this->adminGroup->getOption($name);
    }

    /**
     * Get admin permission.
     *
     * @return string
     */
    public function getPermission($name)
    {
        return $this->adminGroup->getPermission($name);
    }

    /**
     * Get user's color scheme.
     *
     * @return string
     */
    public function getColorScheme()
    {
        if (!empty($this->color_scheme)) {
            return $this->color_scheme;
        } else {
            return \Acelle\Model\Setting::get('backend_scheme');
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
     * Disable admin.
     *
     * @return boolean
     */
    public function disable()
    {
        $this->status = 'inactive';
        return $this->save();
    }

    /**
     * Enable admin.
     *
     * @return boolean
     */
    public function enable()
    {
        $this->status = 'active';
        return $this->save();
    }

	/**
     * Get recent resellers.
     *
     * @return collect
     */
    public function getAllCustomers()
    {
        $query = \Acelle\Model\Customer::getAll();

		if (!$this->user->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->where('customers.admin_id', '=', $this->id);
        }

		return $query;
    }

    /**
     * Get recent resellers.
     *
     * @return collect
     */
    public function recentCustomers()
    {
        return $this->getAllCustomers()->orderBy("created_at", "DESC")->limit(5)->get();
    }

    /**
     * Get all admin's subcriptions.
     *
     * @return collect
     */
    public function getAllSubscriptions()
    {
        $query = \Acelle\Model\Subscription::select('subscriptions.*')
            ->leftJoin('customers', 'customers.id', '=', 'subscriptions.customer_id');

        if (!$this->user->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->where(function ($q) {
                $q->orwhere('customers.admin_id', '=', $this->id)
                    ->orWhere('subscriptions.admin_id', '=', $this->id);
            });
        }

        return $query;
    }

    /**
     * Get subscription notification count.
     *
     * @return collect
     */
    public function subscriptionNotificationCount()
    {
        $query = $this->getAllSubscriptions()
            ->where('subscriptions.status', '=', \Acelle\Model\Subscription::STATUS_INACTIVE)
            ->where('subscriptions.end_at', '>=', \Carbon\Carbon::now()->endOfDay())
            ->count();

        return $query == 0 ? '' : $query;
    }

    /**
     * Get recent subscriptions.
     *
     * @return collect
     */
    public function recentSubscriptions($number=5)
    {
        $query = $this->getAllSubscriptions()
            //->where('subscriptions.status', '=', \Acelle\Model\Subscription::STATUS_INACTIVE)
            ->where('subscriptions.end_at', '>=', \Carbon\Carbon::now()->endOfDay())
            ->orderBy('subscriptions.created_at', 'desc')->limit($number);

        return $query->get();
    }

    /**
     * Get admin language code
     *
     * @return string
     */
    public function getLanguageCode() {
        return (is_object($this->language) ? $this->language->code : null);
    }

    /**
     * Get admin logs of their customers
     *
     * @return string
     */
    public function getLogs() {
        $query = \Acelle\Model\Log::select('logs.*')->leftJoin('customers', 'customers.id', '=', 'logs.customer_id')
            ->leftJoin('admins', 'admins.id', '=', 'customers.admin_id');

        if (!$this->user->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->where('admins.id', '=', $this->id);
        }
        return $query;
    }

    /**
     * Create customer account
     *
     * @return void
     */
    public function createCustomerAccount($admin) {
        if (!$this->hasCustomerAccount()) {
            // Create customer
            $customer = new \Acelle\Model\Customer();
            $customer->user_id = $this->user_id;
			$customer->admin_id = $this->id;
            $customer->language_id = $this->language_id;
            $customer->first_name = $this->first_name;
            $customer->last_name = $this->last_name;
            $customer->image = $this->image;
            $customer->timezone = $this->timezone;
            $customer->status = $this->status;
            $customer->save();

            // Add plan
            $plan = \Acelle\Model\Plan::getDefaultPlan();
            $subscription = new \Acelle\Model\Subscription();
            $subscription->status = \Acelle\Model\Subscription::STATUS_ACTIVE;
            $subscription->admin_id = $admin->id;
            $subscription->plan_id = $plan->id;
            $subscription->customer_id = $customer->id;

            $subscription->fillAttributes([]);

			// Start at default
			$subscription->start_at = \Acelle\Library\Tool::dateTime(\Carbon\Carbon::now());

            $subscription->save();
        }
    }

    /**
     * Check if admin has customer account
     *
     * @return boolean
     */
    public function hasCustomerAccount() {
        return is_object($this->user) && is_object($this->user->customer);
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
     * Custom can for admin.
     *
     * @return boolean
     */
    public function can($action, $item)
    {
        return $this->user->can($action, [$item, 'admin']);
    }

    /**
     * Destroy admin.
     *
     * @return boolean
     */
    public function deleteRecursive()
    {
        // unset all customers
        $this->customers()->update(['admin_id' => NULL]);

        // Delete admin and user
        $user = $this->user;
        $this->delete();
        $user->delete();
    }

	/**
     * Get all subscription count by plan.
     *
     * @return integer
     */
    public function getAllSubscriptionsByPlan($plan)
    {
		return $this->getAllSubscriptions()->where('subscriptions.plan_id', '=', $plan->id);
    }

	/**
     * Get all plans.
     *
     * @return integer
     */
    public function getAllPlans()
    {
		return \Acelle\Model\Plan::getAllActive($this);
    }

	/**
     * Get all payment methods.
     *
     * @return integer
     */
    public function getAllPaymentMethods()
    {
		$query = \Acelle\Model\PaymentMethod::getAll()
			->where("payment_methods.status", "=", \Acelle\Model\PaymentMethod::STATUS_ACTIVE);

		if (!$this->can('readAll', new \Acelle\Model\PaymentMethod())) {
            $query = $query->where('payment_methods.admin_id', '=', $this->id);
        }

		return $query;
    }

	/**
     * Get all admin.
     *
     * @return integer
     */
    public function getAllAdmins()
    {
		$query = \Acelle\Model\Admin::getAll()
			->where("admins.status", "=", \Acelle\Model\Admin::STATUS_ACTIVE);

		if (!$this->can('readAll', new \Acelle\Model\Admin())) {
            $query = $query->where('admins.creator_id', '=', $this->user_id);
        }

		return $query;
    }

	/**
     * Get all admin.
     *
     * @return integer
     */
    public function getAllAdminGroups()
    {
		$query = \Acelle\Model\AdminGroup::getAll();

		if (!$this->can('readAll', new \Acelle\Model\AdminGroup())) {
            $query = $query->where('admin_groups.creator_id', '=', $this->user_id);
        }

		return $query;
    }

	/**
     * Get all sending servers.
     *
     * @return integer
     */
    public function getAllSendingServers()
    {
		$query = \Acelle\Model\SendingServer::getAll();

		if (!$this->can('readAll', new \Acelle\Model\SendingServer())) {
            $query = $query->where('sending_servers.admin_id', '=', $this->id);
        }

        // remove customer sending servers
        $query = $query->whereNull('customer_id');

		return $query;
    }

	/**
     * Get all sending servers.
     *
     * @return integer
     */
    public function getAllSendingDomains()
    {
		$query = \Acelle\Model\SendingDomain::getAll();

		if (!$this->can('readAll', new \Acelle\Model\SendingDomain())) {
            $query = $query->where('sending_domains.admin_id', '=', $this->id);
        }

        // remove customer sending servers
        $query = $query->whereNull('customer_id');

		return $query;
    }

	/**
     * Get all campaigns.
     *
     * @return collect
     */
    public function getAllCampaigns()
    {
		$query = \Acelle\Model\Campaign::getAll();

		if (!$this->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->leftJoin('customers', 'customers.id', '=', 'campaigns.customer_id')
				->where('customers.admin_id', '=', $this->id);
        }

		return $query;
    }

	/**
     * Get all lists.
     *
     * @return collect
     */
    public function getAllLists()
    {
		$query = \Acelle\Model\MailList::getAll();

		if (!$this->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->leftJoin('customers', 'customers.id', '=', 'mail_lists.customer_id')
				->where('customers.admin_id', '=', $this->id);
        }

		return $query;
    }

	/**
     * Get all automations.
     *
     * @return collect
     */
    public function getAllAutomations()
    {
		$query = \Acelle\Model\Automation::getAll();

		if (!$this->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->leftJoin('customers', 'customers.id', '=', 'automations.customer_id')
				->where('customers.admin_id', '=', $this->id);
        }

		return $query;
    }

	/**
     * Get all automations.
     *
     * @return collect
     */
    public function getAllSubscribers()
    {
		$query = \Acelle\Model\Subscriber::getAll();

		if (!$this->can('readAll', new \Acelle\Model\Customer())) {
            $query = $query->leftJoin('mail_lists', 'mail_lists.id', '=', 'subscribers.mail_list_id')
				->leftJoin('customers', 'customers.id', '=', 'mail_lists.customer_id')
				->where('customers.admin_id', '=', $this->id);
        }

		return $query;
    }

	/**
     * Get import jobs.
     *
     * @return number
     */
    public function getImportBlacklistJobs()
    {
        return \Acelle\Model\SystemJob::where("name","=","Acelle\Jobs\ImportBlacklistJob")
            ->where("data","like", "%\"admin_id\":" . $this->id . "%");
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
     * Add email to admin blacklist.
     *
     * @return void
     */
    public function addEmaillToBlacklist($email)
    {
        $email = trim(strtolower($email));

        if (\Acelle\Library\Tool::isValidEmail($email)) {
            $exist = \Acelle\Model\Blacklist::whereNull('customer_id')->where('email','=',$email)->count();
            if (!$exist) {
                $blacklist = new \Acelle\Model\Blacklist();
                $blacklist->admin_id = $this->id;
                $blacklist->email = $email;
                $blacklist->save();
            }
        }
    }
    
    /**
     * Get sub-account sending servers.
     *
     * @return integer
     */
    public function getSubaccountSendingServers()
    {
		$query = $this->getAllSendingServers();

		$query = $query->whereIn('type', \Acelle\Model\SendingServer::getSubAccountTypes());

		return $query;
    }

    /**
     * Get sub-account sending servers options.
     *
     * @return integer
     */
    public function getSubaccountSendingServersSelectOptions()
    {
        $options = [];

		foreach ($this->getSubaccountSendingServers()->get() as $server) {
            $options[] = ['value' => $server->uid, 'text' => $server->name];
        }

		return $options;
    }
}
