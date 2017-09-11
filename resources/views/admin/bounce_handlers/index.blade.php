@extends('layouts.backend')

@section('title', trans('messages.bounce_handlers'))

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
					<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.bounce_handlers') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
				
				<form class="listing-form"
					sort-url="{{ action('Admin\BounceHandlerController@sort') }}"
					data-url="{{ action('Admin\BounceHandlerController@listing') }}"
					per-page="{{ Acelle\Model\BounceHandler::$itemsPerPage }}"					
				>				
					<div class="row top-list-controls">
						<div class="col-md-9">
							@if ($items->count() >= 0)					
								<div class="filter-box">
									<div class="btn-group list_actions hide">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li><a delete-confirm="{{ trans('messages.delete_bounce_handlers_confirm') }}" href="{{ action('Admin\BounceHandlerController@delete') }}"><i class="icon-trash"></i> {{ trans('messages.delete') }}</a></li>
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
											<option value="bounce_handlers.name">{{ trans('messages.name') }}</option>
                                            <option value="bounce_handlers.created_at">{{ trans('messages.created_at') }}</option>
											<option value="bounce_handlers.updated_at">{{ trans('messages.updated_at') }}</option>
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
						@can('create', new Acelle\Model\BounceHandler())
							<div class="col-md-3 text-right">
								<a href="{{ action('Admin\BounceHandlerController@create') }}" type="button" class="btn bg-info-800">
									<i class="icon icon-plus2"></i> {{ trans('messages.create_bounce_handler') }}
								</a>
							</div>
						@endcan
					</div>
					
					<div class="pml-table-container">
						
						
						
					</div>
				</form>				
@endsection
