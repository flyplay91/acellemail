@extends('layouts.frontend')

@section('title', trans('messages.subscription'))

@section('page_script')
@endsection

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		</ul>
		<h1>
			<span class="text-semibold"><i class="icon-quill4"></i> {{ trans('messages.your_subscriptions') }}</span>
		</h1>
	</div>

@endsection

@section('content')

	@include("account._menu")

	<div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <h2 class="text-semibold">{{ trans('messages.subscription') }}</h2>

			@include("payments._billing_information")

            <div class="sub-section">
                <h3 class="text-semibold">{!! trans('messages.pay_by_credit_card') !!}</h3>
				<p>
                    {!! trans('messages.purchasing_intro_' . $payment_method->type, [
                        'plan' => $subscription->plan_name,
                        'price' => Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format)
                    ]) !!}
                </p>

				@if (isset($result) && count($result->errors->deepAll()) > 0)
					<!-- Form Error List -->
					<div class="alert alert-danger alert-noborder">
						<button data-dismiss="alert" class="close" type="button"><span>×</span><span class="sr-only">Close</span></button>
						<strong>{{ trans('messages.something_error_accur') }}</strong>

						<br><br>

						<ul>
							@foreach ($result->errors->deepAll() AS $error)
							  <li>{!! $error->code . ": " . $error->message . "<br />" !!}</li>
							@endforeach
						</ul>
					</div>
				@endif

                <form action="{{ action('PaymentController@stripe_credit_card', $subscription->uid) }}" method="POST">
                    <script
                      src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                      data-key="{{ $apiPublishableKey }}"
                      data-amount="{{ $subscription->stripePrice() }}"
                      data-currency="{{ $subscription->currency_code }}"
                      data-name="{{ \Acelle\Model\Setting::get('site_name') }}"
                      data-description="{{ \Acelle\Model\Setting::get('site_description') }}"
                      data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                      data-locale="{{ language_code() }}"
                      data-zip-code="true"
                      data-label="{{ trans('messages.pay_with_strip_label_button') }}">
                    </script>
                </form>
			</div>
		</div>
	</div>

@endsection
