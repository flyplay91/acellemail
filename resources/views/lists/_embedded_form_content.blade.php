@if ($stylesheet == 'yes')
	<link href="{{ URL::asset('css/embedded.css') }}" rel="stylesheet" type="text/css">
@endif

@if (!empty($custom_css))
	<style>{{ $custom_css }}</style>
@endif

<div class="subscribe-embedded-form">
	@if (!empty($form_title))
		<h2>{{ $form_title }}</h2>
	@endif
	<p class="text-sm text-right"><span class="text-danger">*</span> {{ trans('messages.indicates_required') }}</p>
		@if (!isset($preview))
			<form action="{{ action('MailListController@embeddedFormCaptcha', $list->uid) }}" method="POST" class="form-validate-jqueryz">
		@endif

			@foreach ($list->getFields as $field)
				@if ($field->visible || $show_invisible == 'yes')
					@if(($required_fields == 'yes' && $field->required) || $required_fields == 'no')
						@if ($field->type == "text")
							@include('helpers.form_control', ['type' => $field->type, 'name' => $field->tag, 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "number")
							@include('helpers.form_control', ['type' => 'number', 'name' => $field->tag, 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "textarea")
							@include('helpers.form_control', ['type' => 'textarea', 'name' => $field->tag, 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "dropdown")
							@include('helpers.form_control', ['type' => 'select', 'class' => 'form-control', 'name' => $field->tag, 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'options' => $field->getSelectOptions(), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "multiselect")
							@include('helpers.form_control', ['multiple' => true, 'class' => 'form-control', 'type' => 'select', 'name' => $field->tag . "[]", 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'options' => $field->getSelectOptions(), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "checkbox")
							@include('helpers.form_control', ['multiple' => true, 'type' => 'checkboxes', 'name' => $field->tag . "[]", 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'options' => $field->getSelectOptions(), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "radio")
							@include('helpers.form_control', ['multiple' => true, 'type' => 'radio', 'name' => $field->tag, 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'options' => $field->getSelectOptions(), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "date")
							@include('helpers.form_control', ['multiple' => true, 'type' => 'date', 'name' => $field->tag . "[]", 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'options' => $field->getSelectOptions(), 'rules' => $list->getFieldRules()])
						@elseif ($field->type == "datetime")
							@include('helpers.form_control', ['multiple' => true, 'type' => 'datetime', 'name' => $field->tag . "[]", 'label' => $field->label, 'value' => (isset($values[$field->tag]) ? $values[$field->tag] : $field->default_value), 'options' => $field->getSelectOptions(), 'rules' => $list->getFieldRules()])
						@endif
					@endif
				@endif
			@endforeach

			<div class="form-button">
			  <button class="btn btn-primary">{{ trans('messages.subscribe') }}</button>
			</div>

		@if (!isset($preview))
			<form>
		@endif

        </div>

		@if (!isset($preview))
			@if ($javascript == 'yes')
				<script_tmp type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery.min.js') }}"></script_tmp>
				<script_tmp type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script_tmp>
				<script_tmp>
					$.noConflict();
					jQuery( document ).ready(function( $ ) {
						$(".subscribe-embedded-form form").validate({
							rules: {
							  EMAIL: {
								required: true,
								email: true,
								remote: "{{ action('MailListController@checkEmail', $list->uid) }}"
							  }
							}
						});
					});
				</script_tmp>
			@endif
		@endif
