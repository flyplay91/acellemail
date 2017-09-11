					<div class="row">
						<div class="col-md-3">
							<div class="sub_section">
								<h2 class="text-semibold text-teal-800">{{ trans('messages.profile_photo') }}</h2>
								<div class="media profile-image">
									<div class="media-left">
										<a href="#" class="upload-media-container">
											<img preview-for="image" empty-src="{{ URL::asset('assets/images/placeholder.jpg') }}" src="{{ action('AdminController@avatar', $admin->uid) }}" class="img-circle" alt="">
										</a>
										<input type="file" name="image" class="file-styled previewable hide">
										<input type="hidden" name="_remove_image" value='' />
									</div>
									<div class="media-body text-center">
										<h5 class="media-heading text-semibold">{{ trans('messages.upload_photo') }}</h5>
										{{ trans('messages.photo_at_least', ["size" => "300px x 300px"]) }}
										<br /><br />
										<a href="#upload" onclick="$('input[name=image]').trigger('click')" class="btn btn-xs bg-teal mr-10"><i class="icon-upload4"></i> {{ trans('messages.upload') }}</a>
										<a href="#remove" class="btn btn-xs bg-grey-800 remove-profile-image"><i class="icon-trash"></i> {{ trans('messages.remove') }}</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="sub_section">
								<h2 class="text-semibold text-teal-800">{{ trans('messages.account') }}</h2>

								@include('helpers.form_control', ['type' => 'text', 'name' => 'email', 'value' => $admin->email(), 'help_class' => 'profile', 'rules' => $admin->rules()])

								@include('helpers.form_control', ['type' => 'select',
																'name' => 'admin_group_id',
																'label' => trans('messages.group'),
																'value' => $admin->admin_group_id,
																'options' => Acelle\Model\AdminGroup::getSelectOptions(Auth::user()->admin),
																'include_blank' => trans('messages.choose'),
																'rules' => $admin->rules()
															])

								@include('helpers.form_control', ['type' => 'password', 'label'=> trans('messages.new_password'), 'name' => 'password', 'rules' => $admin->rules()])

								@include('helpers.form_control', ['type' => 'password', 'name' => 'password_confirmation', 'rules' => $admin->rules()])

							</div>
						</div>
						<div class="col-md-5">
							<div class="sub_section">
								<h2 class="text-semibold text-teal-800">{{ trans('messages.basic_information') }}</h2>

								<div class="row">
									<div class="col-md-6">
										@include('helpers.form_control', ['type' => 'text', 'name' => 'first_name', 'value' => $admin->first_name, 'rules' => $admin->rules()])
									</div>
									<div class="col-md-6">
										@include('helpers.form_control', ['type' => 'text', 'name' => 'last_name', 'value' => $admin->last_name, 'rules' => $admin->rules()])
									</div>
								</div>

								@include('helpers.form_control', ['type' => 'select', 'name' => 'timezone', 'value' => $admin->timezone, 'options' => Tool::getTimezoneSelectOptions(), 'include_blank' => trans('messages.choose'), 'rules' => $admin->rules()])

								@include('helpers.form_control', ['type' => 'select', 'name' => 'language_id', 'label' => trans('messages.language'), 'value' => $admin->language_id, 'options' => Acelle\Model\Language::getSelectOptions(), 'include_blank' => trans('messages.choose'), 'rules' => $admin->rules()])

								@if (!$admin->hasCustomerAccount())
									<div class="form-group checkbox-right-switch">
										@include('helpers.form_control', [
											'type' => 'checkbox',
											'name' => 'create_customer_account',
											'label' => trans('messages.create_customer_account'),
											'value' => 'no',
											'options' => ['no', 'yes'],
											'help_class' => 'admin',
											'rules' => $admin->rules()
										])
									</div>
								@endif
							</div>
						</div>

					</div>
					<hr />
					<div class="text-right">
						<button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
					</div>
