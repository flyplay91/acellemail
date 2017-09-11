    <h3 class="mt-10 mb-0"><i class="icon-stats-dots"></i> {{ trans('messages.statistics') }}</h3>
    <div class="sub-h3">{{ trans('messages.campaign_table_chart_intro') }}</div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="chart-container">
                        <div class="chart has-fixed-height" id="d3-bar-horizontal"  data-url="{{ action('CampaignController@chart', $campaign->uid) }}"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="badge-row">
                <span class="badge badge-info bg-slate badge-big">{{ $campaign->readCache('UniqOpenRate') }}%</span>
                {{ trans('messages.opened') }}
                <span class="badge badge-info bg-grey-400 badge-medium">
                    {{ trans('messages.open_uniq_per_total', [
                        'count' => $campaign->readCache('UniqOpenCount'),
                        'total' => $campaign->openCount(),
                    ]) }}
                </span>
                <a href="{{ action('CampaignController@openLog', $campaign->uid) }}"><i class="icon-arrow-right7"></i> {{ trans('messages.view_log') }}</a>
            </div>

            <div class="badge-row">
                <span class="badge badge-info bg-orange badge-big">{{ number_to_percentage($campaign->readCache('NotOpenRate', 0)) }}</span>
                {{ trans('messages.not_opened') }}
                <span class="badge badge-info bg-grey-400 badge-medium">
                    {{ trans('messages.not_open_per_total', [
                        'count' => $campaign->readCache('NotOpenCount', 0),
                        'total' => $campaign->readCache('SubscriberCount', 0),
                    ]) }}
                </span>
                <a href="{{ action('CampaignController@subscribers', ['uid' => $campaign->uid, 'open' => 'not_opened']) }}"><i class="icon-arrow-right7"></i> {{ trans('messages.view_log') }}</a>
            </div>

            <div class="badge-row">
                <span class="badge badge-info bg-blue badge-big">{{ $campaign->readCache('ClickedRate') }}%</span>
                {{ trans('messages.clicked_emails_rate') }}
                <span class="badge badge-info bg-grey-400 badge-medium">
                    {{ trans('messages.count_clicked_opened', [
                        'count' => $campaign->clickedEmailsCount(),
                        'total' => $campaign->openCount()
                    ]) }}
                </span>
                <a href="{{ action('CampaignController@clickLog', $campaign->uid) }}"><i class="icon-arrow-right7"></i> {{ trans('messages.view_log') }}</a>
            </div>

            <div class="badge-row">
                <span class="badge badge-info bg-violet badge-big">{{ $campaign->unsubscribeRate() }}%</span>
                {{ trans('messages.unsubscribed') }}
                <span class="badge badge-info bg-grey-400 badge-medium">
                    {{ trans('messages.count_unsubscribed', [
                        'count' => $campaign->unsubscribeCount()
                    ]) }}
                </span>
                <a href="{{ action('CampaignController@unsubscribeLog', $campaign->uid) }}"><i class="icon-arrow-right7"></i> {{ trans('messages.view_log') }}</a>
            </div>

            <div class="badge-row">
                <span class="badge badge-info bg-brown badge-big">{{ $campaign->bounceRate() }}%</span>
                {{ trans('messages.bounced') }}
                <span class="badge badge-info bg-grey-400 badge-medium">
                    {{ trans('messages.count_bounced', [
                        'count' => $campaign->bounceCount()
                    ]) }}
                </span>
                <a href="{{ action('CampaignController@bounceLog', $campaign->uid) }}"><i class="icon-arrow-right7"></i> {{ trans('messages.view_log') }}</a>
            </div>

            <div class="badge-row">
                <span class="badge badge-info bg-teal badge-big">{{ $campaign->feedbackRate() }}%</span>
                {{ trans('messages.reported') }}
                <span class="badge badge-info bg-grey-400 badge-medium">
                    {{ trans('messages.count_reported', [
                        'count' => $campaign->feedbackCount()
                    ]) }}
                </span>
                <a href="{{ action('CampaignController@feedbackLog', $campaign->uid) }}"><i class="icon-arrow-right7"></i> {{ trans('messages.view_log') }}</a>
            </div>

        </div>
    </div>
