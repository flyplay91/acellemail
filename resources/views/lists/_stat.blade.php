                <div class="row">
                    <div class="col-md-6">
                        <div class="content-group-sm">
                            <div class="pull-right progress-right-info text-teal-800">{{ $list->readCache('UniqOpenRate') }}%</div>
                            <h5 class="text-semibold">{{ trans('messages.average_open_rate') }}</h5>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-teal-600" style="width: {{ $list->readCache('UniqOpenRate') }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content-group-sm">
                            <div class="pull-right progress-right-info text-teal-800">{{ $list->readCache('ClickedRate') }}%</div>
                            <h5 class="text-semibold">{{ trans('messages.average_click_rate') }}</h5>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-teal-600" style="width: {{ $list->readCache('ClickedRate') }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ number_to_percentage($list->readCache('SubscribeRate')) }}</h2>
                                <div class="text-muted">{{ trans('messages.avg_subscribe_rate') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ number_to_percentage($list->readCache('UnsubscribeRate')) }}</h2>
                                <div class="text-muted">{{ trans('messages.avg_unsubscribe_rate') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ number_with_delimiter($list->readCache('UnsubscribeCount')) }}</h2>
                                <div class="text-muted">{{ trans('messages.total_unsubscribers') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ number_with_delimiter($list->readCache('UnconfirmedCount')) }}</h2>
                                <div class="text-muted">{{ trans('messages.total_unconfirmed') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
