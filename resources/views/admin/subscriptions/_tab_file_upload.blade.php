<!--<h3 class="text-teal-800">{{ trans('messages.file_upload') }}</h3>-->
<div class="row">
    <div class="boxing col-md-3">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[max_size_upload_total]',
            'value' => $options['max_size_upload_total'],
            'label' => trans('messages.max_size_upload_total'),
            'help_class' => 'subscription',
            'rules' => $subscription->rules()
        ])
    </div>
    <div class="boxing col-md-3">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'numeric',
            'name' => 'options[max_file_size_upload]',
            'value' => $options['max_file_size_upload'],
            'label' => trans('messages.max_file_size_upload'),
            'help_class' => 'subscription',
            'rules' => $subscription->rules()
        ])
    </div>
</div>
