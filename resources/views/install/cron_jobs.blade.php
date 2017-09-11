@extends('layouts.install')

@section('title', trans('messages.cron_jobs'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('content')
	<form action="{{ action('InstallController@cronJobs') }}" method="POST" class="form-validate-jqueryz">
		{!! csrf_field() !!}
		
		@include('elements._cron_jobs')
    
		<hr>
		<div class="text-right">
			@if($valid)
				<a href="{{ action('InstallController@cronJobs') }}" class="btn btn-primary bg-grey">
					<i class="icon-gear"></i> {!! trans('messages.change_cronjob_setting') !!}
				</a>
				<a href="{{ action('InstallController@finishing') }}" class="btn btn-primary bg-teal">
					{!! trans('messages.next') !!} <i class="icon-arrow-right14 position-right"></i>
				</a>				
			@else
				<button type="submit" class="btn btn-primary bg-teal">
					{!! trans('messages.check_and_save_crontab') !!}
				</button>
			@endif
		</div>
	</form>
@endsection
