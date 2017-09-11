@extends('layouts.backend')

@section('title', trans('messages.dashboard'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('content')
    <h1 class="mb-10">{{ trans('messages.backend_dashboard_hello', ['name' => Auth::user()->admin->displayName()]) }}</h1>
    <p>{{ trans('messages.backend_dashboard_welcome') }}</p>

    <div class="row">
        <div class="col-md-6">
            <h3 class="text-semibold"><i class="icon-users"></i> {{ trans('messages.customers_growth') }}</h3>
            @include('admin.customers._growth_chart')
        </div>
        <div class="col-md-6">
            <h3 class="text-semibold"><i class="icon-clipboard2"></i> {{ trans('messages.plans_chart') }}</h3>
            @include('admin.plans._pie_chart')
        </div>
    </div>

    <div class="row mt-30">
        <div class="col-md-6">
            <h3 class="text-semibold">
                <i class="icon-quill4"></i>
                {{ trans('messages.recent_subscriptions') }}
            </h3>
            <ul class="modern-listing mt-0 mb-0 top-border-none type2">
                @forelse (Auth::user()->admin->recentSubscriptions() as $subscription)
                    <li class="">
                        <div class="row">
                            <div class="col-sm-5 col-md-5">
                                <h6 class="mt-0 mb-0 text-semibold">
                                    <a href="{{ action('Admin\CustomerController@subscriptions', $subscription->customer->uid) }}">
                                        <i class="icon-clipboard2"></i>
                                        {{ $subscription->plan_name }}
                                    </a>
                                </h6>
                                <p class="mb-0">
                                    <!--<img width="40" class="img-circle mr-10 pull-left" src="{{ action('CustomerController@avatar', $subscription->customer->uid) }}" alt="">-->
                                    <i class="icon-user" style="
                                        font-size: 14px;
                                        padding: 0;
                                        margin: 5px 0 0 -8px;
                                        height: auto;"></i>
                                    {{ $subscription->customer->displayName() }}
                                </p>
                            </div>
                            <div class="col-sm-4 col-md-4 text-left">
                                <h6 class="no-margin text-semibold">
                                    {{ Tool::formatDateTime($subscription->created_at) }}
                                </h6>
                                <span class="">{{ trans('messages.created_at') }}</span>
                            </div>
                            <div class="col-sm-3 col-md-3 text-left">
                                <span class="text-muted2 list-status pull-left">
                                    <span class="label label-flat bg-{{ $subscription->status }}">{{ trans('messages.subscription_status_' . $subscription->status) }}</span>
                                </span>
                                <!--<br/>
                                <span class="text-muted2 list-status pull-left">
                                    <span class="label label-sub label-flat bg-{{ $subscription->timeStatus() }}">{{ trans('messages.subscription_time_status_' . $subscription->timeStatus()) }}</span>
                                </span>-->
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="empty-li">
                        {{ trans('messages.empty_record_message') }}
                    </li>
                @endforelse
            </ul>
        </div>
        <div class="col-md-6">
            <h3 class="text-semibold">
                <i class="icon-users"></i>
                {{ trans('messages.recent_customers') }}
            </h3>
            <ul class="modern-listing mt-0 mb-0 top-border-none type2">
                @forelse(Auth::user()->admin->recentCustomers() as $customer)
                    <li class="">
                        <div class="row">
                            <div class="col-sm-5 col-md-5">
                                <img width="40" class="img-circle mr-10 pull-left" src="{{ action('CustomerController@avatar', $customer->uid) }}" alt="">
                                <h6 class="mt-0 mb-0 text-semibold">
                                    <a href="{{ action('Admin\CustomerController@edit', $customer->uid) }}">
                                        {{ $customer->displayName() }}
                                    </a>
                                </h6>
                                <p class="mb-0 admin-line admin-recent-sencond-line" title="{{ $customer->user->email }}">
                                    {{ $customer->user->email }}
                                </p>
                            </div>
                            <div class="col-sm-4 col-md-4 text-left">
                                <h6 class="no-margin text-semibold">
                                    {{ Tool::formatDateTime($customer->created_at) }}
                                </h6>
                                <span class="">{{ trans('messages.created_at') }}</span>
                            </div>
                            <div class="col-sm-3 col-md-3 text-left">
                                <span class="text-muted2 list-status pull-left">
                                    <span class="label label-flat bg-{{ $customer->status }}">{{ trans('messages.subscription_status_' . $customer->status) }}</span>
                                </span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="empty-li">
                        {{ trans('messages.empty_record_message') }}
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <h3 class="text-semibold">
        <i class="icon-history position-left"></i>
        {{ trans('messages.activities') }}
    </h3>
    @if (\Auth::user()->admin->getLogs()->count() == 0)
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
                        @foreach (\Auth::user()->admin->getLogs()->take(20)->get() as $log)
                            <!-- Sales stats -->
                            <div class="timeline-row">
                                <div class="timeline-icon">
                                    <a href="#"><img src="{{ action('CustomerController@avatar', $log->customer->uid) }}" alt=""></a>
                                </div>

                                <div class="panel panel-flat timeline-content">
                                    <div class="panel-heading">
                                        <h6 class="panel-title text-semibold">{{ $log->customer->displayName() }}</h6>
                                        <div class="heading-elements">
                                            <span class="heading-text"><i class="icon-history position-left text-success"></i> {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        {!! $log->message() !!}
                                    </div>
                                </div>
                            </div>
                            <!-- /sales stats -->
                        @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="sub-section mb-20">
        <h3 class="text-semibold mt-40">{{ trans('messages.resources_statistics') }}</h3>
        <p>{{ trans('messages.resources_statistics_intro') }}</p>
        <div class="row">
            <div class="col-md-6">
                <ul class="dotted-list topborder section">
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-users"></i> {{ trans('messages.customers') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllCustomers()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li class="selfclear">
                        <div class="unit size1of2">
                            <strong><i class="icon-quill4"></i> {{ trans('messages.subscriptions') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllSubscriptions()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li class="selfclear">
                        <div class="unit size1of2">
                            <strong><i class="icon-clipboard2"></i> {{ trans('messages.plans') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllPlans()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-credit-card2"></i> {{ trans('messages.payment_methods') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllPaymentMethods()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-address-book2"></i> {{ trans('messages.lists') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllLists()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-users"></i> {{ trans('messages.subscribers') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllSubscribers()->count() }}</mc:flag>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="dotted-list topborder section">
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-user-tie"></i> {{ trans('messages.admins') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllAdmins()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li class="selfclear">
                        <div class="unit size1of2">
                            <strong><i class="icon-users4"></i> {{ trans('messages.admin_groups') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllAdminGroups()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li class="selfclear">
                        <div class="unit size1of2">
                            <strong><i class="icon-server"></i> {{ trans('messages.sending_servers') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllSendingServers()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-earth"></i> {{ trans('messages.sending_domains') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllSendingDomains()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-paperplane"></i> {{ trans('messages.campaigns') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllCampaigns()->count() }}</mc:flag>
                        </div>
                    </li>
                    <li>
                        <div class="unit size1of2">
                            <strong><i class="icon-alarm-check"></i> {{ trans('messages.automations') }}</strong>
                        </div>
                        <div class="lastUnit size1of2">
                            <mc:flag>{{ Auth::user()->admin->getAllAutomations()->count() }}</mc:flag>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
