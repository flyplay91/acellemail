@if (is_object($plan))
    <div class="panel panel-plan box-{{ $plan->status }}">
        <div class="panel-heading" style="background-color: {{ $plan->color }}; color: white">
            <h4 class="pull-left panel-title text-center">													
                {{ $plan->name }}
            </h4>
        </div>
        <div class="plan-price-box">
            @if ($plan->price == 0.0)
                <span class="price">{{ trans('messages.free') }}</span>
            @else
                <span class="price">{{ Acelle\Library\Tool::format_price($plan->price, $plan->currency->format) }}</span> /
                {{ $plan->displayFrequencyTime() }}
            @endif
                
        </div>
        <div class="panel-body pt-0">
            @include('admin.plans._features')
        </div>
        <div class="panel-footer pt-10 pb-10 text-center">												
            <a href="{{ action('SubscriptionController@register', $plan->uid) }}"
                title="{{ trans('messages.edit') }}"
                class="btn bg-teal-800 btn-icon">
                    <i class="icon icon-checkmark4 pr-0 mr-0"></i> {{ trans('messages.register') }}
            </a>
        </div>
    </div>
@endif