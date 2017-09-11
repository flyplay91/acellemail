<?php

namespace Acelle\Http\Controllers\Api;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

/**
 * /api/v1/plans - API controller for managing plans.
 */
class PlanController extends Controller
{
    /**
     * Display all plans.
     *
     * GET /api/v1/plans
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::guard('api')->user();

        // authorize
        if (!$user->can('read', new \Acelle\Model\Plan())) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        $rows = \Acelle\Model\Plan::getAll()->limit(100)
            ->get();

        $plans = $rows->map(function ($plan) {
            return [
                'uid' => $plan->uid,
                'name' => $plan->name,
                'price' => $plan->price,
                'currency_code' => $plan->currency->code,
                'frequency_amount' => $plan->frequency_amount,
                'frequency_unit' => $plan->frequency_unit,
                'options' => $plan->getOptions(),
                'status' => $plan->status,
                'color' => $plan->color,
                'quota' => $plan->quota,
                'custom_order' => $plan->custom_order,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ];
        });

        return \Response::json($plans, 200);
    }

    /**
     * Create a new plan.
     *
     * POST /api/v1/plans
     *
     * @param \Illuminate\Http\Request $request All plan information
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = \Auth::guard('api')->user();

        $plan = new \Acelle\Model\Plan();

        // authorize
        if (!$user->can('create', $plan)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // save posted data
        if ($request->isMethod('post')) {
            $plan->fill($request->all());
            // $plan->options = json_encode($request->options);
            $plan->fillOptions($request->options);

            $validator = \Validator::make($request->all(), $plan->apiRules());
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }

            $rules = [];
            if (isset($request->sending_servers)) {
                foreach ($request->sending_servers as $key => $param) {
                    if ($param['check']) {
                        $rules['sending_servers.'.$key.'.fitness'] = 'required';
                    }
                }
            }

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }

            $plan->admin_id = $user->admin->id;
            $plan->status = \Acelle\Model\Plan::STATUS_ACTIVE;
            $plan->save();

            // For sending servers
            if (isset($request->sending_servers)) {
                $plan->updateSendingServers($request->sending_servers);
            }

            // For email verification servers
            if (isset($request->email_verification_servers)) {
                $plan->updateEmailVerificationServers($request->email_verification_servers);
            }

            return \Response::json(array(
                'message' => trans('messages.plan.created'),
                'plan_uid' => $plan->uid
            ), 200);
        }

        //// validate and save posted data
        //if ($request->isMethod('post')) {
        //    $validator = \Validator::make($request->all(), $subscriber->getRules());
        //    if ($validator->fails()) {
        //        return response()->json($validator->messages(), 403);
        //    }
        //
        //    // Save subscriber
        //    $subscriber->email = $request->EMAIL;
        //    $subscriber->save();
        //
        //    // Update field
        //    $subscriber->updateFields($request->all());
        //
        //    // Log
        //    $subscriber->log('created', $user->customer);
        //
        //    // update MailList cache
        //    $subscriber->mailList->updateCachedInfo();
        //
        //    // update MailList cache
        //    $subscriber->mailList->updateCachedInfo();
        //
        //    return \Response::json(array(
        //        'message' => trans('messages.subscriber.created'),
        //        'subscriber_uid' => $subscriber->uid
        //    ), 200);
        //}
        //
        //
        //
        //// Get current user
        //$user = $request->user();
        //$plan = new \Acelle\Model\Plan();
        //
        //// authorize
        //if (\Gate::denies('create', $plan)) {
        //    return $this->notAuthorized();
        //}
        //
        //// save posted data
        //if ($request->isMethod('post')) {
        //    $plan->fill($request->all());
        //    // $plan->options = json_encode($request->options);
        //    $plan->fillOptions($request->options);
        //
        //    $this->validate($request, $plan->rules());
        //
        //    $rules = [];
        //    if (isset($request->sending_servers)) {
        //        foreach ($request->sending_servers as $key => $param) {
        //            if ($param['check']) {
        //                $rules['sending_servers.'.$key.'.fitness'] = 'required';
        //            }
        //        }
        //    }
        //    $this->validate($request, $rules);
        //
        //    $plan->admin_id = $user->admin->id;
        //    $plan->status = \Acelle\Model\Plan::STATUS_ACTIVE;
        //    $plan->save();
        //
        //    // For sending servers
        //    if (isset($request->sending_servers)) {
        //        $plan->updateSendingServers($request->sending_servers);
        //    }
        //
        //    // For email verification servers
        //    if (isset($request->email_verification_servers)) {
        //        $plan->updateEmailVerificationServers($request->email_verification_servers);
        //    }
        //
        //    $request->session()->flash('alert-success', trans('messages.plan.created'));
        //    return redirect()->action('Admin\PlanController@index');
        //}
    }
}
