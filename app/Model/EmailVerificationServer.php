<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use JsonPath\JsonObject;
use Acelle\Library\QuotaTrackerFile;
use Acelle\Library\Log as MailLog;

class EmailVerificationServer extends Model
{
    // set the table name
    protected $table = 'email_verification_servers';
    protected $quotaTracker;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const WAIT = 30;

    protected $fillable = ['type', 'name'];

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
     * Verify email address
     *
     * @return mixed
     */
    public function verify($email)
    {
        // enforce the verification speed limit
        $logged = false;
        while ($this->isOverQuota()) {
            if (!$logged) {
                $logged = true;
                $options = $this->getOptions();
                $limit = "[{$options['limit_value']} / {$options['limit_base']} {$options['limit_unit']}]";
                MailLog::warning(sprintf("Verification server `%s` (%s) is exceeding speed limit of %s, waiting...", $this->name, $this->id, $limit));
            }
            sleep(self::WAIT);
        }

        // retrieve the service settings
        $config = $this->getConfig();
        $options = $this->getOptions();
        $client = new Client();

        // build the request URI
        $uri = $config['uri'];
        $uri = str_replace('{EMAIL}', $email, $uri);
        $uri = array_key_exists('api_key', $options) ? str_replace('{API_KEY}', $options['api_key'], $uri) : $uri;
        $uri = array_key_exists('api_secret_key', $options) ? str_replace('{API_SECRET_KEY}', $options['api_secret_key'], $uri) : $uri;
        $uri = array_key_exists('username', $options) ? str_replace('{USERNAME}', $options['username'], $uri) : $uri;
        $uri = array_key_exists('password', $options) ? str_replace('{PASSWORD}', $options['password'], $uri) : $uri;

        // actually request to the service
        $response = $client->request($config['request_type'], $uri);

        // fetch the result
        $raw = (string)$response->getBody();
        $jsonObject = new JsonObject($raw);
        $result = $jsonObject->get($config['result_xpath'])[0];

        // map the result value to those of Acelle Mail
        if (!array_key_exists($result, $config['result_map'])) {
            throw new \Exception('Unexpected result from verification service: ' . $raw);
        }
        $mapped = $config['result_map'][$result];
        $this->countUsage();
        return [$mapped, $raw];
    }

    /**
     * Find the configuration settings for a given verification service
     *
     * @return mixed
     */
    public function getConfig()
    {
        $configs = \Config::get('verification.services');
        foreach($configs as $config) {
            if ($config['id'] == $this->type) {
                return $config;
            }
        }

        throw new \Exception('Cannot find settings for verification service ' . $this->type);
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
        $user = $request->user();
        $query = self::select('email_verification_servers.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('email_verification_servers.name', 'like', '%'.$keyword.'%')
                        ->orWhere('email_verification_servers.type', 'like', '%'.$keyword.'%')
                        ->orWhere('email_verification_servers.host', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;
        if (!empty($filters)) {
            if (!empty($filters['type'])) {
                $query = $query->where('email_verification_servers.type', '=', $filters['type']);
            }
        }

        // Other filter
        if(!empty($request->customer_id)) {
            $query = $query->where('email_verification_servers.customer_id', '=', $request->customer_id);
        }

        if(!empty($request->admin_id)) {
            $query = $query->where('email_verification_servers.admin_id', '=', $request->admin_id);
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
     * Get server type select options.
     *
     * @return array
     */
    public static function typeSelectOptions()
    {
        $services = config('verification.services');
        $options = [];
        foreach($services as $service) {
            $options[] = ['value' => $service["id"], 'text' => $service["name"]];
        }

        return $options;
    }

    /**
     * Get campaign validation rules.
     */
    public function rules()
    {
        $rules =  array(
            'name' => 'required',
            'type' => 'required',
            'options.limit_value' => 'required',
            'options.limit_base' => 'required',
            'options.limit_unit' => 'required',
        );

        if ($this->type) {
            foreach ($this->getConfig()["fields"] as $field) {
                $rules['options.' . $field] = 'required';
            }
        }

        return $rules;
    }

    /**
     * Frequency time unit options.
     *
     * @return array
     */
    public static function quotaTimeUnitOptions()
    {
        return [
            ['value' => 'minute', 'text' => trans('messages.minute')],
            ['value' => 'hour', 'text' => trans('messages.hour')],
            ['value' => 'day', 'text' => trans('messages.day')],
        ];
    }

    /**
     * Server status select options.
     *
     * @return array
     */
    public static function statusSelectOptions()
    {
        return [
            ['value' => self::STATUS_ACTIVE, 'text' => trans('messages.email_verification_server_status_' . self::STATUS_ACTIVE)],
			['value' => self::STATUS_INACTIVE, 'text' => trans('messages.email_verification_server_status_' . self::STATUS_INACTIVE)],
        ];
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
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (self::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
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
     * Get all options.
     *
     * @return object
     */
    public function getOptions()
    {
        return !isset($this->options) ? [] : json_decode($this->options, true);
    }

    /**
     * Disable verification server
     *
     * @return array
     */
    public function disable()
    {
        $this->status = self::STATUS_INACTIVE;
        $this->save();
    }

    /**
     * Enable verification server
     *
     * @return array
     */
    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save();
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
            'type' => 'email_verification_server',
            'name' => $name,
            'data' => json_encode($data),
        ]);
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
     * Get credits used
     *
     * @return int credit used
     */
    public function getCreditUsage() {
        return $this->getQuotaTracker()->getUsage();
    }

    /**
     * Get speed limit string
     *
     * @return string speed limit
     */
    public function getSpeedLimitString() {
        $options = $this->getOptions();
        return "{$options['limit_value']} / {$options['limit_base']} {$options['limit_unit']}";
    }

    /**
     * Initialize the quota tracker
     *
     * @return void
     */
    public function initQuotaTracker() {
        $options = $this->getOptions();
        $base = "{$options['limit_base']} {$options['limit_unit']}";
        $limit = [
            "{$base}" => $options['limit_value']
        ];

        $this->quotaTracker = new QuotaTrackerFile($this->getQuotaLockFile(), ['start' => $this->created_at->timestamp, 'max' => -1], $limit);
        $this->quotaTracker->cleanupSeries();
    }

    /**
     * Get sending quota lock file
     *
     * @return string file path
     */
    public function getQuotaLockFile() {
        return storage_path("app/server/quota/{$this->uid}");
    }

    /**
     * Increment quota usage
     *
     * @return mixed
     */
    public function countUsage(Carbon $timePoint = null)
    {
        return $this->getQuotaTracker($timePoint)->add();
    }

    /**
     * Check if user has used up all quota allocated.
     *
     * @return boolean
     */
    public function isOverQuota()
    {
        return !$this->getQuotaTracker()->check();
    }

    /**
     * Get service type name
     *
     * @return string
     */
    public function getTypeName()
    {
        try {
            $service = $this->getConfig();
            return $service['name'];
        } catch (\Exception $ex) {
            return 'Error: Config missing!';
        }
    }
}
