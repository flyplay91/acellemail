@extends('layouts.frontend')

@section('title', trans('messages.subscriptions'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li class="active">{{ trans('messages.api_token') }}</li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-profile"></i> {{ Auth::user()->customer->displayName() }}</span>
        </h1>
    </div>

@endsection

@section('content')

    @include("account._menu")

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <h2 class="text-semibold">{{ trans('messages.subscription') }}</h2>

            <div class="sub-section">
                <h3 class="text-semibold">{{ trans('messages.subscribe_to_a_plan') }}</h3>

                <p>{!! trans('messages.subscribe_to_a_plan_intro') !!}</p>

                <form enctype="multipart/form-data" action="{{ action('SubscriptionController@store') }}" method="POST" class="form-validate-jqueryz subscription-form">
                    {{ csrf_field() }}

                    @include('helpers.form_control', [
                        'type' => 'select_ajax',
                        'class' => 'subsciption-plan-select hook',
                        'name' => 'plan_uid',
                        'label' => trans('messages.select_plan'),
                        'selected' => [
                            'value' => is_object($subscription->plan) ? $subscription->plan->uid : '',
                            'text' => is_object($subscription->plan) ? $subscription->plan->name : ''
                        ],
                        'help_class' => 'subscription',
                        'rules' => $subscription->rules(),
                        'url' => action('PlanController@select2'),
                        'placeholder' => trans('messages.select_plan')
                    ])
                    <div class="ajax-detail-box" data-url="{{ action('SubscriptionController@preview') }}" data-form=".subscription-form">
                        @include('subscriptions.preview', [
                            'subscription' => $subscription
                        ])
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
