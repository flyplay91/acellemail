<li class="child-row">
    @if ($campaign->autoCampaignDesigned())
        <a href="{{ action('AutoEventController@campaignSetup', ['uid' => $event->uid, 'campaign_uid' => $campaign->uid]) }}" class="btn btn-info bg-grey">
            {{ trans('messages.edit') }}
        </a>
    @else
        <a href="{{ action('AutoEventController@campaignSetup', ['uid' => $event->uid, 'campaign_uid' => $campaign->uid]) }}" class="btn btn-info bg-info-800">{{ trans('messages.design') }}</a>
    @endif
    @if ($campaign->autoCampaignDesigned())
        <i class="icon-checkmark4"></i>
    @else
        <i class="icon-cross2 text-danger"></i>
    @endif
    <h5 class="mb-5 text-semibold"><i class="icon-envelop"></i> {{ $campaign->name }}</h5>
    @if ($campaign->autoCampaignDesigned())
        <p>
            {{ trans('messages.updated_at') }}: {{ Tool::formatDateTime($campaign->updated_at) }}
        </p>
    @else
        <p class="text-danger">
            {{ trans('messages.not_designed_yet') }}
        </p>
    @endif
</li>