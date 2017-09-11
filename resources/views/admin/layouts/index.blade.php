@extends('layouts.backend')

@section('title', trans('messages.page_form_layout'))

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
					<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.page_form_layout') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
				<form class="listing-form"
					data-url="{{ action('Admin\LayoutController@listing') }}"
					per-page="{{ Acelle\Model\Layout::$itemsPerPage }}"					
				>
					
					<div class="row top-list-controls hide">
						<div class="col-md-9">
							@if ($items->count() >= 0)					
								<div class="filter-box">
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
										<select class="select" name="sort-order">
											<option value="created_at">{{ trans('messages.created_at') }}</option>
										</select>										
										<button class="btn btn-xs sort-direction" rel="asc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-asc"></i>
										</button>
									</span>
								</div>
							@endif
						</div>
						<div class="col-md-3 text-right">
							
						</div>
					</div>
					
					<div class="pml-table-container">
						
						
						
					</div>
				</form>
					
@endsection
