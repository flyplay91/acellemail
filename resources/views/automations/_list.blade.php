                        @if ($automations->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($automations as $key => $automation)
									<tr>
										<td width="1%">
											<div class="text-nowrap">
												<div class="checkbox inline">
													<label>
														<input type="checkbox" class="node styled"
															custom-order="{{ $automation->custom_order }}"
															name="ids[]"
															value="{{ $automation->uid }}"
														/>
													</label>
												</div>
												@if (request()->sort_order == 'custom_order' && empty(request()->keyword))
													<i data-action="move" class="icon icon-more2 list-drag-button"></i>
												@endif
											</div>
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="{{ action('AutomationController@edit', $automation->uid) }}">
													{{ $automation->name }}
												</a>
											</h5>
											@if ($automation->getInitEvent()->displayMessage())
												<span class="text-mutedz">
													<span data-popup="tooltip" title="{{ strip_tags($automation->getInitEvent()->displayMessage()) }}" class="auto-event-substr">{!! $automation->getInitEvent()->displayMessage() !!}</span>
												</span>
											@endif
											<div class="text-semibold" data-popup="tooltip" title="{{ $automation->displayRecipients() }}">
												{{ number_with_delimiter($automation->readCache('SubscriberCount', 0, true)) }} {{ trans('messages.recipients') }}
											</div>
										</td>
										<td>
											<h5 class="no-margin">
												{{ trans('messages.auto_event_' . $automation->getInitEvent()->event_type) }}
											</h5>
											<span class="text-muted2">{{ trans('messages.type') }}</span>
										</td>
										<td>
											<h5 class="no-margin text-teal-800 stat-num">
												{{ $automation->autoEvents()->count() }}
											</h5>
											<span class="text-muted2">{{ trans('messages.triggers') }}</span>
										</td>
										<td>
											<h5 class="no-margin text-teal-800 stat-num">
												{{ $automation->getCampaigns()->count() }}
											</h5>
											<span class="text-muted2">{{ trans('messages.emails') }}</span>
										</td>
										<td>
											<span class="no-margin text-bold">
												{{ Tool::formatDateTime($automation->created_at) }}
											</span>
											<br />
											<span class="text-muted">{{ trans('messages.created_at') }}</span>
										</td>
										<td class="text-center">
											<span class="text-muted2 list-status">
												<span class="label label-flat bg-{{ $automation->status }}">{{ trans('messages.automation_status_' . $automation->status) }}</span>
											</span>
										</td>
										<td class="text-right text-nowrap">
											@if (\Gate::allows('update', $automation))
												<a data-popup="tooltip" title="{{ trans('messages.edit') }}" href="{{ action('AutomationController@edit', $automation->uid) }}" type="button" class="btn bg-grey btn-icon"> <i class="icon-pencil"></i></a>
											@endif
											@if (\Gate::allows('overview', $automation))
												<a data-popup="tooltip" title="{{ trans('messages.overview') }}" href="{{ action('AutomationController@overviewWorkflow', $automation->uid) }}" type="button" class="btn bg-teal-800 btn-icon">
													<i class="icon-stats-growth mr-0 pr-0"></i>
												</a>
											@endif
											@if (\Gate::allows('delete', $automation) || Auth::user()->can('disable', $automation) || Auth::user()->can('enable', $automation))
												<div class="btn-group">
													<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
													<ul class="dropdown-menu dropdown-menu-right">
														@can('enable', $automation)
															<li>
																<a data-method="PATCH" link-confirm="{{ trans('messages.enable_automations_confirm') }}" href="{{ action('AutomationController@enable', ["uids" => $automation->uid]) }}">
																	<i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
																</a>
															</li>
														@endcan
														@can('disable', $automation)
															<li>
																<a data-method="PATCH" link-confirm="{{ trans('messages.disable_automations_confirm') }}" href="{{ action('AutomationController@disable', ["uids" => $automation->uid]) }}">
																	<i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
																</a>
															</li>
														@endcan
														@if (\Gate::allows('delete', $automation))
															<li><a data-method='delete' delete-confirm="{{ trans('messages.delete_automations_confirm') }}" href="{{ action('AutomationController@delete', ["uids" => $automation->uid]) }}"><i class="icon-trash"></i> {{ trans("messages.delete") }}</a></li>
														@endif
													</ul>
												</div>
											@endif
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select', ["items" => $automations])
							{{ $automations->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-paperplane"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else
							<div class="empty-list">
								<i class="icon-alarm-check"></i>
								<span class="line-1">
									{{ trans('messages.automation_empty_line_1') }}
								</span>
							</div>
						@endif
