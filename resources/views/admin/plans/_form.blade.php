<div class="row">
    <div class="col-md-4">
        <div class="">
            @include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $plan->name, 'help_class' => 'plan', 'rules' => $plan->rules()])
        </div>
    </div>
    <div class="col-md-4">
        <div class="">
            @include('helpers.form_control', [
                'type' => 'select',
                'class' => '',
                'name' => 'color',
                'value' => $plan->color,
                'help_class' => 'admin',
                'options' => $plan->colors("color"),
                'rules' => '',
            ])
        </div>
    </div>
    <div class="col-md-4">
        <div class="">
            @include('helpers.form_control', [
                'type' => 'select_ajax',
                'name' => 'currency_id',
                'label' => trans('messages.currency'),
                'selected' => [
                    'value' => $plan->currency_id,
                    'text' => is_object($plan->currency) ? $plan->currency->displayName() : ''
                ],
                'help_class' => 'plan',
                'rules' => $plan->rules(),
                'url' => action('Admin\CurrencyController@select2'),
                'placeholder' => trans('messages.select_currency')
            ])
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="">
            @include('helpers.form_control', ['class' => 'numeric', 'type' => 'text', 'name' => 'price', 'value' => $plan->price, 'help_class' => 'plan', 'rules' => $plan->rules()])
        </div>
    </div>
    <div class="col-md-4">
        <div class="">
            @include('helpers.form_control', [
                'class' => 'numeric',
                'type' => 'text',
                'name' => 'frequency_amount',
                'value' => $plan->frequency_amount,
                'help_class' => 'plan',
                'rules' => $plan->rules()
            ])
        </div>
    </div>
    <div class="col-md-4">
        <div class="">
            @include('helpers.form_control', ['type' => 'select',
                'name' => 'frequency_unit',
                'value' => $plan->frequency_unit,
                'options' => $plan->timeUnitOptions(),
                'include_blank' => trans('messages.choose'),
                'help_class' => 'plan',
                'rules' => $plan->rules()
            ])
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group checkbox-right-switch">
            @include('helpers.form_control', [
                'class' => '',
                'type' => 'checkbox',
                'name' => 'tax_billing_required',
                'value' => $plan->tax_billing_required,
                'options' => [false,true],
                'help_class' => 'plan',
                'rules' => $plan->rules()
            ])
        </div>
    </div>
</div>


<div class="">

    @include('admin.plans._options')

</div>

<hr />
<div class="text-left">
    <button type='submit' class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
</div>

<script>
    function changeSelectColor() {
        $('.select2 .select2-selection__rendered, .select2-results__option').each(function() {
            var text = $(this).html();
            if (text == '{{ trans('messages.blue') }}') {
                if($(this).find("i").length == 0) {
                    $(this).prepend("<i class='icon-square text-blue'></i>");
                }
            }
            if (text == '{{ trans('messages.green') }}') {
                if($(this).find("i").length == 0) {
                    $(this).prepend("<i class='icon-square text-green'></i>");
                }
            }
            if (text == '{{ trans('messages.brown') }}') {
                if($(this).find("i").length == 0) {
                    $(this).prepend("<i class='icon-square text-brown'></i>");
                }
            }
            if (text == '{{ trans('messages.pink') }}') {
                if($(this).find("i").length == 0) {
                    $(this).prepend("<i class='icon-square text-pink'></i>");
                }
            }
            if (text == '{{ trans('messages.grey') }}') {
                if($(this).find("i").length == 0) {
                    $(this).prepend("<i class='icon-square text-grey'></i>");
                }
            }
            if (text == '{{ trans('messages.white') }}') {
                if($(this).find("i").length == 0) {
                    $(this).prepend("<i class='icon-square text-white'></i>");
                }
            }
        });
    }

    $(document).ready(function() {
        setInterval("changeSelectColor()", 100);
    });
</script>
