                        @if ($items->count() > 0)
                            <table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
                                @foreach ($items as $key => $item)
                                    <tr>
                                        <td width="1%">
                                            <i class="icon-{{ $item->type == 'page' ? "file-openoffice" : "mail-read" }} table-icon-big"></i>
                                        </td>
                                        <td>
                                            <h5 class="no-margin text-bold">
                                                <a href="{{ action('Admin\LayoutController@edit', $item->uid) }}">
                                                    {{ trans('messages.' . $item->alias) }}
                                                </a>
                                            </h5>
                                            <p>{{ $item->group_name }}</p>
                                        </td>
                                        <td>
                                            <div class="single-stat-box pull-left">
                                                <span class="no-margin stat-num">{{ trans('messages.' . $item->type) }}</span>
                                                <br />
                                                <span class="text-muted2">{{ trans("messages.display_type") }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="single-stat-box pull-left">
                                                <span class="no-margin stat-num">{{ $item->pages()->count() }}</span>
                                                <br />
                                                <span class="text-muted2">{{ trans("messages.custom_pages") }}</span>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            @can('update', $item)
                                                <a href="{{ action('Admin\LayoutController@edit', $item->uid) }}" type="button" class="btn bg-info-800 btn-icon"> <i class="icon-pencil"></i> {{ trans('messages.edit') }}</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            @include('elements/_per_page_select')
                            {{ $items->links() }}
                        @elseif (!empty(request()->keyword))
                            <div class="empty-list">
                                <i class="glyphicon glyphicon-file"></i>
                                <span class="line-1">
                                    {{ trans('messages.no_search_result') }}
                                </span>
                            </div>
                        @endif
