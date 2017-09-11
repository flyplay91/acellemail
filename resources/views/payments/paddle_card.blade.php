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
                <h3 class="text-semibold">{!! trans('messages.paddle_card.pay_by_card') !!}</h3>
				<p>
                    {!! trans('messages.paddle_card.purchasing_intro', [
                        'plan' => $subscription->plan_name,
                        'price' => Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format)
                    ]) !!}
                </p>
                <a href="#!" class="paddle_buttons btn btn-primary">Buy Now!</a>
			</div>
		</div>
	</div>

    <script src="https://cdn.paddle.com/paddle/paddle.js"></script>
    <script type="text/javascript">
        try {
            Paddle.Setup({
                vendor: {{ $vendorId }},
                debug: true
            });
        }
        catch(err) {
            swalError(err.message);
        }

        jQuery('.paddle_buttons').click(function() {
            Paddle.Checkout.open({
                override: '{{ $checkoutUrl }}'
            });
        });

    </script>

@endsection
