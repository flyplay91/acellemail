@extends('layouts.backend')

@section('title', trans('messages.create_feedback_loop_handler'))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("Admin\FeedbackLoopHandlerController@index") }}">{{ trans('messages.feedback_loop_handlers') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-plus-circle2"></i> {{ trans('messages.create_feedback_loop_handler') }}</span>
				</h1>
			</div>

@endsection

@section('content')
                <form action="{{ action('Admin\FeedbackLoopHandlerController@store') }}" method="POST" class="form-validate-jquery">
					{{ csrf_field() }}

					@include('admin.feedback_loop_handlers._form')
				<form>

@endsection
