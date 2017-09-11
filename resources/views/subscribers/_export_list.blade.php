                            @if ($system_jobs->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($system_jobs as $key => $item)
									<?php $data = json_decode($item->data); ?>
									<tr>
										<td width="1%">
											<i class="icon-download4 icon-list-big"></i>
										</td>
										<td>
											<h5 class="no-margin text-bold">
												{{ trans('messages.export_subscribers') }}
											</h5>
											
											<span class=""><i class="icon-alarm"></i> {{ $item->runTime() }}</span>
											<br />
											
											<span class="text-muted">{{ trans('messages.run_at') }}: {{ Tool::formatDateTime($item->created_at) }}</span>
											@if ($data->status == 'done')
												<br />
												<span class="text-muted">{{ trans('messages.finished_at') }}: {{ Tool::formatDateTime($item->updated_at) }}</span>
											@endif
											
										</td>
										<td>
											<div class="single-stat-box">                                                
												<span class="no-margin text-teal-800 stat-num">{{ $data->percent }}%</span>
												<div class="progress progress-xxs">
													<div class="progress-bar progress-bar-info" style="width: {{ $data->percent }}%">
													</div>
												</div>
												<span class="no-margin">{!! \Acelle\Helpers\ExportSubscribersHelper::getMessage($item) !!}</span>
											</div>
											<br style="clear:both" />
										</td>
										<td class="text-center">
											<span class="text-muted2 list-status">
												@if ($item->status == 'cancelled')
													<span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.system_job_status_' . $item->status) }}</span>
												@else
													<span class="label label-flat bg-{{ $data->status }}">{{ trans('messages.system_job_status_' . $item->status) }}</span>
												@endif
											</span>
										</td>
										<td class="text-right text-nowrap">
											@if (\Gate::allows('downloadExportCsv', $item))
												<a target="_blank" href="{{ action('SystemJobController@downloadCsv', $item->id) }}" type="button" class="btn bg-teal">
													<i class="icon-download mr-5"></i> {{ trans('messages.download_csv') }}
												</a>
											@endif
											@if (\Gate::allows('cancel', $item))
												<a data-method="POST" link-confirm="{{ trans('messages.cancel_system_jobs_confirm') }}" href="{{ action('SystemJobController@cancel', ["uids" => $item->id]) }}" type="button" class="btn bg-grey btn-icon">
													{{ trans('messages.cancel') }}
												</a>
											@endif
											@if (\Gate::allows('delete', $item))
												<a delete-confirm="{{ trans('messages.delete_system_jobs_confirm') }}" href="{{ action('SystemJobController@delete', ["uids" => $item->id]) }}" type="button" class="btn bg-grey btn-icon">
													<i class="icon-cross2"></i>
												</a>
											@endif											
										</td>
										
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select', ["items" => $system_jobs])
							{{ $system_jobs->links() }}
						@elseif (!empty(request()->keyword))
							<div class="empty-list">
								<i class="icon-make-group"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else					
							<div class="empty-list">
								<i class="icon-task"></i>
								<span class="line-1">
									{{ trans('messages.job_empty_line_1') }}
								</span>
							</div>
						@endif
