                        @if ($logs->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($logs as $key => $item)
									<tr>
                                        <td width="1%">
											<img width="50" class="img-circle mr-10" src="{{ action('CustomerController@avatar', $item->customer->uid) }}" alt="">
										</td>
										<td>
											<p class="mb-0">                                                
                                                {!! $item->message() !!}<br />
												<div class="text-muted2">{{ trans('messages.' . $item->type) }}</div>
                                            </p>
										</td>
										<td>
											<div class="pull-right">
												<div class="text-semibold">{{ $item->created_at->diffForHumans() }}</div>
												<span class="text-muted2">{{ Acelle\Library\Tool::formatDateTime($item->created_at) }}</span>
											</div>
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select', ["items" => $logs])
							{{ $logs->links() }}                            
						@elseif (!empty(request()->keyword) || !empty(request()->filters["type"]))
							<div class="empty-list">
								<i class="icon-history"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else					
							<div class="empty-list">
								<i class="icon-history"></i>
								<span class="line-1">
									{{ trans('messages.no_action_logs') }}
								</span>
							</div>
						@endif