@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.segments'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

			@include("lists._header")

@endsection

@section('content')

				@include("lists._menu")

				<h2 class="text-bold text-teal-800 mb-10"><i class="icon-make-group"></i> {{ trans('messages.segments') }}</h2>
				<br />
				<form class="listing-form"
					data-url="{{ action('SegmentController@listing', $list->uid) }}"
					per-page="{{ Acelle\Model\Segment::$itemsPerPage }}"
				>
					<div class="row top-list-controls">
						<div class="col-md-10">
							@if ($list->segmentsCount() >= 0)
								<div class="filter-box">
									<div class="btn-group list_actions hide mr-10">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a delete-confirm="{{ trans('messages.delete_segments_confirm') }}" href="{{ action('SegmentController@delete', $list->uid) }}">
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
											<option value="segments.name">{{ trans('messages.name') }}</option>
											<option value="segments.created_at">{{ trans('messages.created_at') }}</option>
											<option value="segments.updated_at">{{ trans('messages.updated_at') }}</option>
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
						<div class="col-md-2 text-right">
							<a href="{{ action("SegmentController@create", $list->uid) }}" type="button" class="btn bg-info-800">
								<i class="icon icon-plus2"></i> {{ trans('messages.create_segment') }}
							</a>
						</div>
					</div>

					<div class="pml-table-container">



					</div>
				</form>
@endsection
