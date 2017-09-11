@if ($currencies->count() > 0)
	<table class="table table-box pml-table"
		current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
	>
		@foreach ($currencies as $key => $item)
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
						<a class="kq_search" href="{{ action('Admin\CurrencyController@edit', $item->uid) }}">{{ $item->name }}</a>
					</h5>
					@if (Auth::user()->can('readAll', $item))
						@include ('admin.modules.admin_line', ['admin' => $item->admin])
						<br />
					@endif
					<span class="text-muted">
						{{ trans('messages.updated_at') }}
						{{ Acelle\Library\Tool::formatDateTime($item->updated_at) }}
					</span>
				</td>
				<td class="stat-fix-size-sm">
					<div class="single-stat-box pull-left">
						<span class="no-margin stat-num kq_search">{{ $item->code }}</span>
						<br />
						<span class="text-muted">{{ trans("messages.code") }}</span>
					</div>
				</td>
				<td class="stat-fix-size-sm">
					<div class="single-stat-box pull-left">
						<span class="no-margin stat-num kq_search">{{ $item->format }}</span>
						<br />
						<span class="text-muted">{{ trans("messages.currency_format") }}</span>
					</div>
				</td>
				<td class="stat-fix-size">
					<span class="text-muted2 list-status pull-left">
						<span class="label label-flat bg-{{ $item->status }}">{{ $item->status }}</span>
					</span>
				</td>
				<td class="text-right text-nowrap" width="5%">
					@can('update', $item)
						<a href="{{ action('Admin\CurrencyController@edit', $item->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-pencil pr-0 mr-0"></i></a>
					@endcan
					@if (Auth::user()->can('delete', $item) || Auth::user()->can('enable', $item) || Auth::user()->can('disable', $item) || Auth::user()->can('delete', $item))
						<div class="btn-group">
							<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
							<ul class="dropdown-menu dropdown-menu-right">
								@can('enable', $item)
									<li>
										<a link-confirm="{{ trans('messages.enable_admins_confirm') }}" href="{{ action('Admin\CurrencyController@enable', ["uids" => $item->uid]) }}">
											<i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
										</a>
									</li>
								@endcan
								@can('disable', $item)
									<li>
										<a link-confirm="{{ trans('messages.disable_admins_confirm') }}" href="{{ action('Admin\CurrencyController@disable', ["uids" => $item->uid]) }}">
											<i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
										</a>
									</li>
								@endcan
								<li>
									<a delete-confirm="{{ trans('messages.delete_currencies_confirm') }}" href="{{ action('Admin\CurrencyController@delete', ['uids' => $item->uid]) }}">
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
	@include('elements/_per_page_select', ["items" => $currencies])
	{{ $currencies->links() }}
@elseif (!empty(request()->filters))
	<div class="empty-list">
		<i class="icon-clipboard2"></i>
		<span class="line-1">
			{{ trans('messages.no_search_result') }}
		</span>
	</div>
@else
	<div class="empty-list">
		<i class="icon-currencies"></i>
		<span class="line-1">
			{{ trans('messages.plan_empty_line_1') }}
		</span>
	</div>
@endif
