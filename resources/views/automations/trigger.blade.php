@extends('layouts.frontend')

@section('title', trans('messages.Automation') . " - " . trans('messages.recipients'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
    <div class="page-title">
        @include('automations._head')

        @include('automations._steps', [
            'step' => 'trigger'
        ])
    </div>
@endsection

@section('content')
    <form action="" method="POST" class="form-validate-jqueryz tabable-select-box">
        {{ csrf_field() }}

        <div class="row">
            <div class="col-md-6 tabable-select-radio">
                <h3 class="mt-0">{{ trans('messages.How_to_trigger_automation') }}</h3>
                @include('helpers.form_control', [
                    'type' => 'radio',
                    'name' => 'event_type',
                    'class' => '',
                    'label' => '',
                    'value' => $first_event->event_type,
                    'options' => Acelle\Model\AutoEvent::typeNameSelectOptions(),
                    'rules' => []
                ])
            </div>
        </div>

        <div class="tabable-container">
            <div class="row tabable-tab specific-datetime">
                @include('automations.triggers._specific-datetime')
            </div>
            <div class="row tabable-tab weekly-recurring">
                @include('automations.triggers._weekly-recurring')
            </div>
            <div class="row tabable-tab monthly-recurring">
                @include('automations.triggers._monthly-recurring')
            </div>
            <div class="row tabable-tab subscriber-event">
                @include('automations.triggers._subscriber-event')
            </div>
            <div class="row tabable-tab custom-criteria">
                @include('automations.triggers._custom-criteria')
            </div>
            <div class="row tabable-tab api-call">
                <div class="col-md-12">
                    <hr>
                    <h5 class="text-semibold">{!! trans('messages.make_request_to_run_automation', ['command' => 'POST ' . action('Api\AutomationController@apiCall', [
                        'uid' => $automation->uid,
                        'api_token' => Auth::user()->api_token
                    ])]) !!}</h5>
                </div>
            </div>
            <div class="row tabable-tab custom-criteria subscriber-event list-subscription list-unsubscription">
                <div class="col-md-6">
                    <hr />
                    <h6 class="panel-title text-semibold auto-event-form-line">
                        {{ trans('messages.wait') }}
                        @include('helpers.form_control', [
                            'type' => 'text',
                            'name' => 'delay_value',
                            'class' => 'numeric',
                            'label' => '',
                            'value' => null !== $first_event->getDataValue('delay_value') ? $first_event->getDataValue('delay_value') : '0',
                            'rules' => []
                        ])
                        @include('helpers.form_control', [
                            'type' => 'select',
                            'name' => 'delay_unit',
                            'multiple' => '',
                            'label' => '',
                            'value' => null !== $first_event->getDataValue('delay_unit') ? $first_event->getDataValue('delay_unit') : 'day',
                            'options' => Acelle\Model\AutoEvent::timeUnitOptions(),
                            'rules' => []
                        ])
                        @include('helpers.form_control', [
                            'type' => 'select',
                            'name' => 'delay_type',
                            'multiple' => '',
                            'label' => '',
                            'value' => null !== $first_event->getDataValue('delay_type') ? $first_event->getDataValue('delay_type') : 'before',
                            'options' => Acelle\Model\AutoEvent::delayTypeOptions(),
                            'rules' => []
                        ])
                        <span class="delay-after-text text-bold">
                            {{ trans('messages.delay_after') }}
                        </span>
                        {{ trans('messages.trigger_occurred') }}

                        <span class="tabable-tab subscriber-event">
                            ;
                            <span>{{ trans('messages.at') }}</span>
                            @include('helpers.form_control', [
                                'type' => 'time',
                                'name' => 'at',
                                'label' => '',
                                'class' => ' text-left',
                                'value' => null !== $first_event->getDataValue('at') ? Acelle\Library\Tool::timeStringFromTimestamp(Acelle\Library\Tool::dateTimeFromString($first_event->getDataValue('at'))) : '',
                                'rules' => []
                            ])
                        </span>
                    </h6>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right">
            <button class="btn bg-teal-800">{{ trans('messages.Save_and_Next') }} <i class="icon-arrow-right7"></i> </button>
        </div>

    <form>

    <script>
        $(document).ready(function() {
            // Pick a day from now
            $('.pickadate_from_now').pickadate({
                format: 'yyyy-mm-dd'
            });

            // custom criteria delay type retricted
            $(document).on("change", "input[name='event_type']", function() {
                var type = $("input[name='event_type']:checked").val();

                if(type === '{{ Acelle\Model\AutoEvent::TYPE_CUSTOM_CRITERIA }}' || type === '{{ Acelle\Model\AutoEvent::TYPE_LIST_UNSUBSCRIPTION }}' || type === '{{ Acelle\Model\AutoEvent::TYPE_LIST_SUBSCRIPTION }}') {
                    $('.delay-after-text').show();
                    $('select[name="delay_type"]').val('after').change();
                    $('select[name="delay_type"]').parents('.form-group').hide();
                } else {
                    $('.delay-after-text').hide();
                    $('select[name="delay_type"]').parents('.form-group').show();
                }
            });
            $("input[name='event_type']:checked").trigger('change');
        });
    </script>
@endsection
