                        @if ($segments->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($segments as $key => $item)
									<tr>
										<td width="1%">
											<div class="text-nowrap">
												<div class="checkbox inline">
													<label>
														<input type="checkbox" class="node styled"
															name="ids[]"
															value="{{ $item->uid }}"
														/>
													</label>
												</div>
											</div>
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="{{ action('SegmentController@subscribers', ['list_uid' => $list->uid, 'uid' => $item->uid]) }}">{{ $item->name }}</a>
											</h5>
											<span class="text-muted">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
										</td>
										<td>
											<div class="single-stat-box pull-left">

												<a class="kq_search" href="{{ action('SegmentController@subscribers', ['list_uid' => $list->uid, 'uid' => $item->uid]) }}">
                                                    <span class="no-margin stat-num">{{ number_with_delimiter($item->readCache('SubscriberCount', '#')) }}</span>
                                                </a>
												<br />
												<span class="text-muted">{{ trans("messages.subscribers") }}</span>
											</div>
											<br style="clear:both" />
										</td>

										<td class="text-right text-nowrap">
											@if (\Gate::allows('update', $item))
												<a href="{{ action('SegmentController@edit', ['list_uid' => $list->uid, "uids" => $item->uid]) }}" type="button" class="btn bg-grey btn-icon">
													<i class="icon-pencil"></i> {{ trans('messages.edit') }}
												</a>
											@endif
											<div class="btn-group">
												<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
												<ul class="dropdown-menu dropdown-menu-right">
													@if (\Gate::allows('delete', $item))
														<li><a class="ajax_link" delete-confirm="{{ trans('messages.delete_segments_confirm') }}" href="{{ action('SegmentController@delete', ['list_uid' => $list->uid, "uids" => $item->uid]) }}"><i class="icon-trash"></i> {{ trans("messages.delete") }}</a></li>
													@endif
												</ul>
											</div>
										</td>

									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select', ["items" => $segments])
							{{ $segments->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-make-group"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else
							<div class="empty-list">
								<i class="icon-make-group"></i>
								<span class="line-1">
									{{ trans('messages.segment_empty_line_1') }}
								</span>
							</div>
						@endif
