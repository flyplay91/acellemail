@extends('layouts.backend')

@section('title', trans('messages.dashboard'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    
    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('content')
    <h1>{{ trans('messages.dashboard') }}</h1>
	
    <h2 class="text-teal-800"><i class="icon-paperplane"></i> {{ trans('messages.recently_sent_campaigns') }}</h2>
		
	@if (Acelle\Model\Campaign::where("status", "=", "done")->count() == 0)
		<div class="empty-list">
			<i class="icon-paperplane"></i>
			<span class="line-1">
				{{ trans('messages.no_sent_campaigns') }}
			</span>
		</div>
    @else
		<div class="row">
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'select',
					'class' => 'dashboard-campaign-select',
					'name' => 'campaign_id',
					'label' => '',
					'value' => '',
					'options' => Acelle\Model\Campaign::getSelectOptions(null, "done"),
				])
			</div>
		</div>
		<div class="campaign-quickview-container" data-url="{{ action("CampaignController@quickView") }}"></div>
	@endif
    
    <br />
    <br />
    
    <h2 class="text-teal-800"><i class="icon-address-book2"></i> {{ trans('messages.list_growth') }}</h2>
    
	@if (Acelle\Model\MailList::count() == 0)
		<div class="empty-list">
			<i class="icon-address-book2"></i>
			<span class="line-1">
				{{ trans('messages.no_saved_lists') }}
			</span>
		</div>
    @else
		<div class="row">
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'select',
					'class' => 'dashboard-list-select',
					'name' => 'list_id',
					'label' => '',
					'value' => '',
					'include_blank' => trans('messages.all'),
					'options' => Acelle\Model\MailList::getSelectOptions(),
				])
			</div>
		</div>
		<div class="list-quickview-container" data-url="{{ action("MailListController@quickView") }}"></div>
	@endif
    
    <br />
    <br />
    
    <h2 class="text-teal-800"><i class="icon-podium"></i> {{ trans('messages.top_5') }}</h2>
    <div class="tabbable">
				<ul class="nav nav-tabs nav-tabs-top">
					<li class="active text-semibold"><a href="#top-tab1" data-toggle="tab">
						<i class="icon-folder-open3"></i> {{ trans('messages.campaign_opens') }}</a></li>
					<li class="text-semibold"><a href="#top-tab2" data-toggle="tab">
						<i class="icon-pointer"></i> {{ trans('messages.campaign_clicks') }}</a></li>
					<li class="text-semibold"><a href="#top-tab3" data-toggle="tab">
						<i class="icon-link"></i> {{ trans('messages.clicked_links') }}</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="top-tab1">
						<ul class="modern-listing mt-0 top-border-none">
							@foreach (Acelle\Model\Campaign::topOpens(5, null)->get() as $num => $item)
								<li>
                                    <div class="row">
                                        <div class="col-sm-5 col-md-5">
                                            <i class="number">{{ $num+1 }}</i>
                                            <h6 class="mt-0 mb-0 text-semibold">
                                                <a href="{{ action('CampaignController@overview', $item->uid) }}">
                                                    {{ $item->name }}
                                                </a>
                                            </h6>
                                            <p>
												@if (is_object($item->segment))
													{{ $item->mailList->name }} . {{ $item->segment->name }}													
												@elseif (is_object($item->mailList))
													{{ $item->mailList->name }}
												@endif                                                
                                            </p>
                                        </div>
										<div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ $item->aggregate }}
											</h5>
											<span class="text-muted">{{ trans('messages.opens') }}</span>
                                                <br /><br />
                                        </div>
                                        <div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ $item->openUniqCount() }}
											</h5>
											<span class="text-muted">{{ trans('messages.uniq_opens') }}</span>
                                                <br /><br />
                                        </div>
										<div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ Acelle\Library\Tool::formatDateTime($item->lastOpen()->created_at) }}
											</h5>
											<span class="text-muted">{{ trans('messages.last_open') }}</span>
                                                <br /><br />
                                        </div>	
                                    </div>

                                </li>
							@endforeach
						</ul>
					</div>
					<div class="tab-pane" id="top-tab2">
						<ul class="modern-listing mt-0 top-border-none">							
							@foreach (Acelle\Model\Campaign::topClicks(5, null)->get() as $num => $item)
								<li>
                                    <div class="row">
                                        <div class="col-sm-5 col-md-5">
                                            <i class="number">{{ $num+1 }}</i>
                                            <h6 class="mt-0 mb-0 text-semibold">
                                                <a href="{{ action('CampaignController@overview', $item->uid) }}">
                                                    {{ $item->name }}
                                                </a>
                                            </h6>
                                            <p>
												@if (is_object($item->segment))
													{{ $item->mailList->name }} . {{ $item->segment->name }}													
												@elseif (is_object($item->mailList))
													{{ $item->mailList->name }}
												@endif                                                
                                            </p>
                                        </div>
                                        <div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ $item->aggregate }}
											</h5>
											<span class="text-muted">{{ trans('messages.clicks') }}</span>
                                                <br /><br />
                                        </div>
										<div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ $item->urlCount() }}
											</h5>
											<span class="text-muted">{{ trans('messages.urls') }}</span>
                                                <br /><br />
                                        </div>
										<div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ Acelle\Library\Tool::formatDateTime($item->lastClick()->created_at) }}
											</h5>
											<span class="text-muted">{{ trans('messages.last_clicked') }}</span>
                                                <br /><br />
                                        </div>										
                                    </div>

                                </li>
							@endforeach
						</ul>
					</div>
					<div class="tab-pane" id="top-tab3">
						
						<ul class="modern-listing mt-0 top-border-none">
							@foreach (Acelle\Model\Campaign::topLinks(5, null)->get() as $num => $item)
								<li>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6">
                                            <i class="number">{{ $num+1 }}</i>
                                            <h6 class="mt-0 mb-0 text-semibold url-truncate">
                                                <a title="{{ $item->url }}" href="{{ $item->url }}" target="_blank">
                                                    {{ $item->url }}
                                                </a>
                                            </h6>
                                            <p>
												{{ $item->campaigns()->count() }} {{ trans('messages.campaigns') }}                                              
                                            </p>
                                        </div>
                                        <div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ $item->aggregate }}
											</h5>
											<span class="text-muted">{{ trans('messages.clicks') }}</span>
                                                <br /><br />
                                        </div>
										<div class="col-sm-2 col-md-2 text-left">
											<h5 class="no-margin text-bold">
												{{ Acelle\Library\Tool::formatDateTime($item->lastClick()->created_at) }}          
											</h5>
											<span class="text-muted">{{ trans('messages.last_clicked') }}</span>
                                            <br /><br />
                                        </div>
                                    </div>
                                </li>
							@endforeach
						</ul>
					</div>					
				</div>
			</div>

	<br />
    <br />
    <h2 class="text-teal-800"><i class="icon-history"></i> {{ trans('messages.users_activity_log') }}</h2>
	
	@if (Acelle\Model\Log::count() == 0)
		<div class="empty-list">
			<i class="icon-history"></i>
			<span class="line-1">
				{{ trans('messages.no_activity_logs') }}
			</span>
		</div>
    @else
		<div class="scrollbar-box action-log-box">
			<!-- Timeline -->
			<div class="timeline timeline-left content-group">
				<div class="timeline-container">				
						@foreach (Acelle\Model\Log::take(20)->get() as $log)
							<!-- Sales stats -->
							<div class="timeline-row">
								<div class="timeline-icon">
									<a href="#"><img src="{{ action('UserController@avatar', $log->customer->uid) }}" alt=""></a>
								</div>
	
								<div class="panel panel-flat timeline-content">
									<div class="panel-heading">
										<h6 class="panel-title text-semibold">{{ $log->customer->displayName() }}</h6>
										<div class="heading-elements">
											<span class="heading-text"><i class="icon-history position-left text-success"></i> {{ $log->created_at->diffForHumans() }}</span>
										</div>
									</div>
	
									<div class="panel-body">
										{!! $log->message() !!}
									</div>
								</div>
							</div>
						@endforeach								
				</div>
			</div>
		</div>
	@endif
	
	<br>
	<br>
    
@endsection
