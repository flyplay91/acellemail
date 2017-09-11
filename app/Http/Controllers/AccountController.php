<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as LaravelLog;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    /**
     * Update user profile.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function profile(Request $request)
    {
        // Get current user
        $customer = $request->user()->customer;
        $customer->getColorScheme();

        // Authorize
        if (!$request->user()->customer->can('profile', $customer)) {
            return $this->notAuthorized();
        }

        // Save posted data
        if ($request->isMethod('post')) {
            // Prenvent save from demo mod
            if ($this->isDemoMode()) {
                return $this->notAuthorized();
            }

            $this->validate($request, $customer->rules());

            // Update user account for customer
            $user = $customer->user;
            $user->email = $request->email;
            // Update password
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Save current user info
            $customer->fill($request->all());

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

            if ($customer->save()) {
                $request->session()->flash('alert-success', trans('messages.profile.updated'));
            }
        }

        if (!empty($request->old())) {
            $customer->fill($request->old());
            // User info
            $customer->user->fill($request->old());
        }

        return view('account.profile', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update customer contact information.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     **/
    public function contact(Request $request)
    {
        // Get current user
        $customer = $request->user()->customer;
        $contact = $customer->getContact();

        // Create new company if null
        if (!is_object($contact)) {
            $contact = new \Acelle\Model\Contact();
        }

        // save posted data
        if ($request->isMethod('post')) {
            // Prenvent save contact
            if (isset($contact->id) && $this->isDemoMode()) {
                return $this->notAuthorized();
            }

            $this->validate($request, \Acelle\Model\Contact::$rules);

            $contact->fill($request->all());

            // Save current user info
            if ($contact->save()) {
                if (is_object($contact)) {
                    $customer->contact_id = $contact->id;
                    $customer->save();
                }
                $request->session()->flash('alert-success', trans('messages.customer_contact.updated'));
            }
        }

        return view('account.contact', [
            'customer' => $customer,
            'contact' => $contact->fill($request->old()),
        ]);
    }

    /**
     * User logs.
     */
    public function logs(Request $request)
    {
        $logs = $request->user()->customer->logs;

        return view('account.logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Logs list.
     */
    public function logsListing(Request $request)
    {
        $logs = \Acelle\Model\Log::search($request)->paginate($request->per_page);

        return view('account.logs_listing', [
            'logs' => $logs,
        ]);
    }

    /**
     * Quta logs.
     */
    public function quotaLog(Request $request)
    {
        return view('account.quota_log');
    }

    /**
     * Api token.
     */
    public function api(Request $request)
    {
        return view('account.api');
    }

    /**
     * Api token.
     */
    public function subscription(Request $request)
    {
        $customer = $request->user()->customer;

        // Get current subscription
        $subscription = $customer->getCurrentSubscriptionIgnoreStatus();

        // Get next subscription
        $nextSubscription = $customer->getNextSubscription();

        // If has no subscription
        if (!$subscription) {
            return redirect()->action('AccountController@subscriptionNew');
        }

        return view('account.subscription', [
            'subscription' => $subscription,
            'nextSubscription' => $nextSubscription,
        ]);
    }

    /**
     * Api token.
     */
    public function subscriptionNew(Request $request)
    {
        $customer = $request->user()->customer;

        $subscription = new \Acelle\Model\Subscription();
        $subscription->customer_id = $customer->id;

        if ($request->plan_uid) {
            $subscription->fillAttributes($request->all());
        }

        // authorize
        if (!$request->user()->customer->can('create', $subscription)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $subscription->fillAttributes($request->old());
        }

        return view('account.subscription_new', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Renew api token.
     */
    public function renewToken(Request $request)
    {
        $user = $request->user();

        $user->api_token = str_random(60);
        $user->save();

        // Redirect to my lists page
        $request->session()->flash('alert-success', trans('messages.user_api.renewed'));

        return redirect()->action('AccountController@api');
    }
}
