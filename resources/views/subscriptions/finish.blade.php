@extends('layouts.subscription')

@section('title', trans('messages.finish'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('content')
    @include('subscriptions._steps')

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <h2 class="mt-0"><i class="icon-quill4"></i> {{ trans('messages.finish') }}</h2>
            <div class="panel">
                <div class="panel-body">
                    {!! trans('messages.subscription_finish_messages', ['plan' => $subscription->plan_name]) !!}
                </div>
            </div>

            <br />
            <div class="text-center">
                <a href="{{ action('HomeController@index') }}" class="btn bg-teal"><i class="icon-home"></i> {{ trans('messages.go_to_dashboard') }}</a>
                <a href="{{ action('AccountController@subscription') }}" class="btn bg-grey"><i class="icon-quill4"></i> {{ trans('messages.check_your_subscriptions') }}</a>
            </div>
        </div>
    </div>
@endsection
