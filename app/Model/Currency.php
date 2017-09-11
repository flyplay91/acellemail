<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'format'
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        return array(
            'name' => 'required',
            'code' => 'required|alpha|size:3',
            'format' => 'required|substring:{PRICE}',
        );
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
    public function admin()
    {
        return $this->belongsTo('Acelle\Model\Admin');
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
            while (Currency::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // uppercase for currency code
            $item->code = strtoupper($item->code);
        });

        static::updating(function($item)
        {
            // uppercase for currency code
            $item->code = strtoupper($item->code);
        });
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return Currency::select('*');
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
        $query = self::select('currencies.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('currencies.name', 'like', '%'.$keyword.'%')
                        ->orWhere('currencies.code', 'like', '%'.$keyword.'%');
                });
            }
        }

        if(!empty($request->admin_id)) {
            $query = $query->where('currencies.admin_id', '=', $request->admin_id);
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

        if (!empty($request->sort_order)) {
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        }

        return $query;
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
     * Get customer select2 select options
     *
     * @return array
     */
    public static function select2($request) {
        $data = ['items' => [], 'more' => true];

        $query = Currency::getAll();
        if (isset($request->q)) {
            $keyword = $request->q;
            $query = $query->where(function ($q) use ($keyword) {
                $q->orwhere('currencies.name', 'like', '%'.$keyword.'%')
                    ->orwhere('currencies.code', 'like', '%'.$keyword.'%');
            });
        }

        // Read all check
		if (!$request->user()->admin->can('readAll', new \Acelle\Model\Currency())) {
			$query = $query->where('currencies.admin_id', '=', $request->user()->admin->id);
		}

        foreach ($query->limit(20)->get() as $currency) {
            $data['items'][] = ['id' => $currency->id, 'text' => $currency->displayName()];
        }

        return json_encode($data);
    }

    /**
     * Display currency name.
     *
     * @return collect
     */
    public function displayName()
    {
        return $this->name . " (" . $this->code . ")";
    }
}
