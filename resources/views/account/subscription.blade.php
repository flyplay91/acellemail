@extends('layouts.frontend')

@section('title', trans('messages.subscription'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="active">{{ trans('messages.subscription') }}</li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-quill4"></i> {{ Auth::user()->customer->displayName() }}</span>
        </h1>
    </div>

@endsection

@section('content')

    @include("account._menu")

    <div class="row">
        <div class="col-sm-12 col-md-8 col-lg-8">
            <h2 class="text-semibold">{{ trans('messages.subscription') }}</h2>

            <div class="sub-section">
                <h3 class="text-semibold">{{ trans('messages.current_plan') }}</h3>

                @if (!$subscription->isActive())
                    @if ($subscription->isDisabled())
                        <p>
                            {!! trans('messages.current_plan_disabled_intro', [
                                'plan' => $subscription->plan_name
                            ]) !!}
                        </p>

                        @include('subscriptions._details', ['subscription' => $subscription])

                    @elseif (!$subscription->isPaid())
                        <p>
                            {!! trans('messages.current_plan_notactive_intro', [
                                'plan' => $subscription->plan_name,
                            ]) !!}
                        </p>

                        @if (!$subscription->isPaid() && !$subscription->isActive())
                            <ul class="dotted-list topborder section mb-0">
                                <li class="border-bottom-0">
                                    <div class="unit size1of2 text-bold">
                                        <strong>{{ trans('messages.price') }}</strong>
                                    </div>
                                    <div class="lastUnit size1of2">
                                        <h5 class="mt-0 mb-0 text-semibold">
                                            <mc:flag>
                                                @if ($subscription->price)
                                                    {{ Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format) }}
                                                @else
                                                    {{ trans('messages.free') }}
                                                @endif
                                            </mc:flag>
                                        </h5>
                                    </div>
                                </li>
                            </ul>
                        @endif

                        @include('subscriptions._details', ['subscription' => $subscription])

                        @if (!$subscription->isPaid() && !$subscription->isActive())
                            @if (Auth::user()->customer->can('pay', $subscription))

                                @foreach (\Acelle\Model\PaymentMethod::getAllActive() as $payment_method)
                                    @if ($payment_method->type != \Acelle\Model\PaymentMethod::TYPE_CASH)
                                        @if (
                                            $subscription->isValidPaymentMethod($payment_method)
                                        )
                                            <span class="mb-10">
                                                <a
                                                    href="{{ action('SubscriptionController@selectPaymentMethod', ["uid" => $subscription->uid, 'payment_method_id' => $payment_method->uid]) }}"
                                                    class="btn bg-teal"
                                                >
                                                    {!! trans('messages.payment_method_type_' . $payment_method->type . '_button') !!}
                                                </a>
                                            </span>
                                        @endif
                                    @endif
                                @endforeach

                            @endif
                        @endif
                    @else
                        <p>
                            {!! trans('messages.current_plan_notactive_paid_intro' . ($subscription->isTimeUnlimited() ? '_unlimited' : '')) !!}
                        </p>

                        @include('subscriptions._details', ['subscription' => $subscription])
                    @endif

                    @if (\Auth::user()->customer->can('delete', $subscription))
                        <a data-method="DELETE" delete-confirm="{{ trans('messages.delete_subscriptions_confirm') }}" href="{{ action('SubscriptionController@delete', ["uids" => $subscription->uid]) }}"
                            class="btn bg-grey-300 link-method"
                        >
                            <i class="icon-trash"></i> {{ trans('messages.cancel') }}
                        </a>
                    @endif
                @else
                    {!! trans('messages.current_plan_intro' . ($subscription->isTimeUnlimited() ? '_unlimited' : ''), [
                        'plan' => $subscription->plan_name,
                        'remain' => ($subscription->end_at ? \Acelle\Library\Tool::dateTime($subscription->end_at)->diffForHumans(null, true) : 0),
                        'end_at' => \Acelle\Library\Tool::formatDate($subscription->end_at)
                    ]) !!}

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
                            <li>
                                <div class="unit size1of2">
                                    <strong>{{ trans_choice('messages.days_remain', $subscription->daysRemainCount()) }}</strong>
                                </div>
                                <div class="lastUnit size1of2">
                                    <mc:flag>{{ $subscription->daysRemainCount() }}</mc:flag>
                                </div>
                            </li>
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
                                <mc:flag><span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->getSendingQuotaUsage()) }}/{{ (Auth::user()->customer->getSendingQuota() == -1) ? '∞' : \Acelle\Library\Tool::format_number(Auth::user()->customer->getSendingQuota()) }}</span>
                                &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingQuotaUsage() }}</mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.sending_limit') }}</strong>
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
                                <mc:flag><span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->listsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxLists()) }}</span>
                                &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayListsUsage() }}</mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.max_subscribers_label') }}</strong>
                            </div>
                            <div class="lastUnit size1of2">
                                <mc:flag><span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->readCache('SubscriberCount', 0)) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxSubscribers()) }}</span>
                                &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySubscribersUsage() }}</mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.max_campaigns_label') }}</strong>
                            </div>
                            <div class="lastUnit size1of2">
                                <mc:flag><span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->campaignsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxCampaigns()) }}</span>
                                &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayCampaignsUsage() }}</mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.max_automations_label') }}</strong>
                            </div>
                            <div class="lastUnit size1of2">
                                <mc:flag><span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->automationsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxAutomations()) }}</span>
                                &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayAutomationsUsage() }}</mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.max_size_upload_total_label') }}</strong>
                            </div>
                            <div class="lastUnit size1of2">
                                <mc:flag><span class="text-muted progress-xxs">{{ \Acelle\Library\Tool::format_number(round(Auth::user()->customer->totalUploadSize(),2)) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxTotalUploadSize()) }} (MB)</span>
                                &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->totalUploadSizeUsage() }}%</mc:flag>
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
                                <mc:flag>
                                    @if (Auth::user()->customer->can("create", new Acelle\Model\SendingServer()))
                                        <span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->sendingServersCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxSendingServers()) }}</span>
                                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingServersUsage() }}
                                    @else
                                        {!! $subscription->displayAllowCreateSendingServer() !!}
                                    @endif
                                </mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.allow_create_sending_domains_label') }}</strong>
                            </div>
                            <div class="lastUnit size1of2">
                                <mc:flag>
                                    @if (Auth::user()->customer->can("create", new Acelle\Model\SendingDomain()))
                                        <span class="text-muted">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->sendingDomainsCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxSendingDomains()) }}</span>
                                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displaySendingDomainsUsage() }}
                                    @else
                                        {!! $subscription->displayAllowCreateSendingDomain() !!}
                                    @endif
                                </mc:flag>
                            </div>
                        </li>
                        <li class="hide">
                            <div class="unit size1of2">
                                <strong>{{ trans('messages.allow_create_email_verification_servers_label') }}</strong>
                            </div>
                            <div class="lastUnit size1of2">
                                <mc:flag>
                                    @if (Auth::user()->customer->can("create", new Acelle\Model\EmailVerificationServer()))
                                        <span class="">{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->emailVerificationServersCount()) }}/{{ \Acelle\Library\Tool::format_number(Auth::user()->customer->maxEmailVerificationServers()) }}</span>
                                        &nbsp;&nbsp;&nbsp;{{ Auth::user()->customer->displayEmailVerificationServersUsage() }}
                                    @else
                                        {!! $subscription->displayAllowCreateEmailVerificationServer() !!}
                                    @endif
                                </mc:flag>
                            </div>
                        </li>
                    </ul>
                @endif
            </div>
            @if (!$subscription->isTimeUnlimited())
                    <div class="sub-section">
                    @if (!$nextSubscription)
                        @if (\Auth::user()->customer->can('create', new Acelle\Model\Subscription()))
                            <h3>{{ trans('messages.available_plans') }}</h3>
                            <p>{{ trans('messages.plan.renew.wording') }}</p>
                            <form enctype="multipart/form-data" action="{{ action('SubscriptionController@store') }}" method="POST" class="form-validate-jqueryz subscription-form">
                                {{ csrf_field() }}

                                @include('helpers.form_control', [
                                    'type' => 'select_ajax',
                                    'class' => 'subsciption-plan-select hook',
                                    'name' => 'plan_uid',
                                    'label' => trans('messages.plan'),
                                    'selected' => [],
                                    'help_class' => 'subscription',
                                    'rules' => $subscription->rules(),
                                    'url' => action('PlanController@select2'),
                                    'placeholder' => trans('messages.select_plan')
                                ])
                                <div class="ajax-detail-box" data-url="{{ action('SubscriptionController@preview') }}" data-form=".subscription-form">
                                </div>
                            </form>
                        @endif
                    @elseif (!$nextSubscription->isActive())
                        <h3>{{ trans('messages.next_plan') }}</h3>

                        <p>
                            {!! trans('messages.next_plan_notactive_intro', [
                                'plan' => $nextSubscription->plan_name
                            ]) !!}
                        </p>

                        @if (!$nextSubscription->isPaid() && !$nextSubscription->isActive())
                            <ul class="dotted-list topborder section mb-0">
                                <li class="border-bottom-0">
                                    <div class="unit size1of2 text-bold">
                                        <strong>{{ trans('messages.price') }}</strong>
                                    </div>
                                    <div class="lastUnit size1of2">
                                        <h5 class="mt-0 mb-0 text-semibold">
                                            <mc:flag>
                                                @if ($nextSubscription->price)
                                                    {{ Acelle\Library\Tool::format_price($nextSubscription->price, $nextSubscription->currency_format) }}
                                                @else
                                                    {{ trans('messages.free') }}
                                                @endif
                                            </mc:flag>
                                        </h5>
                                    </div>
                                </li>
                            </ul>
                        @endif

                        @include('subscriptions._details', ['subscription' => $nextSubscription])

                        @if (!$nextSubscription->isPaid() && !$nextSubscription->isActive())
                            @if (Auth::user()->customer->can('pay', $nextSubscription))

                                @foreach (\Acelle\Model\PaymentMethod::getAllActive() as $payment_method)
                                    @if ($payment_method->type != \Acelle\Model\PaymentMethod::TYPE_CASH)
                                        @if (
                                            $nextSubscription->isValidPaymentMethod($payment_method)
                                        )
                                            <span class="mb-10">
                                                <a
                                                    href="{{ action('SubscriptionController@selectPaymentMethod', ["uid" => $nextSubscription->uid, 'payment_method_id' => $payment_method->uid]) }}"
                                                    class="btn bg-teal"
                                                >
                                                    {!! trans('messages.payment_method_type_' . $payment_method->type . '_button') !!}
                                                </a>
                                            </span>
                                        @endif
                                    @endif
                                @endforeach

                            @endif
                        @endif
                        @if (\Auth::user()->customer->can('delete', $nextSubscription))
                            <a data-method="DELETE" delete-confirm="{{ trans('messages.delete_subscriptions_confirm') }}" href="{{ action('SubscriptionController@delete', ["uids" => $nextSubscription->uid]) }}"
                                class="btn bg-grey-300 link-method"
                            >
                                <i class="icon-trash"></i> {{ trans('messages.cancel') }}
                            </a>
                        @endif
                    @else
                        <h3>{{ trans('messages.next_plan') }}</h3>
                        <p>
                            {!! trans('messages.next_plan_active_intro', [
                                'plan' => $nextSubscription->plan_name,
                                'remain' => \Acelle\Library\Tool::dateTime($nextSubscription->end_at)->diffForHumans(null, true),
                                'start_at' => \Acelle\Library\Tool::formatDate($nextSubscription->start_at)
                            ]) !!}
                        </p>

                        @include('subscriptions._details', ['subscription' => $nextSubscription])

                    @endif
                </div>
            @endif
        </div>
    </div>

@endsection
