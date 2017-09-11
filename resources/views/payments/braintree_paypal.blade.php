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
                <h3 class="text-semibold">{!! trans('messages.pay_with_paypal') !!}</h3>
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

				<!-- Load the client component. -->
				<script src="https://js.braintreegateway.com/web/3.9.0/js/client.min.js"></script>

				<!-- Load the PayPal component. -->
				<script src="https://js.braintreegateway.com/web/3.9.0/js/paypal.min.js"></script>
				<p class="loading_paypal text-small">{{ trans('messages.loading_paypal') }}</p>
				<div class="panels paypal_button" style="display:none">
					<div class="panels-body">
						<script src="https://www.paypalobjects.com/api/button.js?"
							data-merchant="braintree"
							data-id="paypal-button"
							data-button="checkout"
							data-color="gold"
							data-size="medium"
							data-shape="pill"
							data-button_type="submit"
							data-button_disabled="false"
						></script>
					</div>
				</div>

				<form id="checkout-form" action="{{ action('PaymentController@braintree_paypal', $subscription->uid) }}" method="post">
					{{ csrf_field() }}
					<input type="hidden" name="payment_method_nonce">
				</form>
			</div>
		</div>
	</div>

	<script>
		var paypalButton = document.querySelector('.paypal-button');
		var form = document.querySelector('#checkout-form');

		// Create a client.
		braintree.client.create({
		  authorization: '{{ $clientToken }}'
		}, function (clientErr, clientInstance) {

		  // Stop if there was a problem creating the client.
		  // This could happen if there is a network error or if the authorization
		  // is invalid.
		  if (clientErr) {
			console.error('Error creating client:', clientErr);
			return;
		  }

		  // Create a PayPal component.
		  braintree.paypal.create({
			client: clientInstance
		  }, function (paypalErr, paypalInstance) {

			// Stop if there was a problem creating PayPal.
			// This could happen if there was a network error or if it's incorrectly
			// configured.
			if (paypalErr) {
			  console.error('Error creating PayPal:', paypalErr);
			  return;
			}

			// Enable the button.
			paypalButton.removeAttribute('disabled');

			// When the button is clicked, attempt to tokenize.
			paypalButton.addEventListener('click', function (event) {

			  // Because tokenization opens a popup, this has to be called as a result of
			  // customer action, like clicking a button—you cannot call this at any time.
			  paypalInstance.tokenize({
				flow: 'vault'
			  }, function (tokenizeErr, payload) {

				// Stop if there was an error.
				if (tokenizeErr) {
					// Handle error in Hosted Fields tokenization
					if (tokenizeErr.message) {
						swalError(tokenizeErr.message);
					}

					return;
				}

				// Tokenization succeeded!
				paypalButton.setAttribute('disabled', true);
				document.querySelector('input[name="payment_method_nonce"]').value = payload.nonce;
				form.submit();

			  });

			}, false);

		  });

			$('.paypal_button').show();
			$('.loading_paypal').hide();
		});
	</script>

@endsection
