                        @if ($items->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($items as $key => $item)
									<tr>
										<td width="1%">
											<div class="text-nowrap">
												<div class="checkbox inline">
													<label>
														<input type="checkbox" class="node styled"
															custom-order="{{ $item->custom_order }}"
															name="ids[]"
															value="{{ $item->uid }}"
														/>
													</label>
												</div>
											</div>
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="{{ action('Admin\BounceHandlerController@edit', $item->uid) }}">{{ $item->name }}</a>
											</h5>
											@if (Auth::user()->can('readAll', $item))
												@include ('admin.modules.admin_line', ['admin' => $item->admin])
												<br />
											@endif
											<span class="text-muted">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
										</td>
										<td>
											<span class="no-margin stat-num kq_search">{{ $item->host }}</span>
											<br />
											<span class="text-muted">{{ trans('messages.host') }}</span>
										</td>
										<td>
											<span class="no-margin stat-num kq_search">{{ $item->username }}</span>
											<br />
											<span class="text-muted">{{ trans('messages.username') }}</span>
										</td>
										<td class="text-right">
											<span class="text-muted2 list-status pull-left">
												<span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.bounce_handler_status_' . $item->status) }}</span>
											</span>
											@can('update', $item)
												<a href="{{ action('Admin\BounceHandlerController@edit', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey btn-icon"><i class="icon-pencil"></i> {{ trans('messages.edit') }}</a>
											@endcan
											@can('delete', $item)
												<div class="btn-group">										
													<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
													<ul class="dropdown-menu dropdown-menu-right">													
														<li>														
															<a delete-confirm="{{ trans('messages.delete_bounce_handlers_confirm') }}" href="{{ action('Admin\BounceHandlerController@delete', ["uids" => $item->uid]) }}">
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
                            @include('elements/_per_page_select', ["items" => $items])
							{{ $items->links() }}
						@elseif (!empty(request()->keyword) || !empty(request()->filters["type"]))
							<div class="empty-list">
								<i class="glyphicon glyphicon-transfer"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else					
							<div class="empty-list">
								<i class="glyphicon glyphicon-transfer"></i>
								<span class="line-1">
									{{ trans('messages.bounce_handler_empty_line_1') }}
								</span>
							</div>
						@endif