    <a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#delete_confirm_model">aa</a>
	<!-- Basic modal -->
	<div id="delete_confirm_model" class="modal fade new-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="confirm-delete-form form-validate-jquery" onkeypress="return event.keyCode != 13;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
					</div>

					<div class="modal-body">

							<h6 class="mt-0"></h6>

							<div class="form-group">
								<label class="text-normal">{!! trans('messages.type_delete_to_confirm') !!}</label>
								<input class="form-control required" name="delete" />
							</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn btn-danger bg-grey delete-confirm-button ajax_link">{{ trans('messages.delete') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#link_confirm_model">Open modal</a>
	<!-- Basic modal -->
	<div id="link_confirm_model" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="confirm-link-form" onkeypress="return event.keyCode != 13;">
					<div class="modal-header bg-info-800">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
					</div>

					<div class="modal-body">

							<h6></h6>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn bg-info-800 link-confirm-button ajax_link">{{ trans('messages.confirm') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#list_delete_confirm_model">aa</a>
	<!-- Basic modal -->
	<div id="list_delete_confirm_model" class="modal fade new-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="list-confirm-delete-form form-validate-jquery" onkeypress="return event.keyCode != 13;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
					</div>

					<div class="modal-body">

							<div class="content">

							</div>

							<div class="form-group">
								<label class="text-normal">{!! trans('messages.type_delete_to_confirm') !!}</label>
								<input class="form-control required" name="delete" />
							</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn btn-danger bg-grey list-delete-confirm-button ajax_link">{{ trans('messages.delete') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#quota_modal"></a>
	<!-- Basic modal -->
	<div id="quota_modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-body">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('messages.close') }}</button>
				</div>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#copy_list"></a>
	<!-- Basic modal -->
	<div id="copy_list" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-teal">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{ trans('messages.name_new_list') }}</h4>
				</div>
				<form action="{{ action('MailListController@copy') }}" method="POST" class="ajax_copy_list_form form-validate-jqueryz">
					{{ csrf_field() }}
					<input type="hidden" name="copy_list_uid" />

					<div class="modal-body">

						@include('helpers.form_control', [
							'type' => 'text',
							'name' => 'copy_list_name',
							'value' => '',
							'label' => trans('messages.what_would_you_like_to_name_your_list'),
							'options' => Acelle\Model\CustomerGroup::timeUnitOptions(),
							'include_blank' => trans('messages.choose'),
							'help_class' => 'list',
							'rules' => ['copy_list_name' => 'required']
						])


						<div class="text-right">
							<button type="submit" class="btn btn-info bg-teal-600 mr-5">{{ trans('messages.copy') }}</button>
							<button type="button" class="btn btn-default ml-0 copy-list-close" data-dismiss="modal">{{ trans('messages.close') }}</button>
						</div>

					</div>

				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<div class="ui-pnotify bg-warning" style="background-color: rgba(255,87,34,0.7); width: auto; right: 20px; top: auto; bottom: 20px; opacity: 1; display: block; overflow: visible; cursor: auto;">
		<div class="btn-close-pnotify"><i class="icon-cross2"></i></div>
		@if (null !== Session::get('orig_customer_id') && Auth::user()->customer)
			<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
				<h4 class="ui-pnotify-title">{!! trans('messages.current_login_as', ["name" => Auth::user()->customer->displayName()]) !!}</h4>
				<div class="ui-pnotify-text">
					{!! trans('messages.click_to_return_to_origin_user', ["link" => action("CustomerController@loginBack")]) !!}
				</div>
				<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
			</div>
		@endif
		@if (null !== Session::get('orig_admin_id') && Auth::user()->admin)
			<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
				<h4 class="ui-pnotify-title">{!! trans('messages.current_login_as', ["name" => Auth::user()->admin->displayName()]) !!}</h4>
				<div class="ui-pnotify-text">
					{!! trans('messages.click_to_return_to_origin_user', ["link" => action("Admin\AdminController@loginBack")]) !!}
				</div>
				<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
			</div>
		@endif
		@if (Acelle\Model\Setting::get("site_online") == 'false')
			<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
				<h4 class="ui-pnotify-title">{!! trans('messages.site_is_offline') !!}</h4>
				<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
			</div>
		@endif

		@if (\Acelle\Library\Tool::currentView() == 'frontend')
			<!-- Alert if customer don't have any subscription -->
			@if (is_object(\Auth::user()->customer) &&
				\Auth::user()->customer->notHaveAnyPlan())
				<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
					<h4 class="ui-pnotify-title">
					{!! trans('messages.not_have_any_plan_notification', [
						'link' => action('AccountController@subscription'),
					]) !!}
					</h4>
					<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
				</div>
			@endif

			<!-- Alert if customer have any subscription but none of them is active -->
			@if (is_object(\Auth::user()->customer)
				&& !is_object(\Auth::user()->customer->getCurrentSubscription())
				&& !\Auth::user()->customer->notHaveAnyPlan())
				<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
					<h4 class="ui-pnotify-title">
					{!! trans('messages.not_have_active_plan_notification', [
						'link' => action('AccountController@subscription'),
					]) !!}
					</h4>
					<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
				</div>
			@endif

			<!-- Alert if customer use their own sending servers but haven't had any one yet -->
			@if (
				is_object(\Auth::user()->customer) &&
				\Auth::user()->customer->getOption("sending_server_option") == \Acelle\Model\Plan::SENDING_SERVER_OPTION_OWN &&
				!\Auth::user()->customer->activeSendingServers()->count())
				<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
					<h4 class="ui-pnotify-title">
					{!! trans('messages.not_have_any_customer_sending_server', [
						'link' => action('SendingServerController@select'),
					]) !!}
					</h4>
					<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
				</div>
			@endif
		@endif

		@if (\Acelle\Library\Tool::currentView() == 'backend')
			<!-- Alert if system haven't had any sending servers -->
			@if (
				!\Acelle\Model\SendingServer::getAllAdminActive()->count()
			)
				<div class="alert ui-pnotify-container alert-primary ui-pnotify-shadow" style="min-height: 16px; overflow: hidden;">
					<h4 class="ui-pnotify-title">
					{!! trans('messages.not_have_any_admin_sending_server', [
						'link' => action('Admin\SendingServerController@select'),
					]) !!}
					</h4>
					<div style="margin-top: 10px; clear: both; text-align: right; display: none;"></div>
				</div>
			@endif
		@endif

        @include('layouts._license_notify')
	</div>

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#copy_campaign"></a>
	<!-- Basic modal -->
	<div id="copy_campaign" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-teal">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{ trans('messages.name_new_campaign') }}</h4>
				</div>
				<form action="{{ action('CampaignController@copy') }}" method="POST" class="ajax_copy_campaign_form form-validate-jquery">
					{{ csrf_field() }}
					<input type="hidden" name="copy_campaign_uid" />

					<div class="modal-body">

						@include('helpers.form_control', [
							'type' => 'text',
							'name' => 'copy_campaign_name',
							'value' => '',
							'label' => trans('messages.what_would_you_like_to_name_your_campaign'),
							'options' => Acelle\Library\Tool::timeUnitOptions(),
							'include_blank' => trans('messages.choose'),
							'help_class' => 'campaign',
							'rules' => ['copy_campaign_name' => 'required']
						])


						<div class="text-right">
							<button type="submit" class="btn btn-info bg-teal-600 mr-5">{{ trans('messages.copy') }}</button>
							<button type="button" class="btn btn-default ml-0 copy-campaign-close" data-dismiss="modal">{{ trans('messages.close') }}</button>
						</div>

					</div>

				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#send_a_test_email"></a>
	<!-- Basic modal send a test email -->
	<div id="send_a_test_email" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-header bg-teal">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('messages.send_a_test_email') }}</h4>
			</div>
			<div class="modal-content">
				<form sending-text='<i class="icon-spinner10 spinner position-left"></i> {{ trans('messages.sending_please_wait') }}' action="{{ action('CampaignController@sendTestEmail') }}" method="POST" class="ajax_send_a_test_email_form form-validate-jquery">
					{{ csrf_field() }}
					<input type="hidden" name="send_test_email_campaign_uid" />

					<div class="modal-body">

						@include('helpers.form_control', [
							'type' => 'text',
							'name' => 'send_test_email',
							'class' => 'email',
							'value' => '',
							'label' => trans('messages.enter_an_email_address_for_testing_campaign'),
							'options' => Acelle\Library\Tool::timeUnitOptions(),
							'include_blank' => trans('messages.choose'),
							'help_class' => 'campaign',
							'rules' => ['send_test_email' => 'required']
						])


						<div class="text-right">
							<button type="submit" class="btn btn-info bg-teal-600 mr-5"><i class="icon-paperplane ml-5"></i> {{ trans('messages.send') }}</button>
							<button type="button" class="btn btn-default ml-0 copy-campaign-close" data-dismiss="modal">{{ trans('messages.close') }}</button>
						</div>

					</div>

				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<!-- Basic modal -->
	<div id="delete_auto_campaign_confirm_model" class="modal fade new-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="confirm-link-form" onkeypress="return event.keyCode != 13;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
					</div>

					<div class="modal-body">

							<h6 class="mt-0"></h6>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn btn-danger bg-grey auto-campaign-delete-confirmed">{{ trans('messages.confirm') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<!-- Basic modal -->
	<div id="delete_auto_event_confirm_model" class="modal fade new-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<form class="confirm-link-form" onkeypress="return event.keyCode != 13;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
					</div>

					<div class="modal-body">

							<h6 class="mt-0"></h6>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">{{ trans('messages.cancel') }}</button>
						<a class="btn btn-danger bg-grey auto-event-delete-confirmed">{{ trans('messages.confirm') }}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<a style="display: none" type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#full_modal"></a>
	<!-- Basic modal -->
	<div id="full_modal" class="modal fade">
		<div class="modal-dialog modal-full width100">
			<div class="modal-content">

				<div class="modal-body">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('messages.close') }}</button>
				</div>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<!-- Basic modal -->
	<div id="list-form-modal" class="modal fade list-form-modal">
		<div class="modal-dialog modal-md">
			<div class="modal-header bg-teal">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
			</div>
			<div class="modal-content">
				<form action="" method="POST" class="ajax_upload_form form-validate-jquery">
					{{ csrf_field() }}
					<input type="hidden" name="_method" value="">
					<input type="hidden" name="uids" value="">

					<div class="modal-body">
						<h4></h4>
						@include('helpers.form_control', [
							'type' => 'textarea',
							'class' => '',
							'label' => trans('messages.description'),
							'name' => 'description',
							'value' => '',
							'help_class' => 'payment',
							'rules' => []
						])
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('messages.close') }}</button>
						<button type="submit" class="btn btn-primary bg-teal">{{ trans('messages.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->

	<!-- Basic modal -->
	<div id="paid-form-modal" class="modal fade list-form-modal">
		<div class="modal-dialog modal-md">
			<div class="modal-header bg-teal">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('messages.are_you_sure') }}</h4>
			</div>
			<div class="modal-content">
				<form action="" method="POST" class="ajax_upload_form form-validate-jquery">
					{{ csrf_field() }}
					<input type="hidden" name="_method" value="">
					<input type="hidden" name="uids" value="">

					<div class="modal-body">
						<h4></h4>
						@include('helpers.form_control', [
							'type' => 'text',
							'class' => '',
							'label' => trans('messages.tax_number'),
							'name' => 'tax_number',
							'value' => '',
							'help_class' => 'payment',
							'rules' => ['tax_number' => 'required']
						])
						@include('helpers.form_control', [
							'type' => 'text',
							'class' => '',
							'label' => trans('messages.billing_address'),
							'name' => 'billing_address',
							'value' => '',
							'help_class' => 'payment',
							'rules' => ['billing_address' => 'required']
						])
						@include('helpers.form_control', [
							'type' => 'textarea',
							'class' => '',
							'label' => trans('messages.description'),
							'name' => 'description',
							'value' => '',
							'help_class' => 'payment',
							'rules' => []
						])
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('messages.close') }}</button>
						<button type="submit" class="btn btn-primary bg-teal">{{ trans('messages.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /basic modal -->
