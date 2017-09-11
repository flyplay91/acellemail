@extends('layouts.backend')

@section('title', trans('messages.blacklist'))

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
            <span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.blacklist') }}</span>
        </h1>
    </div>
@endsection

@section('content')

    @if (is_object($system_job))
        <div class="alert alert-warning mb-30">
            <button data-dismiss="alert" class="close" type="button"><span>×</span><span class="sr-only">Close</span></button>
            {!! trans('messages.blacklist.an_import_job_is_running', [
                'link' => action('Admin\BlacklistController@import')
            ]) !!}
        </div>
    @endif

    <form class="listing-form"
        data-url="{{ action('Admin\BlacklistController@listing') }}"
        per-page="{{ Acelle\Model\Blacklist::$itemsPerPage }}"
    >

        <div class="row top-list-controls">
            <div class="col-md-9">
                @if ($blacklists->count() >= 0)
                    <div class="filter-box">
                        @include('helpers.select_tool')
                        <div class="btn-group list_actions hide">
                            <button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
                                {{ trans('messages.actions') }} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a delete-confirm="{{ trans('messages.remove_blacklist_confirm') }}" href="{{ action('Admin\BlacklistController@delete') }}"><i class="icon-trash"></i> {{ trans('messages.delete') }}</a></li>
                            </ul>
                        </div>
                        <span class="filter-group">
                            <span class="title text-semibold text-muted">{{ trans('messages.sort_by') }}</span>
                            <select class="select" name="sort-order">
                                <option value="blacklists.created_at">{{ trans('messages.created_at') }}</option>
                                <option value="blacklists.email">{{ trans('messages.email') }}</option>
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
            <div class="col-md-3">
                @if (Auth::user()->admin->can('import', new Acelle\Model\Blacklist()))
                    <div class="text-right">
                        <a href="{{ action('Admin\BlacklistController@import') }}" type="button" class="btn bg-info-800">
                            <i class="icon-download4"></i> {{ trans('messages.blacklist.import') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="pml-table-container">
        </div>
    </form>
@endsection
