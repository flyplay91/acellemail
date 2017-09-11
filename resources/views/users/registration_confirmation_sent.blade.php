@extends('layouts.page')

@section('title', trans('messages.select_a_plan'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    
    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-2 col-md-3">
            
        </div>
        <div class="col-sm-8 col-md-6">
            <h2 class="text-semibold mt-40 text-white">{{ trans('messages.activation_email_sent_title') }}</h2>
            <div class="panel panel-body">                        
                {!! trans('messages.activation_email_resent_content') !!}
            </div>
        </div>
    </div>
@endsection