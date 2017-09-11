<li class="child-row">
    <table class="table">
        <tr>
            <td width="40%">
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
            </td>
            <td>
                @if ($campaign->status != 'new')
                    <div class="single-stat-box pull-left ml-20 automation-email-stat-box">
                        <span class="no-margin text-teal-800 stat-num">{{ number_to_percentage($campaign->deliveredRate()) }}</span>
                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-info" style="width: {{ number_to_percentage($campaign->deliveredRate()) }}">
                            </div>
                        </div>
                        <span class="text-semibold">{{ $campaign->deliveredCount() }} / {{ $campaign->readCache('SubscriberCount', 0) }}</span>
                        <br />
                        <span class="text-muted">{{ trans('messages.sent') }}</span>
                    </div>
                    <div class="single-stat-box pull-left ml-20 automation-email-stat-box">
                        <span class="no-margin text-teal-800 stat-num">{{ $campaign->openUniqRate() }}%</span>
                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-info" style="width: {{ $campaign->openUniqRate() }}%">
                            </div>
                        </div>
                        <span class="text-muted">{{ trans('messages.open_rate') }}</span>
                    </div>
                    <div class="single-stat-box pull-left ml-20 automation-email-stat-box">
                        <span class="no-margin text-teal-800 stat-num">{{ $campaign->clickedEmailsRate() }}%</span>
                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-info" style="width: {{ $campaign->clickedEmailsRate() }}%">
                            </div>
                        </div>
                        <span class="text-muted">{{ trans('messages.click_rate') }}</span>
                    </div>
                @endif
            </td>
            <td>
                <a href="{{ action('CampaignController@overview', $campaign->uid) }}" data-popup="tooltip" title="{{ trans('messages.overview') }}" type="button" class="btn bg-teal-600 btn-icon">{{ trans('messages.overview') }}</a>
            </td>
        </tr>
    </table>



</li>
