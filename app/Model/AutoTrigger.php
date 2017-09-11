<?php

/**
 * Automation Event Trigger class.
 *
 * Model class for logging triggered events
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

class AutoTrigger extends Model
{
    protected $fillable = [
        'start_at'
    ];

    protected $dates = ['created_at', 'updated_at', 'start_at'];

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function autoEvent()
    {
        return $this->belongsTo('Acelle\Model\AutoEvent');
    }

    /**
     * Associations
     *
     * @return the associated subscriber, in case of FollowUp trigger
     */
    public function subscriber()
    {
        return $this->belongsTo('Acelle\Model\Subscriber');
    }
}
