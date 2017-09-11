<div class="sub_section">
    <div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $group->name, 'help_class' => 'admin_group', 'rules' => Acelle\Model\AdminGroup::$rules])
        </div>
    </div>
</div>

<div class="">
    <h2><i class="icon-gear"></i> {{ trans('messages.admin_group_options') }}</h2>

    <div class="tabbable">
        <ul class="nav nav-tabs nav-tabs-top">
            <li class="active text-semibold"><a href="#top-tab1" data-toggle="tab">
                <i class="icon-user"></i> {{ trans('messages.permissions') }}</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="top-tab1">
                @foreach (Acelle\Model\AdminGroup::allPermissions() as $key => $items)
                    <h3 class="text-teal-800">{{ trans('messages.' . $key) }}</h3>
                    <div class="row">
                        @foreach ($items as $act => $ops)
                            <div class="col-md-3">
                                @if (count($ops["options"]) > 2)
                                    @include('helpers.form_control', [
                                        'type' => 'select',
                                        'class' => 'numeric',
                                        'name' => 'permissions[' . $key . "_" . $act .']',
                                        'value' => $permissions[$key . "_" . $act],
                                        'label' => trans('messages.' . $act),
                                        'options' => $ops["options"],
                                        'help_class' => 'admin_group',
                                        'rules' => Acelle\Model\AdminGroup::rules()
                                    ])
                                @else
                                    <div class="checkbox-box-group">
                                        @include('helpers.form_control', ['type' => 'checkbox',
                                            'class' => 'numeric',
                                            'name' => 'permissions[' . $key . "_" . $act .']',
                                            'value' => $permissions[$key . "_" . $act],
                                            'label' => trans('messages.' . $act),
                                            'options' => ['no','yes'],
                                            'help_class' => 'admin_group',
                                            'rules' => Acelle\Model\AdminGroup::rules()
                                        ])
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

