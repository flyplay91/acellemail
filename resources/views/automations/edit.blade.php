@extends('layouts.frontend')

@section('title', trans('messages.Create_automation'))
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')	
			<div class="page-title">
				@include('automations._head')
                    
                @include('automations._steps', [
					'step' => 'recipients'
				])
			</div>
@endsection

@section('content')
                <form action="{{ action("AutomationController@update", $automation->uid) }}" method="POST" class="form-validate-jqueryz">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PATCH">
                    
					@include("automations._form")
					
					<hr>
					<div class="text-right">
						<button class="btn bg-teal-800">{{ trans('messages.Save_and_Next') }} <i class="icon-arrow-right7"></i> </button>
					</div>
					
				<form>
@endsection
