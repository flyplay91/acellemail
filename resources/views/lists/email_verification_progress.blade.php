@if ($list->getRunningVerificationJob()->isFailed())
    <div class="alert alert-danger alert-noborder">
        <button data-dismiss="alert" class="close" type="button"><span>×</span><span class="sr-only">Close</span></button>
        <strong>{{ trans('messages.verification.error.job_failed') }}</strong>
    </div>
@endif

@include('helpers._progress_bar', [
    'percent' => $list->getVerifiedSubscribersPercentage(true)
])

<p>{!! trans('messages.verification_process_running', [
    'verified' => $list->countVerifiedSubscribers(),
    'total' => \Acelle\Library\Tool::format_number($list->readCache('SubscriberCount')),
]) !!}</p>

<p>
    <a class="btn bg-grey-300"
        link-confirm="{{ trans('messages.stop_list_verification_confirm') }}" link-method="POST"
        href="{{ action("MailListController@stopVerification", $list->uid) }}">
        {{ trans('messages.verification.button.stop') }}
    </a>
</p>
