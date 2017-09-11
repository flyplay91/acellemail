@extends('layouts.backend')

@section('title', trans('messages.sending_servers'))

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
            <span class="text-semibold"><i class="icon-list2"></i> {{ trans('messages.sending_servers') }}</span>
        </h1>
    </div>

@endsection

@section('content')
    <div class="alert alert-info">
        <p>
            <span class="text-semibold">{{ trans('messages.notification.note') }} </span> {{ trans('messages.notification.sending_servers') }}
        </p>
    </div>
    <p>{{ trans('messages.sending_server.wording') }}</p>


    <form class="listing-form"
        sort-url="{{ action('Admin\SendingServerController@sort') }}"
        data-url="{{ action('Admin\SendingServerController@listing') }}"
        per-page="{{ Acelle\Model\SendingServer::$itemsPerPage }}"
    >
        <div class="row top-list-controls">
            <div class="col-md-10">
                @if ($items->count() >= 0)
                    <div class="filter-box">
                        <div class="btn-group list_actions hide">
                            <button type="button" class="btn btn-xs btn-grey-600 dropdown-toggle" data-toggle="dropdown">
                                {{ trans('messages.actions') }} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a link-confirm="{{ trans('messages.enable_sending_servers_confirm') }}" href="{{ action('Admin\SendingServerController@enable') }}"><i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}</a></li>
                                <li><a link-confirm="{{ trans('messages.disable_sending_servers_confirm') }}" href="{{ action('Admin\SendingServerController@disable') }}"><i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}</a></li>
                                <li><a delete-confirm="{{ trans('messages.delete_sending_servers_confirm') }}" href="{{ action('Admin\SendingServerController@delete') }}"><i class="icon-trash"></i> {{ trans('messages.delete') }}</a></li>
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
                                <option value="sending_servers.name">{{ trans('messages.name') }}</option>
                                <option value="sending_servers.created_at">{{ trans('messages.created_at') }}</option>
                                <option value="sending_servers.updated_at">{{ trans('messages.updated_at') }}</option>
                            </select>
                            <button class="btn btn-xs sort-direction" rel="asc" data-popup="tooltip" title="{{ trans('messages.change_sort_direction') }}" type="button" class="btn btn-xs">
                                <i class="icon-sort-amount-asc"></i>
                            </button>
                        </span>
                        <span class="filter-group">
                            <span class="title text-semibold text-muted">{{ trans('messages.type') }}</span>
                            <select class="select" name="type">
                                <option value="">{{ trans('messages.all') }}</option>
                                @foreach (Acelle\Model\SendingServer::types() as $key => $type)
                                    <option value="{{ $key }}">{{ trans('messages.' . $key) }}</option>
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
            @if (Auth::user()->admin->can('create', new Acelle\Model\SendingServer()))
                <div class="col-md-2 text-right">
                    <a href="{{ action('Admin\SendingServerController@select') }}" type="button" class="btn bg-info-800">
                        <i class="icon icon-plus2"></i> {{ trans('messages.create_sending_server') }}
                    </a>
                </div>
            @endif
        </div>

        <div class="pml-table-container">
        </div>
    </form>


    <!-- Basic modal -->
    <div id="server_types" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header bg-teal">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h2 class="modal-title">{{ trans('messages.select_server_type') }}</h2>
                    </div>

                    <div class="modal-body">

                        <ul class="modern-listing big-icon no-top-border-list mt-0">

                            @foreach (Acelle\Model\SendingServer::types() as $key => $type)

                                <li>
                                    <a href="{{ action('Admin\SendingServerController@create', ["type" => $key]) }}" class="btn btn-info bg-info-800">{{ trans('messages.choose') }}</a>
                                    <span class="server-avatar server-avatar-{{ $key }}">
                                        <i class="icon-server"></i>
                                    </span>
                                    <h4><a href="{{ action('Admin\SendingServerController@create', ["type" => $key]) }}">{{ trans('messages.' . $key) }}</a></h4>
                                    <p>
                                        {{ trans('messages.sending_server_intro_' . $key) }}
                                    </p>
                                </li>

                            @endforeach

                        </ul>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
                    </div>
            </div>
        </div>
    </div>
    <!-- /basic modal -->
@endsection
