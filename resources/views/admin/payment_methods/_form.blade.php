<p>
	{!! trans('messages.' . $payment_method->type . '.wording') !!}
</p>
@if ($errors->has('payment_method_not_valid'))
	<div class="alert alert-danger">{{ $errors->first('payment_method_not_valid') }}</div>
@endif
<div class="row">
    @if (Auth::user()->can('create', $payment_method))
        <div class="col-md-4">
            @include('helpers.form_control', ['type' => 'select',
                'name' => 'type',
                'value' => $payment_method->type,
                'class' => 'hook',
                'label' => trans('messages.payment_method_status'),
                'options' => Acelle\Model\PaymentMethod::typeSelectOptions(),
                'help_class' => 'payment_method',
                'rules' => $payment_method->rules()
            ])
        </div>
    @endif
	<div class="col-md-4">
		@include('helpers.form_control', ['type' => 'select',
			'name' => 'status',
			'value' => $payment_method->status,
			'class' => '',
			'label' => trans('messages.payment_method_status'),
			'options' => Acelle\Model\PaymentMethod::statusSelectOptions(),
			'help_class' => 'payment_method',
			'rules' => $payment_method->rules()
		])
	</div>
</div>
<div class="row">
	<div class="col-md-12 ajax-detail-box" data-url="{{ action('Admin\PaymentMethodController@options', $payment_method->uid) }}" data-form=".payment-method-form">
		@include('admin.payment_methods._options', [
			'payment_method' => $payment_method
		])
	</div>
</div>
