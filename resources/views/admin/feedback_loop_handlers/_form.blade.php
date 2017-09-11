<div class="row">
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => '',
            'name' => 'name',
            'value' => $server->name,
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => '',
            'name' => 'host',
            'value' => $server->host,
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => '',
            'name' => 'port',
            'value' => $server->port,
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => '',
            'name' => 'username',
            'value' => $server->username,
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => '',
            'name' => 'password',
            'value' => $server->password,
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'select',
            'class' => '',
            'name' => 'protocol',
            'value' => $server->protocol,
            'options' => Acelle\Model\FeedbackLoopHandler::protocolSelectOptions(),
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'text',
            'class' => 'email',
            'name' => 'email',
            'value' => $server->email,
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
    <div class="col-sm-6 col-md-4">
        @include('helpers.form_control', [
            'type' => 'select',
            'class' => '',
            'name' => 'encryption',
            'value' => $server->encryption,
            'options' => Acelle\Model\FeedbackLoopHandler::encryptionSelectOptions(),
            'help_class' => 'feedback_loop_handler',
            'rules' => Acelle\Model\FeedbackLoopHandler::rules()
        ])
    </div>
</div>
<hr>
<div class="text-left">
    @can('test', $server)
        <a
            href="{{ action('Admin\FeedbackLoopHandlerController@test', $server->uid) }}"
            type="button"
            class="btn bg-grey-800 mr-5 btn-icon ajax_link"
            data-in-form="true"
            data-method="POST"
            mask-title="{{ trans('messages.feedback_loop_handler.testing_connection') }}"
        >
            <i class="icon-rotate-cw3"></i> {{ trans('messages.feedback_loop_handler.test') }}
        </a>
    @endcan
    <button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
</div>
