<div class="page-title">
	<ul class="breadcrumb breadcrumb-caret position-right">
		<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
		@if (is_object($campaign->autoEvent()))
			<li><a href="{{ action("AutomationController@index") }}">{{ trans('messages.automations') }}</a></li>
			<li><a href="{{ action("AutomationController@overviewWorkflow", $campaign->autoEvent()->automation->uid) }}">{{ $campaign->autoEvent()->automation->name }}</a></li>
			<li><a href="{{ action("AutomationController@overviewWorkflow", $campaign->autoEvent()->automation->uid) }}">{{ trans('messages.emails') }}</a></li>
		@else
			<li><a href="{{ action("CampaignController@index") }}">{{ trans('messages.campaigns') }}</a></li>
		@endif
	</ul>
	<h1>
		<span class="text-semibold">{{ $campaign->name }}</span>
	</h1>
</div>