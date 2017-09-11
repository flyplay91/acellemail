@if ($subscription->isTaxBillingRequired())
    <div class="sub-section">
        <h3 class="text-semibold"><i class="icon-quill4"></i> {{ trans('messages.billing_information') }}</h3>
        <p>
            {!! trans('messages.billing_information_info', [
                'plan' => $subscription->plan_name,
            ]) !!}
        </p>

        <ul class="dotted-list topborder section">
            <li>
                <div class="unit size1of2">
                    <strong>{{ trans('messages.tax_number') }}</strong>
                </div>
                <div class="lastUnit size1of2">
                    <mc:flag>{{ request()->session()->get('billing_information')['tax_number'] }}</mc:flag>
                </div>
            </li>
            <li>
                <div class="unit size1of2">
                    <strong>{{ trans('messages.billing_address') }}</strong>
                </div>
                <div class="lastUnit size1of2">
                    <mc:flag>{{ request()->session()->get('billing_information')['billing_address'] }}</mc:flag>
                </div>
            </li>
        </ul>

        <a href="{{ action('PaymentController@billingInformation', $subscription->uid) }}" class="btn btn-primary bg-grey">
            <i class="icon-pencil"></i> {{ trans('messages.change_billing_information') }}
        </a>

    </div>
@endif
