                        @if ($templates->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($templates as $key => $item)
									<tr>
										<td width="1%">
											<a href="#"  onclick="popupwindow('{{ action('TemplateController@preview', $item->uid) }}', '{{ $item->name }}', 800, 800)">
												<img class="template-thumb mr-10" width="100" height="120" src="{{ action('TemplateController@image', $item->uid) }}?v={{ rand(0,10) }}" />
											</a>
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="#" onclick="popupwindow('{{ action('TemplateController@preview', $item->uid) }}', '{{ $item->name }}', 800, 800)">
													{{ $item->name }}
												</a>
											</h5>
											<span class="text-muted">
												{!! is_object($item->admin) ? '<i class="icon-user-tie"></i>' . $item->admin->displayName() : '' !!}
												{!! is_object($item->customer) ? '<i class="icon-user"></i>' . $item->customer->displayName() : '' !!}
											</span>
											<br />
											<span class="text-muted">{{ trans('messages.updated_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
										</td>

										<td>
											<div class="single-stat-box pull-left">
												<span class="no-margin stat-num">{{ trans('messages.template_type_' . $item->source) }}</span>
												<br>
												<span class="text-muted text-nowrap">{{ trans('messages.type') }}</span>
											</div>
										</td>

										<td class="text-right">
											@if (is_object($campaign->autoEvent()))
												<a href="{{ action('AutoEventController@templateChoose', ['uid' => $campaign->autoEvent()->uid, 'campaign_uid' => $campaign->uid, 'template_uid' => $item->uid]) }}" type="button" class="btn bg-teal btn-icon"> <i class="icon-checkmark4"></i> {{ trans('messages.choose') }}</a>
											@else
												<a href="{{ action('CampaignController@templateChoose', ['uid' => $campaign->uid, 'template_uid' => $item->uid]) }}" type="button" class="btn bg-teal btn-icon"> <i class="icon-checkmark4"></i> {{ trans('messages.choose') }}</a>
											@endif
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select')
							{{ $templates->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-magazine"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else
							<div class="empty-list">
								<i class="icon-magazine"></i>
								<span class="line-1">
									{{ trans('messages.template_empty_line_1') }}
								</span>
							</div>
						@endif
