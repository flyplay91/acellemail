{{ csrf_field() }}

<div class="row">
	<div class="col-sm-12 col-md-12">
		<h3>{{ trans('messages.sending_domain.title') }}</h3>
	</div>
	<div class="col-sm-6 col-md-6">
		@include('helpers.form_control', [
			'type' => 'text',
			'class' => '',
			'name' => 'name',
			'label' => trans('messages.domain_name'),
			'value' => $server->name,
			'help_class' => 'sending_server',
			'rules' => Acelle\Model\SendingDomain::rules()
		])
	</div>
	<div class="col-sm-6 col-md-6">
		<div class="form-group checkbox-right-switch">
			@include('helpers.form_control', [
				'type' => 'checkbox',
				'class' => '',
				'name' => 'signing_enabled',
				'value' => $server->signing_enabled,
				'help_class' => 'sending_domain',
				'options' => [0, 1],
				'rules' => Acelle\Model\SendingDomain::rules()
			])
		</div>
	</div>
</div>
@if (isset($server->id))
	<div class="row">
		<div class="col-sm-6 col-md-6">
			@include('helpers.form_control', [
				'type' => 'textarea',
				'class' => 'dkim_box code',
				'readonly' => 'readonly',
				'name' => 'dkim_private',
				'value' => $server->dkim_private,
				'help_class' => 'sending_domain',
				'rules' => Acelle\Model\SendingDomain::rules()
			])
		</div>
		<div class="col-sm-6 col-md-6">
			@include('helpers.form_control', [
				'type' => 'textarea',
				'class' => 'dkim_box code',
				'readonly' => 'readonly',
				'name' => 'dkim_public',
				'value' => $server->dkim_public,
				'help_class' => 'sending_domain',
				'rules' => Acelle\Model\SendingDomain::rules()
			])
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12 col-md-12">
			<h3>{{ trans('messages.sending_domain.dkim_title') }}</h3>
			<p>{!! trans('messages.sending_domain.dkim_wording') !!}</p>
			<pre>{{ $server->getDnsDkimConfig() }}</pre>
		</div>
	</div>
@endif
<hr >
<div class="text-left">
	<button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
</div>
