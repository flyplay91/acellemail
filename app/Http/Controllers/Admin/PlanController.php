<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Acelle\Http\Requests;
use Acelle\Http\Controllers\Controller;

class PlanController extends Controller
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
        if (\Gate::denies('read', new \Acelle\Model\Plan())) {
            return $this->notAuthorized();
        }

        // If admin can view all sending domains
        if (!$request->user()->admin->can("readAll", new \Acelle\Model\Plan())) {
            $request->merge(array("admin_id" => $request->user()->admin->id));
        }

        $plans = \Acelle\Model\Plan::search($request);

        return view('admin.plans.index', [
            'plans' => $plans,
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
        if (\Gate::denies('read', new \Acelle\Model\Plan())) {
            return $this->notAuthorized();
        }

        // If admin can view all sending domains
        if (!$request->user()->admin->can("readAll", new \Acelle\Model\Plan())) {
            $request->merge(array("admin_id" => $request->user()->admin->id));
        }

        $plans = \Acelle\Model\Plan::search($request)->paginate($request->per_page);

        return view('admin.plans._list', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $plan = new \Acelle\Model\Plan([
            'price' => 0,
            'frequency_amount' => 1,
            'frequency_unit' => 'month'
        ]);
        $plan->status = \Acelle\Model\Plan::STATUS_ACTIVE;

        // authorize
        if (\Gate::denies('create', $plan)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $plan->fill($request->old());
        }

        // For options
        if (isset($request->old()['options'])) {
            $plan->options = json_encode($request->old()['options']);
        }
        $options = $plan->getOptions();

        // Sending servers
        if (isset($request->old()['sending_servers'])) {
            $plan->plansSendingServers = collect([]);
            foreach ($request->old()['sending_servers'] as $key => $param) {
                if ($param['check']) {
                    $server = \Acelle\Model\SendingServer::findByUid($key);
                    $row = new \Acelle\Model\PlansSendingServer();
                    $row->plan_id = $plan->id;
                    $row->sending_server_id = $server->id;
                    $row->fitness = $param['fitness'];
                    $plan->plansSendingServers->push($row);
                }
            }
        }

        // Email verification servers
        if (isset($request->old()['email_verification_servers'])) {
            $plan->fillPlansEmailVerificationServers($request->old()['email_verification_servers']);
        }

        return view('admin.plans.create', [
            'plan' => $plan,
            'options' => $options
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get current user
        $user = $request->user();
        $plan = new \Acelle\Model\Plan();

        // authorize
        if (\Gate::denies('create', $plan)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('post')) {
            $plan->fill($request->all());
            // $plan->options = json_encode($request->options);
            $plan->fillOptions($request->options);

            $this->validate($request, $plan->rules());

            $rules = [];
            if (isset($request->sending_servers)) {
                foreach ($request->sending_servers as $key => $param) {
                    if ($param['check']) {
                        $rules['sending_servers.'.$key.'.fitness'] = 'required';
                    }
                }
            }
            $this->validate($request, $rules);

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

            $request->session()->flash('alert-success', trans('messages.plan.created'));
            return redirect()->action('Admin\PlanController@index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $plan = \Acelle\Model\Plan::findByUid($id);

        // authorize
        if (\Gate::denies('update', $plan)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $plan->fill($request->old());
        }

        // For options
        if (isset($request->old()['options'])) {
            $plan->options = json_encode($request->old()['options']);
        }
        $options = $plan->getOptions();

        // Sending servers
        if (isset($request->old()['sending_servers'])) {
            $plan->plansSendingServers = collect([]);
            foreach ($request->old()['sending_servers'] as $key => $param) {
                if ($param['check']) {
                    $server = \Acelle\Model\SendingServer::findByUid($key);
                    $row = new \Acelle\Model\PlansSendingServer();
                    $row->plan_id = $plan->id;
                    $row->sending_server_id = $server->id;
                    $row->fitness = $param['fitness'];
                    $plan->plansSendingServers->push($row);
                }
            }
        }

        // Email verification servers
        if (isset($request->old()['email_verification_servers'])) {
            $plan->fillPlansEmailVerificationServers($request->old()['email_verification_servers']);
        }

        return view('admin.plans.edit', [
            'plan' => $plan,
            'options' => $options
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get current user
        $user = $request->user();
        $plan = \Acelle\Model\Plan::findByUid($id);

        // authorize
        if (\Gate::denies('update', $plan)) {
            return $this->notAuthorized();
        }

        // prevent action from demo mod
        if($this->isDemoMode()) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('patch')) {
            $plan->fill($request->all());
            // $plan->options = json_encode($request->options);
            $plan->fillOptions($request->options);

            $this->validate($request, $plan->rules());

            $rules = [];
            if (isset($request->sending_servers)) {
                foreach ($request->sending_servers as $key => $param) {
                    if ($param['check']) {
                        $rules['sending_servers.'.$key.'.fitness'] = 'required';
                    }
                }
            }
            $this->validate($request, $rules);

            $plan->save();

            // For sending servers
            if (isset($request->sending_servers)) {
                $plan->updateSendingServers($request->sending_servers);
            }

            // For email verification servers
            if (isset($request->email_verification_servers)) {
                $plan->updateEmailVerificationServers($request->email_verification_servers);
            }

            $request->session()->flash('alert-success', trans('messages.plan.updated'));
            return redirect()->action('Admin\PlanController@edit', $plan->uid);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Enable item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $items = \Acelle\Model\Plan::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::allows('update', $item)) {
                $item->enable();
            }
        }

        // Redirect to my lists page
        echo trans('messages.plans.enabled');
    }

    /**
     * Disable item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $items = \Acelle\Model\Plan::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::allows('update', $item)) {
                $item->disable();
            }
        }

        // Redirect to my lists page
        echo trans('messages.plans.disabled');
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
        $items = \Acelle\Model\Plan::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::denies('delete', $item)) {
                return;
            }
        }

        foreach ($items->get() as $item) {
            $item->delete();
        }

        // Redirect to my lists page
        echo trans('messages.plans.deleted');
    }

    /**
     * Custom sort items.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $sort = json_decode($request->sort);
        foreach ($sort as $row) {
            $item = \Acelle\Model\Plan::findByUid($row[0]);

            // authorize
            if (\Gate::denies('update', $item)) {
                return $this->notAuthorized();
            }

            $item->custom_order = $row[1];
            $item->save();
        }

        echo trans('messages.plans.custom_order.updated');
    }

    /**
     * Select2 plan.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function select2(Request $request)
    {
        echo \Acelle\Model\Plan::select2($request);
    }

    /**
     * Delete confirm message.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteConfirm(Request $request)
    {
        $plans = \Acelle\Model\Plan::whereIn('uid', explode(',', $request->uids));

        return view('admin.plans.delete_confirm', [
            'plans' => $plans,
        ]);
    }

    /**
     * Chart pie chart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function pieChart(Request $request)
    {
        $admin = $request->user()->admin;

        // authorize
        if (\Gate::denies('read', new \Acelle\Model\Plan())) {
            return $this->notAuthorized();
        }

        $result = [
            'title' => '',
            'columns' => [],
            'data' => [],
            'bar_names' => [],
        ];

        $datas = [];
        foreach (\Acelle\Model\Plan::getAllActiveWithDefault()->get() as $plan) {
            $count = $admin->getAllSubscriptionsByPlan($plan)->count();
            // create data
            if ($count) {
                $result['bar_names'][] = $plan->name;
                $datas[] = ['value' => $count, 'name' => $plan->name];
            }
        }

        // datas
        $result['data'][] = [
            'name' => trans('messages.plan_pie_chart'),
            'type' => 'pie',
            'radius' => '70%',
            'center' => ['50%', '57.5%'],
            'data' => $datas
        ];

        $result['pie'] = 1;

        return json_encode($result);
    }
}
