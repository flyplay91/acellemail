@if (is_object($subscription->plan))
    <hr />
    <div class="row">        
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6">
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
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">									
                        <label>{{ trans('messages.subscription_time_registered') }}</label>
                        <div class="text-left">											
                            <h3 class="mt-0 text-semibold">{{ $subscription->plan->displayFrequencyTime() }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">									
                        <label>{{ trans('messages.total') }}</label>
                        <div class="text-left">											
                            <h3 class="mt-0 text-semibold">
                                {{ Acelle\Library\Tool::format_price($subscription->price, $subscription->plan->currency->format) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-center">
            @include('admin/plans/_plan', [
                'plan' => $subscription->plan,
                'readonly' => true,
            ])
        </div>
    </div>
@endif