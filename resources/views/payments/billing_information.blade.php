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

            <div class="sub-section">
                <h3 class="text-semibold">{!! trans('messages.billing_information') !!}</h3>
				<p>
                    {!! trans('messages.please_fill_billing_information', [
                        'plan' => $subscription->plan_name,
                        'price' => Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format)
                    ]) !!}
                </p>

                <form action="{{ action('PaymentController@billingInformation', $subscription->uid) }}" method="POST">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-12">

                            @include('helpers.form_control', [
                                'type' => 'text',
                                'name' => 'tax_number',
                                'value' => (isset($billing_information['tax_number']) ? $billing_information['tax_number'] : ''),
                                'help_class' => 'billing_information',
                                'rules' => $rules,
                            ])

                            @include('helpers.form_control', [
                                'type' => 'text',
                                'name' => 'billing_address',
                                'value' => (isset($billing_information['billing_address']) ? $billing_information['billing_address'] : ''),
                                'help_class' => 'billing_information',
                                'rules' => $rules,
                            ])

                        </div>
                    </div>

					<hr>
					<div class="">
						<button class="btn bg-teal">{{ trans('messages.next') }} <i class="icon-arrow-right7"></i></button>
					</div>
                </form>
			</div>
		</div>
	</div>

@endsection
