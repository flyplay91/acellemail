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

				<div class="panelz">
					<div class="panel-bodyz">
						<form id="checkout-form" action="{{ action('PaymentController@braintree_credit_card', $subscription->uid) }}" method="post">
							{{ csrf_field() }}
						  <div id="error-message"></div>

						  <div class="form-group">
							<label for="card-number">{{ trans('messages.card_number') }}</label>
							<div class="hosted-field" id="card-number"></div>
						  </div>

						  <div class="form-group">
							<label for="cvv">{{ trans('messages.cvv') }}</label>
							<div class="hosted-field" id="cvv"></div>
						  </div>

						  <div class="form-group">
							<label for="expiration-date">{{ trans('messages.expiration_date') }}</label>
							<div class="hosted-field" id="expiration-date"></div>
						  </div>

						  <input type="hidden" name="payment_method_nonce">
						  <input type="submit" value="Pay {{ Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format) }}" class="btn btn-primary bg-teal-800" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Load the Client component. -->
	<script src="https://js.braintreegateway.com/web/3.9.0/js/client.min.js"></script>

	<!-- Load the Hosted Fields component. -->
	<script src="https://js.braintreegateway.com/web/3.9.0/js/hosted-fields.min.js"></script>

	<script>
	// We generated a client token for you so you can test out this code
	// immediately. In a production-ready integration, you will need to
	// generate a client token on your server (see section below).
	var authorization = '{{ $clientToken }}';
	var form = document.querySelector('#checkout-form');
	var submit = document.querySelector('input[type="submit"]');

	braintree.client.create({
	  // Replace this with your own authorization.
	  authorization: authorization
	}, function (clientErr, clientInstance) {
	  if (clientErr) {
		// Handle error in client creation
		return;
	  }

	  braintree.hostedFields.create({
		client: clientInstance,
		styles: {
		  'input': {
			'font-size': '14pt'
		  },
		  'input.invalid': {
			'color': 'red'
		  },
		  'input.valid': {
			'color': 'green'
		  }
		},
		fields: {
		  number: {
			selector: '#card-number',
			placeholder: '4111 1111 1111 1111'
		  },
		  cvv: {
			selector: '#cvv',
			placeholder: '123'
		  },
		  expirationDate: {
			selector: '#expiration-date',
			placeholder: '10/2019'
		  }
		}
	  }, function (hostedFieldsErr, hostedFieldsInstance) {
		if (hostedFieldsErr) {
		  // Handle error in Hosted Fields creation
		  return;
		}

		submit.removeAttribute('disabled');

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
				if (tokenizeErr) {
					// Handle error in Hosted Fields tokenization
					if (tokenizeErr.message) {
						swalError(tokenizeErr.message);
					}
					return;
				}

				// Put `payload.nonce` into the `payment-method-nonce` input, and then
				// submit the form. Alternatively, you could send the nonce to your server
				// with AJAX.
				document.querySelector('input[name="payment_method_nonce"]').value = payload.nonce;
				form.submit();
			});
		}, false);
	  });
	});
	</script>

@endsection
