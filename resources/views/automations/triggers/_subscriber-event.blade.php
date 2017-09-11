                            <div class="col-md-6">
                                <hr>
                                <h5 class="text-semibold">{{ trans('messages.Trigger_the_automation_for_the_following_recur') }}</h5>
								@include('helpers.form_control', [
									'type' => 'radio',
									'name' => 'subscriber_event',
									'class' => '',
									'label' => '',
									'value' => null !== $first_event->getDataValue('event') ? $first_event->getDataValue('event') : '',
									'options' => $first_event->automation->defaultMailList->getSubscriberFieldSelectOptions(),
									'rules' => []
								])
							</div>
                            
                            