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
												<a class="kq_search" href="{{ action('SendingDomainController@edit', $item->uid) }}">{{ $item->name }}</a>
											</h5>
											<span class="text-muted">{{ trans('messages.created_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
										</td>
										<td class="text-center">
											<div class="single-stat-box pull-left">
												<i class="table-checkmark-{{ $item->signing_enabled }}"></i>
												<br />
												<span class="text-muted">{{ trans("messages.signing_enabled") }}</span>
											</div>
										</td>
										<td class="text-right">
											<span class="text-muted2 list-status pull-left">
												<span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.sending_domain_status_' . $item->status) }}</span>
											</span>
											@if (Auth::user()->customer->can('update', $item))
												<a href="{{ action('SendingDomainController@edit', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey btn-icon"><i class="icon-pencil"></i> {{ trans('messages.edit') }}</a>
											@endif
											@if (Auth::user()->customer->can('delete', $item))
												<div class="btn-group">
													<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
													<ul class="dropdown-menu dropdown-menu-right">
														<li>
															<a delete-confirm="{{ trans('messages.delete_sending_domains_confirm') }}" href="{{ action('SendingDomainController@delete', ["uids" => $item->uid]) }}">
																<i class="icon-trash"></i> {{ trans('messages.delete') }}
															</a>
														</li>
													</ul>
												</div>
											@endif
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select')
							{{ $items->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-earth"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else
							<div class="empty-list">
								<i class="icon-earth"></i>
								<span class="line-1">
									{{ trans('messages.sending_domain_empty_line_1') }}
								</span>
							</div>
						@endif
