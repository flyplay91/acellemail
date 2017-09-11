                                <?php
                                    $label = isset($label) ? $label : (Lang::has('messages.'.$name) ? trans('messages.'.$name) : '');
                                    $var_name = str_replace('[]', '', $name);
                                    $var_name = str_replace('][', '.', $var_name);
                                    $var_name = str_replace('[', '.', $var_name);
                                    $var_name = str_replace(']', '', $var_name);
                                    $classes = (isset($rules) && isset($rules[$var_name])) ? ' '.str_replace('|', ' ', $rules[$var_name]) : '';
                                    $required = (isset($rules) && isset($rules[$var_name]) && in_array('required', explode('|', $rules[$var_name]))) ? true : '';
                                ?>

								@if ($type == 'checkbox')
									@include('helpers._' . $type)
								@else
									<div class="form-group{{ $errors->has($var_name) ? ' has-error' : '' }} control-{{ $type }}">


										@if (!empty($label) && $type != 'checkbox2')
											<label>
												{!! $label !!}
												@if ($required)
													<span class="text-danger">*</span>
												@endif
												@if (isset($check_all_none))
													&nbsp;&nbsp;&nbsp;
													<a href="#all" class="checkboxes_check_all">{{ trans('messages.all') }}</a>
													| <a href="#none" class="checkboxes_check_none">{{ trans('messages.none') }}</a>
												@endif
											</label>
										@endif

										@if ($type == 'textarea')
											@if ($errors->has($var_name))
												<span class="help-block">
													<strong>{{ $errors->first($var_name) }}</strong>
												</span>
											@endif
										@endif

										@if (!empty($prefix))
											<span class="prefix">
												{!! $prefix !!}
											</span>
										@endif

										@include('helpers._' . $type)

										@if (!empty($quick_note))
											<span class="quick_note">
												{!! $quick_note !!}
											</span>
										@endif

										@if (!empty($subfix))
											<span class="subfix">
												{!! $subfix !!}
											</span>
										@endif

										@if (isset($help_class) && Lang::has('messages.' . $help_class . '.' . $name . '.help'))
											<div class="help alert alert-info">
												{!! trans('messages.' . $help_class . '.' . $name . '.help') !!}
											</div>
										@endif

										@if ($type != 'textarea')
											@if ($errors->has($var_name))
												<span class="help-block">
													<strong>{{ $errors->first($var_name) }}</strong>
												</span>
											@endif
										@endif
									</div>
								@endif
