@if ($subscriptions->count() > 0)
	<table class="table table-box pml-table table-log"
		current-page="{{ empty(request()->page) ? 1 : empty(request()->page) }}"
	>
		@foreach ($subscriptions as $key => $subscription)
			<tr>
				<td width="1%">
					<img width="80" class="img-circle mr-10" src="{{ action('CustomerController@avatar', $subscription->customer->uid) }}" alt="">
				</td>
				<td>
					<h5 class="no-margin text-bold">
						<a class="kq_search" href="{{ action('Admin\SubscriptionController@edit', $subscription->uid) }}">
							{{ $subscription->customer->displayName() }}
						</a>
					</h5>
					<span class="text-muted kq_search">{{ $subscription->customer->user->email }}</span>
					<br />
					<span><i class="icon-clipboard2"></i> {{ $subscription->plan->name }}</span>
					@if (Auth::user()->admin->can('readAll', $subscription))
						<br />
						@include ('admin.modules.admin_line', ['admin' => $subscription->customer->admin])
					@endif
				</td>
				<td>
					<h5 class="no-margin text-bold">
						{{ Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format) }}
					</h5>
					<span class="text-muted">{{ trans('messages.price') }}</span>
				</td>
                @if (!$subscription->isTimeUnlimited())
                    <td>
                        <h5 class="no-margin text-bold">
                            {{ Acelle\Library\Tool::formatDate($subscription->start_at) }}
                        </h5>
                        <span class="text-muted">{{ trans('messages.start_at') }}</span>
                    </td>
                    <td>
                        <h5 class="no-margin text-bold">
                            {{ Acelle\Library\Tool::formatDate($subscription->end_at) }}
                        </h5>
                        <span class="text-muted">{{ trans('messages.end_at') }}</span>
                    </td>
                    <td>
                        @if ($subscription->beingUsed())
                            <h5 class="no-margin text-bold">
                                {{ $subscription->daysRemainCount() }}
                            </h5>
                            <span class="text-muted">{!! trans_choice('messages.days_remain', $subscription->daysRemainCount()) !!}</span>
                        @else
                            <h5 class="no-margin text-bold">
                                --
                            </h5>
                            <span class="text-muted">{!! trans_choice('messages.days_remain', 0) !!}</span>
                        @endif
                    </td>
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
                <td>
					<span class="text-muted2 list-status pull-left">
						<span class="label label-flat bg-{{ $subscription->getStatus() }}">{{ trans('messages.subscription_status_' . $subscription->getStatus()) }}</span>
					</span>
				</td>
				<td>
                    @if ($subscription->paymentsCount())
                        <span class="text-muted2 list-status pull-left payments-button"
                            data-url="{{ action('Admin\SubscriptionController@payments', $subscription->uid) }}">
                            <span class="label label-sub label-flat bg-{{ $subscription->getPaidStatus() }}">
                                {{ trans('messages.subscription_paid_status_' . $subscription->getPaidStatus()) }}
                                <i class="icon-history"></i>
                            </span>
                        </span>
                    @else
                        <span class="text-muted2 list-status pull-left">
                            <span class="label label-sub label-flat bg-{{ $subscription->getPaidStatus() }}">
                                {{ trans('messages.subscription_paid_status_' . $subscription->getPaidStatus()) }}
                            </span>
                        </span>
                    @endif
				</td>
				<td class="text-right">
					@if (Auth::user()->admin->can('update', $subscription))
						<a href="{{ action('Admin\SubscriptionController@edit', $subscription->uid) }}" data-popup="tooltip" title="{{ trans('messages.edit') }}" type="button" class="btn bg-grey-600 btn-icon"><i class="icon icon-pencil pr-0 mr-0"></i></a>
					@endif
					@if (\Auth::user()->admin->can('delete', $subscription) || Auth::user()->admin->can('enable', $subscription) || Auth::user()->admin->can('disable', $subscription))
						<div class="btn-group">
							<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret ml-0"></span></button>
							<ul class="dropdown-menu dropdown-menu-right">
								@if (Auth::user()->admin->can('enable', $subscription))
									<li>
										<a data-method='PATCH' link-confirm="{{ trans('messages.enable_subscriptions_confirm') }}" href="{{ action('Admin\SubscriptionController@enable', ["uids" => $subscription->uid]) }}">
											<i class="icon-checkbox-checked2"></i> {{ trans('messages.enable') }}
										</a>
									</li>
								@endif
								@if (Auth::user()->admin->can('disable', $subscription))
									<li>
										<a data-method='PATCH' link-confirm="{{ trans('messages.disable_subscriptions_confirm') }}" href="{{ action('Admin\SubscriptionController@disable', ["uids" => $subscription->uid]) }}">
											<i class="icon-checkbox-unchecked2"></i> {{ trans('messages.disable') }}
										</a>
									</li>
								@endif
								@if (Auth::user()->admin->can('paid', $subscription))
									<li>
										<a
											message="{{ trans('messages.paid_subscriptions_confirm') }}" data-toggle="modal" data-target="#paid-form-modal" href="#paid"
											class="list-form-button"
											data-uids="{{ $subscription->uid }}"
											data-method="PATCH"
											data-url="{{ action('Admin\SubscriptionController@paid') }}"
										>
											<i class="icon-checkmark-circle"></i> {{ trans('messages.paid') }}
										</a>
									</li>
								@endif
								@if (Auth::user()->admin->can('unpaid', $subscription))
									<li>
										<a
											message="{{ trans('messages.unpaid_subscriptions_confirm') }}" data-toggle="modal" data-target="#list-form-modal" href="#paid"
											class="list-form-button"
											data-uids="{{ $subscription->uid }}"
											data-method="PATCH"
											data-url="{{ action('Admin\SubscriptionController@unpaid') }}"
										>
											<i class="icon-radio-unchecked"></i> {{ trans('messages.unpaid') }}
										</a>
									</li>
								@endif
								@if (\Auth::user()->admin->can('delete', $subscription))
									<li>
										<a data-method='delete' delete-confirm="{{ trans('messages.delete_subscriptions_confirm') }}" href="{{ action('Admin\SubscriptionController@delete', ['uids' => $subscription->uid]) }}">
											<i class="icon-trash"></i> {{ trans('messages.delete') }}
										</a>
									</li>
								@endif
							</ul>
						</div>
					@endcan
				</td>
			</tr>
		@endforeach
	</table>
	@include('elements/_per_page_select', ["items" => $subscriptions])
	{{ $subscriptions->links() }}
@elseif (!empty(request()->keyword) || !empty(request()->filters))
	<div class="empty-list">
		<i class="icon-quill4"></i>
		<span class="line-1">
			{{ trans('messages.no_search_result') }}
		</span>
	</div>
@else
	<div class="empty-list">
		<i class="icon-quill4"></i>
		<span class="line-1">
			{{ trans('messages.subscription_empty_line_1_admin') }}
		</span>
	</div>
@endif
