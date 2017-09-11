@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.import'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

    @include("lists._header")

@endsection

@section('content')

	@include("lists._menu")

	<h2 class="text-bold text-teal-800"><i class="icon-users4"></i> {{ trans('messages.import_subscribers') }}</h2>

	<div class="row">
		<div class="col-md-12">
			<form
				process-url="{{ action('SubscriberController@importProccess', $list->uid) }}"
				action="{{ action('SubscriberController@import', $list->uid) }}" method="POST" class="ajax_upload_form form-validate-jquery">
				{{ csrf_field() }}

				<div class="alert alert-info">
					{!! trans('messages.import_file_help', ['csv_link' => url('files/csv_import_example.csv')]) !!}
				</div>

				<div class="upload_file before">
					{!! trans('messages.max_file_upload', ['size' => \Acelle\Library\Tool::maxFileUploadInBytes()]) !!}

					@include('helpers.form_control', ['required' => true, 'type' => 'file', 'label' => trans('messages.upload_file'), 'name' => 'file', 'value' => $list->name])

					<div class="text-left">
						<button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.import') }}</button>
					</div>
					<br />
				</div>

				<div class="form-group processing hide">
					<h4>{{ trans('messages.please_wait_import') }}</h4>
					<div class="progress progress-lg">
						<!--<div class="progress-bar progress-success bg-success-400" style="width: 20%">
							<span class="sr-only"><span class="number">20</span>% Complete</span>
						</div>-->

						<div class="progress-bar progress-error progress-bar-danger" style="width: 0%">
							<span><span class="number">0</span>% {{ trans('messages.error') }}</span>
						</div>

						<div class="progress-bar progress-total active" style="width: 0%">
							<span><span class="number">0</span>% {{ trans('messages.complete') }}</span>
						</div>
					</div>
					<label></label>
					<a data-method="POST" link-confirm="{{ trans('messages.cancel_system_jobs_confirm') }}" data-href="{{ action('SystemJobController@cancel', ["uids" => ""]) }}" type="button" class="btn bg-grey btn-icon cancel processing">
						{{ trans('messages.cancel') }}
					</a>
				</div>

				<div class="form-group finish hide">
					<div class="text-left">
						<a target="_blank" href="{{ action('SubscriberController@downloadImportLog', ["list_uid" => $list->uid]) }}" type="button" class="btn bg-teal success">
							<i class="icon-download"></i> {{ trans('messages.download_import_log') }}
						</a>
						<a href="#retry" class="btn bg-grey-800 mr-10 retry"><i class="icon-reload-alt"></i> {{ trans('messages.import_another') }}</a>
					</div>
				</div>
			</form>

		</div>
	</div>

	<script>
		var current_list_uid = '{{ $list->uid }}';

		function check_process(first) {
			var form = $("form.ajax_upload_form")
			var url = form.attr("process-url")
			var bar = form.find('.progress-total');
			var bar_s = form.find('.progress-success');
			var bar_e = form.find('.progress-error');

			$.ajax({
				url : url + "?&current_list_uid="+current_list_uid,
				type: "GET",
				success:function(result, textStatus, jqXHR)
				{
					if(result != "none" && result.job.status != "cancelled") {
						// Update cancel link
						form.find('.cancel').attr('href', form.find('.cancel').attr('data-href') + result.job.id);

						// update progress bar
						var total = parseFloat(result.data.total);
						var success = parseFloat(result.data.processed);
						//var error = parseFloat(result.data.error);

						if (typeof(first) == "undefined") {
							form.find(".processing label").html(result.message);
							bar.find(".number").html(Math.round(success/total*100));
							bar.css({
								width: (success/total*100) + '%'
							});
							//bar_e.find(".number").html(Math.round(error/total*100));
							//bar_e.css({
							//    width: (error/total*100) + '%'
							//});

							if (result.data.status == "failed") {
								form.find('.finish').removeClass("hide");
								form.find('.before').addClass("hide");
								form.find(".processing").removeClass('hide');
								form.find('.success').addClass("hide");
							}

							if (result.data.status == "done") {
								form.find('.upload_file .progress-bar').addClass('success');
								form.find(".before").addClass('hide');
								form.find(".processing").removeClass('hide');
								form.find('.finish').removeClass('hide');
								form.find('.success').removeClass("hide");

								form.find(".processing h4").html('{{ trans('messages.import_completed') }}');

								if(!form.find('.finish').hasClass('hide')) {
									// Success alert
									swal({
										title: '{{ trans('messages.import_completed') }}',
										text: "",
										confirmButtonColor: "#00695C",
										type: "success",
										allowOutsideClick: true,
										confirmButtonText: LANG_OK,
									});
								}
							}

							if (result.job.status == "cancelled") {
								form.find('.finish').addClass("hide");
								form.find('.before').removeClass("hide");
								form.find(".processing").addClass('hide');
								form.find('.success').removeClass("hide");
							}
						}

						if (result.data.status == "running" || result.data.status == "new") {
							setTimeout("check_process()", 1000);
							form.find(".before").addClass('hide');
							form.find(".processing").removeClass('hide');
							form.find('.finish').addClass('hide');
							form.find('.cancel').removeClass('hide');
						} else {
							form.find('.cancel').addClass('hide');
						}
					}

					if (result.job.status == "cancelled") {
						form.find('.retry').trigger('click');
					}
				}
			})
		}

		$(document).on("submit", "form.ajax_upload_form", function() {
			var form = $(this);

			if(!form.valid()) {
				$("label.error").insertAfter(".uploader");
			} else {
				setTimeout("check_process()", 2000);

				var formData = new FormData($(this)[0]);
				var url = form.attr('action');
				var bar = form.find('.progress-total');
				var bar_s = form.find('.progress-success');
				var bar_e = form.find('.progress-error');

				// return to 0
				bar_s.find(".number").html(parseInt(0));
				bar_s.css({
					width: 0 + '%'
				});
				bar_e.find(".number").html(parseInt(0));
				bar_e.css({
					width: 0 + '%'
				});

				form.find(".processing h4").html('{{ trans('messages.please_wait_import') }}');

				$(".processing label").html("{{ trans('messages.uploading') }}");
				$(".before").addClass('hide');
				$(".processing").removeClass('hide');

				$.ajax({
					url: url,
					type: 'POST',
					data: formData,
					success: function (data) {
						check_process();

						if(data == 'max_file_upload') {
							// Success alert
							swal({
								title: '{{ trans('messages.file_import_to_large') }}',
								text: "",
								confirmButtonColor: "#00695C",
								type: "error",
								allowOutsideClick: true,
								confirmButtonText: LANG_OK,
							});

							form.find('.retry').trigger('click');
							return;
						}
					},
					cache: false,
					contentType: false,
					processData: false
				});
			}

			return false;
		});

		$(document).on("click", ".retry", function() {
			$(".input[type=file]").val("");
			$(".finish").addClass('hide');
			$(".processing").addClass('hide');
			$(".before").removeClass('hide');
			var bars = $(".progress-bar");
			bars.find(".number").html(parseInt(0));
			bars.css({
				width: 0 + '%'
			});
		});

		$(document).ready(function() {
			check_process(true);
		});
	</script>

	@include("subscribers._import")

@endsection
