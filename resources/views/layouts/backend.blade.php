<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title') - {{ \Acelle\Model\Setting::get("site_name") }}</title>

	@include('layouts._favicon')

	@include('layouts._head')

	@include('layouts._css')

	@include('layouts._js')

	<script>
		$.cookie('last_language_code', '{{ Auth::user()->admin->getLanguageCode() }}');
	</script>

</head>

<body class="navbar-top  color-scheme-{{ Auth::user()->admin->getColorScheme() }}">

	<!-- Main navbar -->
	<div class="navbar navbar-{{ Auth::user()->admin->getColorScheme() == "white" ? "default" : "inverse" }} navbar-fixed-top">
		<div class="navbar-header">
			<a class="navbar-brand" href="{{ action('Admin\HomeController@index') }}">
				@if (\Acelle\Model\Setting::get('site_logo_small'))
                    <img src="{{ URL::asset(\Acelle\Model\Setting::get('site_logo_small')) }}" alt="">
                @else
                    <img src="{{ URL::asset('images/default_site_logo_small_' . (Auth::user()->admin->getColorScheme() == "white" ? "dark" : "light") . '.png') }}" alt="">
                @endif
			</a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a class="mobile-menu-button" data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-menu7"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li rel0="HomeController">
					<a href="{{ action('Admin\HomeController@index') }}">
						<i class="icon-home"></i> {{ trans('messages.dashboard') }}
					</a>
				</li>

				@if (Auth::user()->can("read", new Acelle\Model\Customer())
					|| Auth::user()->admin->can("read", new Acelle\Model\Subscription())
				)
					<li class="dropdown language-switch"
						rel0="CustomerGroupController"
						rel1="CustomerController"
					>
						<a class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-user"></i> {{ trans('messages.customer') }}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							@if (Auth::user()->can("read", new Acelle\Model\Customer()))
								<li rel0="CustomerController">
									<a href="{{ action('Admin\CustomerController@index') }}">
										<i class="icon-users"></i> {{ trans('messages.customers') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->can("read", new Acelle\Model\Subscription()))
								<li rel0="SubscriptionController">
									<a href="{{ action('Admin\SubscriptionController@index') }}">
										<i class="icon-quill4"></i> {{ trans('messages.subscriptions') }}
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif

				@if (
					Auth::user()->can("read", new Acelle\Model\Plan())
					|| Auth::user()->can("read", new Acelle\Model\Currency())
					|| Auth::user()->can("read", new Acelle\Model\PaymentMethod())
				)
					<li class="dropdown language-switch"
						rel0="PlanController"
						rel1="CurrencyGroupController"
					>
						<a class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-credit-card2"></i> {{ trans('messages.plan') }}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							@if (Auth::user()->can("read", new Acelle\Model\Plan()))
								<li rel0="PlanController">
									<a href="{{ action('Admin\PlanController@index') }}">
										<i class="icon-clipboard2"></i> {{ trans('messages.plans') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->can("read", new Acelle\Model\Currency()))
								<li rel0="CurrencyController">
									<a href="{{ action('Admin\CurrencyController@index') }}">
										<i class="icon-coins"></i> {{ trans('messages.currencies') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->can("read", new Acelle\Model\PaymentMethod()))
								<li rel0="PaymentMethodController">
									<a href="{{ action('Admin\PaymentMethodController@index') }}">
										<i class="icon-credit-card2"></i> {{ trans('messages.payment_methods') }}
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif

				@if (
					Auth::user()->admin->getPermission("admin_read") != 'no'
					|| Auth::user()->admin->getPermission("admin_group_read") != 'no'
				)
					<li class="dropdown language-switch"
						rel0="AdminGroupController"
						rel1="AdminController"
					>
						<a class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-user-tie"></i> {{ trans('messages.admin') }}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							@if (Auth::user()->admin->getPermission("admin_read") != 'no')
								<li rel0="AdminController">
									<a href="{{ action('Admin\AdminController@index') }}">
										<i class="icon-users"></i> {{ trans('messages.admins') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("admin_group_read") != 'no')
								<li rel0="AdminGroupController">
									<a href="{{ action('Admin\AdminGroupController@index') }}">
										<i class="icon-users4"></i> {{ trans('messages.admin_groups') }}
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif

				@if (
					Auth::user()->admin->getPermission("sending_domain_read") != 'no'
					|| Auth::user()->admin->getPermission("sending_server_read") != 'no'
					|| Auth::user()->admin->getPermission("bounce_handler_read") != 'no'
					|| Auth::user()->admin->getPermission("fbl_handler_read") != 'no'
					|| Auth::user()->admin->getPermission("email_verification_server_read") != 'no'
					|| Auth::user()->admin->can('read', new \Acelle\Model\SubAccount())
				)
					<li class="dropdown language-switch"
						rel0="BounceHandlerController"
						rel1="FeedbackLoopHandlerController"
						rel2="SendingServerController"
						rel3="SendingDomainController"
						rel3="SubAccountController"
					>
						<a class="dropdown-toggle" data-toggle="dropdown">
							<i class="glyphicon glyphicon-transfer"></i> {{ trans('messages.sending') }}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							@if (Auth::user()->admin->getPermission("sending_server_read") != 'no')
								<li rel0="SendingServerController">
									<a href="{{ action('Admin\SendingServerController@index') }}">
										<i class="icon-server"></i> {{ trans('messages.sending_severs') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->can('read', new \Acelle\Model\SubAccount()))
								<li rel0="SubAccountController">
									<a href="{{ action('Admin\SubAccountController@index') }}">
										<i class="icon-drive"></i> {{ trans('messages.sub_accounts') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("bounce_handler_read") != 'no')
								<li rel0="BounceHandlerController">
									<a href="{{ action('Admin\BounceHandlerController@index') }}">
										<i class="glyphicon glyphicon-share"></i> {{ trans('messages.bounce_handlers') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("fbl_handler_read") != 'no')
								<li rel0="FeedbackLoopHandlerController">
									<a href="{{ action('Admin\FeedbackLoopHandlerController@index') }}">
										<i class="glyphicon glyphicon-retweet"></i> {{ trans('messages.feedback_loop_handlers') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("sending_domain_read") != 'no')
								<li rel0="SendingDomainController">
									<a href="{{ action('Admin\SendingDomainController@index') }}">
										<i class="icon-earth"></i> {{ trans('messages.sending_domains') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("email_verification_server_read") != 'no')
								<li rel0="EmailVerificationServerController">
									<a href="{{ action('Admin\EmailVerificationServerController@index') }}">
										<i class="icon-database-check"></i> {{ trans('messages.email_verification_servers') }}
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif
				<li class="dropdown language-switch"
					rel0="TemplateController"
					rel1="LayoutController"
					rel2="LanguageController"
					rel3="SettingController"
				>
					<a class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-gear"></i> {{ trans('messages.setting') }}
                        <span class="caret"></span>
					</a>
                    <ul class="dropdown-menu">
						@if (
							Auth::user()->admin->getPermission("setting_general") != 'no' ||
							Auth::user()->admin->getPermission("setting_sending") != 'no' ||
							Auth::user()->admin->getPermission("setting_system_urls") != 'no' ||
							Auth::user()->admin->getPermission("setting_background_job") != 'no'
						)
							<li rel0="SettingController">
								<a href="{{ action('Admin\SettingController@index') }}">
									<i class="icon-equalizer2"></i> {{ trans('messages.all_settings') }}
								</a>
							</li>
						@endif
						@if (Auth::user()->admin->getPermission("template_read") != 'no')
							<li rel0="TemplateController">
								<a href="{{ action('Admin\TemplateController@index') }}">
									<i class="icon-magazine"></i> {{ trans('messages.template_gallery') }}
								</a>
							</li>
						@endif
						@if (Auth::user()->admin->getPermission("layout_read") != 'no')
							<li rel0="LayoutController">
								<a href="{{ action('Admin\LayoutController@index') }}">
									<i class="glyphicon glyphicon-file"></i> {{ trans('messages.page_form_layout') }}
								</a>
							</li>
						@endif
						@if (Auth::user()->admin->getPermission("language_read") != 'no')
							<li rel0="LanguageController">
								<a href="{{ action('Admin\LanguageController@index') }}">
									<i class="glyphicon glyphicon-flag"></i> {{ trans('messages.language') }}
								</a>
							</li>
						@endif
                    </ul>
				</li>

				@if (
					Auth::user()->admin->getPermission("report_blacklist") != 'no'
					|| Auth::user()->admin->getPermission("report_tracking_log") != 'no'
					|| Auth::user()->admin->getPermission("report_bounce_log") != 'no'
					|| Auth::user()->admin->getPermission("report_feedback_log") != 'no'
					|| Auth::user()->admin->getPermission("report_open_log") != 'no'
					|| Auth::user()->admin->getPermission("report_click_log") != 'no'
					|| Auth::user()->admin->getPermission("report_unsubscribe_log") != 'no'
				)
					<li class="dropdown language-switch"
						rel0="TrackingLogController"
						rel1="OpenLogController"
						rel2="ClickLogController"
						rel3="FeedbackLogController"
						rel4="BlacklistController"
						rel5="UnsubscribeLogController"
						rel6="BounceLogController"
					>
						<a class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-file-text2"></i> {{ trans('messages.report') }}
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							@if (Auth::user()->admin->getPermission("report_blacklist") != 'no')
								<li rel0="BlacklistController">
									<a href="{{ action('Admin\BlacklistController@index') }}">
										<i class="glyphicon glyphicon-minus-sign"></i> {{ trans('messages.blacklist') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("report_tracking_log") != 'no')
								<li rel0="TrackingLogController">
									<a href="{{ action('Admin\TrackingLogController@index') }}">
										<i class="icon-file-text2"></i> {{ trans('messages.tracking_log') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("report_bounce_log") != 'no')
								<li rel0="BounceLogController">
									<a href="{{ action('Admin\BounceLogController@index') }}">
										<i class="icon-file-text2"></i> {{ trans('messages.bounce_log') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("report_feedback_log") != 'no')
								<li rel0="FeedbackLogController">
									<a href="{{ action('Admin\FeedbackLogController@index') }}">
										<i class="icon-file-text2"></i> {{ trans('messages.feedback_log') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("report_open_log") != 'no')
								<li rel0="OpenLogController">
									<a href="{{ action('Admin\OpenLogController@index') }}">
										<i class="icon-file-text2"></i> {{ trans('messages.open_log') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("report_click_log") != 'no')
								<li rel0="ClickLogController">
									<a href="{{ action('Admin\ClickLogController@index') }}">
										<i class="icon-file-text2"></i> {{ trans('messages.click_log') }}
									</a>
								</li>
							@endif
							@if (Auth::user()->admin->getPermission("report_unsubscribe_log") != 'no')
								<li rel0="UnsubscribeLogController">
									<a href="{{ action('Admin\UnsubscribeLogController@index') }}">
										<i class="icon-file-text2"></i> {{ trans('messages.unsubscribe_log') }}
									</a>
								</li>
							@endif
						</ul>
					</li>
				@endif
			</ul>

			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<img src="{{ action('AdminController@avatar', Auth::user()->admin->uid) }}" alt="">
						<span>{{ Auth::user()->admin->displayName() }}</span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						@can("customer_access", Auth::user())
							<li><a href="{{ action("HomeController@index") }}"><i class="icon-exit2"></i> {{ trans('messages.customer_view') }}</a></li>
							<li class="divider"></li>
						@endif
						<li><a href="{{ action("Admin\AccountController@profile") }}"><i class="icon-profile"></i> {{ trans('messages.account') }}</a></li>
						<li rel0="AccountController/api">
							<a href="{{ action("Admin\AccountController@api") }}" class="level-1">
								<i class="icon-key position-left"></i> {{ trans('messages.api') }}
							</a>
						</li>
						<li><a href="{{ url("/logout") }}"><i class="icon-switch2"></i> {{ trans('messages.logout') }}</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->

	<!-- Page header -->
	<div class="page-header">
		<div class="page-header-content">

			@yield('page_header')

		</div>
	</div>
	<!-- /page header -->

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- display flash message -->
				@include('common.errors')

				<!-- main inner content -->
				@yield('content')

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->


		<!-- Footer -->
		<div class="footer text-muted">
			{!! trans('messages.copy_right') !!}
		</div>
		<!-- /footer -->

	</div>
	<!-- /page container -->

	@include("layouts._modals")
</body>
</html>
