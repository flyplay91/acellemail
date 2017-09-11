<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title') - {{ \Acelle\Model\Setting::get("site_name") }}</title>
	
	@include('layouts._favicon')
	
	@include('layouts._head')
	
	@include('layouts._css')
	
	@include('layouts._js')
	
</head>

<body class="navbar-top">

	<!-- Main navbar -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-header">
			<a class="navbar-brand" href="{{ action('HomeController@index') }}">
				<img src="{{ URL::asset('assets/images/logo_light.png') }}" alt="">
			</a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a class="mobile-menu-button" data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-menu7"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
                @can("customer_access", Auth::user())
                    <li rel0="HomeController">
                        <a href="{{ action('HomeController@index') }}">
                            <i class="icon-exit2"></i> {{ trans('messages.frontend') }}
                        </a>
                    </li>
                @endif
                @can("admin_access", Auth::user())
                    <li rel0="HomeController">
                        <a href="{{ action('Admin\HomeController@index') }}">
                            <i class="icon-enter2"></i> {{ trans('messages.backend') }}
                        </a>
                    </li>
                @endif
			</ul>

			<ul class="nav navbar-nav navbar-right">
				
				@include('layouts._top_activity_log')

				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<img src="{{ action('CustomerController@avatar', Auth::user()->customer->uid) }}" alt="">
						<span>{{ Auth::user()->customer->displayName() }}</span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						@if (Auth::user()->userGroup->frontend_access)
							<li><a href="{{ action("HomeController@index") }}"><i class="icon-exit2"></i> {{ trans('messages.frontend') }}</a></li>
							<li class="divider"></li>
						@endif
						@if (Auth::user()->userGroup->backend_access)
							<li><a href="{{ action("Admin\HomeController@index") }}"><i class="icon-enter2"></i> {{ trans('messages.admin_view') }}</a></li>
							<li class="divider"></li>
						@endif
						<li class="dropdown">
							<a href="#" class="top-quota-button" data-url="{{ action("AccountController@quotaLog") }}">
								<i class="icon-stats-bars4"></i>
								<span class="">{{ trans('messages.used_quota') }}</span>
							</a>
						</li>
						<li><a href="{{ action("AccountController@profile") }}"><i class="icon-profile"></i> {{ trans('messages.account') }}</a></li>
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
