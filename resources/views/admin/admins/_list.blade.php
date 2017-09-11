                        @if ($admins->count() > 0)
							<table class="table table-box pml-table"
                                current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
                            >
								@foreach ($admins as $key => $item)
									<tr>
										<td width="1%">
											<img width="80" class="img-circle mr-10" src="{{ action('AdminController@avatar', $item->uid) }}" alt="">
										</td>
										<td>
											<h5 class="no-margin text-bold">
												<a class="kq_search" href="{{ action('Admin\AdminController@edit', $item->uid) }}">{{ $item->displayName() }}</a>
											</h5>
											<span class="text-muted kq_search">{{ $item->user->email }}</span>
											@if (Auth::user()->can('readAll', $item) && $item->creator)
												<br />
												@include ('admin.modules.admin_line', ['admin' => $item->creator->admin])
											@endif
										</td>
										<td>
											<h5 class="no-margin text-bold kq_search">
												@if (Auth::user()->admin->getPermission("admin_group_update") != 'no')
													<a class="kq_search" href="{{ action('Admin\AdminGroupController@edit', $item->adminGroup->id) }}">
														{{ $item->adminGroup->name }}
													</a>
												@else
													{{ $item->adminGroup->name }}
												@endif
											</h5>
											<span class="text-muted">{{ trans('messages.admin_group') }}</span>
										</td>
										<td class="stat-fix-size-sm">
											<div class="single-stat-box pull-left">
												<a href="{{ action('Admin\CustomerController@index') }}">
													<span class="no-margin stat-num">{{ $item->customers()->count() }}</span>
												</a>
												<br />
												<span class="text-muted">{{ trans("messages." . Acelle\Library\Tool::getPluralPrase('customer', $item->customers()->count())) }}</span>
											</div>
										</td>
										<td>
											<h5 class="no-margin text-bold kq_search">
												{{ Tool::formatDateTime($item->created_at) }}
											</h5>
											<span class="text-muted">{{ trans('messages.created_at') }}</span>
										</td>
										<td class="stat-fix-size">
											<span class="text-muted2 list-status pull-left">
												<span class="label label-flat bg-{{ $item->status }}">{{ $item->status }}</span>
											</span>
										</td>
										<td class="text-right">
											@can('loginAs', $item)
												<a href="{{ action('Admin\AdminController@loginAs', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.login_as_this_admin') }}" type="button" class="btn bg-teal-600 btn-icon"><i class="glyphicon glyphicon-random pr-5"></i></a>
											@endcan
											@can('update', $item)
												<a href="{{ action('Admin\AdminController@edit', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-pencil pr-0 mr-0"></i></a>
											@endcan
											@if (Auth::user()->can('delete', $item) || Auth::user()->can('enable', $item) || Auth::user()->can('disable', $item) || Auth::user()->can('delete', $item))
												<div class="btn-group">
													<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
													<ul class="dropdown-menu dropdown-menu-right">
														@can('enable', $item)
															<li>
																<a link-confirm="{{ trans('messages.enable_admins_confirm') }}" href="{{ action('Admin\AdminController@enable', ["uids" => $item->uid]) }}">
																	<i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
																</a>
															</li>
														@endcan
														@can('disable', $item)
															<li>
																<a link-confirm="{{ trans('messages.disable_admins_confirm') }}" href="{{ action('Admin\AdminController@disable', ["uids" => $item->uid]) }}">
																	<i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
																</a>
															</li>
														@endcan
														@can('delete', $item)
															<li>
																<a delete-confirm="{{ trans('messages.delete_admins_confirm') }}" href="{{ action('Admin\AdminController@delete', ['uids' => $item->uid]) }}">
																	<i class="icon-trash"></i> {{ trans('messages.delete') }}
																</a>
															</li>
														@endcan
													</ul>
												</div>
											@endcan
										</td>
									</tr>
								@endforeach
							</table>
                            @include('elements/_per_page_select', ["items" => $admins])
							{{ $admins->links() }}
						@elseif (!empty(request()->filters))
							<div class="empty-list">
								<i class="icon-users"></i>
								<span class="line-1">
									{{ trans('messages.no_search_result') }}
								</span>
							</div>
						@else
							<div class="empty-list">
								<i class="icon-users"></i>
								<span class="line-1">
									{{ trans('messages.admin_empty_line_1') }}
								</span>
							</div>
						@endif
