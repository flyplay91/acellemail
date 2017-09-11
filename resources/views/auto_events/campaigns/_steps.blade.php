                <ul class="nav nav-pills campaign-steps">
					<li class="{{ $current == 1 ? "active" : "" }}">
						<a href="{{ action('AutoEventController@campaignSetup', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]) }}">
							<i class="icon-gear"></i> {{ trans('messages.setup') }}
						</a>
					</li>
					<li class="{{ $current == 2 ? "active" : "" }}">
						<a href="{{ action('AutoEventController@template', ['uid' => $auto_event->uid, 'campaign_uid' => $campaign->uid]) }}">
							<i class="icon-magazine"></i> {{ trans('messages.template') }}
						</a>
					</li>
				</ul>