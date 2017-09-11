<div class="form-group control-radio">
    <div class="radio_box" data-popup='tooltip' title="">
        <label class="main-control">
            <input {{ ($options['sending_server_option'] == \Acelle\Model\Plan::SENDING_SERVER_OPTION_SYSTEM ? 'checked' : '') }} type="radio"
                name="options[sending_server_option]"
                value="{{ \Acelle\Model\Plan::SENDING_SERVER_OPTION_SYSTEM }}" class="styled" /><rtitle>{{ trans('messages.plan_option.system_s_sending_server') }}</rtitle>
            <div class="desc text-normal mb-10">
                {{ trans('messages.plan_option.system_s_sending_server.intro') }}
            </div>
        </label>
        <div class="radio_more_box">
            <span class="notoping">
                @include('helpers.form_control', ['type' => 'checkbox2',
                    'class' => '',
                    'name' => 'options[all_sending_servers]',
                    'value' => $options['all_sending_servers'],
                    'label' => trans('messages.use_all_sending_servers'),
                    'options' => ['no','yes'],
                    'help_class' => $help_class,
                    'rules' => $rules
                ])
            </span>

            @if(!Auth()->user()->admin->getAllSendingServers()->count())
                <div class="alert alert-danger mt-20">
                    {!! trans('messages.plan_option.there_no_sending_server') !!}
                </div>
            @else
                <div class="sending-servers">
                    <hr>
                    <div class="row text-muted">
                        <div class="col-md-6">
                            <label>{{ trans('messages.select_sending_servers') }}</label>
                        </div>
                        <div class="col-md-6">
                            <label>{{ trans('messages.fitness') }}</label>
                        </div>
                    </div>
                    @foreach (Auth()->user()->admin->getAllSendingServers()->orderBy("name")->get() as $server)
                        <div class="row mb-5">
                            <div class="col-md-6">
                                @include('helpers.form_control', [
                                    'type' => 'checkbox2',
                                    'name' => 'sending_servers[' . $server->uid . '][check]',
                                    'value' => $relatedSendingServers->contains('sending_server_id', $server->id),
                                    'label' => $server->name,
                                    'options' => [false, true],
                                    'help_class' => $help_class,
                                    'rules' => $rules
                                ])
                            </div>
                            <div class="col-md-6" show-with-control="input[name='{{ 'sending_servers[' . $server->uid . '][check]' }}']">
                                <?php $found_key = array_search($server->id, array_column($relatedSendingServers->toArray(), 'sending_server_id')) ?>
                                @include('helpers.form_control', [
                                    'type' => 'text',
                                    'class' => 'numeric',
                                    'name' => 'sending_servers[' . $server->uid . '][fitness]',
                                    'label' => '',
                                    'value' => $found_key !== false ? $relatedSendingServers->toArray()[$found_key]["fitness"] : '100',
                                    'help_class' => $help_class,
                                    'rules' => $rules
                                ])
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
    <hr>
    <div class="radio_box" data-popup='tooltip' title="">
        <label class="main-control">
            <input {{ ($options['sending_server_option'] == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN ? 'checked' : '') }} type="radio"
                name="options[sending_server_option]"
                value="{{ \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN }}" class="styled" /><rtitle>{{ trans('messages.plan_option.own_sending_server') }}</rtitle>
            <div class="desc text-normal mb-10">
                {{ trans('messages.plan_option.own_sending_server.intro') }}
            </div>
        </label>
        <div class="radio_more_box">
            <div class="boxing">
                @include('helpers.form_control', [
                    'type' => 'text',
                    'class' => 'numeric',
                    'name' => 'options[sending_servers_max]',
                    'value' => $options['sending_servers_max'],
                    'label' => trans('messages.max_sending_servers'),
                    'help_class' => $help_class,
                    'options' => ['true', 'false'],
                    'rules' => $rules,
                    'unlimited_check' => true,
                ])
            </div>

            <p>
                @include('helpers.form_control', ['type' => 'checkbox2',
                    'class' => '',
                    'name' => 'options[all_sending_server_types]',
                    'value' => $options['all_sending_server_types'],
                    'label' => trans('messages.allow_adding_all_sending_server_types'),
                    'options' => ['no','yes'],
                    'help_class' => $help_class,
                    'rules' => $rules
                ])
            </p>
            <div class="all_sending_server_types_no">
                <hr>
                <label class="text-semibold text-muted">{{ trans('messages.select_allowed_sending_server_types') }}</label>
                <div class="row">
                    @foreach (Acelle\Model\SendingServer::types() as $key => $type)
                        <div class="col-md-4 pt-10">
                            &nbsp;&nbsp;<span class="text-semibold text-italic">{{ trans('messages.' . $key) }}</span>
                            <span class="notoping pull-left">
                                @include('helpers.form_control', ['type' => 'checkbox',
                                    'class' => '',
                                    'name' => 'options[sending_server_types][' . $key . ']',
                                    'value' => isset($options['sending_server_types'][$key]) ? $options['sending_server_types'][$key] : 'no',
                                    'label' => '',
                                    'options' => ['no','yes'],
                                    'help_class' => $help_class,
                                    'rules' => $rules
                                ])
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="radio_box" data-popup='tooltip' title="">
        <label class="main-control">
            <input {{ ($options['sending_server_option'] == \Acelle\Model\Plan::SENDING_SERVER_OPTION_SUBACCOUNT ? 'checked' : '') }} type="radio"
                name="options[sending_server_option]"
                value="{{ \Acelle\Model\Plan::SENDING_SERVER_OPTION_SUBACCOUNT }}" class="styled" /><rtitle>{{ trans('messages.plan_option.sub_account') }}</rtitle>
            <div class="desc text-normal mb-10">
                {{ trans('messages.plan_option.sub_account.intro') }}
            </div>
        </label>
        <div class="radio_more_box">
            @if (Auth()->user()->admin->getSubaccountSendingServers()->count())
                <div class="row">
                    <div class="col-md-6">
                        @include('helpers.form_control', [
                            'type' => 'select',
                            'class' => 'numeric',
                            'name' => 'options[sending_server_subaccount_uid]',
                            'value' => $options['sending_server_subaccount_uid'],
                            'label' => '',
                            'help_class' => $help_class,
                            'include_blank' => trans('messages.select_sending_server_with_subaccount'),
                            'options' => Auth()->user()->admin->getSubaccountSendingServersSelectOptions(),
                            'rules' => $rules
                        ])
                    </div>
                </div>
            @else
                <div class="alert alert-danger">
                    {!! trans('messages.plan_option.there_no_subaccount_sending_server') !!}
                </div>
            @endif
        </div>
    </div>
</div>
