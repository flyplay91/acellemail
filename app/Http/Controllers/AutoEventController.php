<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

use Acelle\Http\Requests;

class AutoEventController extends Controller
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
     * List campaigns of auto event.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function campaigns(Request $request, $uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaigns = $auto_event->campaigns;
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        return view('auto_events.campaigns', [
            'auto_event' => $auto_event,
            'campaigns' => $campaigns
        ]);
    }
    
    /**
     * Add campaign to auto event.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function addCampaign(Request $request, $uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        $campaign = $auto_event->addCampaign();
        
        return redirect()->action('AutoEventController@campaignSetup', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]);
    }
    
    /**
     * Delete campaign.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteCampaign(Request $request, $uid)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $campaign->autoEvent())) {
            return $this->notAuthorized();
        }
        
        $campaign->delete();
    }
    
    /**
     * Delete auto event.
     *
     * @param \Illuminate\Http\Request $request
     * @param String                   $uid
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        $auto_event->delete();
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            // fill data
            $auto_event->fillAttributes($request->all());
            $auto_event->save();
            
            echo trans('messages.auto_event.updated');
        }
    }
    
    /**
     * Setting up auto campaign.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function campaignSetup(Request $request, $uid, $campaign_uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        // get infor from mail list
        $campaign->getInfoFromMailList($auto_event->automation->defaultMailList);
        
        // Get old post values
        if (null !== $request->old()) {
            $campaign->fill($request->old());
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            // Check validation
            $this->validate($request, $campaign->automatedCampaignRules());

            // Save campaign
            $campaign->fill($request->all());
            $campaign->type = $request->type;
            $campaign->save();
            
            return redirect()->action('AutoEventController@template', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]);
        }
        
        return view('auto_events.campaigns.setup', [
            'auto_event' => $auto_event,
            'campaign' => $campaign
        ]);
    }
    
    /**
     * Template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function template(Request $request, $uid, $campaign_uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        $user = $request->user();
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        // Get old post values
        if (null !== $request->old()) {
            $campaign->fill($request->old());
        }
        
        $rules = [];
        // validate and save posted data
        if ($request->isMethod('post')) {
            // Check validation
            $this->validate($request, $rules);

            // Save campaign
            $campaign->fill($request->all());
            
            // convert html to plain text if plain text is empty
            if (trim($request->plain) == '') {
                $campaign->plain = preg_replace('/\s+/',' ',preg_replace('/\r\n/',' ',strip_tags($request->html)));
            }
            
            $campaign->save();
            
            if(isset($request->template_source) || !$campaign->unsubscribe_url_valid()) {
                return redirect()->action('AutoEventController@templatePreview', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]);
            } else {
                $request->session()->flash('alert-success', trans('messages.email.updated'));
                $url = action('AutomationController@workflow', $auto_event->automation->uid) . "#event-" . $auto_event->uid;
                return redirect()->away($url);
            }
        }
        
        // redirect page
        if(!empty($campaign->html) || $campaign->type == 'plain-text') {
            return redirect()->action('AutoEventController@templatePreview', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]);
        } else {
            return redirect()->action('AutoEventController@templateSelect', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]);
        }

        return view('auto_events.campaigns.template', [
            'campaign' => $campaign,
            'rules' => $rules,
            'auto_event' => $auto_event
        ]);
    }
    
    /**
     * Select template type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateSelect(Request $request, $uid, $campaign_uid)
    {
        $user = $request->user();
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }

        return view('auto_events.campaigns.template_select', [
            'campaign' => $campaign,
            'auto_event' => $auto_event
        ]);
    }
    
    /**
     * Buiding email template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateBuild(Request $request, $uid, $campaign_uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        $elements = [];
        if(isset($request->style)) {
            $elements = \Acelle\Model\Template::templateStyles()[$request->style];
        }

        return view('auto_events.campaigns.template_build', [
            'campaign' => $campaign,
            'elements' => $elements,
            'auto_event' => $auto_event
        ]);
    }
    
    /**
     * Template preview.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templatePreview(Request $request, $uid, $campaign_uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }

        $rules = [];
        
        return view('auto_events.campaigns.template_preview', [
            'campaign' => $campaign,
            'rules' => $rules,
            'auto_event' => $auto_event
        ]);
    }
    
    /**
     * Template preview iframe.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateIframe(Request $request, $uid, $campaign_uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        return view('campaigns.preview', [
            'campaign' => $campaign,
        ]);
    }
    
    /**
     * Re-Buiding email template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateRebuild(Request $request, $uid, $campaign_uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }

        return view('auto_events.campaigns.template_rebuild', [
            'campaign' => $campaign,
            'list' => $campaign->mailList,
            'auto_event' => $auto_event
        ]);
    }
    
    /**
     * Choose an existed template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateChoose(Request $request, $uid, $campaign_uid)
    {
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        $campaign = \Acelle\Model\Campaign::findByUid($campaign_uid);
        $template = \Acelle\Model\Template::findByUid($request->template_uid);
        
        // authorize
        if (\Gate::denies('update', $auto_event)) {
            return $this->notAuthorized();
        }
        
        $campaign->html = $template->content;
        $campaign->template_source = $template->source;
        $campaign->save();

        return redirect()->action('AutoEventController@templatePreview', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]);
    }
    
    /**
     * Enable event.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request, $uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('enable', $auto_event)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            // fill data
            $auto_event->enable();
            
            $request->session()->flash('alert-success', trans('messages.auto_event.updated'));
            $url = action('AutomationController@workflow', $auto_event->automation->uid) . "#event-" . $auto_event->uid;
            return redirect()->away($url);
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
    public function disable(Request $request, $uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('disable', $auto_event)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            // fill data
            $auto_event->disable();
            
            $request->session()->flash('alert-success', trans('messages.auto_event.updated'));
            $url = action('AutomationController@workflow', $auto_event->automation->uid) . "#event-" . $auto_event->uid;
            return redirect()->away($url);
        }
    }
    
    /**
     * Move event up.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function moveUp(Request $request, $uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('moveUp', $auto_event)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            // fill data
            $auto_event->moveUp();
            
            $request->session()->flash('alert-success', trans('messages.auto_event.moved'));
            $url = action('AutomationController@workflow', $auto_event->automation->uid) . "#event-" . $auto_event->uid;
            return redirect()->away($url);
        }
    }
    
    /**
     * Move event up.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function moveDown(Request $request, $uid)
    {
        // Generate info
        $auto_event = \Acelle\Model\AutoEvent::findByUid($uid);
        
        // authorize
        if (\Gate::denies('moveDown', $auto_event)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('patch')) {
            // fill data
            $auto_event->moveDown();
            
            $request->session()->flash('alert-success', trans('messages.auto_event.moved'));
            $url = action('AutomationController@workflow', $auto_event->automation->uid) . "#event-" . $auto_event->uid;
            return redirect()->away($url);
        }
    }
    
}
