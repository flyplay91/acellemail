<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Acelle\Library\Log as MailLog;
use Illuminate\Support\Facades\Log as LaravelLog;

class CampaignController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', ['except' => [
            'open',
            'click',
            'unsubscribe',
            'webView'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customer = $request->user()->customer;
        $campaigns = $customer->getNormalCampaigns();

        return view('campaigns.index', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        $campaigns = \Acelle\Model\Campaign::search($request)->paginate($request->per_page);

        return view('campaigns._list', [
            'campaigns' => $campaigns,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $customer = $request->user()->customer;
        $campaign = new \Acelle\Model\Campaign([
            'track_open' => true,
            'track_click' => true,
            'sign_dkim' => true,
        ]);

        // authorize
        if (\Gate::denies('create', $campaign)) {
            return $this->noMoreItem();
        }

        $campaign->name = trans('messages.untitled');
        $campaign->customer_id = $customer->id;
        $campaign->status = \Acelle\Model\Campaign::STATUS_NEW;
        $campaign->type = $request->type;
        $campaign->save();

        return redirect()->action('CampaignController@recipients', ['uid' => $campaign->uid]);
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
        $campaign = \Acelle\Model\Campaign::findByUid($id);

        // Trigger the CampaignUpdate event to update the campaign cache information
        // The second parameter of the constructor function is false, meanining immediate update
        try {
            event(new \Acelle\Events\CampaignUpdated($campaign));
        } catch (\Exception $ex) {
            // in case TrackingLog record does not exist yet (open before logged!)
        }

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        if ($campaign->status == 'new') {
            return redirect()->action('CampaignController@edit', ['uid' => $campaign->uid]);
        } else {
            return redirect()->action('CampaignController@overview', ['uid' => $campaign->uid]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($id);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        // Check step and redirect
        if ($campaign->step() == 0) {
            return redirect()->action('CampaignController@recipients', ['uid' => $campaign->uid]);
        } elseif ($campaign->step() == 1) {
            return redirect()->action('CampaignController@setup', ['uid' => $campaign->uid]);
        } elseif ($campaign->step() == 2) {
            return redirect()->action('CampaignController@template', ['uid' => $campaign->uid]);
        } elseif ($campaign->step() == 3) {
            return redirect()->action('CampaignController@schedule', ['uid' => $campaign->uid]);
        } elseif ($campaign->step() >= 4) {
            return redirect()->action('CampaignController@confirm', ['uid' => $campaign->uid]);
        }
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
            $item = \Acelle\Model\Campaign::findByUid($row[0]);

            // authorize
            if (\Gate::denies('sort', $item)) {
                return $this->notAuthorized();
            }

            $item->custom_order = $row[1];
            $item->save();
        }

        echo trans('messages.campaigns.custom_order.updated');
    }

    /**
     * Recipients.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function recipients(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        // Get rules and data
        $rules = $campaign->recipientsRules($request->all());
        $campaign->fillRecipients($request->all());

        if (!empty($request->old())) {
            $rules = $campaign->recipientsRules($request->old());
            $campaign->fillRecipients($request->old());
        }

        if ($request->isMethod('post')) {
            // Check validation
            $this->validate($request, $rules);

            $campaign->saveRecipients($request->all());

            // Trigger the CampaignUpdate event to update the campaign cache information
            // The second parameter of the constructor function is false, meanining immediate update
            event(new \Acelle\Events\CampaignUpdated($campaign));

            // redirect to the next step
            return redirect()->action('CampaignController@setup', ['uid' => $campaign->uid]);
        }

        //// validate and save posted data
        //if ($request->isMethod('post')) {
        //
        //    // Check validation
        //    $this->validate($request, \Acelle\Model\Campaign::$rules);
        //
        //    // Save campaign
        //    $campaign->mail_list_id = \Acelle\Model\MailList::findByUid($request->mail_list_uid)->id;
        //    if ($request->segment_uid) {
        //        $campaign->segment_id = \Acelle\Model\Segment::findByUid($request->segment_uid)->id;
        //    } else {
        //        $campaign->segment_id = null;
        //    }
        //    $campaign->save();
        //
        //    return redirect()->action('CampaignController@setup', ['uid' => $campaign->uid]);
        //}

        return view('campaigns.recipients', [
            'campaign' => $campaign,
            'rules' => $rules
        ]);
    }

    /**
     * Campaign setup.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function setup(Request $request)
    {
        $customer = $request->user()->customer;
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        $campaign->from_name = !empty($campaign->from_name) ? $campaign->from_name : $campaign->defaultMailList->from_name;
        $campaign->from_email = !empty($campaign->from_email) ? $campaign->from_email : $campaign->defaultMailList->from_email;
        $campaign->subject = !empty($campaign->subject) ? $campaign->subject : $campaign->defaultMailList->default_subject;

        $rules = array(
            'name' => 'required',
            'subject' => 'required',
            'from_email' => 'required|email',
            'from_name' => 'required',
            'reply_to' => 'required|email',
        );

        // Get old post values
        if (null !== $request->old()) {
            $campaign->fill($request->old());
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Check validation
            $this->validate($request, $rules);

            // Save campaign
            $campaign->fill($request->all());
            $campaign->save();

            // Log
            $campaign->log('created', $customer);

            return redirect()->action('CampaignController@template', ['uid' => $campaign->uid]);
        }

        return view('campaigns.setup', [
            'campaign' => $campaign,
            'rules' => $rules,
        ]);
    }

    /**
     * Template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function template(Request $request)
    {
        $customer = $request->user()->customer;
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        // Required tags
        $validate = 'required';

        // @todo hard-coded here
        $requiredTags = array(
            array('name' => '{UNSUBSCRIBE_URL}', 'required' => true),
        );

        foreach ($requiredTags as $tag) {
            if ($tag['required']) {
                $validate .= '|substring:"'.$tag['name'].'"';
            }
        }

        $rules = [];

        // if campaign type is not plain text
        if ($campaign->type != 'plain-text' && $customer->getOption('unsubscribe_url_required') == 'yes') {
            $rules['html'] = $validate;
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

            if(isset($request->template_source)) {
                return redirect()->action('CampaignController@templatePreview', ['uid' => $campaign->uid]);
            } else {
                return redirect()->action('CampaignController@schedule', ['uid' => $campaign->uid]);
            }
        }

        // redirect page
        if(!empty($campaign->html) || $campaign->type == 'plain-text') {
            return redirect()->action('CampaignController@templatePreview', ['uid' => $campaign->uid]);
        } else {
            return redirect()->action('CampaignController@templateSelect', ['uid' => $campaign->uid]);
        }

        return view('campaigns.template', [
            'campaign' => $campaign,
            'rules' => $rules,
        ]);
    }

    /**
     * Select template type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateSelect(Request $request)
    {
        $user = $request->user();
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.template_select', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Choose an existed template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateChoose(Request $request)
    {
        $user = $request->user();
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);
        $template = \Acelle\Model\Template::findByUid($request->template_uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        $campaign->html = $template->content;
        $campaign->template_source = $template->source;
        // $campaign->plain = preg_replace('/\s+/',' ',preg_replace('/\r\n/',' ',strip_tags($campaign->html)));
        $campaign->save();

        if(!$campaign->is_auto) {
            return redirect()->action('CampaignController@templatePreview', ['uid' => $campaign->uid]);
        } else {
            return redirect()->action('AutoEventController@templatePreview', ['uid' => $campaign->autoEvent()->uid, 'campaign_uid' => $campaign->uid]);
        }
    }

    /**
     * Template preview.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templatePreview(Request $request)
    {
        $user = $request->user();
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        $rules = [];

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.template_preview', [
            'campaign' => $campaign,
            'rules' => $rules
        ]);
    }

    /**
     * Template preview iframe.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateIframe(Request $request)
    {
        $user = $request->user();
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.preview', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Schedule.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function schedule(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // check step
        if($campaign->step() < 3) {
            return redirect()->action('CampaignController@template', ['uid' => $campaign->uid]);
        }

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        $delivery_date = isset($campaign->run_at) && $campaign->run_at != '0000-00-00 00:00:00' ? \Acelle\Library\Tool::dateTime($campaign->run_at)->format('Y-m-d') : \Acelle\Library\Tool::dateTime(\Carbon\Carbon::now())->format('Y-m-d');
        $delivery_time = isset($campaign->run_at) && $campaign->run_at != '0000-00-00 00:00:00' ? \Acelle\Library\Tool::dateTime($campaign->run_at)->format('H:i') : \Acelle\Library\Tool::dateTime(\Carbon\Carbon::now())->format('H:i');

        $rules = array(
            'delivery_date' => 'required',
            'delivery_time' => 'required',
        );

        // Get old post values
        if (null !== $request->old()) {
            $campaign->fill($request->old());
        }

        // validate and save posted data
        if ($request->isMethod('post')) {
            // Check validation
            // $this->validate($request, $rules);

            //// Save campaign
            $time = \Acelle\Library\Tool::systemTimeFromString($request->delivery_date . ' ' . $request->delivery_time);
            $campaign->run_at = $time;
            $campaign->save();

            return redirect()->action('CampaignController@confirm', ['uid' => $campaign->uid]);
        }

        return view('campaigns.schedule', [
            'campaign' => $campaign,
            'rules' => $rules,
            'delivery_date' => $delivery_date,
            'delivery_time' => $delivery_time,
        ]);
    }

    /**
     * Cofirm.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $customer = $request->user()->customer;
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // check step
        if($campaign->step() < 4) {
            return redirect()->action('CampaignController@schedule', ['uid' => $campaign->uid]);
        }

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        // validate and save posted data
        if ($request->isMethod('post') && $campaign->step() >= 5) {
            // Save campaign
            // @todo: check campaign status before requeuing. Otherwise, several jobs shall be created and campaign will get sent several times
            $campaign->requeue();

            // Log
            $campaign->log('started', $customer);

            return redirect()->action('CampaignController@index');
        }

        return view('campaigns.confirm', [
            'campaign' => $campaign,
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
        $customer = $request->user()->customer;

        if (isSiteDemo()) {
            echo trans('messages.operation_not_allowed_in_demo');
            return;
        }

        $items = \Acelle\Model\Campaign::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::allows('delete', $item)) {
                $item->delete();
                // Log
                $item->log('deleted', $customer);
            }
        }

        // Redirect to my lists page
        echo trans('messages.campaigns.deleted');
    }

    /**
     * Campaign overview.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function overview(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // Trigger the CampaignUpdate event to update the campaign cache information
        // The second parameter of the constructor function is false, meanining immediate update
        try {
            event(new \Acelle\Events\CampaignUpdated($campaign));
        } catch (\Exception $ex) {
            // in case TrackingLog record does not exist yet (open before logged!)
        }

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.overview', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Campaign links.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function links(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.links', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * 24-hour chart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chart24h(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $result = [
            'columns' => [],
            'data' => [],
            'bar_names' => [trans('messages.opened'), trans('messages.clicked')],
        ];

        $hours = [];

        // columns
        for ($i = 23; $i >= 0; --$i) {
            $result['columns'][] = \Acelle\Library\Tool::dateTime(\Carbon\Carbon::now())->subHours($i)->format('h:A');
            $hours[] = \Acelle\Library\Tool::dateTime(\Carbon\Carbon::now())->subHours($i)->format('H');
        }

        // 24h collection
        $openData24h = $campaign->openUniqHours(\Acelle\Library\Tool::dateTime(\Carbon\Carbon::now())->subHours(24), \Carbon\Carbon::now());
        $clickData24h = $campaign->clickHours(\Acelle\Library\Tool::dateTime(\Carbon\Carbon::now())->subHours(24), \Carbon\Carbon::now());

        // datas
        foreach ($result['bar_names'] as $key => $bar) {
            $data = [];
            if ($key == 0) {
                foreach ($hours as $ohour) {
                    $num = isset($openData24h[$ohour]) ? count($openData24h[$ohour]) : 0;
                    $data[] = $num;
                }
            } else {
                foreach ($hours as $chour) {
                    $num = isset($clickData24h[$chour]) ? count($clickData24h[$chour]) : 0;
                    $data[] = $num;
                }
            }

            $result['data'][] = [
                'name' => $bar,
                'type' => 'line',
                'smooth' => true,
                'data' => $data,
                'itemStyle' => [
                    'normal' => [
                        'areaStyle' => [
                            'type' => 'default',
                        ],
                    ],
                ],
            ];
        }

        return json_encode($result);
    }

    /**
     * Chart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chart(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $result = [
            'columns' => [],
            'data' => [],
            'bar_names' => [
                trans('messages.recipients'),
                trans('messages.delivered'),
                trans('messages.failed'),
                trans('messages.Open'),
                trans('messages.Click'),
                trans('messages.Bounce'),
                trans('messages.report'),
                trans('messages.unsubscribe'),
            ],
        ];

        // columns
        $result['columns'][] = trans("messages.count");

        // datas
        $result['data'][] = [
            'name' => trans('messages.unsubscribe'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->unsubscribeCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#D81B60'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.report'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->feedbackCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#00897B'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.Bounce'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->bounceCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#6D4C41'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.Click'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->clickedEmailsCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#039BE5'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.Open'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->openUniqCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#546E7A'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.failed'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->failedCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#E53935'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.delivered'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->deliveredCount()],
            'itemStyle' => [
                'normal' => [
                    'color' => '#7CB342'
                ]
            ],
        ];

        $result['data'][] = [
            'name' => trans('messages.recipients'),
            'type' => 'bar',
            'smooth' => true,
            'data' => [$campaign->readCache('SubscriberCount', 0)],
            'itemStyle' => [
                'normal' => [
                    'color' => '#555'
                ]
            ],
        ];


        $result['horizontal'] = 1;

        return json_encode($result);
    }

    /**
     * Chart Country.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chartCountry(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $result = [
            'title' => '',
            'columns' => [],
            'data' => [],
            'bar_names' => [],
        ];

        // create data
        $datas = [];
        $total = $campaign->openCount();
        $count = 0;
        foreach ($campaign->topCountries()->get() as $location) {
            $country_name = (!empty($location->country_name) ? $location->country_name : trans('messages.unknown'));
            $result['bar_names'][] = $country_name;

            $datas[] = ['value' => $location->aggregate, 'name' => $country_name];
            $count += $location->aggregate;
        }

        // others
        if($total > $count) {
            $result['bar_names'][] = trans('messages.others');
            $datas[] = ['value' => $total - $count, 'name' => trans('messages.others')];
        }

        // datas
        $result['data'][] = [
            'name' => trans('messages.country'),
            'type' => 'pie',
            'radius' => '70%',
            'center' => ['50%', '57.5%'],
            'data' => $datas
        ];

        $result['pie'] = 1;

        return json_encode($result);
    }

    /**
     * Chart Country by clicks.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function chartClickCountry(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $result = [
            'title' => '',
            'columns' => [],
            'data' => [],
            'bar_names' => [],
        ];

        // create data
        $datas = [];
        $total = $campaign->clickCount();
        $count = 0;
        foreach ($campaign->topClickCountries()->get() as $location) {
            $result['bar_names'][] = $location->country_name;

            $datas[] = ['value' => $location->aggregate, 'name' => $location->country_name];
            $count += $location->aggregate;
        }

        // others
        if($total > $count) {
            $result['bar_names'][] = trans('messages.others');
            $datas[] = ['value' => $total - $count, 'name' => trans('messages.others')];
        }

        // datas
        $result['data'][] = [
            'name' => trans('messages.country'),
            'type' => 'pie',
            'radius' => '70%',
            'center' => ['50%', '57.5%'],
            'data' => $datas
        ];

        $result['pie'] = 1;

        return json_encode($result);
    }

    /**
     * 24-hour quickView.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function quickView(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns._quick_view', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Select2 campaign.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function select2(Request $request)
    {
        $data = ['items' => [], 'more' => true];

        $data['items'][] = ['id' => 0, 'text' => trans('messages.all')];
        foreach (\Acelle\Model\Campaign::getAll()->get() as $campaign) {
            $data['items'][] = ['id' => $campaign->uid, 'text' => $campaign->name];
        }

        echo json_encode($data);
    }

    /**
     * Tracking when open.
     */
    public function open(Request $request)
    {
        $log = new \Acelle\Model\OpenLog();
        $log->message_id = \Acelle\Library\StringHelper::base64UrlDecode($request->message_id);
        $location = \Acelle\Model\IpLocation::add($_SERVER['REMOTE_ADDR']);
        $log->ip_address = $location->ip_address;
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];

        // Trigger the CampaignUpdate event to update the campaign cache information
        // The second parameter of the constructor function is false, meanining immediate update
        try {
            $log->save();

            event(new \Acelle\Events\MailListUpdated($log->trackingLog->subscriber->mailList));
        } catch (\Exception $ex) {
            // in case TrackingLog record does not exist yet (open before logged!)
            LaravelLog::warning('Cannot save open tracking log for message: ' . $log->message_id);
        }
        # Just return a blank image
        return response()->file(public_path('images/transparent.gif'));
    }

    /**
     * Tracking when click link.
     */
    public function click(Request $request)
    {
        $decoded_url = \Acelle\Library\StringHelper::base64UrlDecode($request->url);
        try {
            // redirect base64_decode($url);
            $log = new \Acelle\Model\ClickLog();
            $log->message_id = \Acelle\Library\StringHelper::base64UrlDecode($request->message_id);
            $location = \Acelle\Model\IpLocation::add($_SERVER['REMOTE_ADDR']);
            $log->ip_address = $location->ip_address;
            $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $log->url = $decoded_url;

            // Trigger the CampaignUpdate event to update the campaign cache information
            // The second parameter of the constructor function is false, meanining immediate update
            try {
                $log->save();
                event(new \Acelle\Events\MailListUpdated($log->trackingLog->subscriber->mailList));
            } catch (\Exception $ex) {
                // in case TrackingLog record does not exist yet (open before logged!)
                LaravelLog::warning('Cannot save click tracking log for message: ' . $log->message_id);
            }
        } catch (\Exception $e) {
            // Allow click from a test email
        } finally {
            return redirect()->away($decoded_url);
        }
    }

    /**
     * Unsubscribe url.
     */
    public function unsubscribe(Request $request)
    {
        $message_id = \Acelle\Library\StringHelper::base64UrlDecode($request->message_id);
        $tracking_log = \Acelle\Model\TrackingLog::where('message_id', '=', $message_id)->first();

        if (!is_object($tracking_log)) {
            LaravelLog::error("Tracking log not exists");
            return view('somethingWentWrong', ['message' => trans('messages.the_email_no_longer_exists')]);
        }

        $subscriber = $tracking_log->subscriber;

        if ($subscriber->status != 'unsubscribed') {
            // Unsubcribe
            $subscriber->status = 'unsubscribed';
            $subscriber->save();

            // Page content
            $list = $subscriber->mailList;
            $layout = \Acelle\Model\Layout::where('alias', 'unsubscribe_success_page')->first();
            $page = \Acelle\Model\Page::findPage($list, $layout);

            $page->renderContent(null, $subscriber);

            // Unsubscribe log
            $log = new \Acelle\Model\UnsubscribeLog();
            $log->message_id = $message_id;
            $location = \Acelle\Model\IpLocation::add($_SERVER['REMOTE_ADDR']);
            $log->ip_address = $location->ip_address;
            $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $log->save();

            try {
                // Send goodbye email
                if($list->unsubscribe_notification) {
                    // SEND subscription confirmation email
                    $list->sendUnsubscriptionNotificationEmail($subscriber);
                }
            } catch (\Exception $e) {
            }

            return view('pages.default', [
                'list' => $list,
                'page' => $page,
                'subscriber' => $subscriber,
            ]);
        } else {
            return view('notice', ['message' => trans('messages.you_are_already_unsubscribed')]);
        }
    }

    /**
     * Tracking logs.
     */
    public function trackingLog(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = $campaign->trackingLogs();

        return view('campaigns.tracking_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Tracking logs ajax listing.
     */
    public function trackingLogListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\TrackingLog::search($request, $campaign)->paginate($request->per_page);

        return view('admin.tracking_logs._list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Bounce logs.
     */
    public function bounceLog(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = $campaign->bounceLogs();

        return view('campaigns.bounce_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Bounce logs listing.
     */
    public function bounceLogListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\BounceLog::search($request, $campaign)->paginate($request->per_page);

        return view('admin.bounce_logs._list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * FBL logs.
     */
    public function feedbackLog(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = $campaign->openLogs();

        return view('campaigns.feedback_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * FBL logs listing.
     */
    public function feedbackLogListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\FeedbackLog::search($request, $campaign)->paginate($request->per_page);

        return view('admin.feedback_logs._list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Open logs.
     */
    public function openLog(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = $campaign->openLogs();

        return view('campaigns.open_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Open logs listing.
     */
    public function openLogListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\OpenLog::search($request, $campaign)->paginate($request->per_page);

        return view('admin.open_logs._list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Click logs.
     */
    public function clickLog(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = $campaign->clickLogs();

        return view('campaigns.click_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Click logs listing.
     */
    public function clickLogListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\ClickLog::search($request, $campaign)->paginate($request->per_page);

        return view('admin.click_logs._list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Unscubscribe logs.
     */
    public function unsubscribeLog(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = $campaign->unsubscribeLogs();

        return view('campaigns.unsubscribe_log', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Unscubscribe logs listing.
     */
    public function unsubscribeLogListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $items = \Acelle\Model\UnsubscribeLog::search($request, $campaign)->paginate($request->per_page);

        return view('admin.unsubscribe_logs._list', [
            'items' => $items,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Open map.
     */
    public function openMap(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.open_map', [
            'campaign' => $campaign,
        ]);
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
        $lists = \Acelle\Model\Campaign::whereIn('uid', explode(',', $request->uids));

        return view('campaigns.delete_confirm', [
            'lists' => $lists,
        ]);
    }

    /**
     * Pause the specified campaign.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function pause(Request $request)
    {
        $customer = $request->user()->customer;
        $items = \Acelle\Model\Campaign::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            if (\Gate::allows('pause', $item)) {
                $item->status = 'paused';
                $item->save();

                // Log
                $item->log('paused', $customer);
            }
        }

        // Redirect to my lists page
        echo trans('messages.campaigns.paused');
    }

    /**
     * Pause the specified campaign.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function restart(Request $request)
    {
        $customer = $request->user()->customer;
        $items = \Acelle\Model\Campaign::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            if (\Gate::allows('restart', $item)) {
                $item->requeue();

                // Log
                $item->log('restarted', $customer);
            }
        }

        // Redirect to my lists page
        echo trans('messages.campaigns.restarted');
    }

    /**
     * Subscribers list.
     */
    public function subscribers(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        $subscribers = $campaign->subscribers()->groupBy('subscribers.email');

        return view('campaigns.subscribers', [
            'subscribers' => $subscribers,
            'campaign' => $campaign,
            'list' => $campaign->defaultMailList,
        ]);
    }

    /**
     * Subscribers listing.
     */
    public function subscribersListing(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return;
        }

        $subscribers = $campaign->subscribers($request->all())->groupBy('subscribers.email')
                                ->paginate($request->per_page);
        $fields = $campaign->defaultMailList->getFields->whereIn('uid', explode(',', $request->columns));

        return view('campaigns._subscribers_list', [
            'subscribers' => $subscribers,
            'list' => $campaign->defaultMailList,
            'campaign' => $campaign,
            'fields' => $fields,
        ]);
    }

    /**
     * Buiding email template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateBuild(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        $elements = [];
        if(isset($request->style)) {
            $elements = \Acelle\Model\Template::templateStyles()[$request->style];
        }

        return view('campaigns.template_build', [
            'campaign' => $campaign,
            'elements' => $elements,
            'list' => $campaign->defaultMailList,
        ]);
    }

    /**
     * Re-Buiding email template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateRebuild(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.template_rebuild', [
            'campaign' => $campaign,
            'list' => $campaign->defaultMailList
        ]);
    }

    /**
     * Copy campaign.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->copy_campaign_uid);

        // authorize
        if (\Gate::denies('copy', $campaign)) {
            return $this->notAuthorized();
        }

        $campaign->copy($request->copy_campaign_name);

        echo trans('messages.campaign.copied');
    }

    /**
     * Send email for testing campaign.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sendTestEmail(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->send_test_email_campaign_uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        $sending = $campaign->sendTestEmail($request->send_test_email);

        return json_encode($sending);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function templateList(Request $request)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);
        $request->request->add(['source' => 'template']);

        $campaigns = \Acelle\Model\Campaign::search($request, 'all')
            ->where('id', '!=', $campaign->id)
            ->where('html', '!=', "")
            ->paginate($request->per_page);

        return view('campaigns._template_list', [
            'campaigns' => $campaigns,
            'uid' => $campaign->uid
        ]);
    }

    /**
     * Preview template.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($id);

        // authorize
        if (\Gate::denies('preview', $campaign)) {
            return $this->not_authorized();
        }

        // Convert to inline css if template source is builder
        if ($campaign->template_source == 'builder') {
            $cssToInlineStyles = new CssToInlineStyles();
            $html = $campaign->html;
            $css = file_get_contents(public_path("css/res_email.css"));

            // output
            $campaign->html = $cssToInlineStyles->convert(
                $html,
                $css
            );
        }

        return view('campaigns.preview', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * Save campaign screenshot.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function saveImage(Request $request, $id)
    {
        $campaign = \Acelle\Model\Campaign::findByUid($id);

        // authorize
        if (\Gate::denies('saveImage', $campaign)) {
            return $this->not_authorized();
        }

        $upload_loca = 'app/campaign_templates/';
        $upload_path = storage_path($upload_loca);
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $filename = 'screenshot-'.$id.'.png';

        // remove "data:image/png;base64,"
        $uri = substr($request->data, strpos($request->data, ',') + 1);

        // save to file
        file_put_contents($upload_path.$filename, base64_decode($uri));

        // create thumbnails
        $img = \Image::make($upload_path.$filename);
        $img->fit(178, 200)->save($upload_path.$filename.'.thumb.jpg');

        // save
        $campaign->image = $upload_loca.$filename;
        $campaign->save();
    }

    /**
     * Template screenshot.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function image(Request $request)
    {
        // Get current user
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('image', $campaign)) {
            return $this->notAuthorized();
        }

        if (!empty($campaign->image) && file_exists(storage_path($campaign->image).'.thumb.jpg')) {
            $img = \Image::make(storage_path($campaign->image).'.thumb.jpg');
        } else {
            $img = \Image::make(public_path('assets/images/placeholder.jpg'));
        }

        return $img->response();
    }

    /**
     * Choose an existed campaign template.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function campaignTemplateChoose(Request $request)
    {
        $user = $request->user();
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);
        $from_campaign = \Acelle\Model\Campaign::findByUid($request->from_uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        $campaign->html = $from_campaign->html;
        $campaign->template_source = $from_campaign->template_source;
        // $campaign->plain = preg_replace('/\s+/',' ',preg_replace('/\r\n/',' ',strip_tags($campaign->html)));
        $campaign->save();

        if(!$campaign->is_auto) {
            return redirect()->action('CampaignController@templatePreview', ['uid' => $campaign->uid]);
        } else {
            return redirect()->action('AutoEventController@templatePreview', ['uid' => $campaign->autoEvent()->uid, 'campaign_uid' => $campaign->uid]);
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
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('update', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns._list_segment_form', [
            'campaign' => $campaign,
            'lists_segment_group' => [
                'list' => null,
                'is_default' => false
            ]
        ]);
    }

    /**
     * Email web view.
     */
    public function webView(Request $request)
    {
        $message_id = \Acelle\Library\StringHelper::base64UrlDecode($request->message_id);
        $tracking_log = \Acelle\Model\TrackingLog::where('message_id', '=', $message_id)->first();

        try {
            if (!is_object($tracking_log)) {
                throw new \Exception(trans("messages.web_view_can_not_find_tracking_log_with_message_id"));
            }

            $subscriber = $tracking_log->subscriber;
            $campaign = $tracking_log->campaign;

            if (!is_object($campaign) || !is_object($subscriber)) {
                throw new \Exception(trans("messages.web_view_can_not_find_campaign_or_subscriber"));
            }

            return view('campaigns.web_view', [
                'campaign' => $campaign,
                'subscriber' => $subscriber,
                'message_id' => $message_id,
            ]);

        } catch (\Exception $e) {
            MailLog::error($e->getMessage());
            return view('somethingWentWrong', ['message' => trans('messages.the_email_no_longer_exists')]);
        }
    }

    /*
     * Select campaign type page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function selectType(Request $request)
    {
        // authorize
        if (\Gate::denies('create', new \Acelle\Model\Campaign())) {
            return $this->notAuthorized();
        }

        return view('campaigns.select_type');
    }

    /**
     * Template review.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateReview(Request $request)
    {
        // Get current user
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.template_review', [
            'campaign' => $campaign
        ]);
    }

    /**
     * Template review iframe
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function templateReviewIframe(Request $request)
    {
        // Get current user
        $campaign = \Acelle\Model\Campaign::findByUid($request->uid);

        // authorize
        if (\Gate::denies('read', $campaign)) {
            return $this->notAuthorized();
        }

        return view('campaigns.template_review_iframe', [
            'campaign' => $campaign
        ]);
    }

    /**
     * Resend the specified campaign.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request, $uid)
    {
        $customer = $request->user()->customer;
        $campaign = \Acelle\Model\Campaign::findByUid($uid);

        // authorize
        if (\Gate::allows('resend', $campaign)) {
            $campaign->ready();
            $campaign->requeue();
            // Redirect to my lists page
            echo trans('messages.campaign.resent');
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => trans('messages.not_authorized_message')
            ]);
        }
    }
}
