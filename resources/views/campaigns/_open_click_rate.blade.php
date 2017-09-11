            <div class="sub-h3">{{ trans('messages.campaign_open_click_rate_intro') }}</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="content-group-sm">
                        <div class="pull-right progress-right-info text-teal-800">{{ $campaign->readCache('UniqOpenRate') }}%</div>
                        <h4 class="text-semibold mt-0">{{ trans('messages.open_rate') }}</h4>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-teal-400" style="width: {{ $campaign->readCache('UniqOpenRate') }}%">
                            </div>
                        </div>
                    </div>
                    <div class="stat-table">
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ $campaign->readCache('DeliveredCount') }}
                                <span class="text-muted2">{{ $campaign->readCache('DeliveredRate') }}%</span>
                            </div>
                            <p class="text-muted">{{ trans('messages.successful_deliveries') }}</p>
                        </div>
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ $campaign->openCount() }}
                            </div>
                            <p class="text-muted">{{ trans('messages.total_opens') }}</p>
                        </div>
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ $campaign->readCache('UniqOpenCount') }}
                            </div>
                            <p class="text-muted">{{ trans('messages.uniq_opens') }}</p>
                        </div>
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ is_object($campaign->lastOpen()) ? Acelle\Library\Tool::formatDateTime($campaign->lastOpen()->created_at) : "" }}
                            </div>
                            <p class="text-muted">{{ trans('messages.last_opened') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ action('CampaignController@openLog', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.open_log') }} <i class="icon-arrow-right8"></i></a>
                    </div>
                    <br />
                </div>
                <div class="col-md-6">
                    <div class="content-group-sm">
                        <div class="pull-right progress-right-info text-teal-800">{{ $campaign->readCache('ClickedRate') }}%</div>
                        <h4 class="text-semibold mt-0">{{ trans('messages.click_rate') }}</h4>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-teal-400" style="width: {{ $campaign->readCache('ClickedRate') }}%">
                            </div>
                        </div>
                    </div>
                    <div class="stat-table">
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ $campaign->clickCount() }}
                            </div>
                            <p class="text-muted">{{ trans('messages.total_clicks') }}</p>
                        </div>
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ $campaign->openCount() }}
                            </div>
                            <p class="text-muted">{{ trans('messages.total_opens') }}</p>
                        </div>
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ $campaign->abuseFeedbackCount() }}
                            </div>
                            <p class="text-muted">{{ trans('messages.abuse_reports') }}</p>
                        </div>
                        <div class="stat-row">
                            <div class="pull-right num">
                                {{ is_object($campaign->lastClick()) ? Acelle\Library\Tool::formatDateTime($campaign->lastClick()->created_at) : "" }}
                            </div>
                            <p class="text-muted">{{ trans('messages.last_clicked') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ action('CampaignController@clickLog', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.click_log') }} <i class="icon-arrow-right8"></i></a>
                    </div>
                    <br />
                </div>
            </div>
