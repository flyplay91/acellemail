<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('title')</title>

	@include('layouts._favicon')

	@include('layouts._head')

	@include('layouts._css')

	@include('layouts._js')
</head>

<body class="bg-slate-800 color-scheme-{{ isset($list) && is_object($list) ? $list->user->getFrontendScheme() : '' }}">
	<!-- Page container -->
	<div class="page-container login-container">

		<div class="text-right user-top-right">
			@if (is_object(\Auth::user()))
				<a class="dropdown-toggle" data-toggle="dropdown">
					<img src="{{ action('CustomerController@avatar', Auth::user()->customer->uid) }}" alt="">
					<span>{{ Auth::user()->customer->displayName() }}</span>
				</a>
				/
				<a href="{{ url("/logout") }}"><i class="icon-switch2"></i> {{ trans('messages.logout') }}</a>
			@else
				<a class="text-semibold" href="{{ url("/login") }}"><i class="icon-user"></i> {{ trans('messages.login') }}</a>
			@endif
		</div>

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

                @yield('content')

            </div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

		<!-- Footer -->
		<div class="footer text-white">
			{!! trans('messages.copy_right_light') !!}
		</div>
		<!-- /footer -->

	</div>
	<!-- /page container -->
</body>
</html>
