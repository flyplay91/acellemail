@extends('layouts.frontend')

@section('title', $campaign->name)

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

			@include("campaigns._header")

@endsection

@section('content')

      @include("campaigns._menu")

			<h2 class="text-semibold text-teal-800">{{ trans('messages.subscribers') }}</h2>

			<form class="listing-form"
					data-url="{{ action('CampaignController@subscribersListing', $campaign->uid) }}"
					per-page="{{ Acelle\Model\Subscriber::$itemsPerPage }}"
				>
					<div class="row top-list-controls">
						<div class="col-md-12">
							@if ($subscribers->count() >= 0)
								<div class="filter-box">
									@include('helpers.select_tool', [
											'disable_all_items' => true
									])
									<div class="btn-group list_actions hide mr-10">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.actions') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<a link-confirm="{{ trans('messages.subscribe_subscribers_confirm') }}" href="{{ action('SubscriberController@subscribe', $list->uid) }}">
													<i class="icon-enter"></i> {{ trans('messages.subscribe') }}
												</a>
											</li>
											<li>
												<a link-confirm="{{ trans('messages.unsubscribe_subscribers_confirm') }}" href="{{ action('SubscriberController@unsubscribe', $list->uid) }}">
													<i class="icon-exit"></i> {{ trans('messages.unsubscribe') }}
												</a>
											</li>
											<li>
												<a href="#" class="copy_move_subscriber"
													data-url="{{ action('SubscriberController@copyMoveForm', [
														'from_uid' => $list->uid,
														'action' => 'copy',
													]) }}">
														<i class="icon-copy4"></i> {{ trans('messages.copy_to') }}
												</a>
											</li>
											<!--<li>
												<a href="#move" class="copy_move_subscriber"
													data-url="{{ action('SubscriberController@copyMoveForm', [
														'from_uid' => $list->uid,
														'action' => 'move',
													]) }}">
													<i class="icon-move-right"></i> {{ trans('messages.move_to') }}
												</a>
											</li>-->
											<li>
												<a delete-confirm="{{ trans('messages.delete_subscribers_confirm') }}" href="{{ action('SubscriberController@delete', $list->uid) }}">
													<i class="icon-trash"></i> {{ trans('messages.delete') }}
												</a>
											</li>
										</ul>
									</div>
									<div class="btn-group list_columns ml-10">
										<button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
											{{ trans('messages.columns') }} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu dropdown-menu-right">
											@foreach ($list->getFields as $field)
												@if ($field->tag != "EMAIL")
													<li>
														<div class="checkbox">
															<label>
																<input {{ ($field->required ? "checked='checked'" : "") }} type="checkbox" id="{{ $field->tag }}" name="columns[]" value="{{ $field->uid }}" class="styled">
																{{ $field->label }}
															</label>
														</div>
													</li>
												@endif
											@endforeach
											<li>
												<div class="checkbox">
													<label>
														<input type="checkbox" id="created_at" name="columns[]" value="created_at" class="styled">
														{{ trans('messages.created_at') }}
													</label>
												</div>
											</li>
											<li>
												<div class="checkbox">
													<label>
														<input type="checkbox" id="updated_at" name="columns[]" value="updated_at" class="styled">
														{{ trans('messages.updated_at') }}
													</label>
												</div>
											</li>
										</ul>
									</div>
									<span class="filter-group ml-20">
										<span class="title text-semibold text-muted">{{ trans('messages.subscribers_who') }}</span>
										<select class="select" name="open">
											<option value="">-- {{ trans('messages.open') }} --</option>
											<option value="opened">{{ trans('messages.opened') }}</option>
											<option {{ (request()->open == 'not_opened' ? "selected" : '') }} value="not_opened">{{ trans('messages.not_opened') }}</option>
										</select>
										<!--<span class="small-select2">
											<select class="select" name="and_or">
												<option value="and">{{ trans('messages.and') }}</option>
												<option value="or">{{ trans('messages.or') }}</option>
											</select>
										</span>-->
										<select class="select" name="click">
											<option value="">-- {{ trans('messages.click') }} --</option>
											<option value="clicked">{{ trans('messages.clicked') }}</option>
											<option value="not_clicked">{{ trans('messages.not_clicked') }}</option>
										</select>
									</span>
									<span class="filter-group mr-20">
										<span class="title text-semibold text-muted">{{ trans('messages.tracking_status') }}</span>
										<select class="select" name="tracking_status">
											<option value="">-- {{ trans('messages.all') }} --</option>
											<!--<option value="not_sent">{{ trans('messages.not_sent') }}</option>-->
											<option value="error">{{ trans('messages.error') }}</option>
											<option value="sent">{{ trans('messages.sent') }}</option>
										</select>
									</span>
									<span class="text-nowrap">
										<input name="search_keyword" class="form-control search" placeholder="{{ trans('messages.type_to_search') }}" />
										<i class="icon-search4 keyword_search_button"></i>
									</span>
								</div>
							@endif
						</div>
					</div>

					<div class="pml-table-container">


					</div>
				</form>
@endsection
