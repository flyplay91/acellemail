<?php

/**
 * ImportSubscribersSystemJob class, inherit from the SystemJob model.
 *
 * Model class for tracking subscriber importing system jobs.
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

use Acelle\Model\SystemJob;

class ImportSubscribersSystemJob extends SystemJob
{
    protected $table = 'system_jobs';

    public function getLog() {
        $data = json_decode($this->data, true);
        return $data['log'];
    }
}
