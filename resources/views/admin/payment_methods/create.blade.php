@extends('layouts.backend')

@section('title', trans('messages.create_payment_method'))
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
	
	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li><a href="{{ action("Admin\PaymentMethodController@index") }}">{{ trans('messages.payment_methods') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><i class="icon-plus-circle2"></i> {{ trans('messages.create_payment_method') }}</span>
		</h1>
	</div>

@endsection

@section('content')
	
	<form enctype="multipart/form-data" action="{{ action('Admin\PaymentMethodController@store') }}" method="POST" class="form-validate-jqueryz payment-method-form">
		{{ csrf_field() }}
		
		@include('admin.payment_methods._form')			
		
	<form>
	
@endsection
