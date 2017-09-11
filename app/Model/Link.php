<?php

/**
 * Link class.
 *
 * Model class for links
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

class Link extends Model
{
    /**
     * Get the campaigns for link.
     */
    public function campaigns()
    {
        return $this->belongsToMany('Acelle\Model\Campaign', 'campaign_links');
    }

    public function clicks($customer = null, $campaign = null)
    {
        $query = ClickLog::select('click_logs.*')
            ->where('click_logs.url', '=', $this->url);

        if (isset($customer)) {
            $query = $query->join('tracking_logs', 'tracking_logs.message_id', '=', 'click_logs.message_id')
                ->join('campaigns', 'campaigns.id', '=', 'tracking_logs.campaign_id')
                ->where('campaigns.customer_id', '=', $customer->id);
        }

        if (isset($campaign)) {
            $query = $query->join('tracking_logs', 'tracking_logs.message_id', '=', 'click_logs.message_id')
                ->where('tracking_logs.campaign_id', '=', $campaign->id);
        }

        return $query;
    }

    public function lastClick($customer = null, $campaign = null)
    {
        $query = $this->clicks($customer, $campaign)->orderBy('created_at', 'desc')->first();

        return $query;
    }
}
