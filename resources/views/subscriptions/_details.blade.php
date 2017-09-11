<ul class="dotted-list topborder section">
    <li>
        <div class="unit size1of2">
            <strong>{{ trans('messages.plan_name') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $subscription->plan_name }}</mc:flag>
        </div>
    </li>
    <li class="selfclear">
        <div class="unit size1of2">
            <strong>{{ trans('messages.start_at') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $subscription->start_at ? \Acelle\Library\Tool::dateTime($subscription->start_at)->format(trans('messages.date_format')) : '' }}</mc:flag>
        </div>
    </li>
    @if (!$subscription->isTimeUnlimited())

        <li class="selfclear">
            <div class="unit size1of2">
                <strong>{{ trans('messages.end_at') }}</strong>
            </div>
            <div class="lastUnit size1of2">
                <mc:flag>{{ $subscription->end_at ? \Acelle\Library\Tool::dateTime($subscription->end_at)->format(trans('messages.date_format')) : '' }}</mc:flag>
            </div>
        </li>
        @if ($subscription->beingUsed())
            <li>
                <div class="unit size1of2">
                    <strong>{{ trans_choice('messages.days_remain', $subscription->daysRemainCount()) }}</strong>
                </div>
                <div class="lastUnit size1of2">
                    <mc:flag>
                         {{ $subscription->daysRemainCount() }}
                    </mc:flag>
                </div>
            </li>
        @endif
    @else
        <li class="selfclear">
            <div class="unit size1of2">
                <strong>{{ trans('messages.end_at') }}</strong>
            </div>
            <div class="lastUnit size1of2">
                <mc:flag>{{ trans('messages.unlimited') }}</mc:flag>
            </div>
        </li>
        <li>
            <div class="unit size1of2">
                <strong>{{ trans_choice('messages.days_remain', $subscription->daysRemainCount()) }}</strong>
            </div>
            <div class="lastUnit size1of2">
                <mc:flag>{{ trans('messages.unlimited') }}</mc:flag>
            </div>
        </li>
    @endif
    <li class="more">
        <a href="#more">{{ trans('messages.more_details') }}</a>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.sending_total_quota_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{{ $subscription->displayTotalQuota() }}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.sending_quota_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $subscription->displayQuota() }}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_lists_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{{ $subscription->displayMaxList() }}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_subscribers_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{{ $subscription->displayMaxSubscriber() }}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_campaigns_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{{ $subscription->displayMaxCampaign() }}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_automations_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{{ $subscription->displayMaxSizeUploadTotal() }}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_size_upload_total_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="text-muted progress-xxs">{{ $subscription->displayFileSizeUpload() }} MB</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.max_file_size_upload_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag>{{ $subscription->displayFileSizeUpload() }} MB</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.allow_create_sending_servers_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{!! $subscription->displayAllowCreateSendingServer() !!}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.allow_create_sending_domains_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="text-muted">{!! $subscription->displayAllowCreateSendingDomain() !!}</mc:flag>
        </div>
    </li>
    <li class="hide">
        <div class="unit size1of2">
            <strong>{{ trans('messages.allow_create_email_verification_servers_label') }}</strong>
        </div>
        <div class="lastUnit size1of2">
            <mc:flag><span class="">{!! $subscription->displayAllowCreateEmailVerificationServer() !!}</mc:flag>
        </div>
    </li>
</ul>
