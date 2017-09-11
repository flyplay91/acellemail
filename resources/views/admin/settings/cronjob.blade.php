@extends('layouts.backend')

@section('title', trans('messages.settings'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
				</ul>
				<h1>
					<span class="text-gear"><i class="icon-list2"></i> {{ trans('messages.settings') }}</span>
				</h1>
			</div>

@endsection

@section('content')
				<div class="tabbable">

					@include("admin.settings._tabs")

                    <form action="{{ action('Admin\SettingController@cronjob') }}" method="POST" class="form-validate-jqueryz">
						{!! csrf_field() !!}

						@include('elements._cron_jobs', ['show_all' => true])

						<hr>
						<div class="text-left">
							<button class="btn btn-primary bg-teal">
								{!! trans('messages.save') !!}
							</button>
						</div>
					</form>
				</div>
@endsection
