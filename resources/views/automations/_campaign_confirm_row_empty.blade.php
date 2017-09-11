<li class="child-row">
    <a link-method="POST" href="{{ action('AutoEventController@addCampaign', ['uid' => $event->uid]) }}" class="btn btn-info bg-info-800">
        {{ trans('messages.add_email') }}
    </a>
    <i class="icon-cross2 text-danger"></i>
    <h5 class="mb-5 text-semibold"><i class="icon-envelop text-grey"></i> {{ trans('messages.emails') }}</h5>
    <p class="text-danger">
        {{ trans('messages.auto_event_email_empty_error') }}
    </p>
</li>