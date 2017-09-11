@extends('layouts.frontend')

@section('title', trans('messages.email_verification_servers'))

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
			<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.email_verification_servers') }}</span>
		</h1>
	</div>

@endsection

@section('content')
	<p>{{ trans('messages.email_verification_server.wording') }}</p>

	<form class="listing-form"
		sort-url="{{ action('EmailVerificationServerController@sort') }}"
		data-url="{{ action('EmailVerificationServerController@listing') }}"
		per-page="{{ Acelle\Model\EmailVerificationServer::$itemsPerPage }}"
	>
		<div class="row top-list-controls">
			<div class="col-md-10">
				@if ($servers->count() >= 0)
					<div class="filter-box">
						<div class="btn-group list_actions hide">
							<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
								{{ trans('messages.actions') }} <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a link-confirm="{{ trans('messages.enable_email_verification_servers_confirm') }}" href="{{ action('EmailVerificationServerController@enable') }}"><i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}</a></li>
								<li><a link-confirm="{{ trans('messages.disable_email_verification_servers_confirm') }}" href="{{ action('EmailVerificationServerController@disable') }}"><i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}</a></li>
								<li><a delete-confirm="{{ trans('messages.delete_email_verification_servers_confirm') }}" href="{{ action('EmailVerificationServerController@delete') }}"><i class="icon-trash"></i> {{ trans('messages.delete') }}</a></li>
							</ul>
						</div>
						<div class="checkbox inline check_all_list">
							<label>
								<input type="checkbox" class="styled check_all">
							</label>
						</div>
						<span class="filter-group">
							<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
							<select class="select" name="sort-order">
								<option value="email_verification_servers.name">{{ trans('messages.name') }}</option>
								<option value="email_verification_servers.created_at">{{ trans('messages.created_at') }}</option>
								<option value="email_verification_servers.updated_at">{{ trans('messages.updated_at') }}</option>
							</select>
							<button class="btn btn-xs sort-direction" rel="asc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
								<i class="icon-sort-amount-asc"></i>
							</button>
						</span>
						<span class="filter-group">
							<span class="title text-semibold text-muted">{{ trans('messages.type') }}</span>
							<select class="select" name="type">
								<option value="">{{ trans('messages.all') }}</option>
								@foreach (Acelle\Model\EmailVerificationServer::typeSelectOptions() as $service)
									<option value="{{ $service['value'] }}">{{ $service['text'] }}</option>
								@endforeach
							</select>
						</span>
						<span class="text-nowrap">
							<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
							<i class="icon-search4 keyword_search_button"></i>
						</span>
					</div>
				@endif
			</div>
			@if (Auth::user()->customer->can('create', new Acelle\Model\EmailVerificationServer()))
				<div class="col-md-2 text-right">
					<a href="{{ action("EmailVerificationServerController@create") }}" type="button" class="btn bg-info-800">
						<i class="icon icon-plus2"></i> {{ trans('messages.create_email_verification_server') }}
					</a>
				</div>
			@endif
		</div>

		<div class="pml-table-container">
		</div>
	</form>
@endsection
