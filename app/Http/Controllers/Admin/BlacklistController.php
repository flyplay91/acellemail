<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Acelle\Http\Controllers\Controller;

class BlacklistController extends Controller
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
     * Search items.
     */
    public function search($request)
    {
        $request->merge(array("admin_id" => $request->user()->admin->id));
        $blacklists = \Acelle\Model\Blacklist::search($request);

        return $blacklists;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin = $request->user()->admin;

        if (!$admin->can('read', new \Acelle\Model\Blacklist())) {
            return $this->notAuthorized();
        }

        $blacklists = $this->search($request);

        # Get current job
        $system_job = $admin->getLastActiveImportBlacklistJob();

        return view('admin.blacklists.index', [
            'blacklists' => $blacklists,
            'system_job' => $system_job,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        if (!$request->user()->admin->can('read', new \Acelle\Model\Blacklist())) {
            return $this->notAuthorized();
        }

        $blacklists = $this->search($request)->paginate($request->per_page);

        return view('admin.blacklists._list', [
            'blacklists' => $blacklists,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        if ($request->select_tool == 'all_items') {
            $blacklists = $this->search($request);
        } else {
            $blacklists = \Acelle\Model\Blacklist::whereIn('id', explode(',', $request->uids));
        }

        foreach ($blacklists->get() as $blacklist) {
            // authorize
            if ($request->user()->admin->can('delete', $blacklist)) {
                // Log
                $blacklist->delete();
            }
        }

        // Redirect to my lists page
        echo trans('messages.blacklists.deleted');
    }

    /**
     * Start import process.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $admin = $request->user()->admin;

        if ($request->isMethod('post')) {
            // authorize
            if (!$admin->can('import', new \Acelle\Model\Blacklist())) {
                return $this->notAuthorized();
            }

            if ($request->hasFile('file')) {
                // Start system job
                $job = new \Acelle\Jobs\ImportBlacklistJob($request->file('file')->path(), NULL, $admin);
                $this->dispatch($job);
            } else {
                // @note: use try/catch instead
                echo "max_file_upload";
            }
        }

        // Get current job
        $system_job = $admin->getLastActiveImportBlacklistJob();

        return view('admin.blacklists.import', [
            'system_job' => $system_job
        ]);
    }

    /**
     * Check import proccessing.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function importProcess(Request $request)
    {
        $admin = $request->user()->admin;

        $system_job = \Acelle\Model\SystemJob::find($request->system_job_id);

        // authorize
        if (!$admin->can('read', new \Acelle\Model\Blacklist())) {
            return $this->notAuthorized();
        }

        return view('admin.blacklists.import_process', [
            'system_job' => $system_job
        ]);
    }

    /**
     * Cancel importing job.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        $admin = $request->user()->admin;
        $system_job = $admin->getLastActiveImportBlacklistJob();

        // authorize
        if (!$admin->can('importCancel', new \Acelle\Model\Blacklist())) {
            return $this->notAuthorized();
        }

        $system_job->setCancelled();

        $request->session()->flash('alert-success', trans('messages.blacklist.import.cancelled'));
        return redirect()->action('Admin\BlacklistController@index');
    }
}
