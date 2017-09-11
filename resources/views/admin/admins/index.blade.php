@extends('layouts.backend')

@section('title', trans('messages.admins'))

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
					<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.admins') }}</span>
				</h1>
			</div>

@endsection

@section('content')

				<form class="listing-form"
					sort-url="{{ action('Admin\AdminController@sort') }}"
					data-url="{{ action('Admin\AdminController@listing') }}"
					per-page="{{ Acelle\Model\Admin::$itemsPerPage }}"
				>
					<div class="row top-list-controls">
						<div class="col-md-10">
							@if ($admins->count() >= 0)
								<div class="filter-box">
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
										<select class="select" name="sort-order">
                                            <option value="admins.created_at">{{ trans('messages.created_at') }}</option>
											<option value="admins.updated_at">{{ trans('messages.updated_at') }}</option>
											<option value="admins.name">{{ trans('messages.name') }}</option>
										</select>
										<button class="btn btn-xs sort-direction" rel="desc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-desc"></i>
										</button>
									</span>
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.group') }}</span>
										<select class="select" name="admin_group_id">
											<option value="">{{ trans('messages.all') }}</option>
											@foreach (Acelle\Model\AdminGroup::getSelectOptions(Auth::user()->admin) as $option)
												<option value="{{ $option["value"] }}">{{ $option["text"] }}</option>
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
						@can('create', new Acelle\Model\Admin())
							<div class="col-md-2 text-right">
								<a href="{{ action("Admin\AdminController@create") }}" type="button" class="btn bg-info-800">
									<i class="icon icon-plus2"></i> {{ trans('messages.create_admin') }}
								</a>
							</div>
						@endcan
					</div>

					<div class="pml-table-container">



					</div>
				</form>
@endsection
