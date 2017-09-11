@extends('layouts.install')

@section('title', trans('messages.configuration'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('content')

<form action="{{ action('InstallController@siteInfo') }}" method="POST" class="form-validate-jqueryz">
	{!! csrf_field() !!}
	
	<h3 class="text-teal-800"><i class="icon-office"></i> {{ trans('messages.general') }}</h3>
    <div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'site_name',
                'value' => (isset($site_info["site_name"]) ? $site_info["site_name"] : ""),
                'help_class' => 'install',
                'rules' => ["site_name" => "required"]
            ])
		</div>
		<div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'site_keyword',
                'value' => (isset($site_info["site_keyword"]) ? $site_info["site_keyword"] : ""),
                'help_class' => 'install',
                'rules' => ["site_keyword" => "required"]
            ])
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'license',
				'label' => trans('messages.license_optional'),
                'value' => (isset($site_info["license"]) ? $site_info["license"] : ""),
                'help_class' => 'install',
                'rules' => []
            ])
            
        </div>
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'textarea',
                'name' => 'site_description',
                'value' => (isset($site_info["site_description"]) ? $site_info["site_description"] : ""),
                'help_class' => 'install',
                'rules' => ["site_description" => "required"]
            ])
        </div>
    </div>
	<hr />
	<h3 class="text-teal-800"><i class="icon-user-tie"></i> {{ trans('messages.admin_info') }}</h3>
	<div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'first_name',
                'value' => (isset($site_info["first_name"]) ? $site_info["first_name"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
        </div>
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'last_name',
                'value' => (isset($site_info["last_name"]) ? $site_info["last_name"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
        </div>
    </div>
        
    <div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'email',
                'value' => (isset($site_info["email"]) ? $site_info["email"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
        </div>
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'password',
                'value' => (isset($site_info["password"]) ? $site_info["password"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])      
        </div>
    </div>
			
    <div class="row">
        <div class="col-md-6">
			@include('helpers.form_control', ['type' => 'select', 'name' => 'timezone', 'value' => (isset($site_info["timezone"]) ? $site_info["timezone"] : ""), 'options' => Tool::getTimezoneSelectOptions(), 'include_blank' => trans('messages.choose'), 'rules' => $rules])
        </div>
		<div class="col-md-6">
			<div class="form-group checkbox-right-switch">
				@include('helpers.form_control', [
					'type' => 'checkbox',
					'name' => 'create_customer_account',
					'label' => trans('messages.create_customer_account'),
					'value' => (isset($site_info["create_customer_account"]) ? $site_info["create_customer_account"] : "yes"),
					'options' => ['no', 'yes'],
					'help_class' => 'admin',
					'rules' => $rules
				])
			</div>
        </div>
    </div>
	
	<hr />
	<h3 class="text-teal-800"><i class="icon-envelop5"></i> {{ trans('messages.system_email_configuration') }}</h3>
	<div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'select',
                'name' => 'mail_driver',
								'label' => trans('messages.mail_driver'),
                'value' => (isset($site_info["mail_driver"]) ? $site_info["mail_driver"] : ""),
								'options' => [["value" => "mail", "text" => trans('messages.php_mail')],["value" => "smtp", "text" => trans('messages.smtp')]],
                'help_class' => 'install',
                'rules' => $rules
            ])
				</div>
	</div>
		
	<div class="smtp_box">
		<div class="row">
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'text',
					'name' => 'smtp_hostname',
					'label' => trans('messages.hostname'),
					'value' => (isset($site_info["smtp_hostname"]) ? $site_info["smtp_hostname"] : ""),
					'help_class' => 'install',
					'rules' => $smtp_rules
				])
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6">
						@include('helpers.form_control', [
							'type' => 'text',
							'name' => 'smtp_port',
							'label' => trans('messages.port'),
							'value' => (isset($site_info["smtp_port"]) ? $site_info["smtp_port"] : ""),
							'help_class' => 'install',
							'rules' => $smtp_rules
						])
					</div>
					<div class="col-md-6">
						@include('helpers.form_control', [
							'type' => 'text',
							'name' => 'smtp_encryption',
							'label' => trans('messages.encryption'),
							'value' => (isset($site_info["smtp_encryption"]) ? $site_info["smtp_encryption"] : ""),
							'help_class' => 'install',
							'rules' => $smtp_rules
						])
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'text',
					'name' => 'smtp_username',
					'label' => trans('messages.username'),
					'value' => (isset($site_info["smtp_username"]) ? $site_info["smtp_username"] : ""),
					'help_class' => 'install',
					'rules' => $smtp_rules
				])
			</div>
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'text',
					'name' => 'smtp_password',
					'label' => trans('messages.password'),
					'value' => (isset($site_info["smtp_password"]) ? $site_info["smtp_password"] : ""),
					'help_class' => 'install',
					'rules' => $smtp_rules
				])
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'text',
					'name' => 'smtp_from_email',
					'label' => trans('messages.from_email'),
					'value' => (isset($site_info["smtp_from_email"]) ? $site_info["smtp_from_email"] : ""),
					'help_class' => 'install',
					'rules' => $smtp_rules
				])
			</div>
			<div class="col-md-6">
				@include('helpers.form_control', [
					'type' => 'text',
					'name' => 'smtp_from_name',
					'label' => trans('messages.from_name'),
					'value' => (isset($site_info["smtp_from_name"]) ? $site_info["smtp_from_name"] : ""),
					'help_class' => 'install',
					'rules' => $smtp_rules
				])
			</div>
		</div>
	</div>
	
	<hr >
	<div class="text-right">                                    
		<button data-wait="{{ trans('messages.button_processing') }}" type="submit" class="btn btn-primary bg-teal">{!! trans('messages.next') !!} <i class="icon-arrow-right14 position-right"></i></button>
	</div>
	
</form>
<script>
	function toogleMailer() {
		var value = $("select[name='mail_driver']").val();
		if(value == 'mail') {
			$('.smtp_box').hide();
		} else {
			$('.smtp_box').show();
		}
	}
	$(document).ready(function() {	
		toogleMailer();
		$("select[name='mail_driver']").change(function() {
			toogleMailer();
		});
	});
</script>
	
@endsection
