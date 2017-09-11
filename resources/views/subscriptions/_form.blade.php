<div class="row">
	<div class="col-md-12">
		<div class="row">                                    
			<div class="col-md-6">
				<div class="">
					@include('helpers.form_control', [
						'type' => 'select_ajax',
						'class' => 'subsciption-plan-select hook',
						'name' => 'plan_uid',
						'label' => trans('messages.plan'),
						'selected' => [
							'value' => is_object($subscription->plan) ? $subscription->plan->uid : '',
							'text' => is_object($subscription->plan) ? $subscription->plan->name : ''
						],
						'help_class' => 'subscription',
						'rules' => $subscription->rules(),
						'url' => action('PlanController@select2'),
						'placeholder' => trans('messages.select_plan')
					])
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12 ajax-detail-box" data-url="{{ action('SubscriptionController@preview') }}" data-form=".subscription-form">
		@include('subscriptions.preview', [
			'subscription' => $subscription
		])
	</div>
</div> 