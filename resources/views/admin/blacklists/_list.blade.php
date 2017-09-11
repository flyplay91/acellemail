@if ($blacklists->count() > 0)
	<table class="table table-box pml-table table-log mt-10"
		current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
	>
		<tr>
			<th>
				<div class="checkbox inline check_all_list">
					<label>
						<input type="checkbox" class="styled check_all">
					</label>
				</div>
			</th>
			<th>{{ trans('messages.email') }}</th>
			<th>{{ trans('messages.created_at') }}</th>
			<th class="text-right">{{ trans('messages.action') }}</th>
		</tr>
		@foreach ($blacklists as $key => $blacklist)
			<tr>
				<td width="1%">
					<div class="checkbox inline">
						<label>
							<input type="checkbox" class="node styled"
								name="ids[]"
								value="{{ $blacklist->id }}"
							/>
						</label>
					</div>
				</td>
				<td>
					<span class="no-margin kq_search">{{ $blacklist->email }}</span>
					<span class="text-muted second-line-mobile">{{ trans('messages.email') }}</span>
				</td>
				<td>
					<span class="no-margin kq_search">{{ Tool::formatDateTime($blacklist->created_at) }}</span>
					<span class="text-muted second-line-mobile">{{ trans('messages.created_at') }}</span>
				</td>
				<td class="text-right">
					@if (Auth::user()->admin->can('delete', $blacklist))
						<a
							delete-confirm="{{ trans('messages.remove_blacklist_confirm') }}"
							href="{{ action('Admin\BlacklistController@delete', ["uids" => $blacklist->id]) }}"
							class="btn btn-primary btn-xs bg-grey"
							data-popup="tooltip" title="{{ trans('messages.remove_from_blacklist') }}"
						>
							{{ trans('messages.blacklist.remove') }}
						</a>
					@endif
				</td>
			</tr>
		@endforeach
	</table>
	@include('elements/_per_page_select', ["items" => $blacklists])
	{{ $blacklists->links() }}
@elseif (!empty(request()->keyword) || !empty(request()->filters["campaign_uid"]))
	<div class="empty-list">
		<i class="glyphicon glyphicon-minus-sign"></i>
		<span class="line-1">
			{{ trans('messages.no_search_result') }}
		</span>
	</div>
@else
	<div class="empty-list">
		<i class="glyphicon glyphicon-minus-sign"></i>
		<span class="line-1">
			{{ trans('messages.blacklist_empty_line_1') }}
		</span>
	</div>
@endif
