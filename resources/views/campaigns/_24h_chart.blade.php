                <h3 class="mt-10 mb-0"><i class="icon-stats-dots"></i> {{ trans('messages.24h_performance') }}</h3>
                <div class="sub-h3">{{ trans('messages.campaign_24h_intro') }}</div>
                <div class="panel panel-flat">
                    <div class="panel-body">
                        <div class="chart-container">
                            <div class="chart has-fixed-height" id="basic_lines" data-url="{{ action('CampaignController@chart24h', $campaign->uid) }}"></div>
                        </div>
                    </div>
                </div>