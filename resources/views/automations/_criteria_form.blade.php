                                <?php $index = isset($index) ? $index : '__index__' ?>
								
								<div class="row condition-line" rel="{{ $index }}">
									<div class="col-md-4">
										<div class="form-group">
											<select class="select" name="custom_criteria[{{ $index }}][field_uid]">
												<optgroup label="{{ trans('messages.Subscriber_attributes') }}">
													@foreach($automation->defaultMailList->getFields as $field)
														@if (!in_array($field->type, ['datetime','date']))
															<option {{ isset($criteria) && $criteria["field_uid"] == $field->uid ? 'selected' : '' }} value="{{ $field->uid }}">{{ $field->label }}</option>
														@endif
													@endforeach
												</optgroup>
											</select>
										</div>
									</div>
									<div class="col-md-3 operator-col">
										@include('helpers.form_control', [
											'type' => 'select',
											'name' => 'custom_criteria[' . $index . '][operator]',
											'label' => '',
											'value' => isset($criteria) ? $criteria["operator"] : '',
											'options' => Acelle\Model\AutoEvent::operators()
										])
									</div>
									<div class="col-md-4 value-col" {!! (isset($criteria) && in_array($criteria["operator"], [Acelle\Model\AutoEvent::OPERATOR_BLANK, Acelle\Model\AutoEvent::OPERATOR_NOT_BLANK])) ? 'style="visibility: hidden"' : '' !!}>
										@include('helpers.form_control', [
											'type' => 'text',
											'name' => 'custom_criteria[' . $index . '][value]',
											'label' => '',
											'placeholder' => trans('messages.value'),
											'value' => isset($criteria) ? $criteria["value"] : ''
										])
									</div>
									<div class="col-md-1">
										<a onclick="$(this).parents('.condition-line').remove()" href="#delete" class="btn bg-danger-400"><i class="icon-trash"></i></a>
									</div>
								</div>