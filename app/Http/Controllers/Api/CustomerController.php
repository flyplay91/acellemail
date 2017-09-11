<?php

namespace Acelle\Http\Controllers\Api;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

/**
 * /api/v1/customers - API controller for managing customers.
 */
class CustomerController extends Controller
{
    /**
     * Create new customer.
     *
     * POST /api/v1/customers/store
     *
     * @param \Illuminate\Http\Request $request All customer information.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get current user
        $current_user = \Auth::guard('api')->user();
        $customer = new \Acelle\Model\Customer();

        // authorize
        if (!$current_user->can('create', $customer)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // save posted data
        if ($request->isMethod('post')) {
            $rules = $customer->apiRules();

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }

            // Create user account for customer
            $user = new \Acelle\Model\User();
            $user->email = $request->email;
            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Save current user info
            $customer->user_id = $user->id;
            $customer->admin_id = $current_user->admin->id;
            $customer->fill($request->all());
            $customer->status = 'active';

            if ($customer->save()) {
                // Upload and save image
                if ($request->hasFile('image')) {
                    if ($request->file('image')->isValid()) {
                        // Remove old images
                        $customer->removeImage();
                        $customer->image = $customer->uploadImage($request->file('image'));
                        $customer->save();
                    }
                }

                // Remove image
                if ($request->_remove_image == 'true') {
                    $customer->removeImage();
                    $customer->image = '';
                }

                return \Response::json(array(
                    'message' => trans('messages.customer.created'),
                    'customer_uid' => $customer->uid,
                    'api_token' => $customer->user->api_token
                ), 200);
            }
        }
    }

    /**
     * Update customer.
     *
     * PATCH /api/v1/customers
     *
     * @param \Illuminate\Http\Request $request All customer information.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid)
    {
        // Get current user
        $current_user = \Auth::guard('api')->user();
        $customer = \Acelle\Model\Customer::findByUid($uid);

        // check if item exists
        if (!is_object($customer)) {
            return \Response::json(array('message' => 'Item not found'), 404);
        }

        // authorize
        if (!$current_user->can('update', $customer)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // save posted data
        if ($request->isMethod('patch')) {

            if($this->isDemoMode()) {
                return $this->notAuthorized();
            }

            $rules = $customer->apiUpdateRules($request);

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 403);
            }

            // Update user account for customer
            $user = $customer->user;
            if (!empty($request->email)) {
                $user->email = $request->email;
            }
            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Save current user info
            $customer->fill($request->all());
            $customer->save();

            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    // Remove old images
                    $customer->removeImage();
                    $customer->image = $customer->uploadImage($request->file('image'));
                }
            }

            // Remove image
            if ($request->_remove_image == 'true') {
                $customer->removeImage();
                $customer->image = '';
            }

            return \Response::json(array(
                'message' => trans('messages.customer.updated'),
                'customer_uid' => $customer->uid
            ), 200);
        }
    }

    /**
     * Display the specified customer information.
     *
     * GET /api/v1/customers/{uid}
     *
     * @param int $id customer's uid
     *
     * @return \Illuminate\Http\Response
     */
    public function show($uid)
    {
        $user = \Auth::guard('api')->user();

        $customer = \Acelle\Model\Customer::findByUid($uid);

        // check if item exists
        if (!is_object($customer)) {
            return \Response::json(array('message' => 'Customer not found'), 404);
        }

        // authorize
        if (!$user->can('read', $customer)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        // Customer info
        $result = [
            'uid' => $customer->uid,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'image' => $customer->image,
            'timezone' => $customer->timezone,
            'status' => $customer->status,
            'options' => $customer->getOptions(),
            'next_subscription_start_at' => $customer->getNextScriptionStartAt(),
        ];

        // Customer contact
        $contact = $customer->contact;
        if (is_object($contact)) {
            $result['contact'] = [
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'company' => $contact->company,
                'address_1' => $contact->address_1,
                'address_2' => $contact->address_2,
                'country' => $contact->countryName(),
                'state' => $contact->state,
                'city' => $contact->city,
                'zip' => $contact->zip,
                'phone' => $contact->phone,
                'url' => $contact->url,
                'email' => $contact->email,
            ];
        }

        // Current subscription
        $subscription = $customer->getCurrentSubscription();
        if (is_object($subscription)) {
            $result['current_subscription'] = [
                'uid' => $subscription->uid,
                'plan_name' => $subscription->plan_name,
                'price' => $subscription->price,
                'currency_code' => $subscription->currency_code,
                'start_at' => $subscription->start_at,
                'end_at' => $subscription->end_at,
                'status' => $subscription->status,
                'paid' => $subscription->paid,
                'time_status' => $subscription->timeStatus(),
            ];
        }

        // All subscription
        $subscriptions = $customer->subscriptions()->get();
        $result['subscriptions'] = [];
        foreach ($subscriptions as $subscription) {
            $result['subscriptions'][] = [
                'uid' => $subscription->uid,
                'plan_name' => $subscription->plan_name,
                'price' => $subscription->price,
                'currency_code' => $subscription->currency_code,
                'start_at' => $subscription->start_at,
                'end_at' => $subscription->end_at,
                'status' => $subscription->status,
                'paid' => $subscription->paid,
                'time_status' => $subscription->timeStatus(),
            ];
        }

        return \Response::json(['customer' => $result], 200);
    }

    /**
     * Enable customer.
     *
     * PATCH /api/v1/customers/{uid}
     *
     * @param int $id customer's uid
     *
     * @return \Illuminate\Http\Response
     */
    public function enable($uid)
    {
        $user = \Auth::guard('api')->user();

        $customer = \Acelle\Model\Customer::findByUid($uid);

        // check if item exists
        if (!is_object($customer)) {
            return \Response::json(array('message' => 'Customer not found'), 404);
        }

        // authorize
        if (!$user->can('enable', $customer)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        $customer->enable();

        return \Response::json(array(
            'message' => trans('messages.customer.enabled'),
            'customer_uid' => $customer->uid
        ), 200);
    }

    /**
     * Disable customer.
     *
     * PATCH /api/v1/customers/{uid}
     *
     * @param int $id customer's uid
     *
     * @return \Illuminate\Http\Response
     */
    public function disable($uid)
    {
        $user = \Auth::guard('api')->user();

        $customer = \Acelle\Model\Customer::findByUid($uid);

        // check if item exists
        if (!is_object($customer)) {
            return \Response::json(array('message' => 'Customer not found'), 404);
        }

        // authorize
        if (!$user->can('disable', $customer)) {
            return \Response::json(array('message' => 'Unauthorized'), 401);
        }

        $customer->disable();

        return \Response::json(array(
            'message' => trans('messages.customer.disabled'),
            'customer_uid' => $customer->uid
        ), 200);
    }
}
