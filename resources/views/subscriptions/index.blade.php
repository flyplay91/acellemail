@extends('layouts.frontend')

@section('title', trans('messages.your_subscriptions'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><i class="icon-quill4"></i> {{ trans('messages.your_subscriptions') }}</span>
		</h1>
	</div>

@endsection

@section('content')

	<form class="listing-form"
		sort-url="{{ action('SubscriptionController@sort') }}"
		data-url="{{ action('SubscriptionController@listing') }}"
		per-page="{{ Acelle\Model\Subscription::$itemsPerPage }}"
	>
		<div class="pml-table-container">



		</div>
	</form>

@endsection
