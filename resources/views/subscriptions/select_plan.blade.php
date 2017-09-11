@extends('layouts.subscription')

@section('title', trans('messages.select_a_plan'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    
    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('content')
    @include('subscriptions._steps')
    
    <div class="subscription-plans">
        @foreach ($plans as $key => $plan)
            <div class="plan-box-center">
    
                @include('plans._plan')
                
            </div>
        @endforeach
    </div>
    
@endsection
