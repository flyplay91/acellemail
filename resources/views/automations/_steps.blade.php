<ul class="nav nav-pills campaign-steps">
	<li class="{{ $step == "recipients" ? "active" : "" }}">
		<a href="{{ action('AutomationController@edit', $automation->uid) }}">
			<i class="icon-users4"></i> {{ trans('messages.Recipients') }}
		</a>
	</li>
	<li class="{{ $step == "trigger" ? "active" : "" }}">
		<a href="{{ action('AutomationController@trigger', $automation->uid) }}">
			<i class="icon-alignment-align"></i> {{ trans('messages.Trigger') }}
		</a>
	</li>
	<li class="{{ $step == "workflow" ? "active" : "" }}">
		<a href="{{ action('AutomationController@workflow', $automation->uid) }}">
			<i class="icon-stack-play"></i> {{ trans('messages.workflow') }}
		</a>
	</li>
	<li class="{{ $step == "confirm" ? "active" : "" }}">
		<a href="{{ action('AutomationController@confirm', $automation->uid) }}">
			<i class="icon-checkmark4"></i> {{ trans('messages.review_and_confirm') }}
		</a>
	</li>
</ul>