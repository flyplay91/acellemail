<p>{!! trans('messages.options.wording') !!}</p>
<div class="row boxing">
    <div class="col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[sending_quota]',
            'value' => $options['sending_quota'],
            'label' => trans('messages.sending_quota'),
            'help_class' => 'subscription',
            'rules' => $subscription->rules()
        ])
        <div class="checkbox inline unlimited-check text-semibold">
            <label>
                <input{{ $options['sending_quota']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                {{ trans('messages.unlimited') }}
            </label>
        </div>
    </div>
    <div class="col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[sending_quota_time]',
            'value' => $options['sending_quota_time'],
            'label' => trans('messages.quota_time'),
            'help_class' => 'subscription',
            'rules' => $subscription->rules()
        ])
    </div>
    <div class="col-md-4">
        @include('helpers.form_control', ['type' => 'select',
            'name' => 'options[sending_quota_time_unit]',
            'value' => $options['sending_quota_time_unit'],
            'label' => trans('messages.quota_time_unit'),
            'options' => Acelle\Model\Plan::quotaTimeUnitOptions(),
            'help_class' => 'subscription',
            'rules' => $subscription->rules()
        ])
    </div>
</div>
