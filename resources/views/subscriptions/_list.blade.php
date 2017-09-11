@can('create', new Acelle\Model\Subscription())
	<div class="text-right pull-right {{ !$subscription ? 'inlist-new-button' : '' }}">
		<a href="{{ action("SubscriptionController@create") }}" type="button" class="btn bg-info-800">
			<i class="icon icon-plus2"></i> {{ trans('messages.subscribe_a_new_plan') }}
		</a>
	</div>
@else
	<div class="text-right pull-right {{ !$subscription ? 'inlist-new-button' : '' }}" data-popup="tooltip" title="{{ trans('messages.you_have_inactive_subscription') }}">
		<a href="{{ action("SubscriptionController@create") }}" type="button" class="btn btn-default disabled">
			<i class="icon icon-plus2"></i> {{ trans('messages.subscribe_a_new_plan') }}
		</a>
	</div>
@endcan
	
@if (isset($subscription))
	<div class="row">
		<div class="col-md-4 form-groups-bottom-0">
			@include('helpers.form_control', [
				'type' => 'select',
				'class' => '',
				'name' => 'subscription_uid',
				'value' => $subscription->uid,
				'help_class' => 'subscription',
				'options' => $subscriptions->map(function ($sub) {
					return ['value' => $sub->uid, 'text' => $sub->longTitle()];
				}),
				'rules' => []
			])
		</div>
	</div>
	<div class="tab-content mt-20">
		<div class="tab-pane pt-10 active" id="subscription-{{ $subscription->uid }}">
			<h2 class="text-info-800 text-semibold mt-0 mb-10">{{ trans('messages.plan_overview') }}</h2>
			@if ($subscription->isCurrent())
				{{ trans('messages.you_are_currently_subscribed_to_plan', [
					'plan' => $subscription->plan_name,
					'end_at' => \Acelle\Library\Tool::formatDate($subscription->end_at),
					'remain' => \Acelle\Library\Tool::dateTime($subscription->end_at)->diffForHumans(null, true),
				]) }}
			@else
				{{ trans('messages.you_are_subscribed_to_plan_future', [
					'plan' => $subscription->plan_name,
					'start_at' => \Acelle\Library\Tool::formatDate($subscription->start_at),
					'end_at' => \Acelle\Library\Tool::formatDate($subscription->end_at),
					'remain' => \Acelle\Library\Tool::dateTime($subscription->start_at)->diffForHumans(null, true),
				]) }}
			@endif
			<div class="mt-10">
				<span class="label label-flat bg-{{  $subscription->timeStatus() == \Acelle\Model\Subscription::TIME_STATUS_CURRENT ? 'info-800' : 'grey' }}">
					{{ trans('messages.subscription_time_status_' . $subscription->timeStatus()) }}
				</span>&nbsp;
				<span class="label label-flat bg-{{ $subscription->status }}">
				{{ trans('messages.subscription_status_' . $subscription->status) }}</span>
			</div>

			<h4 class="text-semibold mt-40 mb-0">{{ trans('messages.plan_detail') }}</h4>
			<div class="row">
				<div class="col-md-6">
					<div class="stat-table">
						<div class="stat-row">
							<div class="pull-right num-medium text-semibold">
								{{ $subscription->daysRemainCount() }}
							</div>
							<p class="">
								{{ trans_choice('messages.days_remain', $subscription->daysRemainCount()) }}
							</p>
						</div>
						<div class="stat-row">
							<div class="pull-right num-medium text-semibold">
								{{ $subscription->start_at ? \Acelle\Library\Tool::dateTime($subscription->start_at)->format(trans('messages.date_format')) : '' }}
							</div>
							<p class="">
								{{ trans('messages.start_at') }}
							</p>
						</div>
						<div class="stat-row">
							<div class="pull-right num-medium text-semibold">
								{{ $subscription->end_at ? \Acelle\Library\Tool::dateTime($subscription->end_at)->format(trans('messages.date_format')) : '' }}
							</div>
							<p class="">
								{{ trans('messages.end_at') }}
							</p>
						</div>
						<div class="stat-row">
							<div class="pull-right num-medium text-semibold">
								<span class="label label-flat bg-{{ $subscription->status }}">
								{{ trans('messages.subscription_status_' . $subscription->status) }}</span>
							</div>
							<p class="">
								{{ trans('messages.status') }}
							</p>
						</div>
					</div>
						
					@if ($subscription->isCurrent())
						<div class="mt-40">
							@include('account.quota_log')
						</div>
					@else
						<div class="stat-table">
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayTotalQuota() }}
								</div>
								<p class="">
									{{ trans('messages.sending_total_quota_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayQuota() }}
								</div>
								<p class="">
									{{ trans('messages.sending_quota_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayMaxList() }}
								</div>
								<p class="">
									{{ trans('messages.max_lists_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayMaxSubscriber() }}
								</div>
								<p class="">
									{{ trans('messages.max_subscribers_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayMaxCampaign() }}
								</div>
								<p class="">
									{{ trans('messages.max_campaigns_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayMaxSizeUploadTotal() }} MB
								</div>
								<p class="">
									{{ trans('messages.max_size_upload_total_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{{ $subscription->displayFileSizeUpload() }} MB
								</div>
								<p class="">
									{{ trans('messages.max_file_size_upload_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{!! $subscription->displayAllowCreateSendingServer() !!}
								</div>
								<p class="">
									{{ trans('messages.allow_create_sending_servers_label') }}
								</p>
							</div>
							<div class="stat-row">
								<div class="pull-right num-medium text-semibold">
									{!! $subscription->displayAllowCreateSendingDomain() !!}
								</div>
								<p class="">
									{{ trans('messages.allow_create_sending_domains_label') }}
								</p>
							</div>
						</div>
					@endif
				</div>
			</div>
			
			@if (Auth::user()->can('delete', $subscription)
				|| Auth::user()->can('pay', $subscription)
			)
				<h4 class="text-semibold mt-40">{{ trans('messages.plan_actions') }}</h4>
				@if (!$subscription->isPaid() && !$subscription->isActive())
					@can('pay', $subscription)
						
						@foreach (\Acelle\Model\PaymentMethod::getAllActive() as $payment_method)
							@if ($payment_method->type != \Acelle\Model\PaymentMethod::TYPE_CASH)
								<span class="mr-10">
									<a
										href="{{ action('SubscriptionController@selectPaymentMethod', ["uid" => $subscription->uid, 'payment_method_id' => $payment_method->uid]) }}"
										class="btn btn-primary bg-info-800"
									>
										{!! trans('messages.payment_method_type_' . $payment_method->type . '_button') !!}
									</a>
								</span>
							@endif
						@endforeach
						
					@endcan
				@endif
				@can('delete', $subscription)
					<a delete-confirm="{{ trans('messages.delete_subscriptions_confirm') }}" href="{{ action('SubscriptionController@delete', ["uids" => $subscription->uid]) }}"
						class="btn btn-primary bg-grey"
					>
						<i class="icon-trash"></i> {{ trans('messages.delete') }}
					</a>
				@endcan
			@endif
		</div>
	</div>
@elseif (!empty(request()->keyword))
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
			{{ trans('messages.subscription_empty_line_1') }}
		</span>
	</div>
@endif