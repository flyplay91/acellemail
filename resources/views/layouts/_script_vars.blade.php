<script>
	var DATATABLE_TRANSLATE_URL = '{{ action('Controller@datatable_locale') }}';
	var JVALIDATE_TRANSLATE_URL = '{{ action('Controller@jquery_validate_locale') }}';
	var APP_URL = '{{ url('/') }}';
	var LANG_OK = '{{ trans('messages.ok') }}';
	var LANG_DELETE_VALIDATE = '{{ trans('messages.delete_validate') }}';
	var LANG_DATE_FORMAT = '{{ trans('messages.j_date_format') }}';
	var LANG_ANY_DATETIME_FORMAT = '{{ trans('messages.any_datetime_format') }}';
	var CSRF_TOKEN = "{{ csrf_token() }}";
</script>