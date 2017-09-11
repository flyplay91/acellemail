<ul class="breadcrumb breadcrumb-caret position-right">
    <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
    <li><a href="{{ action("AutomationController@index") }}">{{ trans('messages.automations') }}</a></li>
</ul>
<h1>
    <span class="text-semibold"><i class="icon-alarm-check"></i> {{ $automation->name }}</span>
    <span class="label label-flat bg-{{ $automation->status }}">{{ trans('messages.automation_status_' . $automation->status) }}</span>
</h1>