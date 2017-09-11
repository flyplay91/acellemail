

            <div class="row">
                <div class="col-md-6 campaigns-summary">
					<h5 class="mt-10 text-semibold">{{ trans('messages.sent_to') }} <span class="text-bold badge badge-info bg-info-800 badge-big">{{ $campaign->readCache('SubscriberCount', 0) }}</span> {{ trans('messages.recipients') }}</h5>
                    <div class="mb-10">
                        <span class="text-bold text-muted">{{ trans('messages.from') }}:</span>
                        {!! $campaign->displayRecipients() !!}
                    </div>
                </div>
                <div class="col-md-6">
					<div class="mb-10">
                        <span class="text-bold text-muted">{{ trans('messages.subject') }}:</span>
                        {{ $campaign->subject }}
                    </div>
                    <div class="mb-10">
                        <span class="text-bold text-muted">{{ trans('messages.run_at') }}:</span>
                        {{ isset($campaign->run_at) ? Tool::formatDateTime($campaign->run_at) : "" }}
                    </div>
					<div class="mb-10">
                        <span class="text-bold text-muted">{{ trans('messages.delivery_at') }}:</span>
                        {{ isset($campaign->delivery_at) ? Tool::formatDateTime(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $campaign->delivery_at)) : "" }}
                    </div>
                </div>
            </div>

            @include("campaigns._chart")
            <br />
			@include("campaigns._open_click_rate")

            <br />

            @include("campaigns._24h_chart")

			<br />

			@include("campaigns._most_open_country")
