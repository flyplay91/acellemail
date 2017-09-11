@extends('layouts.backend')

@section('title', $customer->displayName())

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("Admin\CustomerController@index") }}">{{ trans('messages.customers') }}</a></li>
					<li class="active">{{ trans('messages.update') }}</li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-profile"></i> {{ $customer->displayName() }}</span>
				</h1>
			</div>

@endsection

@section('content')
	@include('admin.customers._tabs')

    <form class="listing-form"
        sort-url="{{ action('Admin\SubscriptionController@sort') }}"
        data-url="{{ action('Admin\SubscriptionController@listing') }}"
        per-page="{{ Acelle\Model\Subscription::$itemsPerPage }}"
    >
        <input type="hidden" name="customer_uid" value="{{ $customer->uid }}" />
        <div class="row top-list-controls">
            <div class="col-md-11">
                <div class="filter-box">
                    <span class="filter-group">
                        <!--<span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>-->
                        <select class="select" name="sort-order">
                            <option value="subscriptions.created_at">{{ trans('messages.created_at') }}</option>
                            <option value="subscriptions.start_at">{{ trans('messages.start_at') }}</option>
                            <option value="subscriptions.end_at">{{ trans('messages.end_at') }}</option>
                        </select>
                        <button class="btn btn-xs sort-direction" rel="desc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
                            <i class="icon-sort-amount-desc"></i>
                        </button>
                    </span>
					<span class="mr-10 input-medium">
						<select placeholder="{{ trans('messages.plan') }}"
							class="select2-ajax"
							name="plan_uid"
							data-url="{{ action('Admin\PlanController@select2') }}">
						</select>
					</span>
					<span class="filter-group">
						<select class="select" name="status">
							<option value="">{{ trans('messages.all_status') }}</option>
							@foreach (Acelle\Model\Subscription::statusSelectOptions() as $option)
								<option value="{{ $option['value'] }}">
									{{ $option['text'] }}
								</option>
							@endforeach
						</select>
					</span>
                    <span class="filter-group">
                        <select class="select" name="time_status">
                            <option value="">{{ trans('messages.all_times') }}</option>
                            @foreach (Acelle\Model\Subscription::timeStatusSelectOptions() as $option)
                                <option value="{{ $option['value'] }}">
                                    {{ $option['text'] }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                    <span class="filter-group">
                        <select class="select" name="paid_status">
                            <option value="">{{ trans('messages.all_payments') }}</option>
                            @foreach (Acelle\Model\Subscription::paidStatusSelectOptions() as $option)
                                <option value="{{ $option['value'] }}">
                                    {{ $option['text'] }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                    <span class="text-nowrap">
                        <input name="search_keyword" class="form-control search mini-input" placeholder="{{ trans('messages.type_to_search') }}" />
                        <i class="icon-search4 keyword_search_button"></i>
                    </span>
                </div>
            </div>
            @if(Auth::user()->admin->can('create', new Acelle\Model\Subscription()))
                <div class="col-md-1 text-right">
                    <a href="{{ action("Admin\SubscriptionController@create") }}" type="button" class="btn bg-info-800">
                        <i class="icon icon-plus2"></i> {{ trans('messages.create_subscription') }}
                    </a>
                </div>
            @endif
        </div>

        <div class="pml-table-container">



        </div>
    </form>
@endsection
