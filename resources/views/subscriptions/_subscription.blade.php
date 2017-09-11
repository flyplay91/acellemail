<div class="panel panel-plan mr-30 {{ $subscription->beingUsed() ? 'box-shadow border-dark' : '' }}">
    <div class="panel-heading {{ $subscription->beingUsed() ? 'bg-teal-800' : 'bg-grey-800' }} subscription-state subscription-state-{{ $subscription->timeStatus() }}">
        <h4 class="pull-left panel-title text-center">
            {{ $subscription->plan_name }}
        </h4>
        <span class="label label-flat bg-white pull-right">
                        {{ trans('messages.subscription_time_status_' . $subscription->timeStatus()) }}</span>
    </div>
    <div class="panel-body pt-0 subscription-state subscription-state-{{ $subscription->timeStatus() }}">
        <ul class="mt-0 mb-0 top-border-none plans-intro">
            <li>
                @if ($subscription->beingUsed())
                    {{ trans_choice('messages.days_remain', $subscription->daysRemainCount()) }}:
                    <span class="text-bold text-warning">{{ $subscription->daysRemainCount() }}</span>
                @else
                    {{ trans_choice('messages.days_remain', 0) }}:
                    <span class="text-bold">--</span>
                @endif
            </li>
            <li>
                {{ trans('messages.start_at') }}: <span class="text-bold">
                    {{ $subscription->start_at ? \Acelle\Library\Tool::dateTime($subscription->start_at)->format(trans('messages.date_format')) : '' }}
                </span>
            </li>
            <li>
                {{ trans('messages.end_at') }}: <span class="text-bold">
                    {{ $subscription->end_at ? \Acelle\Library\Tool::dateTime($subscription->end_at)->format(trans('messages.date_format')) : '' }}
                </span>
            </li>
            <li>
                {{ trans('messages.status') }}: <span class="text-muted2 list-status">
                    <span class="label label-flat bg-{{ $subscription->status }}">
                        {{ trans('messages.subscription_status_' . $subscription->status) }}</span>
                </span>
            </li>
            <li>
                <h4 class="text-semibold text-teal-800 mb-0">{{ trans('messages.options') }}</h4>
            </li>
            <li>
                {!! trans('messages.sending_total_quota_intro', ["value" => $subscription->displayTotalQuota()]) !!}
            </li>
            <li>
                {!! trans('messages.sending_quota_intro', ["value" => $subscription->displayQuota()]) !!}
            </li>
            <li>
                {!! trans('messages.max_lists_intro', ["value" => $subscription->displayMaxList()]) !!}
            </li>
            <li>
                {!! trans('messages.max_subscribers_intro', ["value" => $subscription->displayMaxSubscriber()]) !!}
            </li>
            <li>
                {!! trans('messages.max_campaigns_intro', ["value" => $subscription->displayMaxCampaign()]) !!}
            </li>
            <li>
                {!! trans('messages.max_size_upload_total_intro', ["value" => $subscription->displayMaxSizeUploadTotal()]) !!}
            </li>
            <li>
                {!! trans('messages.max_file_size_upload_intro', ["value" => $subscription->displayFileSizeUpload()]) !!}
            </li>
            <li>
                {!! trans('messages.allow_create_sending_servers_intro', ["value" => $subscription->displayAllowCreateSendingServer()]) !!}
            </li>
            <li>
                {!! trans('messages.allow_create_sending_domains_intro', ["value" => $subscription->displayAllowCreateSendingDomain()]) !!}
            </li>
        </ul>
    </div>
    @if (!isset($readonly))
        <div class="panel-footer pt-10 pb-10 text-left">

            @if (Auth::user()->can('delete', $subscription)
                || Auth::user()->can('pay', $subscription)
            )
                <div class="btn-group">
                    <button type="button" class="btn bg-teal-800 dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-loop4"></i> {{ trans('messages.actions') }} <span class="caret ml-0"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left">
                        @if (!$subscription->isPaid() && !$subscription->isActive())
                            @can('pay', $subscription)
                                @foreach (\Acelle\Model\PaymentMethod::getAllActive() as $payment_method)
                                    <li>
                                        <a href="{{ action('SubscriptionController@selectPaymentMethod', ["uid" => $subscription->uid, 'payment_method_id' => $payment_method->uid]) }}">
                                            {!! trans('messages.payment_method_type_' . $payment_method->type . '_button') !!}
                                        </a>
                                    </li>
                                @endforeach
                            @endcan
                        @endif
                        @can('delete', $subscription)
                            <li>
                                <a delete-confirm="{{ trans('messages.delete_subscriptions_confirm') }}" href="{{ action('SubscriptionController@delete', ["uids" => $subscription->uid]) }}">
                                    <i class="icon-trash"></i> {{ trans('messages.delete') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            @endcan
        </div>
    @endif
</div>
