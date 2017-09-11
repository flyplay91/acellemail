@extends('layouts.backend')

@section('title', $customer->displayName())
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
	
			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("Admin\CustomerController@index") }}">{{ trans('messages.customers') }}</a></li>
					<li class="active">{{ trans('messages.update') }}</li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-profile"></i> {{ $customer->displayName() }}</span>
				</h1>
			</div>
				
@endsection

@section('content')
	@include('admin.customers._tabs')

	<form enctype="multipart/form-data" action="{{ action('Admin\CustomerController@update', $customer->uid) }}" method="POST" class="form-validate-jquery">
		{{ csrf_field() }}
		<input type="hidden" name="_method" value="PATCH">
		
		@include('admin.customers._form')
		
	<form>
@endsection