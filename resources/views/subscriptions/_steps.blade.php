<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="text-center subscription-header login-header mb-0">
            <a class="main-logo-big" href="{{ action('HomeController@index') }}">
                @if (\Acelle\Model\Setting::get('site_logo_big'))
                    <img src="{{ URL::asset(\Acelle\Model\Setting::get('site_logo_big')) }}" alt="">
                @else
                    <img src="{{ URL::asset('images/logo_big.png') }}" alt="">
                @endif
            </a>

            <h3 class="text-center text-muted2" style="color: #ccc">{{ trans('messages.subscription') }}</h3>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="wizard wizard-dark">
            <div class="steps">
                <ul role="tablist">
                    <li role="tab" class="first
                        {{ request()->route()->getActionName() == 'Acelle\Http\Controllers\SubscriptionController@selectPlan' ? 'current' : 'done' }}"
                        aria-disabled="false" aria-selected="true">
                        <a id="steps-uid-0-t-0" href="{{ action('SubscriptionController@selectPlan') }}" aria-controls="steps-uid-0-p-0">
                            <span class="current-info audible">current step: </span>
                            <span class="number">1</span>
                            {{ trans('messages.select_a_plan') }}
                        </a>
                    </li>
                    <li role="tab" class="{{ in_array(request()->route()->getActionName(), [
                    'Acelle\Http\Controllers\SubscriptionController@register']) ? 'current' : '' }}
                    {{ in_array(request()->route()->getActionName(), [
                    'Acelle\Http\Controllers\SubscriptionController@finish',
                    'Acelle\Http\Controllers\SubscriptionController@subscription']) || strpos(request()->route()->getActionName(), 'Acelle\Http\Controllers\PaymentController@') !== false ? 'done' : '' }}
                    "
                        aria-disabled="true">
                        <a id="steps-uid-0-t-1" href="#steps-uid-0-h-1" aria-controls="steps-uid-0-p-1">
                        <span class="number">2</span>
                        {{ trans('messages.customer_account') }}
                    </a>
                    <li role="tab" class="{{
                        (request()->route()->getActionName() == 'Acelle\Http\Controllers\SubscriptionController@subscription'
                            || strpos(request()->route()->getActionName(), 'Acelle\Http\Controllers\PaymentController@braintree') !== false
                        ) ? 'current' : '' }}
                        {{ in_array(request()->route()->getActionName(), [
                        'Acelle\Http\Controllers\SubscriptionController@finish',
                        'Acelle\Http\Controllers\PaymentController@success']) ? 'done' : '' }}
                    " aria-disabled="true">
                        <a id="steps-uid-0-t-1" href="#steps-uid-0-h-1" aria-controls="steps-uid-0-p-1">
                        <span class="number">3</span>
                        {{ trans('messages.subscription') }}
                    </a>
                    <li role="tab" class="{{
                    in_array(request()->route()->getActionName(), [
                        'Acelle\Http\Controllers\SubscriptionController@finish',
                        'Acelle\Http\Controllers\PaymentController@success'])
                    ? 'current' : '' }}" aria-disabled="true">
                        <a id="steps-uid-0-t-1" href="#steps-uid-0-h-1" aria-controls="steps-uid-0-p-1">
                        <span class="number">4</span>
                        {{ trans('messages.finish') }}
                    </a>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>
