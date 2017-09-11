<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Acelle\Http\Requests;
use Acelle\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log as LaravelLog;

class SubscriptionController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // authorize
        if (!$request->user()->admin->can('read', new \Acelle\Model\Subscription())) {
            return $this->notAuthorized();
        }

        // If admin can view all subscriptions of their customer
        if (!$request->user()->admin->can('readAll', new \Acelle\Model\Subscription())) {
            $request->merge(array("customer_admin_id" => $request->user()->admin->id));
        }
        $subscriptions = \Acelle\Model\Subscription::getAll();

        return view('admin.subscriptions.index', [
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
        // authorize
        if (!$request->user()->admin->can('read', new \Acelle\Model\Subscription())) {
            return $this->notAuthorized();
        }

        // If admin can view all subscriptions of their customer
        if (!$request->user()->admin->can('readAll', new \Acelle\Model\Subscription())) {
            $request->merge(array("customer_admin_id" => $request->user()->admin->id));
        }
        $subscriptions = \Acelle\Model\Subscription::search($request)->paginate($request->per_page);

        return view('admin.subscriptions._list', [
            'subscriptions' => $subscriptions,
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
        if (!$request->user()->admin->can('create', $subscription)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $subscription->fillAttributes($request->old());
        }

        return view('admin.subscriptions.create', [
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
        $subscription = new \Acelle\Model\Subscription();
        $subscription->status = \Acelle\Model\Subscription::STATUS_ACTIVE;

        // authorize
        if (!$request->user()->admin->can('create', $subscription)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, $subscription->rules());

            $subscription->fillAttributes($request->all());
            $subscription->admin_id = $user->admin->id;

            // Allow admin update end at date
            if(!empty($request->start_at)) {
                $subscription->start_at = \Acelle\Library\Tool::systemTimeFromString($request->start_at . ' 00:00');
            } else {
                $subscription->start_at = \Carbon\Carbon::now();
            }

            if(!empty($request->end_at)) {
                $subscription->end_at = \Acelle\Library\Tool::systemTimeFromString($request->end_at . ' 00:00');
            }

            $subscription->save();

            // Create sub-account if plan sending server type is subaccount
            try {
                if ($subscription->useSubAccount()) {
                    $subscription->createSubAccount();
                }

                $request->session()->flash('alert-success', trans('messages.subscription.created'));
            } catch (\Exception $ex) {
                // just surpress the error, the subscription shall be disabled, awaiting admin to enable
                $request->session()->flash('alert-warning', 'Subscription created but with error: ' . ($ex->getMessage()));
            } finally {
                return redirect()->action('Admin\SubscriptionController@index');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $uid)
    {
        $subscription = \Acelle\Model\Subscription::findByUid($uid);

        // authorize
        if (!$request->user()->admin->can('update', $subscription)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $subscription->fillAttributes($request->old());
        }

        // Sending servers
        if (isset($request->old()['sending_servers'])) {
            $subscription->subscriptionsSendingServers = collect([]);
            foreach ($request->old()['sending_servers'] as $key => $param) {
                if ($param['check']) {
                    $server = \Acelle\Model\SendingServer::findByUid($key);
                    $row = new \Acelle\Model\SubscriptionsSendingServer();
                    $row->subscription_id = $subscription->id;
                    $row->sending_server_id = $server->id;
                    $row->fitness = $param['fitness'];
                    $subscription->subscriptionsSendingServers->push($row);
                }
            }
        }

        // Email verification servers
        if (isset($request->old()['email_verification_servers'])) {
            $subscription->fillEmailVerificationServers($request->old()['email_verification_servers']);
        }

        // For options
        if (isset($request->old()['options'])) {
            $subscription->options = json_encode($request->old()['options']);
        }
        $options = $subscription->getOptions();

        return view('admin.subscriptions.edit', [
            'subscription' => $subscription,
            'options' => $options
        ]);
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
        $subscription = \Acelle\Model\Subscription::findByUid($uid);

        // prevent action from demo mod
        if($this->isDemoMode()) {
            return $this->notAuthorized();
        }

        // authorize
        if (!$request->user()->admin->can('update', $subscription)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('patch')) {
            $subscription->fillOptions($request->options);

            $this->validate($request, $subscription->rules());

            $rules = [];
            if (isset($request->sending_servers)) {
                foreach ($request->sending_servers as $key => $param) {
                    if ($param['check']) {
                        $rules['sending_servers.'.$key.'.fitness'] = 'required';
                    }
                }
            }
            $this->validate($request, $rules);

            // Allow admin update end at date
            if(!empty($request->start_at)) {
                $subscription->start_at = \Acelle\Library\Tool::systemTimeFromString($request->start_at . ' 00:00');
            }
            if(!empty($request->end_at)) {
                $subscription->end_at = \Acelle\Library\Tool::systemTimeFromString($request->end_at . ' 00:00');
            }

            $subscription->save();

            // For sending servers
            if (isset($request->sending_servers)) {
                $subscription->updateSendingServers($request->sending_servers);
            }

            // For email verification servers
            if (isset($request->email_verification_servers)) {
                $subscription->updateEmailVerificationServers($request->email_verification_servers);
            }

            $request->session()->flash('alert-success', trans('messages.subscription.updated'));
            return redirect()->action('Admin\SubscriptionController@edit', $subscription->uid);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        $subscription->customer_id = $request->customer_id;

        // authorize
        if (!$request->user()->admin->can('read', $subscription)) {
            return $this->notAuthorized();
        }

        $subscription->fillAttributes($request->all());

        return view('admin.subscriptions.preview', [
            'subscription' => $subscription
        ]);
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
            if ($request->user()->admin->can('delete', $subscription)) {
                $subscription->delete();
            }
        }

        // Redirect to my lists page
        echo trans('messages.subscriptions.deleted');
    }

    /**
     * Enable subscriptions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $subscriptions = \Acelle\Model\Subscription::whereIn('uid', explode(',', $request->uids));

        foreach ($subscriptions->get() as $subscription) {
            // authorize
            if ($request->user()->admin->can('enable', $subscription)) {
                try {
                    $subscription->enable();
                } catch (\Exception $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ]);
                    return;
                }
            }
        }

        // Redirect to my lists page
        echo trans('messages.subscriptions.enabled');
    }

    /**
     * Disable subscriptions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $subscriptions = \Acelle\Model\Subscription::whereIn('uid', explode(',', $request->uids));

        foreach ($subscriptions->get() as $subscription) {
            // authorize
            if ($request->user()->admin->can('disable', $subscription)) {
                $subscription->disable();
            }
        }

        // Redirect to my lists page
        echo trans('messages.subscriptions.disabled');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payments(Request $request, $uid)
    {
        $subscription = \Acelle\Model\Subscription::findByUid($uid);

        // authorize
        if (!$request->user()->admin->can('read', $subscription)) {
            return $this->notAuthorized();
        }

        return view('admin.subscriptions.payments', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Set paid subscriptions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function paid(Request $request)
    {
        $subscriptions = \Acelle\Model\Subscription::whereIn('uid', explode(',', $request->uids));

        foreach ($subscriptions->get() as $subscription) {
            // authorize
            if ($request->user()->admin->can('paid', $subscription)) {
                $subscription->setPaid($request->all());
            }
        }

        // Redirect to my lists page
        echo trans('messages.subscriptions.paid');
    }

    /**
     * Set un-paid subscriptions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function unpaid(Request $request)
    {
        $subscriptions = \Acelle\Model\Subscription::whereIn('uid', explode(',', $request->uids));

        foreach ($subscriptions->get() as $subscription) {
            // authorize
            if ($request->user()->admin->can('unpaid', $subscription)) {
                $subscription->setUnPaid($request->description);
            }
        }

        // Redirect to my lists page
        echo trans('messages.subscriptions.unpaid');
    }
}
