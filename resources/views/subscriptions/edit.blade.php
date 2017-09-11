@extends('layouts.frontend')

@section('title', trans('messages.update_subscription'))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li><a href="{{ action("AccountController@subscription") }}">{{ trans('messages.subscriptions') }}</a></li>
			<li class="active">{{ trans('messages.update') }}</li>
		</ul>
		<h1>
			<span class="text-semibold"><i class="icon-profile"></i> {{ $subscription->created_at }}</span>
		</h1>
	</div>

@endsection

@section('content')

	<form enctype="multipart/form-data" action="{{ action('SubscriptionController@update', $subscription->uid) }}" method="POST" class="subscription-form">
		{{ csrf_field() }}
		<input type="hidden" name="_method" value="PATCH">

		<div class="row">
			<div class="col-md-4">

			</div>
			<div class="col-md-6">
				@include('subscriptions._subscription')
			</div>
		</div>
	</form>

@endsection
