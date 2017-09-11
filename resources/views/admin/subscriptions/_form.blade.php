<div class="row">
	<div class="col-md-10">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6">
						<div class="">
							@include('helpers.form_control', [
								'type' => 'select_ajax',
								'name' => 'customer_uid',
								'class' => 'hook',
								'label' => trans('messages.customer'),
								'selected' => [
									'value' => is_object($subscription->customer) ? $subscription->customer->uid : '',
									'text' => is_object($subscription->customer) ? $subscription->customer->displayNameEmail() : ''
								],
								'help_class' => 'subscription',
								'rules' => $subscription->rules(),
								'url' => action('Admin\CustomerController@select2'),
								'placeholder' => trans('messages.select_customer')
							])
						</div>
					</div>
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
								'url' => action('Admin\PlanController@select2'),
								'placeholder' => trans('messages.select_plan')
							])
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 ajax-detail-box" data-url="{{ action('Admin\SubscriptionController@preview') }}" data-form=".subscription-form">
				@include('admin.subscriptions.preview', [
					'subscription' => $subscription
				])
			</div>
		</div>

		<hr />
		<div class="text-left">
			<button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
		</div>
	</div>
</div>
