@extends('layouts.backend')

@section('title', trans('messages.customer_groups'))

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
					<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.customer_groups') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
				
				<form class="listing-form"
					sort-url="{{ action('Admin\CustomerGroupController@sort') }}"
					data-url="{{ action('Admin\CustomerGroupController@listing') }}"
					per-page="{{ Acelle\Model\CustomerGroup::$itemsPerPage }}"					
				>				
					<div class="row top-list-controls">
						<div class="col-md-10">
							@if ($groups->count() >= 0)					
								<div class="filter-box">											
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
										<select class="select" name="sort-order">
                                            <option value="custom_order">{{ trans('messages.custom_order') }}</option>
											<option value="customer_groups.name">{{ trans('messages.name') }}</option>
                                            <option value="customer_groups.created_at">{{ trans('messages.created_at') }}</option>
										</select>										
										<button class="btn btn-xs sort-direction" rel="asc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-asc"></i>
										</button>
									</span>																
									<span class="text-nowrap">
										<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
										<i class="icon-search4 keyword_search_button"></i>
									</span>
								</div>
							@endif
						</div>
						@can('create', new Acelle\Model\CustomerGroup())
							<div class="col-md-2 text-right">
								<a href="{{ action("Admin\CustomerGroupController@create") }}" type="button" class="btn bg-info-800">
									<i class="icon icon-plus2"></i> {{ trans('messages.create_customer_group') }}
								</a>
							</div>
						@endcan
					</div>
					
					<div class="pml-table-container">
						
						
						
					</div>
				</form>
@endsection
