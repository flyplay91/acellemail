<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

class CustomerGroupController extends Controller
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
        if (\Gate::denies('read', new \Acelle\Model\CustomerGroup())) {
            return $this->notAuthorized();
        }
        
        $groups = \Acelle\Model\CustomerGroup::getAll();

        return view('admin.customer_groups.index', [
            'groups' => $groups,
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
        if (\Gate::denies('read', new \Acelle\Model\CustomerGroup())) {
            return $this->notAuthorized();
        }
        
        $groups = \Acelle\Model\CustomerGroup::search($request)->paginate($request->per_page);

        return view('admin.customer_groups._list', [
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Generate info
        $user = $request->user();

        $group = new \Acelle\Model\CustomerGroup([
                                'backend_access' => false,
                                'frontend_access' => true,
                            ]);
        $group->fill($request->old());
        
        // authorize
        if (\Gate::denies('create', $group)) {
            return $this->notAuthorized();
        }

        // For options
        if (isset($request->old()['options'])) {
            $group->options = json_encode($request->old()['options']);
        }
        $options = $group->getOptions();

        // For sending servers
        if (isset($request->old()['sending_servers'])) {
            $group->customer_group_sending_servers = collect([]);
            foreach ($request->old()['sending_servers'] as $key => $param) {
                if ($param['check']) {
                    $server = \Acelle\Model\SendingServer::findByUid($key);
                    $row = new \Acelle\Model\CustomerGroupSendingServer();
                    $row->customer_group_id = $group->id;
                    $row->sending_server_id = $server->id;
                    $row->fitness = $param['fitness'];
                    $group->customer_group_sending_servers->push($row);
                }
            }
        }

        return view('admin.customer_groups.create', [
            'group' => $group,
            'options' => $options,
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
        // Generate info
        $user = $request->user();
        $group = new \Acelle\Model\CustomerGroup(array());
        
        // authorize
        if (\Gate::denies('create', $group)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            $this->validate($request, \Acelle\Model\CustomerGroup::rules());

            $rules = [];
            if (isset($request->sending_servers)) {
                foreach ($request->sending_servers as $key => $param) {
                    if ($param['check']) {
                        $rules['sending_servers.'.$key.'.fitness'] = 'required';
                    }
                }
            }
            $this->validate($request, $rules);

            $group->fill($request->all());
            $group->options = json_encode($request->options);
            $group->admin_id = $user->admin->id;
            $group->save();

            // For sending servers
            if (isset($request->sending_servers)) {
                $group->updateSendingServers($request->sending_servers);
            }

            $request->session()->flash('alert-success', trans('messages.customer_group.created'));

            return redirect()->action('Admin\CustomerGroupController@index');
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
        // Generate info
        $user = $request->user();
        $group = \Acelle\Model\CustomerGroup::find($id);
        $group->fill($request->old());

        // authorize
        if (\Gate::denies('update', $group)) {
            return $this->notAuthorized();
        }

        // For options
        if (isset($request->old()['options'])) {
            $group->options = json_encode($request->old()['options']);
        }
        $options = $group->getOptions();

        // For sending servers
        if (isset($request->old()['sending_servers'])) {
            $group->customer_group_sending_servers = collect([]);
            foreach ($request->old()['sending_servers'] as $key => $param) {
                if ($param['check']) {
                    $server = \Acelle\Model\SendingServer::findByUid($key);
                    $row = new \Acelle\Model\CustomerGroupSendingServer();
                    $row->customer_group_id = $group->id;
                    $row->sending_server_id = $server->id;
                    $row->fitness = $param['fitness'];
                    $group->customer_group_sending_servers->push($row);
                }
            }
        }

        return view('admin.customer_groups.edit', [
            'group' => $group,
            'options' => $options,
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
        // Generate info
        $user = $request->user();

        $group = \Acelle\Model\CustomerGroup::find($id);

        // authorize
        if (\Gate::denies('update', $group)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            
            if($this->isDemoMode()) {
                return $this->notAuthorized();
            }   
            
            $this->validate($request, \Acelle\Model\CustomerGroup::rules());

            $rules = [];
            if (isset($request->sending_servers)) {
                foreach ($request->sending_servers as $key => $param) {
                    if ($param['check']) {
                        $rules['sending_servers.'.$key.'.fitness'] = 'required';
                    }
                }
            }
            $this->validate($request, $rules);

            $group->fill($request->all());
            $group->options = json_encode($request->options);
            $group->save();

            // For sending servers
            if (isset($request->sending_servers)) {
                $group->updateSendingServers($request->sending_servers);
            }

            $request->session()->flash('alert-success', trans('messages.customer_group.updated'));

            return redirect()->action('Admin\CustomerGroupController@index');
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
            $item = \Acelle\Model\CustomerGroup::find($row[0]);

            // authorize
            if (\Gate::denies('sort', $item)) {
                return $this->notAuthorized();
            }

            $item->custom_order = $row[1];
            $item->save();
        }

        echo trans('messages.customer_groups.custom_order.updated');
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
        $lists = \Acelle\Model\CustomerGroup::whereIn('id', explode(',', $request->ids));

        foreach ($lists->get() as $item) {
            // authorize
            if (\Gate::denies('delete', $item)) {
                return;
            }
        }

        foreach ($lists->get() as $item) {
            $item->delete();
        }

        // Redirect to my lists page
        echo trans('messages.customer_groups.deleted');
    }
}
