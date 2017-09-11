<button type='submit'
    class="btn bg-teal payment_method_type_button"
    data-value="{{ $payment_method->uid }}">
    {!! trans('messages.pay_with_paypal') !!}
</button>