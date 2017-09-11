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


                <script src="https://www.paypalobjects.com/api/checkout.js"></script>

                <div id="paypal-button-container"></div>

                <script>

                    // Render the PayPal button

                    paypal.Button.render({

                        // Set your environment

                        env: '{{ $payment_method->getOption('environment') }}', // sandbox | production

                        // PayPal Client IDs - replace with your own
                        // Create a PayPal app: https://developer.paypal.com/developer/applications/create

                        client: {
                            sandbox:    '{{ $payment_method->getOption('clientID') }}',
                            production: '{{ $payment_method->getOption('clientID') }}'
                        },

                        // Wait for the PayPal button to be clicked

                        payment: function() {

                            // Make a client-side call to the REST api to create the payment

                            return paypal.rest.payment.create(this.props.env, this.props.client, {
                                transactions: [
                                    {
                                        amount: { total: '{{ $subscription->price }}', currency: '{{ $subscription->currency_code }}' }
                                    }
                                ]
                            });
                        },

                        // Wait for the payment to be authorized by the customer

                        onAuthorize: function(data, actions) {

                            return actions.payment.execute().then(function() {
                                //// Execute the payment
                                //$.post('{{ action('PaymentController@paypal', $subscription->uid) }}', {
                                //    paymentID: data.paymentID,
                                //    payerID: data.payerID
                                //}).done(function (res) {
                                //
                                //});

                                var newForm = jQuery('<form>', {
                                    'action': '{{ action('PaymentController@paypal', $subscription->uid) }}',
                                    'method': 'POST'
                                });
                                newForm.append(jQuery('<input>', {
                                    'name': '_token',
                                    'value': CSRF_TOKEN,
                                    'type': 'hidden'
                                }));
                                newForm.append(jQuery('<input>', {
                                    'name': 'paymentID',
                                    'value': data.paymentID,
                                    'type': 'hidden'
                                }));
                                newForm.append(jQuery('<input>', {
                                    'name': 'payerID',
                                    'value': data.payerID,
                                    'type': 'hidden'
                                }));
                                $(document.body).append(newForm);
                                newForm.submit();
                            });

                        },
						onError: function(err) {
							// Show an error page here, when an error occurs
							// console.log(err);
							swalError(err);
						}

                    }, '#paypal-button-container');

                </script>

			</div>
		</div>
	</div>

@endsection
