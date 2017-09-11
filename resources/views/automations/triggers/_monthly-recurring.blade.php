                            <div class="col-md-10">
                                <hr>								
								<div class="row">
									<div class="col-md-12">
										<div class="">
											<div class="row ">
												<div class="col-md-3 day-of-month-box">
													@include('helpers.form_control', [
														'type' => 'checkboxes',
														'name' => 'monthly_recurring_days[]',
														'class' => '',
														'check_all_none' => true,
														'label' => trans('messages.choose_a_day'),
														'value' => null !== $first_event->getDataValue('days') ? implode(",", $first_event->getDataValue('days')) : '',
														'options' => Acelle\Library\Tool::dayOfMonthSelectOptions(),
														'rules' => []
													])
												</div>												
												<div class="col-md-3">
													@include('helpers.form_control', [
														'type' => 'checkboxes',
														'name' => 'monthly_recurring_months[]',
														'multiple' => '',
														'check_all_none' => true,
														'label' => ucfirst(trans('messages.months')),
														'value' => null !== $first_event->getDataValue('months') ? implode(",", $first_event->getDataValue('months')) : '',
														'include_blank' => trans('messages.Every_month'),
														'options' => Acelle\Library\Tool::monthSelectOptions(),
														'rules' => []
													])
												</div>
												<div class="col-md-3">
													@include('helpers.form_control', [
														'type' => 'time',
														'name' => 'monthly_recurring_time',
														'label' => ucfirst(trans('messages.at')),
														'value' => null !== $first_event->getDataValue('time') ? Acelle\Library\Tool::timeStringFromTimestamp(Acelle\Library\Tool::dateTimeFromString($first_event->getDataValue('time'))) : '',
														'rules' => $first_event->rules(),
														'help_class' => 'trigger'
													])
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>