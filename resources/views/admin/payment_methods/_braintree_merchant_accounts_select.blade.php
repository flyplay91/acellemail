@if ($error)
    <div class="alert alert-danger">{{ $error }}</div>
@else
    <div class="row">
		<div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'select',
                'class' => 'hook2',
                'name' => 'options[merchantAccountID]',
                'label' => trans('messages.merchant_account'),
                'value' => $payment_method->getOption('merchantAccountID'),
                'options' => $payment_method->getBraintreeMerchantAccountSelectOptions($accounts),
                'help_class' => 'payment_method',
                'rules' => $payment_method->rules()
            ])
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ trans('messages.currency_code') }}</label>
                <?php $code = $payment_method->getBraintreeMerchantAccountByID($accounts, $payment_method->getOption('merchantAccountID'))->currencyIsoCode; ?>
                <h5 class="text-semibold mt-5">{{ $code }}</h5>
                <input type="hidden" name="options[currencyCode]" value="{{ $code }}" />
            </div>
        </div>
    </div>

    <hr />
    <div class="text-left">
        <button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
    </div>
@endif
