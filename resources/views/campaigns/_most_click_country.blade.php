            <h3 class="mt-10"><i class="icon-stats-dots"></i> {{ trans('messages.top_country_by_clicks') }}</h3>
            <div class="row">
                <div class="col-md-6">
                    @if (!$campaign->clickCount())                        
                        <div class="empty-chart-pie">
                            <div class="empty-list">
                                <i class="icon-file-text2"></i>
                                <span class="line-1">
                                    {{ trans('messages.log_empty_line_1') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="stat-table">
                            @foreach ($campaign->topClickCountries(7)->get() as $location)
                                <div class="stat-row">
                                    <div class="pull-right num">
                                        {{ $location->aggregate }}
                                    </div>
                                    <p class="text-muted">{{ $location->country_name }}</p>
                                </div>
                            @endforeach 
                        </div>
                        <div class="text-right">
                            <a href="{{ action('CampaignController@clickLog', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.click_log') }} <i class="icon-arrow-right8"></i></a>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    @if ($campaign->clickCount())
                        <div class="panel panel-flat">
                            <div class="panel-body">
                                <div class="chart-container has-scroll">
                                    <div class="chart has-fixed-height" id="basic_pie_4"  data-url="{{ action('CampaignController@chartClickCountry', $campaign->uid) }}"></div>
                                </div>
                            </div>
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