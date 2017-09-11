@extends('layouts.backend')

@section('title', trans('messages.currencies'))

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
					<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.currencies') }}</span>
				</h1>
			</div>

@endsection

@section('content')

				<form class="listing-form"
					sort-url="{{ action('Admin\CurrencyController@sort') }}"
					data-url="{{ action('Admin\CurrencyController@listing') }}"
					per-page="{{ Acelle\Model\Admin::$itemsPerPage }}"
				>
					<div class="row top-list-controls">
						<div class="col-md-10">
							@if ($currencies->count() >= 0)
								<div class="filter-box">
									<div class="btn-group list_actions hide mr-10">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li><a link-confirm="{{ trans('messages.enable_currencies_confirm') }}" href="{{ action('Admin\CurrencyController@enable') }}"><i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}</a></li>
											<li><a link-confirm="{{ trans('messages.disable_currencies_confirm') }}" href="{{ action('Admin\CurrencyController@disable') }}"><i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}</a></li>
											<li>
												<a delete-confirm="{{ trans('messages.delete_currencies_confirm') }}" href="{{ action('Admin\CurrencyController@delete') }}">
													<i class="icon-trash"></i> {{ trans('messages.delete') }}
												</a>
											</li>
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
                                            <option value="currencies.created_at">{{ trans('messages.created_at') }}</option>
											<option value="currencies.name">{{ trans('messages.name') }}</option>
											<option value="currencies.code">{{ trans('messages.code') }}</option>
										</select>
										<button class="btn btn-xs sort-direction" rel="desc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-desc"></i>
										</button>
									</span>
									<span class="text-nowrap">
										<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
										<i class="icon-search4 keyword_search_button"></i>
									</span>
								</div>
							@endif
						</div>
						@can('create', new Acelle\Model\Currency())
							<div class="col-md-2 text-right">
								<a href="{{ action("Admin\CurrencyController@create") }}" type="button" class="btn bg-info-800">
									<i class="icon icon-plus2"></i> {{ trans('messages.create_currency') }}
								</a>
							</div>
						@endcan
					</div>

					<div class="pml-table-container">



					</div>
				</form>
@endsection
