<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

use Acelle\Http\Requests;

class SystemJobController extends Controller
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
     * Display a listing of subscriber import job.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        $system_jobs = \Acelle\Model\SystemJob::search($request)->paginate($request->per_page);

        return view('system_jobs._' . $request->type, [
            'system_jobs' => $system_jobs,
        ]);
    }

    /**
     * Download log.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadLog(Request $request)
    {
        $job = \Acelle\Model\ImportSubscribersSystemJob::find($request->id);

        // authorize
        // @todo: redundant find() here
        if (\Gate::denies('downloadImportLog', \Acelle\Model\SystemJob::find($request->id))) {
            return $this->notAuthorized();
        }

        return response()->download($job->getLog());
    }

    /**
     * Delete job.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $jobs = \Acelle\Model\SystemJob::whereIn('id', explode(',', $request->uids));

        foreach ($jobs->get() as $job) {
            // authorize
            if (\Gate::allows('delete', $job)) {
                $job->delete();
            }
        }

        echo trans('messages.system_jobs.deleted');
    }

    /**
     * Download log.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadCsv(Request $request)
    {
        $job = \Acelle\Model\SystemJob::find($request->id);

        // authorize
        if (\Gate::denies('downloadExportCsv', $job)) {
            return $this->notAuthorized();
        }

        return response()->download(storage_path('job/'.$job->id.'/data.csv'));
    }

    /**
     * Cancel stukced running import job.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        $jobs = \Acelle\Model\SystemJob::whereIn('id', explode(',', $request->uids));

        foreach ($jobs->get() as $job) {
            // authorize
            if (\Gate::allows('cancel', $job)) {
                $job->setCancelled();
            } else {
                echo "dddd";
                return;
            }
        }

        echo trans('messages.system_jobs.cancelled');
    }
}
