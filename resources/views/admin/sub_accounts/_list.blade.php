@if ($accounts->count() > 0)
    <table class="table table-box pml-table"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($accounts as $key => $account)
            <tr>
                <td width="1%">
                    <img width="50" class="img-circle mr-10" src="{{ action('CustomerController@avatar', $account->customer->uid) }}" alt="">
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ $account->customer->displayName() }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.customer') }}</span>
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ $account->sendingServer->name }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.' . $account->sendingServer->type) }}</span>
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ $account->username }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.name') }}</span>
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ $account->getSecuredApiKey() }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.sub_account.api_key') }}</span>
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ Tool::formatDateTime($account->created_at) }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.created_at') }}</span>
                    </div>
                </td>
                <td class="text-right text-nowrap">
                    @if (Auth::user()->admin->can('delete', $account))
                        <a href="{{ action('Admin\SubAccountController@delete', $account->uid) }}"
                            data-popup="tooltip" title="{{ trans('messages.subaccount.delete.tooltip') }}"
                            type="button" class="btn btn-danger btn-icon"
                            data-method="delete"
                            list-delete-confirm="{{ action('Admin\SubAccountController@deleteConfirm', $account->uid) }}"
                        >
                                <i class="icon-cross2"></i> {{ trans('messages.subaccount.delete') }}
                        </a>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ['items' => $accounts])
    {{ $accounts->links() }}
@elseif (!empty(request()->keyword) || !empty(request()->filters["type"]))
    <div class="empty-list">
        <i class="icon-drive"></i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="icon-drive"></i>
        <span class="line-1">
            {{ trans('messages.sub_account_empty_line_1') }}
        </span>
    </div>
@endif
