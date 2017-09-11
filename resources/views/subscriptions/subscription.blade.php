@extends('layouts.subscription')

@section('title', trans('messages.subscription'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    
    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('content')
    @include('subscriptions._steps')
    
    <form enctype="multipart/form-data" action="{{ action('SubscriptionController@subscription', request()->plan_uid) }}" method="POST" class="form-validate-jqueryz subscription-form">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                @include('common.errors')
                <h2 class="mt-0"><i class="icon-quill4"></i> {{ trans('messages.plan_information') }}</h2>
                <div class="panel">
                    <div class="panel-body">
                        @include('subscriptions._form')                
                    </div>
                </div>                
            </div>
            <div class="col-md-1"></div>
        </div>
    </form>    
    
@endsection
