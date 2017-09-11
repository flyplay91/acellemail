                            <div class="col-md-6">
								<hr>
								<div class="row">
									<div class="col-md-6">
										@include('helpers.form_control', [
											'type' => 'date',
											'class' => '_from_now',
											'name' => 'specific_day',
											'label' => trans('messages.Send_on'),
											'value' => null !== $first_event->getDataValue('datetime') ? Acelle\Library\Tool::dayStringFromTimestamp(Acelle\Library\Tool::dateTimeFromString($first_event->getDataValue('datetime'))) : '',
											'rules' => $first_event->rules(),
											'help_class' => 'trigger'
										])
									</div>
									<div class="col-md-6">
										<div class="row pt-29">
											<div class="col-md-2">
												<label class="col-center">{{ trans('messages.at') }}</label>
											</div>
											<div class="col-md-10">
												@include('helpers.form_control', [
													'type' => 'time',
													'name' => 'specific_time',
													'label' => '',
													'value' => null !== $first_event->getDataValue('datetime') ? Acelle\Library\Tool::timeStringFromTimestamp(Acelle\Library\Tool::dateTimeFromString($first_event->getDataValue('datetime'))) : '',
													'rules' => $first_event->rules(),
													'help_class' => 'trigger'
												])
											</div>
										</div>
									</div>
								</div>
							</div>