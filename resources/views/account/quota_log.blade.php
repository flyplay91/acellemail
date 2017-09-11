<h3 class="mt-0 mb-0"><i class="icon-stats-bars4"></i> {{ trans("messages.used_quota") }}</h3>

<!-- Alert if customer don't have any subscription -->
@if (is_object(\Auth::user()->customer) &&
    \Auth::user()->customer->notHaveAnyPlan() &&
    !\Auth::user()->customer->hasAdminAccount())
    <div class="alert alert-warning mt-20">
        <h4 class="ui-pnotify-title text-nowrap">
        {!! trans('messages.not_have_any_plan_notification', [
            'link' => action('SubscriptionController@selectPlan'),
        ]) !!}
        </h4>
        <div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
    </div>
@elseif (is_object(\Auth::user()->customer)
    && !is_object(\Auth::user()->customer->getCurrentSubscription())
    && !\Auth::user()->customer->notHaveAnyPlan() &&
    !\Auth::user()->customer->hasAdminAccount()
)
    <div class="alert alert-warning mt-20">
        <h4 class="ui-pnotify-title text-nowrap">
        {!! trans('messages.not_have_active_plan_notification', [
            'link' => action('AccountController@subscription'),
        ]) !!}
        </h4>
        <div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
    </div>
@else
    <div class="row quota_box">
        <div class="col-md-12">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-teal-800 text-semibold">
                    <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->getSendingQuotaUsage()) }}/{{ (Auth::user()->customer->getSendingQuota() == -1) ? '∞' : \Acelle\Library\Tool::format_number(Auth::user()->customer->getSendingQuota()) }}</span>
                    &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingQuotaUsage() }}
                </div>
                <h5 class="text-semibold mb-5">{{ trans('messages.sending_quota') }}</h5>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->getSendingQuotaUsagePercentage() }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-group-sm">
                <div class="pull-right text-teal-800 text-semibold">
                    <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->listsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxLists()) }}</span>
                    &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayListsUsage() }}
                </div>
                <h5 class="text-semibold mb-5">{{ trans('messages.list') }}</h5>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->listsUsage() }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-teal-800 text-semibold">
                    <span class="text-muted progress-xxs">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->campaignsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxCampaigns()) }}</span>
                    &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayCampaignsUsage() }}
                </div>
                <h5 class="text-semibold mb-5 mt-0">{{ trans('messages.campaign') }}</h5>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->campaignsUsage() }}%">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="content-group-sm">
                <div class="pull-right text-teal-800 text-semibold">
                    <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->readCache('SubscriberCount')) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxSubscribers()) }}</span>
                    &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySubscribersUsage() }}
                </div>
                <h5 class="text-semibold mb-5">{{ trans('messages.subscriber') }}</h5>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->readCache('SubscriberUsage') }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-teal-800 text-semibold">
                    <span class="text-muted progress-xxs">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->automationsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxAutomations()) }}</span>
                    &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayAutomationsUsage() }}
                </div>
                <h5 class="text-semibold mb-5 mt-0">{{ trans('messages.automation') }}</h5>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->automationsUsage() }}%">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="content-group-sm mt-20">
                <div class="pull-right text-teal-800 text-semibold">
                    <span class="text-muted progress-xxs">{{ \Acelle\Library\Tool::format_number(round(Auth::user()->customer->totalUploadSize(),2)) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxTotalUploadSize()) }} (MB)</span>
                    &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->totalUploadSizeUsage() }}%
                </div>
                <h5 class="text-semibold mb-5 mt-0">{{ trans('messages.total_upload_size') }}</h5>
                <div class="progress progress-xxs">
                    <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->totalUploadSizeUsage() }}%">
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->customer->can("create", new Acelle\Model\SendingServer()))
            <div class="col-md-12">
                <div class="content-group-sm">
                    <div class="pull-right text-teal-800 text-semibold">
                        <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->sendingServersCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxSendingServers()) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingServersUsage() }}
                    </div>
                    <h5 class="text-semibold mb-5">{{ trans('messages.sending_server') }}</h5>
                    <div class="progress progress-xxs">
                        <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->sendingServersUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::user()->customer->can("create", new Acelle\Model\SendingDomain()))
            <div class="col-md-12">
                <div class="content-group-sm">
                    <div class="pull-right text-teal-800 text-semibold">
                        <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->sendingDomainsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxSendingDomains()) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingDomainsUsage() }}
                    </div>
                    <h5 class="text-semibold mb-5">{{ trans('messages.sending_domain') }}</h5>
                    <div class="progress progress-xxs">
                        <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->sendingDomainsUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::user()->customer->can("create", new Acelle\Model\EmailVerificationServer()))
            <div class="col-md-12">
                <div class="content-group-sm">
                    <div class="pull-right text-teal-800 text-semibold">
                        <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->emailVerificationServersCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxEmailVerificationServers()) }}</span>
                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayEmailVerificationServersUsage() }}
                    </div>
                    <h5 class="text-semibold mb-5">{{ trans('messages.email_verification_server') }}</h5>
                    <div class="progress progress-xxs">
                        <div class="progress-bar bg-warning" style="width: {{ Auth::user()->customer->emailVerificationServersUsage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
