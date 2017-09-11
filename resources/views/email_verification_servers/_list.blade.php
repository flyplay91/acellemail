@if ($servers->count() > 0)
    <table class="table table-box pml-table"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($servers as $key => $server)
            <tr>
                <td width="1%">
                    <div class="text-nowrap">
                        <div class="checkbox inline">
                            <label>
                                <input type="checkbox" class="node styled"
                                    custom-order="{{ $server->custom_order }}"
                                    name="ids[]"
                                    value="{{ $server->uid }}"
                                />
                            </label>
                        </div>
                    </div>
                </td>
                <td>
                    <h5 class="no-margin text-bold">
                        <a class="kq_search" href="{{ action('EmailVerificationServerController@edit', $server->uid) }}">{{ $server->name }}</a>
                    </h5>
                    <span class="text-muted">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($server->created_at) }}</span>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ $server->getTypeName() }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.email_verification_server_type') }}</span>
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="text-muted"><strong>{{ trans('messages.email_verification_server.credits_usage', ['count' => number_with_delimiter($server->getCreditUsage()) ]) }}</strong></span>
                        <br />
                        <span class="text-muted2">{{ trans('messages.sending_server.speed', ['limit' => $server->getSpeedLimitString()]) }}</span>
                    </div>
                </td>
                <td>
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-{{ $server->status }}">{{ trans('messages.email_verification_server_status_' . $server->status) }}</span>
                    </span>
                </td>
                <td class="text-right text-nowrap">
                    @if (Auth::user()->customer->can('update', $server))
                        <a href="{{ action('EmailVerificationServerController@edit', ["uid" => $server->uid]) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey btn-icon"><i class="icon-pencil"></i> {{ trans('messages.edit') }}</a>
                    @endif
                    @if (Auth::user()->customer->can('delete', $server) || Auth::user()->customer->can('disable', $server) || Auth::user()->customer->can('enable', $server))
                        <div class="btn-group">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                @if (Auth::user()->customer->can('enable', $server))
                                    <li>
                                        <a link-confirm="{{ trans('messages.enable_email_verification_servers_confirm') }}" href="{{ action('EmailVerificationServerController@enable', ["uids" => $server->uid]) }}">
                                            <i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::user()->customer->can('disable', $server))
                                    <li>
                                        <a link-confirm="{{ trans('messages.disable_email_verification_servers_confirm') }}" href="{{ action('EmailVerificationServerController@disable', ["uids" => $server->uid]) }}">
                                            <i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
                                        </a>
                                    </li>
                                @endif
                                @if (Auth::user()->customer->can('delete', $server))
                                    <li>
                                        <a delete-confirm="{{ trans('messages.delete_email_verification_servers_confirm') }}" href="{{ action('EmailVerificationServerController@delete', ["uids" => $server->uid]) }}">
                                            <i class="icon-trash"></i> {{ trans('messages.delete') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select')
    {{ $servers->links() }}
@elseif (!empty(request()->keyword) || !empty(request()->filters["type"]))
    <div class="empty-list">
        <i class="icon-server"></i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="icon-server"></i>
        <span class="line-1">
            {{ trans('messages.email_verification_server_empty_line_1') }}
        </span>
    </div>
@endif
