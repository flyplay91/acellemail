                                    @foreach($campaigns as $key => $campaign)
										<div class="panel panel-flat panel-auto-campaign timeline-content">
											<div class="panel-body">
												<table>
													<tr>
														<td width="1%">
															<i class="icon-envelop auto-campaign-icon"></i>
														</td>
														<td width="30%">
															<h6 class="text-semibold mt-0 mb-0">
																{{ $campaign->name }}
															</h6>
															<span class="text-muted2">{{ trans('messages.updated_at') }}: {{ Tool::formatDateTime($campaign->updated_at) }}</span>
														</td>
														<td width="20%">
															<h6 class="text-semibold mt-0 mb-0">
																{{ trans('messages.' . $campaign->type) }}
															</h6>
															<span class="text-muted">{{ trans('messages.type') }}</span>
														</td>
														<td width="20%">
															<h6 class="text-semibold mt-0 mb-0">
																{{ $campaign->subject ? $campaign->subject : '--' }}
															</h6>
															<span class="text-muted">{{ trans('messages.subject') }}</span>
														</td>
														<td width="20%">
															<h6 class="text-semibold mt-0 mb-0">
																{{ $campaign->from_name ? $campaign->from_name : '--' }}
															</h6>
															<span class="text-muted">{{ trans('messages.from_name') }}</span>
														</td>														
														<td class="text-right text-nowrap">
															<a href="{{ action('AutoEventController@campaignSetup', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]) }}" class="btn btn-info bg-info-800 link-out">
																@if ($campaign->autoCampaignDesigned())
																	<i class="icon-pencil mr-5"></i> {{ trans('messages.edit') }}
																@else
																	<i class="icon-pencil mr-5"></i> {{ trans('messages.design') }}
																@endif
															</a>
															<button
																data-popup="tooltip" title="{{ trans('messages.delete') }}"
																class="btn btn-danger auto-campaign-delete"
																data-confirm="{{ trans('messages.delete_auto_campaign_confirm', ['name' => $campaign->name]) }}"
																data-url="{{ action('AutoEventController@deleteCampaign', $campaign->uid) }}">
																	<i class="icon-trash"></i>
															</button>															
														</td>
													</tr>
												</table>
											</div>
										</div>
									@endforeach