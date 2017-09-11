<?php

/**
 * Field class.
 *
 * Model class for List's custom fields
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

class Field extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mail_list_id', 'type', 'label', 'tag', 'default_value', 'visible', 'required', 'custom_order',
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public static $fields_rules = array(

    );

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function mailList()
    {
        return $this->belongsTo('Acelle\Model\MailList');
    }

    public function fieldOptions()
    {
        return $this->hasMany('Acelle\Model\FieldOption');
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
            while (Field::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }

    /**
     * Format string to field tag.
     *
     * @var string
     */
    public static function formatTag($string)
    {
        return strtoupper(preg_replace('/[^0-9a-zA-Z_]/m', '', $string));
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
     * Get select options.
     *
     * @return array
     */
    public function getSelectOptions()
    {
        $options = $this->fieldOptions->map(function ($item) {
            return ['value' => $item->value, 'text' => $item->label];
        });

        return $options;
    }
    
    /**
     * Get control name.
     *
     * @return array
     */
    public static function getControlNameByType($type)
    {
        if($type == 'date') {
            return 'date';
        } elseif ($type == 'datetime') {
            return 'datetime';
        }
        
        return 'text';
    }
}
