@extends('layouts.backend')

@section('title', trans('messages.update_subscription'))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("Admin\SubscriptionController@index") }}">{{ trans('messages.subscriptions') }}</a></li>
					<li class="active">{{ trans('messages.update') }}</li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-profile"></i> {{ $subscription->customer->displayName() }}: {{ $subscription->plan_name }}</span>
				</h1>
			</div>

@endsection

@section('content')

	<form enctype="multipart/form-data" action="{{ action('Admin\SubscriptionController@update', $subscription->uid) }}" method="POST" class="subscription-form">
		{{ csrf_field() }}
		<input type="hidden" name="_method" value="PATCH">

		<div class="row">
			<div class="col-md-12">
                @if (!$subscription->isTimeUnlimited())
                    <div class="row">
                        <div class="col-md-3">
                            <div class="">
                                @include('helpers.form_control', [
                                    'type' => 'date',
                                    'name' => 'start_at',
                                    'label' => trans('messages.start_at'),
                                    'value' => $subscription->start_at ? \Acelle\Library\Tool::dateTime($subscription->start_at)->format('Y-m-d') : '',
                                    'help_class' => 'subscription',
                                    'rules' => $subscription->rules(),
                                    'placeholder' => trans('messages.start_at')
                                ])
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="">
                                @include('helpers.form_control', [
                                    'type' => 'date',
                                    'name' => 'end_at',
                                    'label' => trans('messages.end_at'),
                                    'value' => $subscription->end_at ? \Acelle\Library\Tool::dateTime($subscription->end_at)->format('Y-m-d') : '',
                                    'help_class' => 'subscription',
                                    'rules' => $subscription->rules(),
                                    'placeholder' => trans('messages.end_at')
                                ])
                            </div>
                        </div>
                    </div>
                @endif

				@include('admin.subscriptions._options')
			</div>
			<!--<div class="col-md-3 text-center">
				@include('subscriptions._subscription', ['readonly' => true])
			</div>-->
		</div>



		<hr />
		<div class="text-left">
			<button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
		</div>
	</form>

@endsection
