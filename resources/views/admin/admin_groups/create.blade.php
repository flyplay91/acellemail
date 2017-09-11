@extends('layouts.backend')

@section('title', trans('messages.create_admin_group'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li><a href="{{ action("Admin\AdminGroupController@index") }}">{{ trans('messages.admin_groups') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><i class="icon-plus-circle2"></i> {{ trans('messages.create_admin_group') }}</span>
		</h1>
	</div>

@endsection

@section('content')

	<form action="{{ action('Admin\AdminGroupController@store') }}" method="POST" class="form-validate-jqueryz">
		{{ csrf_field() }}

		@include("admin.admin_groups._form")
		<hr />
		<div class="text-left">
			<button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
			<a href="{{ action('Admin\AdminGroupController@index') }}" class="btn bg-grey-800"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
		</div>
	<form>

@endsection
