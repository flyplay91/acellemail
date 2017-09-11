        @if ($campaign->getTopLinks()->count())
			<h3 class="text-semibold"><i class="icon-link"></i> {{ trans('messages.top_links_clicked') }}</h3>
            
			<div class="stat-table">
				@foreach ($campaign->getTopLinks()->get() as $link)
					<div class="stat-row">
						<div class="pull-right num">
							{{ $link->aggregate }}
						</div>
						<p class="text-muted">
							<a target="_blank" href="{{ $link->url }}">{{ $link->url }}</a>
						</p>
					</div>
				@endforeach
			</div>

			<div class="text-right">
				<a href="{{ action('CampaignController@clickLog', $campaign->uid) }}" class="btn btn-info bg-teal-600">{{ trans('messages.click_log') }} <i class="icon-arrow-right8"></i></a>
			</div>
		@endif