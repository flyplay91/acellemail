@extends('layouts.frontend')

@section('title', trans('messages.templates'))

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
					<span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.templates') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
				<form class="listing-form"
					sort-url="{{ action('TemplateController@sort') }}"
					data-url="{{ action('TemplateController@listing') }}"
					per-page="{{ Acelle\Model\Template::$itemsPerPage }}"					
				>				
					<div class="row top-list-controls">
						<div class="col-md-9">
							@if ($templates->count() >= 0)					
								<div class="filter-box">
									<div class="btn-group list_actions hide">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a delete-confirm="{{ trans('messages.delete_templates_confirm') }}" href="{{ action('TemplateController@delete') }}">
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
											<option value="custom_order" class="active">{{ trans('messages.custom_order') }}</option>
											<option value="name">{{ trans('messages.name') }}</option>
											<option value="created_at">{{ trans('messages.created_at') }}</option>
										</select>										
										<button class="btn btn-xs sort-direction" rel="asc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
											<i class="icon-sort-amount-asc"></i>
										</button>
									</span>
									<span class="filter-group">
										<span class="title text-semibold text-muted">{{ trans('messages.from') }}</span>
										<select class="select" name="from">
											<option value="all">{{ trans('messages.all') }}</option>
											<option value="mine" selected='selected'>{{ trans('messages.my_templates') }}</option>
											<option value="gallery">{{ trans('messages.gallery') }}</option>
										</select>										
									</span>
									<span class="text-nowrap">
										<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
										<i class="icon-search4 keyword_search_button"></i>
									</span>
								</div>
							@endif
						</div>
						<div class="col-md-3 text-right">
							<a href="{{ action('TemplateController@upload') }}" type="button" class="btn bg-info-800">
								<i class="icon icon-upload"></i> {{ trans('messages.upload') }}
							</a>
							<a href="{{ action('TemplateController@buildSelect') }}" type="button" class="btn bg-info-800">
								<i class="icon icon-plus2"></i> {{ trans('messages.create') }}
							</a>
						</div>
					</div>
					
					<div class="pml-table-container">
						
						
						
					</div>
				</form>
					
@endsection
