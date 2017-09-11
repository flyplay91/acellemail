                            <div class="col-md-8">
                                <hr>
                                <h5 class="text-semibold">{{ trans('messages.Trigger_the_automation_for_the_following_conditions_recur') }}</h5>
								@if ($errors->has("custom_criteria_empty"))
									<div class="text-danger text-semibold">
										<strong>{{ $errors->first("custom_criteria_empty") }}</strong>
									</div>
									<br />
								@endif
                                <div class="addable-multiple-form">
                                    <div class="addable-multiple-container">
										@if (null !== $first_event->getDataValue('criteria'))
											<?php $num = 0 ?>
											@foreach ($first_event->getDataValue('criteria') as $key => $criteria)
												@include('automations._criteria_form', [
													'criteria' => $criteria,
													'index' => $num
												])
												<?php $num++ ?>
											@endforeach
										@endif
                                    </div>
                                        
                                    <br />
                                    <a sample-url="{{ action('AutomationController@criteriaForm', $automation->uid) }}" href="#add_condition" class="btn btn-info bg-info-800 add-form">
                                        <i class="icon-plus2"></i> {{ trans('messages.Add_criteria') }}
                                    </a>
                                </div>
							</div>