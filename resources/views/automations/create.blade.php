@extends('layouts.frontend')

@section('title', trans('messages.Create_automation'))
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')	
			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("AutomationController@index") }}">{{ trans('messages.automations') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-alarm-check"></i> {{ trans('messages.Create_automation') }}</span>
				</h1>				
			</div>
@endsection

@section('content')
                <form action="{{ action("AutomationController@store") }}" method="POST" class="form-validate-jqueryz">
                    {{ csrf_field() }}
                    
					@include("automations._form")
					
					<hr>
					<div class="text-right">
						<button class="btn bg-teal-800">{{ trans('messages.Create') }} <i class="icon-arrow-right7"></i> </button>
					</div>
					
				<form>
@endsection
