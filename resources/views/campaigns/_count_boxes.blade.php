                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ $campaign->openCount() }}</h2>
                                <div class="text-muted">{{ trans('messages.opened') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ $campaign->clickCount() }}</h2>
                                <div class="text-muted">{{ trans('messages.clicked') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ $campaign->bounceCount() }}</h2>
                                <div class="text-muted">{{ trans('messages.bounced') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-white bg-teal-400">
                            <div class="panel-body text-center">
                                <h2 class="text-semibold mb-10 mt-0">{{ $campaign->unsubscribeCount() }}</h2>
                                <div class="text-muted">{{ trans('messages.unsubscribed') }}</div>
                            </div>
                        </div>
                    </div>
                </div>