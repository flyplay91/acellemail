<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

use Acelle\Http\Requests;

class AutomationController extends Controller
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
        $user = $request->user();
        $automations = $user->customer->automations;

        return view('automations.index', [
            'automations' => $automations,
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        $automations = \Acelle\Model\Automation::search($request)->paginate($request->per_page);

        return view('automations._list', [
            'automations' => $automations,
        ]);
    }
    
    /**
     * Creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $automation = new \Acelle\Model\Automation();
        
        // authorize
        if (\Gate::denies('create', $automation)) {
            return $this->noMoreItem();
        }
        
        if (!empty($request->old())) {
            $rules = $automation->recipientsRules($request->old());
            $automation->fillRecipients($request->old());
        }

        return view('automations.create', [
            'automation' => $automation,
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
        $current_user = $request->user();
        $automation = new \Acelle\Model\Automation();
        
        // authorize
        if (\Gate::denies('create', $automation)) {
            return $this->noMoreItem();
        }

        // Get rules and data
        $rules = $automation->recipientsRules($request->all());
        $automation->fillRecipients($request->all());        

        if ($request->isMethod('post')) {
            // Check validation
            $this->validate($request, $rules);
            
            $automation->fill($request->all());            
            $automation->customer_id = $current_user->customer->id;
            $automation->status = \Acelle\Model\Automation::STATUS_DRAFT;
            $automation->save();
            $automation->saveRecipients($request->all());
            
            return redirect()->action('AutomationController@trigger', $automation->uid);
        }

        //// Create automation
        //if ($request->isMethod('post')) {
        //    // Check validation
        //    $this->validate($request, $automation->rules()["init"]);
        //    
        //    // create automation
        //    $automation->fillAttributes($request->all());
        //    $automation->user_id = $current_user->id;
        //    $automation->status = \Acelle\Model\Automation::STATUS_DRAFT;
        //    $automation->save();
        //    
        //    // Log
        //    $automation->log('created', $request->user());
        //
        //    // Redirect to next step
        //    return redirect()->action('AutomationController@trigger', $automation->uid);
        //}
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }
        
        if (!empty($request->old())) {
            $rules = $automation->recipientsRules($request->old());
            $automation->fillRecipients($request->old());
        }

        return view('automations.edit', [
            'automation' => $automation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }
        
        // Get rules and data
        $rules = $automation->recipientsRules($request->all());
        $automation->fillRecipients($request->all());        

        // Update automation
        if ($request->isMethod('patch')) {
            $this->validate($request, $rules);
            
            $automation->fill($request->all());            
            $automation->save();
            $automation->saveRecipients($request->all());
            
            return redirect()->action('AutomationController@trigger', $automation->uid);
        }
    }
    
    /**
     * Trigger for automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function trigger(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }
        
        // Find automation first auto event
        $first_event = $automation->getInitEvent();
        
        // Fill old post data
        if(!empty($request->old())) {
            $first_event->fillAttributes($request->old());
        }
        
        // Save trigger
        if ($request->isMethod('post')) {
            // fill data
            $first_event->fillAttributes($request->all());
            
            // validation
            $this->validate($request, $first_event->rules($request));
            
            // Save to database
            $first_event->save();
            
            // Log
            $automation->log('triggered', $request->user()->customer);
            
            // Redirect to next step
            return redirect()->action('AutomationController@workflow', $automation->uid);
        }
        
        return view('automations.trigger', [
            'automation' => $automation,
            'first_event' => $first_event,
            'old' => $request->old(),
        ]);
    }
    
    /**
     * Trigger for automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function criteriaForm(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }
        
        return view('automations._criteria_form', [
            'automation' => $automation,
        ]);
    }
    
    /**
     * Workflows for automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function workflow(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        $first_event = $automation->getInitEvent();
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }

        return view('automations.workflow', [
            'automation' => $automation,
            'old' => $request->old(),
            'first_event' => $first_event
        ]);
    }
    
    /**
     * Next auto event form.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function nextEventForm(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }
        
        $auto_event = $automation->createAutoEvent();
        
        return view('auto_events.show', [
            'automation' => $automation,
            'auto_event' => $auto_event
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
        if (isSiteDemo()) {
            echo trans('messages.operation_not_allowed_in_demo');
            return;
        }
        
        $automations = \Acelle\Model\Automation::whereIn('uid', explode(',', $request->uids));

        foreach ($automations->get() as $automation) {
            // authorize
            if (\Gate::allows('delete', $automation)) {
                $automation->doDelete();
                
                // Log
                $automation->log('deleted', $request->user()->customer);
            }
        }

        // Redirect to my lists page
        echo trans('messages.automations.deleted');
    }
    
    /**
     * Cofirm.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        $first_event = $automation->getInitEvent();
        
        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // authorize
            if (\Gate::denies('confirm', $automation)) {
                return $this->notAuthorized();
            } else {
                $automation->setActive();
                
                // Log
                $automation->log('started', $request->user()->customer);
                
                return redirect()->action('AutomationController@index');
            }
        }

        return view('automations.confirm', [
            'automation' => $automation,
            'first_event' => $first_event
        ]);
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
            $item = \Acelle\Model\Automation::findByUid($row[0]);

            // authorize
            if (\Gate::denies('sort', $item)) {
                return $this->notAuthorized();
            }

            $item->custom_order = $row[1];
            $item->save();
        }

        echo trans('messages.automations.custom_order.updated');
    }
    
    /**
     * Workflow overview automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function overviewWorkflow(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        $first_event = $automation->getInitEvent();
        
        // authorize
        if (\Gate::denies('overview', $automation)) {
            return $this->notAuthorized();
        }

        return view('automations.overview.workflow', [
            'automation' => $automation,
            'first_event' => $first_event
        ]);
    }
    
    /**
     * Campaigns overview automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function overviewCampaigns(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        $campaigns = $automation->getCampaigns();
        
        // authorize
        if (\Gate::denies('overview', $automation)) {
            return $this->notAuthorized();
        }

        return view('automations.overview.campaigns', [
            'automation' => $automation,
            'campaigns' => $campaigns,
        ]);
    }
    
    /**
     * Campaigns listing overview automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function overviewCampaignsList(Request $request, $uid)
    {
        $automation = \Acelle\Model\Automation::findByUid($uid);
        $campaigns = $automation->getCampaigns($request)->paginate($request->per_page);
        
        // authorize
        if (\Gate::denies('overview', $automation)) {
            return $this->notAuthorized();
        }

        return view('automations.overview._campaigns_list', [
            'automation' => $automation,
            'campaigns' => $campaigns,
        ]);
    }
    
    /**
     * Enable automation.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        // validate and save posted data
        if ($request->isMethod('patch')) {
            $automations = \Acelle\Model\Automation::whereIn('uid', explode(',', $request->uids));

            foreach ($automations->get() as $automation) {
                // authorize
                if (\Gate::allows('enable', $automation)) {
                    $automation->enable();
                }
            }
            
            if($request->ajax()){
                echo trans('messages.automations.enabled');
            } else {
                $request->session()->flash('alert-success', trans('messages.automation.enabled'));
                $url = action('AutomationController@workflow', $automation->uid) . "#add-event-button";
                return redirect()->away($url);
            }            
        }
    }
    
    /**
     * Disable event.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        // validate and save posted data
        if ($request->isMethod('patch')) {
            $automations = \Acelle\Model\Automation::whereIn('uid', explode(',', $request->uids));

            foreach ($automations->get() as $automation) {
                // authorize
                if (\Gate::allows('disable', $automation)) {
                    $automation->disable();
                }
            }
            
            if($request->ajax()){
                echo trans('messages.automations.disabled');
            } else {
                $request->session()->flash('alert-success', trans('messages.automation.disabled'));
                $url = action('AutomationController@workflow', $automation->uid) . "#add-event-button";
                return redirect()->away($url);
            }            
        }
    }
    
    /**
     * List segment form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function listSegmentForm(Request $request)
    {
        // Get current user
        $automation = new \Acelle\Model\Automation();

        // authorize
        if (\Gate::denies('update', $automation)) {
            return $this->notAuthorized();
        }

        return view('automations._list_segment_form', [
            'automation' => $automation,
            'lists_segment_group' => [
                'list' => null,
                'is_default' => false
            ]
        ]);
    }
}
