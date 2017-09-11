@if ($payment_method->type == \Acelle\Model\PaymentMethod::TYPE_PAYPAL)
    <div class="row">
        <div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'select',
				'name' => 'options[environment]',
                'label' => trans('messages.environment'),
				'value' => $payment_method->getOption('environment'),
				'options' => [
					['value' => 'sandbox', 'text' => trans('messages.sandbox')],
					['value' => 'production', 'text' => trans('messages.production')],
				],
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>
	<div class="row">
		<div class="col-md-8">
            @include('helpers.form_control', [
				'type' => 'text',
				'name' => 'options[clientID]',
                'label' => trans('messages.paypal_client_id'),
				'value' => $payment_method->getOption('clientID'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>
	<div class="row">
		<div class="col-md-8">
            @include('helpers.form_control', [
				'type' => 'text',
				'name' => 'options[secret]',
                'label' => trans('messages.paypal_secret'),
				'value' => $payment_method->getOption('secret'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>

    <hr />
    <div class="text-left">
        <button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
    </div>
@endif

@if ($payment_method->type == \Acelle\Model\PaymentMethod::TYPE_BRAINTREE_PAYPAL ||
	$payment_method->type == \Acelle\Model\PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD
)
    <div class="row">
        <div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'select',
				'class' => 'hook2',
				'name' => 'options[environment]',
                'label' => trans('messages.environment'),
				'value' => $payment_method->getOption('environment'),
				'options' => [
					['value' => 'sandbox', 'text' => trans('messages.sandbox')],
					['value' => 'production', 'text' => trans('messages.production')],
				],
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
		<div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'text',
				'class' => 'hook2',
				'name' => 'options[merchantId]',
                'label' => trans('messages.merchant_id'),
				'value' => $payment_method->getOption('merchantId'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>
	<div class="row">
		<div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'text',
				'class' => 'hook2',
				'name' => 'options[publicKey]',
                'label' => trans('messages.public_key'),
				'value' => $payment_method->getOption('publicKey'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
		<div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'text',
				'class' => 'hook2',
				'name' => 'options[privateKey]',
                'label' => trans('messages.private_key'),
				'value' => $payment_method->getOption('privateKey'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
    </div>
	<div class="row" style="height: 250px">
		<div class="col-md-8">
			<h3>Select Merchant Account ID</h3>
			<div
				class="ajax-detail-box"
				data-url="{{ action('Admin\PaymentMethodController@braintreeMerchantAccountSelect', $payment_method->uid) }}"
				data-form=".payment-method-form"
				hook="hook2"
				loading-message="{{ trans('messages.finding_merchant_accounts') }}"
			>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('[name="options[environment]"]').trigger('change');
		});
	</script>

@endif

@if ($payment_method->type == \Acelle\Model\PaymentMethod::TYPE_STRIPE_CREDIT_CARD)
    <div class="row">
		<div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'text',
				'name' => 'options[api_publishable_key]',
                'label' => trans('messages.stripe_api_publishable_key'),
				'value' => $payment_method->getOption('api_publishable_key'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
		<div class="col-md-4">
            @include('helpers.form_control', [
				'type' => 'text',
				'name' => 'options[api_secret_key]',
                'label' => trans('messages.stripe_api_secret_key'),
				'value' => $payment_method->getOption('api_secret_key'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>

    <hr />
    <div class="text-left">
        <button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
    </div>
@endif

@if ($payment_method->type == \Acelle\Model\PaymentMethod::TYPE_CASH)
    <hr />
    <div class="text-left">
        <button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
    </div>
@endif

@if ($payment_method->type == \Acelle\Model\PaymentMethod::TYPE_PADDLE_CARD)
    <div class="row">
		<div class="col-md-6">
            @include('helpers.form_control', [
				'type' => 'text',
				'name' => 'options[vendor_id]',
                'label' => trans('messages.paddle.vendor_id'),
				'value' => $payment_method->getOption('vendor_id'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>
	<div class="row">
		<div class="col-md-6">
            @include('helpers.form_control', [
				'type' => 'text',
				'name' => 'options[vendor_auth_code]',
                'label' => trans('messages.paddle.vendor_auth_code'),
				'value' => $payment_method->getOption('vendor_auth_code'),
				'help_class' => 'payment_method',
				'rules' => $payment_method->rules()
			])
        </div>
	</div>

    <hr />
    <div class="text-left">
        <button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
    </div>
@endif
