                        @if (Auth::user()->admin->getPermission("setting_general") == 'yes')
							<div class="tab-pane active" id="top-smtp">
								<div class="row">
									<div class="col-md-6">
										@include('helpers.form_control', [
											'type' => 'select',
											'name' => 'env[MAIL_DRIVER]',
											'label' => trans('messages.mail_driver'),
											'value' => (isset($env["MAIL_DRIVER"]) ? $env["MAIL_DRIVER"] : ""),
											'options' => [["value" => "mail", "text" => trans('messages.php_mail')],["value" => "smtp", "text" => trans('messages.smtp')]],
											'help_class' => 'env',
											'rules' => $env_rules
										])
									</div>
								</div>
								<div class="smtp_box">
									<div class="row box">
										<div class="col-md-6">
											@include('helpers.form_control', [
												'type' => 'text',
												'name' => 'env[MAIL_HOST]',
												'label' => trans('messages.hostname'),
												'value' => (isset($env["MAIL_HOST"]) ? $env["MAIL_HOST"] : ""),
												'help_class' => 'env',
												'rules' => $env_rules
											])
										</div>
										<div class="col-md-6">
											<div class="row box">
												<div class="col-md-6">
													@include('helpers.form_control', [
														'type' => 'text',
														'name' => 'env[MAIL_PORT]',
														'label' => trans('messages.port'),
														'value' => (isset($env["MAIL_PORT"]) ? $env["MAIL_PORT"] : ""),
														'help_class' => 'env',
														'rules' => $env_rules
													])
												</div>
												<div class="col-md-6">
													@include('helpers.form_control', [
														'type' => 'text',
														'name' => 'env[MAIL_ENCRYPTION]',
														'label' => trans('messages.encryption'),
														'value' => (isset($env["MAIL_ENCRYPTION"]) ? $env["MAIL_ENCRYPTION"] : ""),
														'help_class' => 'env',
														'rules' => $env_rules
													])
												</div>
											</div>
										</div>
									</div>
										
									<div class="row box">
										<div class="col-md-6">
											@include('helpers.form_control', [
												'type' => 'text',
												'name' => 'env[MAIL_USERNAME]',
												'label' => trans('messages.username'),
												'value' => (isset($env["MAIL_USERNAME"]) ? $env["MAIL_USERNAME"] : ""),
												'help_class' => 'env',
												'rules' => $env_rules
											])
										</div>
										<div class="col-md-6">
											@include('helpers.form_control', [
												'type' => 'text',
												'name' => 'env[MAIL_PASSWORD]',
												'label' => trans('messages.password'),
												'value' => (isset($env["MAIL_PASSWORD"]) ? $env["MAIL_PASSWORD"] : ""),
												'help_class' => 'env',
												'rules' => $env_rules
											])
										</div>
									</div>
										
									<div class="row box">
										<div class="col-md-6">
											@include('helpers.form_control', [
												'type' => 'text',
												'name' => 'env[MAIL_FROM_EMAIL]',
												'label' => trans('messages.from_email'),
												'value' => (isset($env["MAIL_FROM_EMAIL"]) ? $env["MAIL_FROM_EMAIL"] : ""),
												'help_class' => 'env',
												'rules' => $env_rules
											])
										</div>
										<div class="col-md-6">
											@include('helpers.form_control', [
												'type' => 'text',
												'name' => 'env[MAIL_FROM_NAME]',
												'label' => trans('messages.from_name'),
												'value' => (isset($env["MAIL_FROM_NAME"]) ? $env["MAIL_FROM_NAME"] : ""),
												'help_class' => 'env',
												'rules' => $env_rules
											])  
										</div>
									</div>
								</div>
                                
								<br />
								<div class="text-left">
									<button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
								</div>
							</div>
						@endif