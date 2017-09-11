@extends('layouts.backend')

@section('title', trans('messages.system_logs'))

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
					<span class="text-gear"><i class="icon-list2"></i> {{ trans('messages.system_logs') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
      <h2 class="text-semibold text-teal-800 mt-0">{{ trans('messages.last_300_logs') }}</h2>
			<textarea class="system_logs">{{ $error_logs }}</textarea>
@endsection
