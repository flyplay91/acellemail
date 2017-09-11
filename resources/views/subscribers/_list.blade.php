@if ($total > 0)
	<table class="table table-box pml-table table-sub"
		current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
	>
		@foreach ($subscribers as $key => $item)
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
					<div class="no-margin text-bold">
						<a class="kq_search" href="{{ action('SubscriberController@edit', ['list_uid' => $list->uid ,'uid' => $item->uid]) }}">
							{{ $item->email }}
						</a>
						<br />
						<span class="label label-flat bg-{{ $item->status }}">{{ trans('messages.' . $item->status) }}</span>
						<span class="label label-flat bg-{{ $item->verify_result }}">{{ trans('messages.email_verification_result_' . $item->verify_result) }}</span>
					</div>
				</td>

				@foreach ($fields as $field)
					<?php $value = $item->getValueByField($field); ?>
					<td>
						<span class="no-margin stat-num kq_search">{{ empty($value) ? "--" : $value }}</span>
						<br>
						<span class="text-muted2">{{ $field->label }}</span>
					</td>
				@endforeach

				@if (in_array("created_at", explode(",", request()->columns)))
					<td>
						<span class="no-margin stat-num">{{ Tool::formatDateTime($item->created_at) }}</span>
						<br>
						<span class="text-muted2">{{ trans('messages.created_at') }}</span>
					</td>
				@endif

				@if (in_array("updated_at", explode(",", request()->columns)))
					<td>
						<span class="no-margin stat-num">{{ Tool::formatDateTime($item->updated_at) }}</span>
						<br>
						<span class="text-muted2">{{ trans('messages.updated_at') }}</span>
					</td>
				@endif

				<td class="text-right text-nowrap">
					@if (\Gate::allows('update', $item))
						<a href="{{ action('SubscriberController@edit', ['list_uid' => $list->uid, "uids" => $item->uid]) }}" type="button" class="btn bg-grey btn-icon">
							<i class="icon-pencil"></i>
						</a>
					@endif
					<div class="btn-group">
						<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
						<ul class="dropdown-menu dropdown-menu-right">
							@if (\Gate::allows('subscribe', $item))
								<li><a class="ajax_link" href="{{ action('SubscriberController@subscribe', ['list_uid' => $list->uid, "uids" => $item->uid]) }}"><i class="icon-enter"></i> {{ trans('messages.subscribe') }}</a></li>
							@endif
							@if (\Gate::allows('unsubscribe', $item))
								<li><a class="ajax_link" href="{{ action('SubscriberController@unsubscribe', ['list_uid' => $list->uid, "uids" => $item->uid]) }}"><i class="icon-exit"></i> {{ trans('messages.unsubscribe') }}</a></li>
							@endif

							<li>
								<a href="#copy" class="copy_move_subscriber" data-method="POST"
									data-url="{{ action('SubscriberController@copyMoveForm', [
										'uids' => $item->uid,
										'from_uid' => $list->uid,
										'action' => 'copy',
									]) }}">
										<i class="icon-copy4"></i> {{ trans('messages.copy_to') }}
								</a>
							</li>
							<li>
								<a href="#move" class="copy_move_subscriber" data-method="POST"
									data-url="{{ action('SubscriberController@copyMoveForm', [
										'uids' => $item->uid,
										'from_uid' => $list->uid,
										'action' => 'move',
									]) }}">
									<i class="icon-move-right"></i> {{ trans('messages.move_to') }}
								</a>
							</li>

							@if (\Gate::allows('delete', $item))
								<li><a delete-confirm="{{ trans('messages.delete_subscribers_confirm') }}" href="{{ action('SubscriberController@delete', ['list_uid' => $list->uid, "uids" => $item->uid]) }}"><i class="icon-trash"></i> {{ trans("messages.delete") }}</a></li>
							@endif
						</ul>
					</div>
				</td>

			</tr>
		@endforeach
	</table>
	@include('elements/_per_page_select', ["items" => $subscribers])
	{{ $subscribers->links() }}
@elseif (!empty(request()->keyword))
	<div class="empty-list">
		<i class="icon-users4"></i>
		<span class="line-1">
			{{ trans('messages.no_search_result') }}
		</span>
	</div>
@else
	<div class="empty-list">
		<i class="icon-users4"></i>
		<span class="line-1">
			{{ trans('messages.subscriber_empty_line_1') }}
		</span>
	</div>
@endif
