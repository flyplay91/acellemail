                <ul class="nav nav-pills campaign-steps">
					<li class="{{ $current == 1 ? "active" : "" }}">
						<a href="{{ action('CampaignController@recipients', $campaign->uid) }}">
							<i class="icon-users4"></i> {{ trans('messages.recipients') }}
						</a>
					</li>
					<li class="{{ $current == 2 ? "active" : "" }} {{ $campaign->step() > 0 ? "" : "disabled" }}">
						<a href="{{ action('CampaignController@setup', $campaign->uid) }}">
							<i class="icon-gear"></i> {{ trans('messages.setup') }}
						</a>
					</li>
					<li class="{{ $current == 3 ? "active" : "" }} {{ $campaign->step() > 1 ? "" : "disabled" }}">
						<a href="{{ action('CampaignController@template', $campaign->uid) }}">
							<i class="icon-magazine"></i> {{ trans('messages.template') }}
						</a>
					</li>
					<li class="{{ $current == 4 ? "active" : "" }} {{ $campaign->step() > 2 ? "" : "disabled" }}">
						<a href="{{ action('CampaignController@schedule', $campaign->uid) }}">
							<i class="icon-alarm-check"></i> {{ trans('messages.schedule') }}
						</a>
					</li>
					<li class="{{ $current == 5 ? "active" : "" }} {{ $campaign->step() > 3 ? "" : "disabled" }}">
						<a href="{{ action('CampaignController@confirm', $campaign->uid) }}">
							<i class="icon-checkmark4"></i> {{ trans('messages.confirm') }}
						</a>
					</li>
				</ul>