<div class="modal-header bg-teal">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">{{ trans('messages.test_sending_server') }}</h4>
</div>
<div class="modal-content">
    <form action="" method="POST" class="ajax_upload_form form-validate-jquery">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="">
        <input type="hidden" name="uids" value="">

        @foreach (request()->all() as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        <div class="modal-body">
            <p>{{ trans('messages.test_sending_server.intro') }}</p>
            @include('helpers.form_control', [
                'type' => 'text',
                'class' => 'email',
                'label' => trans('messages.from_email'),
                'name' => 'from_email',
                'value' => '',
                'help_class' => 'sending_server',
                'rules' => ['from_email' => 'required']
            ])
            @include('helpers.form_control', [
                'type' => 'text',
                'class' => 'email',
                'label' => trans('messages.to_email'),
                'name' => 'to_email',
                'value' => '',
                'help_class' => 'sending_server',
                'rules' => ['to_email' => 'required']
            ])
            @include('helpers.form_control', [
                'type' => 'text',
                'class' => '',
                'label' => trans('messages.subject'),
                'name' => 'subject',
                'value' => '',
                'help_class' => 'sending_server',
                'rules' => ['subject' => 'required']
            ])
            @include('helpers.form_control', [
                'type' => 'textarea',
                'class' => '',
                'label' => trans('messages.content'),
                'name' => 'content',
                'value' => '',
                'help_class' => 'sending_server',
                'rules' => ['content' => 'required']
            ])
        </div>
        <div class="modal-footer text-left">
            <a
                href="{{ action('SendingServerController@test', $server->uid) }}"
                type="button"
                class="btn bg-teal mr-5 ajax_link"
                data-in-form="true"
                data-method="POST"
                mask-title="{{ trans('messages.sending_server.testing_connection') }}"
            >
                {{ trans('messages.send') }}
            </a>
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('messages.close') }}</button>
        </div>
    </form>
</div>
