                    <div class="sub_section">
						<div class="row">
							<div class="col-md-8">
								@include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $segment->name, 'rules' => Acelle\Model\Segment::$rules])
							</div>
							<div class="col-md-4">
								@include('helpers.form_control', ['type' => 'select', 'name' => 'matching', 'label' => trans('messages.how_subscribers_matching'), 'value' => $segment->matching, 'help_class' => 'segment', 'options' => Acelle\Model\Segment::getTypeOptions(), 'rules' => Acelle\Model\Segment::$rules])
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<h4 class="text-semibold">{{ trans('messages.conditions') }}</h4>

								@if ($errors->has("segment_conditions_empty"))
									<div class="text-danger text-semibold">
										<strong>{{ $errors->first("segment_conditions_empty") }}</strong>
									</div>
								@endif

								<div class="segment-conditions-container">
									@foreach ($segment->segmentConditions as $condition)
										<div class="row condition-line" rel="{{ $condition->uid }}">
											<div class="col-md-4">
												<div class="form-group">
													<select class="select condition-field-select" name="conditions[{{ $condition->uid }}][field_id]">
														<optgroup label="{{ trans('messages.list_fields') }}">
															@foreach($list->getFields as $field)
																<option{{ $condition->field_id == $field->uid || $condition->field_id == $field->id ? " selected" : "" }} value="{{ $field->uid }}">{{ $field->label }}</option>
															@endforeach
														</optgroup>
														<optgroup label="{{ trans('messages.email_verification') }}">
															<option{{ strpos($condition->operator, 'verification') !== false ? " selected" : "" }} value="verification">{{ trans('messages.verification_result') }}</option>
														</optgroup>
													</select>
												</div>
											</div>
											<div class="col-md-7 operator_value_col" data-url="{{ action('SegmentController@conditionValueControl') }}">
												@include('segments._condition_value_control', [
													'field' => $field,
													'field_uid' => $condition->field_id,
													'operator' => $condition->operator,
													'index' => $condition->uid,
													'operator' => $condition->operator,
													'value' => $condition->value
												])
											</div>
											<div class="col-md-1">
												<a onclick="$(this).parents('.condition-line').remove()" href="#delete" class="btn bg-danger-400"><i class="icon-trash"></i></a>
											</div>
										</div>
									@endforeach
								</div>

								<br />
								<a sample-url="{{ action('SegmentController@sample_condition', $list->uid) }}" href="#add_condition" class="btn btn-info bg-teal-800 add-segment-condition">
									{{ trans('messages.add_condition') }}
								</a>

							</div>
						</div>
					</div>
