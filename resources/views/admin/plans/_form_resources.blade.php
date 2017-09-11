<div class="row boxing">
    <div class="col-md-12">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[email_max]',
            'value' => $options['email_max'],
            'label' => trans('messages.max_emails'),
            'help_class' => $help_class,
            'options' => ['true', 'false'],
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[list_max]',
            'value' => $options['list_max'],
            'label' => trans('messages.max_lists'),
            'help_class' => $help_class,
            'options' => ['true', 'false'],
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[subscriber_max]',
            'value' => $options['subscriber_max'],
            'label' => trans('messages.max_subscribers'),
            'help_class' => $help_class,
            'options' => ['true', 'false'],
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[subscriber_per_list_max]',
            'value' => $options['subscriber_per_list_max'],
            'label' => trans('messages.max_subscribers_per_list'),
            'help_class' => $help_class,
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[segment_per_list_max]',
            'value' => $options['segment_per_list_max'],
            'label' => trans('messages.segment_per_list_max'),
            'help_class' => $help_class,
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        @include('helpers.form_control', ['type' => 'text',
            'class' => 'numeric',
            'name' => 'options[campaign_max]',
            'value' => $options['campaign_max'],
            'label' => trans('messages.max_campaigns'),
            'help_class' => $help_class,
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        @include('helpers.form_control', ['type' => 'text',
            'class' => 'numeric',
            'name' => 'options[automation_max]',
            'value' => $options['automation_max'],
            'label' => trans('messages.max_automations'),
            'help_class' => $help_class,
            'rules' => $rules,
            'unlimited_check' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="mt-0 mb-5 text-semibold">{{ trans('messages.max_number_of_processes') }}</label>
            @include('helpers.form_control', ['type' => 'select',
                'name' => 'options[max_process]',
                'value' => $options['max_process'],
                'label' => '',
                'options' => \Acelle\Model\Plan::multiProcessSelectOptions(),
                'help_class' => $help_class,
                'rules' => $rules
            ])
        </div>
    </div>
</div>
<div class="row">
    <div class="boxing col-md-6">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[max_size_upload_total]',
            'value' => $options['max_size_upload_total'],
            'label' => trans('messages.max_size_upload_total'),
            'help_class' => $help_class,
            'rules' => $rules
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-6">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[max_file_size_upload]',
            'value' => $options['max_file_size_upload'],
            'label' => trans('messages.max_file_size_upload'),
            'help_class' => $help_class,
            'rules' => $rules
        ])
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        <span class="">
            @include('helpers.form_control', ['type' => 'checkbox2',
                'class' => '',
                'name' => 'options[unsubscribe_url_required]',
                'value' => $options['unsubscribe_url_required'],
                'label' => trans('messages.unsubscribe_url_required'),
                'options' => ['no','yes'],
                'help_class' => $help_class,
                'rules' => $rules
            ])
        </span>
    </div>
</div>
<div class="row">
    <div class="boxing col-md-12">
        <span class="notoping">
            @include('helpers.form_control', ['type' => 'checkbox2',
                'class' => '',
                'name' => 'options[access_when_offline]',
                'value' => $options['access_when_offline'],
                'label' => trans('messages.access_when_offline'),
                'options' => ['no','yes'],
                'help_class' => $help_class,
                'rules' => $rules
            ])
        </span>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <span class="notoping">
            @include('helpers.form_control', ['type' => 'checkbox2',
                'class' => '',
                'name' => 'options[list_import]',
                'value' => $options['list_import'],
                'label' => trans('messages.can_import_list'),
                'options' => ['no','yes'],
                'help_class' => $help_class,
                'rules' => $rules
            ])
        </span>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <span class="notoping">
            @include('helpers.form_control', ['type' => 'checkbox2',
                'class' => '',
                'name' => 'options[list_export]',
                'value' => $options['list_export'],
                'label' => trans('messages.can_export_list'),
                'options' => ['no','yes'],
                'help_class' => $help_class,
                'rules' => $rules
            ])
        </span>
    </div>
</div>
