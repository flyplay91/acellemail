<?php

namespace Acelle\Http\Controllers\Api;

use Acelle\Http\Controllers\Controller;

/**
 * /api/v1/campaigns - API controller for managing campaigns.
 */
class AutomationController extends Controller
{
    /**
     * Call api for automation api call type.
     *
     * GET /api/v1/campaigns
     *
     * @return \Illuminate\Http\Response
     */
    public function apiCall($uid)
    {
        $user = \Auth::guard('api')->user();
        $automation = \Acelle\Model\Automation::findByUid($uid);
        
        try {            
            // check if automation exists
            if (!is_object($automation)) {
                throw new \Exception("Automation not found"); 
            }
            
            // authorize
            if (!$user->can('update', $automation)) {
                throw new \Exception("Unauthorized"); 
            }
            
            // start automation
            $automation->start();
            return \Response::json(array('message' => trans('messages.automation.started')), 200);
        } catch (\Exception $e) {
            return \Response::json(array('message' => $e->getMessage()), 400);
        }
    }
}
