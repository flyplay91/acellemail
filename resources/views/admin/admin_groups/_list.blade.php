@if ($groups->count() > 0)
    <table class="table table-box pml-table"
        current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
    >
        @foreach ($groups as $key => $item)
            <tr>
                <td width="1%">
                    <div class="text-nowrap">
                        @if (request()->sort_order == 'custom_order' && empty(request()->keyword))
                            <input type="hidden" class="node styled"
                                    custom-order="{{ $item->custom_order }}"
                                    name="ids[]"
                                    value="{{ $item->id }}"
                                />
                            <i data-action="move" class="icon icon-more2 list-drag-button"></i>
                        @endif
                    </div>
                </td>
                <td>
                    <h5 class="no-margin text-bold">
                        <a class="kq_search" href="{{ action('Admin\AdminGroupController@edit', $item->id) }}">{{ $item->name }}</a>
                    </h5>
                    @if (Auth::user()->can('readAll', $item) && $item->creator)
                        @include ('admin.modules.admin_line', ['admin' => $item->creator->admin])
                        <br />
                    @endif
                    <span class="text-muted">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
                </td>
                <td>
                    <div class="single-stat-box pull-left">
                        <span class="no-margin stat-num">{{ $item->admins()->count() }}</span>
                        <br />
                        <span class="text-muted">{{ trans("messages.admins") }}</span>
                    </div>
                </td>
                <td class="text-right">
                    <span class="text-muted2 list-status pull-left">
                        <span class="label label-flat bg-{{ $item->status }}">{{ $item->status }}</span>
                    </span>
                    @can('update', $item)
                        <a href="{{ action('Admin\AdminGroupController@edit', $item->id) }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-pencil"></i> {{ trans('messages.edit') }}</a>
                    @endcan
                    @can('delete', $item)
                        <div class="btn-group">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a delete-confirm="{{ trans('messages.delete_admin_groups_confirm') }}" href="{{ action('Admin\AdminGroupController@delete', ['ids' => $item->id]) }}">
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
    @include('elements/_per_page_select', ["items" => $groups])
    {{ $groups->links() }}
@elseif (!empty(request()->keyword))
    <div class="empty-list">
        <i class="icon-users4"></i>
        <span class="line-1">
            {{ trans('messages.no_search_result') }}
        </span>
    </div>
@else
    <div class="empty-list">
        <i class="icon-users4"></i>
        <span class="line-1">
            {{ trans('messages.admin_group_empty_line_1') }}
        </span>
    </div>
@endif
