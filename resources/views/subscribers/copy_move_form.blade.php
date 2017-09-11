<!-- Basic modal -->
<div id="copy-move-subscribers-form" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ action("SubscriberController@" . request()->action) }}" method="POST" class="form-validate-jquery">
                {{ csrf_field() }}
                <input type="hidden" name="from_uid" value="{{ $from_list->uid }}" />
                @foreach (request()->all() as $name => $value)
                    @if (is_array($value))
                        @foreach ($value as $v)
                            <input type="hidden" name="{{ $name }}[]" value="{{ $v }}" />
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
                    @endif
                @endforeach

                <div class="modal-header bg-teal">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h2 class="modal-title">
                        {{ trans('messages.' . request()->action . '_subscriber') }}
                    </h2>
                </div>

                <div class="modal-body">
                    <h4>{!! trans('messages.subscribers_' . request()->action . '_message', ['number' => $subscribers->count()]) !!}</h4>

                    <input type="hidden" name="action" value="{{ request()->action }}" />
                    <input type="hidden" name="uids" value="{{ request()->uids }}" />
                    <?php
                        $lists = collect(Auth::user()->customer->readCache('MailListSelectOptions', []));
                        $lists = $lists->filter(function ($record, $key) use($from_list) { return $record['id'] != $from_list->id; });
                    ?>
                    @include('helpers.form_control', [
                        'type' => 'select',
                        'name' => 'to_uid',
                        'class' => 'required',
                        'required' => true,
                        'label' => trans('messages.select_the_target_list'),
                        'value' => '',
                        'include_blank' => trans('messages.choose'),
                        'options' => $lists,
                        'rules' => []
                    ])

                    @include('helpers.form_control', [
                        'type' => 'radio',
                        'name' => 'type',
                        'class' => '',
                        'label' => trans('messages.action_when_email_exist'),
                        'value' => 'update',
                        'options' => Acelle\Model\Subscriber::copyMoveExistSelectOptions(),
                        'rules' => []
                    ])

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary bg-teal">{{ trans('messages.submit') }}</button>
                    <button type="button" class="btn btn-white" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
                </div>
        </div>
    </div>
</div>
<!-- /basic modal -->
