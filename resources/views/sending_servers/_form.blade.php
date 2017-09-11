<input type="hidden" name="type" value="{{ $server->type }}" />
<div class="row">
	@if (array_key_exists("name", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'name',
				'value' => $server->name,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("domain", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'domain',
				'value' => $server->domain,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("api_key", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'api_key',
				'value' => $server->api_key,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("host", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'host',
				'value' => $server->host,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("aws_access_key_id", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'aws_access_key_id',
				'value' => $server->aws_access_key_id,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("aws_secret_access_key", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'aws_secret_access_key',
				'value' => $server->aws_secret_access_key,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("aws_region", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'select',
				'class' => '',
				'name' => 'aws_region',
				'value' => $server->aws_region,
				'help_class' => 'sending_server',
				'options' => Acelle\Model\SendingServer::awsRegionSelectOptions(),
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("smtp_username", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'smtp_username',
				'value' => $server->smtp_username,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("smtp_password", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'smtp_password',
				'value' => $server->smtp_password,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("smtp_port", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'smtp_port',
				'value' => $server->smtp_port,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("smtp_protocol", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'smtp_protocol',
				'value' => $server->smtp_protocol,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
	@if (array_key_exists("sendmail_path", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
		<div class="col-sm-6 col-md-4">
			@include('helpers.form_control', [
				'type' => 'text',
				'class' => '',
				'name' => 'sendmail_path',
				'value' => $server->sendmail_path,
				'help_class' => 'sending_server',
				'rules' => Acelle\Model\SendingServer::rules(request()->type)
			])
		</div>
	@endif
</div>

<h4 class="text-semibold text-teal-800">{{ trans('messages.sending_quota') }}</h4>

<div class="row boxing">
	<div class="col-md-12">
		<p>{!! trans('messages.options.wording') !!}</p>
	</div>
	<div class="col-md-4">
		@include('helpers.form_control', [
			'type' => 'text',
			'class' => 'numeric',
			'name' => 'quota_value',
			'value' => $server->quota_value,
			'help_class' => 'sending_server',
			'rules' => Acelle\Model\SendingServer::rules(request()->type),
			'default_value' => '1000',
		])
		<div class="checkbox inline unlimited-check text-semibold">
			<label>
				<input{{ $server->quota_value  == -1 ? " checked=checked" : "" }} type="checkbox" class="styled">
				{{ trans('messages.unlimited') }}
			</label>
		</div>
	</div>
	<div class="col-md-4">
		@include('helpers.form_control', [
			'type' => 'text',
			'class' => 'numeric',
			'name' => 'quota_base',
			'value' => $server->quota_base,
			'help_class' => 'sending_server',
			'rules' => Acelle\Model\SendingServer::rules(request()->type),
			'default_value' => '1',
		])
	</div>
	<div class="col-md-4">
		@include('helpers.form_control', ['type' => 'select',
			'name' => 'quota_unit',
			'value' => $server->quota_unit,
			'label' => trans('messages.quota_time_unit'),
			'options' => Acelle\Model\Plan::quotaTimeUnitOptions(),
			'include_blank' => trans('messages.choose'),
			'help_class' => 'sending_server',
			'rules' => Acelle\Model\SendingServer::rules(request()->type)
		])
	</div>
</div>

@if ( array_key_exists("bounce_handler_id", Acelle\Model\SendingServer::types()[request()->type]["cols"])
	|| array_key_exists("feedback_loop_handler_id", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
	<h4 class="text-semibold text-teal-800">{{ trans('messages.handlers') }}</h4>
	<div class="row">
		@if (array_key_exists("bounce_handler_id", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
			<div class="col-md-4">
				@include('helpers.form_control', [
					'type' => 'select',
					'class' => '',
					'name' => 'bounce_handler_id',
					'label' => trans("messages.bounce_handler"),
					'value' => $server->bounce_handler_id,
					'help_class' => 'sending_server',
					'include_blank' => trans('messages.choose'),
					'options' => Acelle\Model\BounceHandler::getSelectOptions(),
					'rules' => Acelle\Model\SendingServer::rules(request()->type)
				])
			</div>
		@endif
		@if (array_key_exists("feedback_loop_handler_id", Acelle\Model\SendingServer::types()[request()->type]["cols"]))
			<div class="col-md-4">
				@include('helpers.form_control', [
					'type' => 'select',
					'class' => '',
					'name' => 'feedback_loop_handler_id',
					'label' => trans("messages.feedback_loop_handler"),
					'value' => $server->feedback_loop_handler_id,
					'help_class' => 'sending_server',
					'include_blank' => trans('messages.choose'),
					'options' => Acelle\Model\FeedbackLoopHandler::getSelectOptions(),
					'rules' => Acelle\Model\SendingServer::rules(request()->type)
				])
			</div>
		@endif
	</div>
@endif
<hr >
<div class="text-left">
	@if(Auth::user()->customer->can('test', $server))
		<a
			href="{{ action('SendingServerController@test', $server->uid) }}"
			type="button"
			class="btn bg-teal mr-5 btn-icon modal_link"
			data-in-form="true"
			data-method="GET"
		>
			<i class="icon-rotate-cw3"></i> {{ trans('messages.sending_server.test') }}
		</a>
	@endif
	<button class="btn bg-teal mr-5"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
	<a href="{{ action('SendingServerController@index') }}" type="button" class="btn bg-grey">
		<i class="icon-cross2"></i> {{ trans('messages.cancel') }}
	</a>
</div>
