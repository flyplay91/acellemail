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
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'name',
                'value' => trans("messages.copy_of_template", ['name' => $template->name]),
                'label' => trans('messages.what_would_you_like_to_name_your_template'),
                'help_class' => 'template',
                'rules' => ['name' => 'required']
            ])


            <div class="text-right">
                <a
                    href="{{ action('Admin\TemplateController@copy', $template->uid) }}"
                    type="button"
                    class="btn bg-teal mr-5 ajax_link"
                    data-in-form="true"
                    data-method="POST"
                    mask-title="{{ trans('messages.template.copying...') }}"
                >{{ trans('messages.copy') }}</a>
                <button type="button" class="btn btn-default ml-0 copy-campaign-close" data-dismiss="modal">{{ trans('messages.close') }}</button>
            </div>
        </div>
    </form>
</div>
