            <br />
            <div class="row">
                <div class="col-md-6">
                    <div class="content-group-sm">
                        <h4 class="text-semibold"><i class="icon-users4"></i> {{ trans('messages.subscribers_most_open') }}</h4>                        
                    </div>
                    @if ($campaign->openCount())
                        <div class="stat-table">
                            @foreach ($campaign->getTopOpenSubscribers()->get() as $subscriber)
                                <div class="stat-row">
                                    <div class="pull-right num">
                                        {{ $subscriber->aggregate }}
                                    </div>
                                    <p class="text-muted">{{ $subscriber->email }}</p>
                                </div>
                            @endforeach                        
                        </div>
                        <div class="text-right">
                            <a href="{{ action('CampaignController@openLog', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.open_log') }} <i class="icon-arrow-right8"></i></a>
                        </div>
                    @else
                        <div class="empty-chart-pie">
                            <div class="empty-list">
                                <i class="icon-file-text2"></i>
                                <span class="line-1">
                                    {{ trans('messages.log_empty_line_1') }}
                                </span>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="col-md-6">
                    <div class="content-group-sm">
                        <h4 class="text-semibold"><i class="icon-location4"></i> {{ trans('messages.top_location_by_opens') }}</h4>
                    </div>
                        
                    @if ($campaign->openCount())
                        <div class="stat-table">
                            @foreach ($campaign->topLocations()->get() as $location)
                                <div class="stat-row">
                                    <div class="pull-right num">
                                        {{ $location->aggregate }}
                                    </div>
                                    <p class="text-muted">{{ $location->ip_address }} - {{ $location->name() }}</p>
                                </div>
                            @endforeach 
                        </div>
                        <div class="text-right">
                            <a href="{{ action('CampaignController@openMap', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.open_map') }} <i class="icon-arrow-right8"></i></a>
                        </div>
                    @else
                        <div class="empty-chart-pie">
                            <div class="empty-list">
                                <i class="icon-file-text2"></i>
                                <span class="line-1">
                                    {{ trans('messages.log_empty_line_1') }}
                                </span>
                            </div>
                        </div>
                    @endif

                </div>
            </div>