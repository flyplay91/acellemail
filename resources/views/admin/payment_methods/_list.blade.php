@if ($payment_methods->count() > 0)
    <table class="table table-box pml-table row-sortable"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($payment_methods as $key => $payment_method)
            <tr>
                <td width="1%">
                    <div class="text-nowrap">
                        <div class="checkbox inline">
                            <input type="hidden" class="node styled"
                                custom-order="{{ $payment_method->custom_order }}"
                                name="ids[]"
                                value="{{ $payment_method->uid }}"
                            />
                        </div>
                        @if (request()->sort_order == 'custom_order' && empty(request()->keyword))
                            <i data-action="move" class="icon icon-more2 list-drag-button"></i>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <a class="kq_search" href="{{ action('Admin\PaymentMethodController@edit', $payment_method->uid) }}">
                            <span class="no-margin stat-num kq_search">{{ trans('messages.' . $payment_method->type) }}</span>
                        </a>
                        <br />
                        <span class="text-muted">{{ trans('messages.type') }}</span>
                    </div>
                </td>
                <td>
                    <div class="single-stat-box pull-left ml-20">
                        <span class="no-margin stat-num kq_search">{{ Acelle\Library\Tool::formatDateTime($payment_method->updated_at) }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.updated_at') }}</span>
                    </div>
                </td>
                <td>
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-{{ $payment_method->status }}">{{ trans('messages.payment_method_status_' . $payment_method->status) }}</span>
                    </span>
                </td>
                <td class="text-right text-nowrap">
                    @can('update', $payment_method)
                        <a href="{{ action('Admin\PaymentMethodController@edit', ["uid" => $payment_method->uid, "type" => $payment_method->type]) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey btn-icon"><i class="icon-pencil"></i> {{ trans('messages.edit') }}</a>
                    @endcan
                    @if (Auth::user()->can('delete', $payment_method) || Auth::user()->can('disable', $payment_method) || Auth::user()->can('enable', $payment_method))
                        <div class="btn-group">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                @can('enable', $payment_method)
                                    <li>
                                        <a link-confirm="{{ trans('messages.enable_payment_methods_confirm') }}" href="{{ action('Admin\PaymentMethodController@enable', ["uids" => $payment_method->uid]) }}">
                                            <i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('disable', $payment_method)
                                    <li>
                                        <a link-confirm="{{ trans('messages.disable_payment_methods_confirm') }}" href="{{ action('Admin\PaymentMethodController@disable', ["uids" => $payment_method->uid]) }}">
                                            <i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
                                        </a>
                                    </li>
                                @endcan
                                @can('delete', $payment_method)
                                    <li>
                                        <a delete-confirm="{{ trans('messages.delete_payment_methods_confirm') }}" href="{{ action('Admin\PaymentMethodController@delete', ["uids" => $payment_method->uid]) }}">
                                            <i class="icon-trash"></i> {{ trans('messages.delete') }}
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
    @include('elements/_per_page_select', ["items" => $payment_methods])
    {{ $payment_methods->links() }}
@elseif (!empty(request()->keyword) || !empty(request()->filters["type"]))
    <div class="empty-list">
        <i class="icon-credit-card2"></i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="icon-credit-card2"></i>
        <span class="line-1">
            {{ trans('messages.payment_method_empty_line_1') }}
        </span>
    </div>
@endif
