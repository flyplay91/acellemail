@if ($customers->count() > 0)
    <table class="table table-box pml-table"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($customers as $key => $item)
            <tr>
                <td width="1%">
                    <img width="80" class="img-circle mr-10" src="{{ action('CustomerController@avatar', $item->uid) }}" alt="">
                </td>
                <td>
                    <h5 class="no-margin text-bold">
                        <a class="kq_search" href="{{ action('Admin\CustomerController@edit', $item->uid) }}">{{ $item->displayName() }}</a>
                    </h5>
                    <span class="text-muted kq_search">{{ $item->user->email }}</span>
                    @can('readAll', $item)
                        <br />
                        @include ('admin.modules.admin_line', ['admin' => $item->admin])
                    @endcan
                    <br />
                    <span class="text-muted2">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
                </td>
                <td>
                    <h5 class="no-margin">
                        <span><i class="icon-clipboard2"></i> {{ $item->currentPlanName() }}</span>
                    </h5>
                    <span class="text-muted2">{{ trans('messages.current_plan') }}</span>
                </td>
                <td class="stat-fix-size">
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin text-teal-800 stat-num">{{ $item->displaySendingQuotaUsage() }}</span>
                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-info" style="width: {{ $item->getSendingQuotaUsagePercentage() }}%">
                            </div>
                        </div>
                        <span class="text-muted"><strong>{{ \Acelle\Library\Tool::format_number($item->getSendingQuotaUsage()) }}/{{ ($item->getSendingQuota() == -1) ? '∞' : \Acelle\Library\Tool::format_number($item->getSendingQuota()) }}</strong>
                            <div class="text-nowrap">{{ trans('messages.sending_credits_used') }}</div>
                        </span>
                    </div>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin text-teal-800 stat-num">{{ $item->displaySubscribersUsage() }}</span>
                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-info" style="width: {{ $item->readCache('SubscriberUsage') }}%">
                            </div>
                        </div>
                        <span class="text-muted"><strong>{{ number_with_delimiter($item->readCache('SubscriberCount')) }}/{{ number_with_delimiter($item->maxSubscribers()) }}</strong><br /> {{ trans('messages.subscribers') }}</span>
                    </div>
                </td>
                <td>
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.user_status_' . $item->status) }}</span>
                    </span>
                </td>
                <td class="text-right">
                    @can('loginAs', $item)
                        <a href="{{ action('Admin\CustomerController@loginAs', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.login_as_this_customer') }}" type="button" class="btn bg-teal-600 btn-icon"><i class="glyphicon glyphicon-random pr-5"></i></a>
                    @endcan
                    @can('update', $item)
                        <a href="{{ action('Admin\CustomerController@edit', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-pencil pr-0 mr-0"></i></a>
                    @endcan
                    @if (Auth::user()->can('delete', $item) || Auth::user()->can('enable', $item) || Auth::user()->can('disable', $item))
                        <div class="btn-group">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                @can('enable', $item)
                                    <li>
                                        <a link-confirm="{{ trans('messages.enable_customers_confirm') }}" href="{{ action('Admin\CustomerController@enable', ["uids" => $item->uid]) }}">
                                            <i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('disable', $item)
                                    <li>
                                        <a link-confirm="{{ trans('messages.disable_customers_confirm') }}" href="{{ action('Admin\CustomerController@disable', ["uids" => $item->uid]) }}">
                                            <i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('read', $item)
                                    <li>
                                        <a href="{{ action('Admin\CustomerController@subscriptions', $item->uid) }}">
                                            <i class="icon-quill4"></i> {{ trans('messages.subscriptions') }}
                                        </a>
                                    </li>
                                @endcan
                                <li>
                                    <a delete-confirm="{{ trans('messages.delete_users_confirm') }}" href="{{ action('Admin\CustomerController@delete', ['uids' => $item->uid]) }}">
                                        <i class="icon-trash"></i> {{ trans('messages.delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ["items" => $customers])
    {{ $customers->links() }}
@elseif (!empty(request()->keyword))
    <div class="empty-list">
        <i class="icon-users"></i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="icon-users"></i>
        <span class="line-1">
            {{ trans('messages.customer_empty_line_1') }}
        </span>
    </div>
@endif
