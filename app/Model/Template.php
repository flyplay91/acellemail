<?php

/**
 * Template class.
 *
 * Model class for template
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

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'content',
    ];

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

        // Create uid when creating item.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (Template::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // Update custom order
            Template::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;
        });

        static::deleted(function ($item) {
            $uploaded_dir = $item->getUploadDir();
            \Acelle\Library\Tool::xdelete($uploaded_dir);
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

    public function admin()
    {
        return $this->belongsTo('Acelle\Model\Admin');
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
        $query = self::select('templates.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            $query = $query->where('name', 'like', '%'.$request->keyword.'%');
        }

        // filters
        $filters = $request->filters;

        // Customer
        if (!empty($request->customer_id)) {
            if (!empty($filters) && !empty($filters['from'])) {
                if ($filters['from'] == 'mine') {
                    $query = $query->where('customer_id', '=', $request->customer_id);
                } elseif ($filters['from'] == 'gallery') {
                    $query = $query->where('customer_id', '=', NULL);
                } else {
                    $query = $query->where('customer_id', '=', NULL)
                        ->orWhere('customer_id', '=', $request->customer_id);
                }
            }
        }

        // Admin
        if (!empty($request->admin_id)) {
            $query = $query->where('admin_id', '=', $request->admin_id);
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
     * Filter items.
     *
     * @return collect
     */
    public static function adminFilter($request)
    {
        $user = $request->user();
        $query = self::where('shared', '=', true);

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
    public static function adminSearch($request)
    {
        $query = self::adminFilter($request);

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
     * Define all template styles.
     *
     * @return object
     */
    public static function templateStyles()
    {
        return [
            "1_column" => [
                "full_width_banner",
                "single_text_row",
                "footer"
            ],
            "1_2_column" => [
                "full_width_banner",
                "single_text_row",
                "two_image_text_columns",
                "footer"
            ],
            "1_2_1_column" => [
                "full_width_banner",
                "single_text_row",
                "two_image_text_columns",
                "single_text_row",
                "footer"
            ],
            "1_3_column" => [
                "full_width_banner",
                "single_text_row",
                "three_image_text_columns",
                "single_text_row",
                "footer"
            ],
            "1_3_1_column" => [
                "full_width_banner",
                "single_text_row",
                "three_image_text_columns",
                "single_text_row",
                "footer"
            ],
            "1_3_2_column" => [
                "full_width_banner",
                "single_text_row",
                "three_image_text_columns",
                "two_image_text_columns",
                "footer"
            ],
            "2_column" => [
                "full_width_banner",
                "two_image_text_columns",
                "footer"
            ],
            "2_1_column" => [
                "full_width_banner",
                "two_image_text_columns",
                "single_text_row",
                "footer"
            ],
            "2_1_2_column" => [
                "full_width_banner",
                "two_image_text_columns",
                "single_text_row",
                "two_image_text_columns",
                "footer"
            ],
            "3_1_3_column" => [
                "full_width_banner",
                "three_image_text_columns",
                "single_text_row",
                "three_image_text_columns",
                "footer"
            ],
        ];
    }

    /**
     * Template tags.
     *
     * All availabel template tags
     */
    public static function tags($list=null)
    {
        $tags = [];

        $tags[] = ['name' => 'SUBSCRIBER_EMAIL', 'required' => false];

        // List field tags
        if(isset($list)) {
            foreach ($list->fields as $field) {
                if($field->tag != 'EMAIL') {
                    $tags[] = ['name' => 'SUBSCRIBER_'.$field->tag, 'required' => false];
                }
            }
        }

        $tags = array_merge($tags, [
            ['name' => 'UNSUBSCRIBE_URL', 'required' => (\Auth::user()->customer && \Auth::user()->customer->getOption('unsubscribe_url_required') == 'yes' ? true : false)],
            ['name' => 'SUBSCRIBER_UID', 'required' => false],
            ['name' => 'WEB_VIEW_URL', 'required' => false],
            ['name' => 'CAMPAIGN_NAME', 'required' => false],
            ['name' => 'CAMPAIGN_UID', 'required' => false],
            ['name' => 'CAMPAIGN_SUBJECT', 'required' => false],
            ['name' => 'CAMPAIGN_FROM_EMAIL', 'required' => false],
            ['name' => 'CAMPAIGN_FROM_NAME', 'required' => false],
            ['name' => 'CAMPAIGN_REPLY_TO', 'required' => false],
            ['name' => 'CURRENT_YEAR', 'required' => false],
            ['name' => 'CURRENT_MONTH', 'required' => false],
            ['name' => 'CURRENT_DAY', 'required' => false],
            ['name' => 'CONTACT_NAME', 'required' => false],
            ['name' => 'CONTACT_COUNTRY', 'required' => false],
            ['name' => 'CONTACT_STATE', 'required' => false],
            ['name' => 'CONTACT_CITY', 'required' => false],
            ['name' => 'CONTACT_ADDRESS_1', 'required' => false],
            ['name' => 'CONTACT_ADDRESS_2', 'required' => false],
            ['name' => 'CONTACT_PHONE', 'required' => false],
            ['name' => 'CONTACT_URL', 'required' => false],
            ['name' => 'CONTACT_EMAIL', 'required' => false],
            ['name' => 'LIST_NAME', 'required' => false],
            ['name' => 'LIST_SUBJECT', 'required' => false],
            ['name' => 'LIST_FROM_NAME', 'required' => false],
            ['name' => 'LIST_FROM_EMAIL', 'required' => false],
        ]);

        return $tags;
    }

    /**
     * Replace url from content.
     *
     * @return void
     */
    public function replaceHtmlUrl($template_path)
    {
        $this->content = \Acelle\Library\Tool::replaceHtmlUrl($this->content, $template_path);
    }

    /**
     * Display creator name.
     *
     * @return string
     */
    public function displayCreatorName()
    {
        return is_object($this->admin) ? $this->admin->displayName() : (is_object($this->customer) ? $this->customer->displayName() : '');
    }

    /**
     * Copy new template.
     */
    public function copy($name, $customer=NULL, $admin=NULL)
    {
        $copy = $this->replicate();
        $copy->name = $name;
        $copy->created_at = \Carbon\Carbon::now();
        $copy->updated_at = \Carbon\Carbon::now();
        $copy->custom_order = 0;
        if ($customer) {
            $copy->admin_id = NULL;
            $copy->customer_id = $customer->id;
        }
        if ($admin) {
            $copy->admin_id = $admin->id;
            $copy->customer_id = NULL;
        }
        $copy->save();

        // Copy uploaded folder
        if (file_exists($this->getUploadDir())) {
            if (!file_exists($copy->getUploadDir())) {
                mkdir($copy->getUploadDir(), 0777, true);
            }

            \Acelle\Library\Tool::xcopy($this->getUploadDir(), $copy->getUploadDir());
            // replace image url
            $copy->content = str_replace($this->getUploadUri(), $copy->getUploadUri(), $copy->content);
            $copy->save();
        }
    }

    /**
     * Get public template upload uri.
     */
    public function getUploadUri()
    {
        return 'upload/templates/template_'.$this->uid;
    }

    /**
     * Get public template upload dir.
     */
    public function getUploadDir()
    {
        return public_path($this->getUploadUri());
    }
}
