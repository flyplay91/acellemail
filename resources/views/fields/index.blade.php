@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.manage_list_fields') )

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

			@include("lists._header")

@endsection

@section('content')

                @include("lists._menu")

                <h2 class="text-bold text-teal-800"><i class="icon-list3"></i> {{ trans('messages.manage_list_fields') }}</h2>
                <br />
                <p>{!! trans('messages.fields_intro') !!}</p>

				@if ($errors->has("miss_main_field_tag"))
					<div class="text-danger text-semibold">
						<strong>{{ $errors->first("miss_main_field_tag") }}</strong>
					</div>
				@endif
				@if ($errors->has("conflict_field_tags"))
					<div class="text-danger text-semibold">
						<strong>{{ $errors->first("conflict_field_tags") }}</strong>
					</div>
				@endif

                <form action="{{ action('FieldController@store', $list->uid) }}" class="listing-form"
					sort-urla="{{ action('FieldController@sort', $list->uid) }}"
					per-page="1"
					method="POST"
                >
					{{ csrf_field() }}

                    @if ($fields->count() > 0)
                        <table class="table table-box table-box-head field-list"
                            current-page="1"
                        >
								<th width="1%"></th>
                                <th>{{ trans('messages.field_label_and_type') }}</th>
                                <th>{{ trans('messages.required?') }}</th>
                                <th>{{ trans('messages.visible?') }}</th>
                                <th>{{ trans('messages.tag') }}</th>
                                <th>{{ trans('messages.default_value') }}</th>
								<th></th>
                            @foreach ($fields as $key => $item)
                                <tr class="draggable" rel="{{ $item->uid }}">
									<td>
										<input type="hidden" class="custom_order"
                                            value="{{ $item->custom_order }}"
											name="fields[{{ $item->uid }}][custom_order]"
                                        />
										<input type="hidden" class="node"
                                            custom-order="{{ $item->custom_order }}"
                                            value="{{ $item->uid }}"
											name="fields[{{ $item->uid }}][uid]"
                                        />
										<i data-action="move" class="icon icon-more2 list-drag-button"></i>
									</td>
                                    <td class="text-nowrap">
                                        @include('helpers.form_control', ['type' => 'text', 'name' => 'fields[' . $item->uid . '][label]', 'label' => '', 'subfix' => trans('messages.' . $item->type), 'value' => $item->label, 'help_class' => 'field'])
										<input type="hidden"
                                            value="{{ $item->type }}"
											name="fields[{{ $item->uid }}][type]"
                                        />
                                    </td>
                                    <td class="text-nowrap">
										@include('helpers.form_control', ['disabled' => $item->tag == 'EMAIL', 'type' => 'checkbox', 'name' => 'fields[' . $item->uid . '][required]', 'label' => '', 'value' => $item->required, 'options' => [false,true], 'help_class' => 'field'])
									</td>
                                    <td class="text-nowrap">
										@include('helpers.form_control', ['disabled' => $item->tag == 'EMAIL', 'type' => 'checkbox', 'name' => 'fields[' . $item->uid . '][visible]', 'label' => '', 'value' => $item->visible, 'options' => [false,true], 'help_class' => 'field'])
									</td>
                                    <td class="text-nowrap">
										@include('helpers.form_control', ['disabled' => $item->tag == 'EMAIL', 'type' => 'text', 'name' => 'fields[' . $item->uid . '][tag]', 'label' => '', 'value' => $item->tag, 'help_class' => 'field', 'prefix' => "[", 'subfix' => "]"])
									</td>
                                    <td class="text-nowrap">
										@include('helpers.form_control', ['type' => Acelle\Model\Field::getControlNameByType($item->type), 'name' => 'fields[' . $item->uid . '][default_value]', 'label' => '', 'value' => $item->default_value, 'help_class' => 'field'])
									</td>
									<td>
										@if ($item->tag != 'EMAIL')
											@if (is_object(Acelle\Model\Field::findByUid($item->uid)))
												<a no-ajax="true" href="{{ action('FieldController@delete', ['list_uid' => $list->uid, 'uid' => $item->uid]) }}" delete-confirm="{!! trans('messages.delete_field_alert') !!}" class="btn bg-danger-400 remove-field-button">
													<i class="icon-trash"></i>
												</a>
											@else
												<a href="#delete" class="btn bg-danger-400 remove-not-saved-field"><i class="icon-trash"></i></a>
											@endif
										@endif
									</td>
                                </tr>

								@if (count($item->fieldOptions))
									<tr class="child" parent="{{ $item->uid }}">
										<td></td>
										<td colspan="5" class="sub_field_options">
											<div class="row">
												<div class="col-md-12">
													<div class="row label-value-groups">
														@foreach ($item->fieldOptions as $key => $option)
															<div class="col-md-6 text-nowrap label-value-group" rel="{{ $option->uid }}">
																<div class="pull-left mr-10">@include('helpers.form_control', ['type' => 'text', 'placeholder' => trans('messages.label'), 'name' => 'fields[' . $item->uid . '][options][' . $option->uid . '][label]', 'label' => '', 'value' => $option->label, 'help_class' => 'field'])</div>
																<div class="pull-left mr-10">@include('helpers.form_control', ['type' => 'text', 'placeholder' => trans('messages.value'), 'name' => 'fields[' . $item->uid . '][options][' . $option->uid . '][value]', 'label' => '', 'value' => $option->value, 'help_class' => 'field'])</div>
																<div class="pull-left"><a href="#remove" onclick="$(this).parents('.label-value-group').remove()" class="btn btn-xs bg-grey-600"><i class="icon-cross"></i></a></div>
															</div>
														@endforeach
													</div>
												</div>
											</div>
										</td>
										<td>
											<a href="#add_more" class="btn bg-teal add_label_value_group">{{ trans('messages.add_more') }}</a>
										</td>
									</tr>
								@endif
                            @endforeach
                        </table>
                    @endif
					<br />
					<h4>{{ trans('messages.add_field') }}</h4>
					<div>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "text"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="text">
							<i class="icon-font-size2"></i> {{ trans('messages.text_field') }}
						</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "number"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="number">
							<i class="icon-sort-numeric-asc"></i> {{ trans('messages.number_field') }}
						</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "dropdown"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="dropdown">
							<i class="icon-menu2"></i> {{ trans('messages.dropdown_field') }}
						</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "multiselect"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="multiselect"><i class="icon-menu3"></i> {{ trans('messages.multiselect_field') }}</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "checkbox"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="checkbox"><i class="icon-checkbox-partial2"></i> {{ trans('messages.checkbox_field') }}</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "radio"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="radio"><i class="icon-circles"></i> {{ trans('messages.radio_field') }}</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "date"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="date"><i class="icon-calendar52"></i> {{ trans('messages.date_field') }}</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "datetime"]) }}" class="btn btn-default btn-xs add-custom-field-button mr-10" type_name="datetime"><i class="icon-alarm"></i> {{ trans('messages.datetime_field') }}</span>
						<span sample-url="{{ action("FieldController@sample", ['list_uid' => $list->uid, "type" => "textarea"]) }}" class="btn btn-default btn-xs add-custom-field-button" type_name="textarea"><i class="icon-menu6"></i> {{ trans('messages.textarea_field') }}</span>
					</div>

					<hr /><br />
					<div class="">
						<button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.save_change') }}</button>
					</div>
                </form>
@endsection
