<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Acelle\Library\Log as MailLog;
use Illuminate\Support\Facades\Log as LaravelLog;

class SubscriptionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', [
            'except' => [
                'selectPlan',
                'register',
                'preview',
            ]
        ]);
    }

    /**
     * Select a plan.
     */
    public function selectPlan()
    {
        $plans = \Acelle\Model\Plan::getAllActive()->get();

        return view('subscriptions.select_plan', [
            'plans' => $plans
        ]);
    }

    /**
     * Customer registration.
     */
    public function register(Request $request)
    {
        if (\Acelle\Model\Setting::get('enable_user_registration') == 'no') {
            return $this->notAuthorized();
        }

        // Customer account
        $user = $request->user();
        $is_customer_logged_in = is_object($user) && is_object($user->customer);
        if($is_customer_logged_in) {
            return redirect()->action('SubscriptionController@subscription', $request->plan_uid);
        } else {
            $customer = new \Acelle\Model\Customer();
            $customer->uid = 0;
            $customer->status = \Acelle\Model\Customer::STATUS_ACTIVE;
            if (!empty($request->old())) {
                $customer->fill($request->old());
                // User info
                $customer->user = new \Acelle\Model\User();
                $customer->user->fill($request->old());
            }
        }

         // save posted data
        if ($request->isMethod('post')) {
            // Validation
            if($is_customer_logged_in) {
            } else {
                $rules = $customer->rules();

                // Captcha check
                if (\Acelle\Model\Setting::get('registration_recaptcha') == 'yes') {
                    $success = \Acelle\Library\Tool::checkReCaptcha($request);
                    if (!$success) {
                        $rules['recaptcha_invalid'] = 'required';
                    }
                }

                $this->validate($request, $rules);
            }

            // Save customer
            if(!$is_customer_logged_in) {
                $customer->updateInformation($request);
            }

            // Send registration confirmation email
            if($is_customer_logged_in) {
            } else {
                $user = \Acelle\Model\User::find($customer->user_id);

                try {
                    $user->sendActivationMail($customer->displayName(), action('AccountController@subscriptionNew', ['plan_uid' => $request->plan_uid]));
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                    MailLog::error( $error );
                    return view('somethingWentWrong', ['message' => trans('messages.something_went_wrong_with_email_service') . ": " . $error]);
                }

                return view('subscriptions.register_confirmation_notice');
            }
        }

        return view('subscriptions.register', [
            'customer' => $customer,
            'is_customer_logged_in' => $is_customer_logged_in
        ]);
    }

    /**
     * Customer subscription.
     */
    public function subscription(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        // Check if customer has current inactive subscription
        if ($customer->subscriptionsCount()) {
            return redirect()->action('AccountController@subscription');
        }

        // Subscription
        $subscription = new \Acelle\Model\Subscription();
        $subscription->customer_id = $customer->id;

        if($request->plan_uid) {
            $plan = \Acelle\Model\Plan::findByUid($request->plan_uid);
            $subscription->plan_id = $plan->id;
        }

        $subscription->fillAttributes($request->all());
        if (!empty($request->old())) {
            $subscription->fillAttributes($request->old());
        }

         // save posted data
        if ($request->isMethod('post')) {
            // Validation
            $this->validate($request, $subscription->frontendRules());

            // Save current user info
            $subscription->fillAttributes($request->all());

            if ($subscription->isFree()) {
                $subscription->status = \Acelle\Model\Subscription::STATUS_ACTIVE;
                $subscription->save();

                $request->session()->flash('alert-success', trans('messages.subscription.created'));
                return redirect()->action('SubscriptionController@finish');
            } else {
                $subscription->status = \Acelle\Model\Subscription::STATUS_INACTIVE;
                $subscription->save();

                return redirect()->action('SubscriptionController@checkout', $subscription->uid);
            }
        }

        return view('subscriptions.subscription', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Subscription preview.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        // Get current user
        $subscription = new \Acelle\Model\Subscription();

        $subscription->customer_id = $request->user()->customer->id;
        $subscription->fillAttributes($request->all());

        return view('subscriptions.preview', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Finish page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function finish(Request $request)
    {
        $customer = $request->user()->customer;
        $subscription = $customer->getCurrentSubscription();

        return view('subscriptions.finish', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer = $request->user()->customer;

        $request->merge(array("customer_id" => $customer->id));
        $request->merge(array("customer_list" => true));
        $subscriptions = \Acelle\Model\Subscription::search($request);

        return view('subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        $customer = $request->user()->customer;

        $request->merge(array("customer_id" => $customer->id));
        $request->merge(array("customer_list" => true));
        $subscriptions = \Acelle\Model\Subscription::search($request)->paginate($request->per_page);

        if ($request->filters["subscription_uid"]) {
            $subscription = \Acelle\Model\Subscription::findByUid($request->filters["subscription_uid"]);
        }
        if (!isset($subscription)) {
            $subscription = $subscriptions->first();
        }

        return view('subscriptions._list', [
            'subscriptions' => $subscriptions,
            'subscription' => $subscription,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $subscription = new \Acelle\Model\Subscription();

        // authorize
        if (!$request->user()->customer->can('create', $subscription)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $subscription->fillAttributes($request->old());
        }

        return view('subscriptions.create', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get current user
        $user = $request->user();
        $customer = $user->customer;
        $subscription = new \Acelle\Model\Subscription();
        $subscription->status = \Acelle\Model\Subscription::STATUS_INACTIVE;
        $subscription->customer_id = $customer->id;

        // authorize
        if (!$customer->can('create', $subscription)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, $subscription->frontendRules());

            $subscription->fillAttributes($request->all());

            // start_at must always be set
            if (empty($subscription->start_at)) {
                $subscription->start_at = \Carbon\Carbon::now();
            }

            $subscription->save();

            if ($subscription->isFree()) {
                try {
                    $subscription->enable();
                } catch (\Exception $ex) {
                    // just suppress the error and leave the subscription disabled
                    LaravelLog::warning("Cannot enable subscription {$subscription->id}, proceed anyway");
                }
                
                $request->session()->flash('alert-success', trans('messages.subscription.created'));
                return redirect()->action('AccountController@subscription');
            } else {
                return redirect()->action('SubscriptionController@checkout', $subscription->uid);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $uid)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $subscriptions = \Acelle\Model\Subscription::whereIn('uid', explode(',', $request->uids));

        foreach ($subscriptions->get() as $subscription) {
            // authorize
            if ($request->user()->customer->can('delete', $subscription)) {
                $subscription->delete();
            }
        }

        // Redirect to my subscription page
        $request->session()->flash('alert-success', trans('messages.subscriptions.deleted'));
        return redirect()->action('AccountController@subscription');
    }

    /**
     * Subscription checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkout(Request $request)
    {
        // Get current user
        $subscription = \Acelle\Model\Subscription::findByUid($request->uid);

        if (is_object($subscription->paymentMethod)) {
            $action = action('PaymentController@' . $subscription->paymentMethod->type, $subscription->uid);

            // save current checkout link
            $request->session()->set('current_payment_link', $action);

            return redirect()->away($action);
        }
    }

    /**
     * Subscription select payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectPaymentMethod(Request $request)
    {
        // Get current user
        $subsciption = \Acelle\Model\Subscription::findByUid($request->uid);
        $subsciption->payment_method_id = \Acelle\Model\PaymentMethod::findByUid($request->payment_method_uid)->id;
        $subsciption->save();

        return redirect()->action('SubscriptionController@checkout', $subsciption->uid);
    }
}
