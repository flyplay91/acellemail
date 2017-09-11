<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

class LayoutController extends Controller
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
        if ($request->user()->admin->getPermission('layout_read') == 'no') {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\Layout::getAll();

        return view('admin.layouts.index', [
            'items' => $items,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        if ($request->user()->admin->getPermission('layout_read') == 'no') {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\Layout::search($request)->paginate($request->per_page);

        return view('admin.layouts._list', [
            'items' => $items,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $layout = \Acelle\Model\Layout::findByUid($id);

        // authorize
        if (\Gate::denies('update', $layout)) {
            return $this->notAuthorized();
        }

        // Get old post values
        if (null !== $request->old()) {
            $layout->fill($request->old());
        }

        return view('admin.layouts.edit', [
            'layout' => $layout,
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
        $layout = \Acelle\Model\Layout::findByUid($id);
        
        if($this->isDemoMode()) {
            return $this->notAuthorized();
        }

        // authorize
        if (\Gate::denies('update', $layout)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            $rules = array(
                'content' => 'required',
                'subject' => 'required',
            );

            $this->validate($request, $rules);

            // Save template
            $layout->fill($request->all());
            $layout->save();

            // Redirect to my lists page
            $request->session()->flash('alert-success', trans('messages.layout.updated'));

            return redirect()->action('Admin\LayoutController@edit', $layout->uid);
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
}
