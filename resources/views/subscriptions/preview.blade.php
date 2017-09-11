@if (is_object($subscription->plan))
    <div class="row">
        <div class="col-md-12 plan-left">
            <!--<div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ trans('messages.start_at') }}</label>
                        <div class="text-left">
                            <h3 class="mt-0 text-bold">{{
                            $subscription->start_at ? \Acelle\Library\Tool::dateTime($subscription->start_at)->format(trans('messages.date_format')) : '' }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ trans('messages.end_at') }}</label>
                        <div class="text-left">
                            <h3 class="mt-0 text-bold">{{
                            $subscription->end_at ? \Acelle\Library\Tool::dateTime($subscription->end_at)->format(trans('messages.date_format')) : '' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ trans('messages.subscription_time_registered') }}</label>
                        <div class="text-left">
                            <h3 class="mt-0 text-bold">
                            {{ $subscription->plan->displayFrequencyTime() }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ trans('messages.total') }}</label>
                        <div class="text-left">
                            <h3 class="mt-0 text-bold">
                                @if ($subscription->price)
                                    {{ Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format) }}
                                @else
                                    {{ trans('messages.free') }}
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            </div>-->

            <ul class="dotted-list topborder section mb-0">
                <li class="border-bottom-0">
                    <div class="unit size1of2 text-bold">
                        <strong>{{ trans('messages.price') }}</strong>
                    </div>
                    <div class="lastUnit size1of2">
                        <h5 class="mt-0 mb-0 text-semibold">
                            <mc:flag>
                                @if ($subscription->price)
                                    {{ Acelle\Library\Tool::format_price($subscription->price, $subscription->currency_format) }}
                                @else
                                    {{ trans('messages.free') }}
                                @endif
                            </mc:flag>
                        </h5>
                    </div>
                </li>
            </ul>

            @include('subscriptions._details', ['subscription' => $subscription])

            @if ($subscription->isFree())
                <div class="text-left">
                    <button type='submit' class="btn bg-teal">
                    {{ trans('messages.get_started') }}
                    <i class="icon-arrow-right7"></i></button>
                </div>
            @else
                <input type="hidden" name="payment_method_uid" value='' />
                <div class="text-left">
                    @foreach (\Acelle\Model\PaymentMethod::getAllActive() as $payment_method)
                        @include('payments._button_' . $payment_method->type)
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
