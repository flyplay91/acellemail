                                <!-- Sales stats -->
								<div id="event-{{ $auto_event->uid }}" class="timeline-row condition-line auto-event-line event-{{ $auto_event->status }}" rel="{{ $auto_event->uid }}">
									<div class="before"></div>
									<div class="timeline-icon timeline-icon-i">
                                        <!--<i class="icon-paperplane text-teal"></i>-->
									</div>
									<div class="panel panel-flat timeline-content">
										<div class="panel-heading">
											<form action="{{ action('AutoEventController@update', $auto_event->uid) }}" method="POST" class="form-validate-jqueryz auto-event-form">
												{{ csrf_field() }}
												<input type="hidden" name="_method" value="PATCH">
												<h6 class="panel-title text-semibold auto-event-form-line">
													{{ trans('messages.wait') }}
													@include('helpers.form_control', [
														'type' => 'text',
														'name' => 'delay_value',
														'class' => 'numeric',
														'label' => '',
														'value' => null !== $auto_event->getDataValue('delay_value') ? $auto_event->getDataValue('delay_value') : '',
														'rules' => []
													])
													@include('helpers.form_control', [
														'type' => 'select',
														'name' => 'delay_unit',
														'multiple' => '',
														'label' => '',
														'value' => null !== $auto_event->getDataValue('delay_unit') ? $auto_event->getDataValue('delay_unit') : '',
														'options' => Acelle\Model\AutoEvent::timeUnitOptions(),
														'rules' => []
													])
													{{ trans('messages.after_previous_email_is') }}
													<span class="select-medium">
														@include('helpers.form_control', [
															'type' => 'select',
															'name' => 'event_type',
															'label' => '',
															'value' => $auto_event->event_type,
															'options' => Acelle\Model\AutoEvent::emailEventOptions(),
															'rules' => []
														])
													</span>
													<span class="btn-controls">
														<button class="btn btn-primary bg-primary-800 btn-save"><i class="icon-checkmark4 mr-5"></i> {{ trans('messages.save') }}</button>
														<a class="btn btn-warning bg-grey-600 btn-close"><i class="icon-cross2 mr-5"></i> {{ trans('messages.close') }}</a>
													</span>
												</h6>
												<div class="heading-elements">
													<span class="text-muted2 list-status">
														<span class="label label-flat bg-{{ $auto_event->status }}">{{ trans('messages.auto_event_status_' . $auto_event->status) }}</span>
													</span>
													<span class="ml-20">														
														@if (\Gate::allows('enable', $auto_event))
															<a
																href="{{ action('AutoEventController@enable', $auto_event->uid) }}"
																link-method="PATCH"
																class="btn btn-default auto-event-action link-method link-out">
																	<i class=" icon-checkmark4"></i> {{ trans('messages.enable') }}
															</a>
														@endif
														@if (\Gate::allows('disable', $auto_event))
															<a
																href="{{ action('AutoEventController@disable', $auto_event->uid) }}"
																link-method="PATCH"
																class="btn btn-default auto-event-action link-method link-out">
																	<i class="icon-blocked"></i> {{ trans('messages.disable') }}
															</a>
														@endif
														<button
															data-popup="tooltip" title="{{ trans('messages.delete') }}"
															data-confirm="{{ trans('messages.delete_auto_event_confirm') }}"
															data-url="{{ action('AutoEventController@delete', $auto_event->uid) }}"
															data-id="{{ $auto_event->uid }}"
															class="btn btn-danger auto-event-delete">
																<i class="icon-trash"></i>
														</button>
														<div class="btn-group btn-updown link-out">
															<a data-popup="tooltip" title="{{ trans('messages.move_up') }}"
																href="{{ action('AutoEventController@moveUp', $auto_event->uid) }}"
																class="btn btn-default {{ \Gate::denies('moveUp', $auto_event) ? 'disabled' : '' }}"
																link-method="PATCH">
																<i class="icon-arrow-up5 position-left mr-0"></i>
															</a>
															<a data-popup="tooltip" title="{{ trans('messages.move_down') }}"
																href="{{ action('AutoEventController@moveDown', $auto_event->uid) }}"
																class="btn btn-default {{ \Gate::denies('moveDown', $auto_event) ? 'disabled' : '' }}"
																link-method="PATCH">
																<i class="icon-arrow-down5 mr-0"></i>
															</a>
														</div>
													</span>
												</div>
											</form>
										</div>
                                    </div>
                                    <div class="event-campaigns-box box-out">
										<div class="event-campaigns-container" data-url="{{ action('AutoEventController@campaigns', $auto_event->uid) }}">
										</div>

										<div class="text-center">
											<button class="btn btn-xs bg-teal event-campaign-add" data-url="{{ action('AutoEventController@addCampaign', $auto_event->uid) }}">
												<i class="icon-plus2"></i> {{ trans('messages.add_email') }}
											</button>
										</div>
									</div>
                                    <br /><br />
								</div>
								<!-- /sales stats -->