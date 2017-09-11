          <div class="sub_section">
                        <div class="row">
                            <div class="col-md-12">

                                    @include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $group->name, 'help_class' => 'customer_group', 'rules' => Acelle\Model\CustomerGroup::$rules])

                            </div>
                        </div>

                    </div>

                    <div class="">
                        <h2><i class="icon-gear"></i> {{ trans('messages.customer_group_options') }}</h2>

                        <div class="tabbable">
                            <ul class="nav nav-tabs nav-tabs-top">
                                <li class="active text-semibold"><a href="#top-tab1" data-toggle="tab">
                                    <i class="icon-user"></i> {{ trans('messages.frontend') }}</a></li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="top-tab1">
                                    <h3 class="text-teal-800">{{ trans('messages.list_or_subscriber_or_segment_or_campaign') }}</h3>
                                    <div class="row">
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[list_max]',
                                                'value' => $options['list_max'],
                                                'label' => trans('messages.max_lists'),
                                                'help_class' => 'customer_group',
                                                'options' => ['true', 'false'],
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['list_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[subscriber_max]',
                                                'value' => $options['subscriber_max'],
                                                'label' => trans('messages.max_subscribers'),
                                                'help_class' => 'customer_group',
                                                'options' => ['true', 'false'],
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['subscriber_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[subscriber_per_list_max]',
                                                'value' => $options['subscriber_per_list_max'],
                                                'label' => trans('messages.max_subscribers_per_list'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['subscriber_per_list_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[segment_per_list_max]',
                                                'value' => $options['segment_per_list_max'],
                                                'label' => trans('messages.segment_per_list_max'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['segment_per_list_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', ['type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[campaign_max]',
                                                'value' => $options['campaign_max'],
                                                'label' => trans('messages.max_campaigns'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['campaign_max']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>

                                        </div>
                                        <div class="boxing col-md-3">
                                            <label class="text-semibold">{{ trans('messages.unsubscribe_url_required') }} <span class="text-danger">*</span></label>
                                            <br />
                                            <span class="notoping">
                                                @include('helpers.form_control', ['type' => 'checkbox',
                                                    'class' => '',
                                                    'name' => 'options[unsubscribe_url_required]',
                                                    'value' => $options['unsubscribe_url_required'],
                                                    'label' => '',
                                                    'options' => ['no','yes'],
                                                    'help_class' => 'customer_group',
                                                    'rules' => Acelle\Model\CustomerGroup::rules()
                                                ])
                                            </span>
                                        </div>
                                        <div class="boxing col-md-3">
                                            <label class="text-semibold">{{ trans('messages.access_when_offline') }} <span class="text-danger">*</span></label>
                                            <br />
                                            <span class="notoping">
                                                @include('helpers.form_control', ['type' => 'checkbox',
                                                    'class' => '',
                                                    'name' => 'options[access_when_offline]',
                                                    'value' => $options['access_when_offline'],
                                                    'label' => '',
                                                    'options' => ['no','yes'],
                                                    'help_class' => 'customer_group',
                                                    'rules' => Acelle\Model\CustomerGroup::rules()
                                                ])
                                            </span>
                                        </div>
                                    </div>

                                    <h3 class="text-teal-800">{{ trans('messages.file_upload') }}</h3>
                                    <div class="row">
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[max_size_upload_total]',
                                                'value' => $options['max_size_upload_total'],
                                                'label' => trans('messages.max_size_upload_total'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                        </div>
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[max_file_size_upload]',
                                                'value' => $options['max_file_size_upload'],
                                                'label' => trans('messages.max_file_size_upload'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                        </div>
                                    </div>


                                    <h3 class="text-teal-800">{{ trans('messages.sending_quota') }}</h3>
                                    <div class="row">
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[sending_quota]',
                                                'value' => $options['sending_quota'],
                                                'label' => trans('messages.sending_quota'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['sending_quota']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="boxing col-md-3">
                                            @include('helpers.form_control', [
                                                'type' => 'text',
                                                'class' => 'numeric',
                                                'name' => 'options[sending_quota_time]',
                                                'value' => $options['sending_quota_time'],
                                                'label' => trans('messages.quota_time'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                            <div class="checkbox inline unlimited-check text-semibold">
                                                <label>
                                                    <input{{ $options['sending_quota_time']  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
                                                    {{ trans('messages.unlimited') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            @include('helpers.form_control', ['type' => 'select',
                                                'name' => 'options[sending_quota_time_unit]',
                                                'value' => $options['sending_quota_time_unit'],
                                                'label' => trans('messages.quota_time_unit'),
                                                'options' => Acelle\Model\CustomerGroup::timeUnitOptions(),
                                                'include_blank' => trans('messages.choose'),
                                                'help_class' => 'customer_group',
                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                            ])
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="mt-0 mb-5 text-semibold">{{ trans('messages.max_number_of_processes') }}</label>
                                                @include('helpers.form_control', ['type' => 'select',
                                                    'name' => 'options[max_process]',
                                                    'value' => $options['max_process'],
                                                    'label' => '',
                                                    'options' => Acelle\Model\CustomerGroup::multiProcessSelectOptions(),
                                                    'help_class' => 'customer_group',
                                                    'rules' => Acelle\Model\CustomerGroup::rules()
                                                ])
                                            </div>
                                        </div>
                                    </div>

                                    <h3 class="text-teal-800">{{ trans('messages.sending_servers') }}</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            {{ trans('messages.all_sending_servers') }}&nbsp;&nbsp;&nbsp;
                                            <span class="notoping">
                                                @include('helpers.form_control', ['type' => 'checkbox',
                                                    'class' => '',
                                                    'name' => 'options[all_sending_servers]',
                                                    'value' => $options['all_sending_servers'],
                                                    'label' => '',
                                                    'options' => ['no','yes'],
                                                    'help_class' => 'customer_group',
                                                    'rules' => Acelle\Model\CustomerGroup::rules()
                                                ])
                                            </span>

                                        </div>
                                    </div>
                                    <br />
                                    <div class="row sending-servers">
                                        @foreach (Acelle\Model\SendingServer::getAll()->orderBy("name")->get() as $server)

                                            <div class="col-md-6">
                                                <h5 class="mt-0 mb-5 text-semibold text-teal-600">{{ $server->name }}</h5>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="mt-0 mb-5 text-semibold">{{ trans('messages.choose') }}</label>
                                                            @include('helpers.form_control', [
                                                                'type' => 'checkbox',
                                                                'name' => 'sending_servers[' . $server->uid . '][check]',
                                                                'value' => $group->customer_group_sending_servers->contains('sending_server_id', $server->id),
                                                                'label' => '',
                                                                'options' => [false, true],
                                                                'help_class' => 'customer_group',
                                                                'rules' => Acelle\Model\CustomerGroup::rules()
                                                            ])
                                                        </div>
                                                        <br><br>
                                                    </div>
                                                    <div class="col-md-9">
                                                        @include('helpers.form_control', [
                                                            'type' => 'text',
                                                            'class' => 'numeric',
                                                            'name' => 'sending_servers[' . $server->uid . '][fitness]',
                                                            'label' => trans('messages.fitness'),
                                                            'value' => (is_object($group->customer_group_sending_servers->where('sending_server_id', $server->id)->first()) ? $group->customer_group_sending_servers->where('sending_server_id', $server->id)->first()->fitness : "100"),
                                                            'help_class' => 'customer_group',
                                                            'rules' => Acelle\Model\CustomerGroup::rules()
                                                        ])
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                <script>
                    $(document).ready(function() {
                        // all sending servers checking
                        $(document).on("change", "input[name='options[all_sending_servers]']", function(e) {
                            if($("input[name='options[all_sending_servers]']:checked").length) {
                                $(".sending-servers").find("input[type=checkbox]").each(function() {
                                    if($(this).is(":checked")) {
                                        $(this).parents(".form-group").find(".switchery").eq(1).click();
                                    }
                                });
                                $(".sending-servers").hide();
                            } else {
                                $(".sending-servers").show();
                            }
                        });

                        $("input[name='options[all_sending_servers]']").trigger("change");
                    });
                </script>

