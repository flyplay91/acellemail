@extends('layouts.backend')

@section('title', trans('messages.settings'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
		
	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>		
@endsection

@section('page_header')

			<div class="page-title">				
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
				</ul>
				<h1>
					<span class="text-gear"><i class="icon-list2"></i> {{ trans('messages.settings') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
			<form action="{{ action('Admin\SettingController@index') }}" method="POST" class="form-validate-jqueryz">
				{{ csrf_field() }}
				
				<div class="tabbable">
					<ul class="nav nav-tabs nav-tabs-top">
						@if (Auth::user()->getOption("backend", "setting_general") == 'yes')
							<li class="active text-semibold"><a href="#top-general" data-toggle="tab">
								<i class="icon-equalizer2"></i> {{ trans('messages.general') }}</a></li>
						@endif
						@if (Auth::user()->getOption("backend", "setting_general") == 'yes')
							<li class="text-semibold"><a href="#top-smtp" data-toggle="tab">
								<i class="icon-envelop"></i> {{ trans('messages.system_email') }}</a></li>
						@endif
						@if (Auth::user()->getOption("backend", "setting_sending") == 'yes')
							<li class="text-semibold"><a href="#top-sending" data-toggle="tab">
								<i class="icon-paperplane"></i> {{ trans('messages.sending') }}</a></li>
						@endif
						@if (Auth::user()->getOption("backend", "setting_system_urls") == 'yes')
							<li class="text-semibold"><a href="#top-system_urls" data-toggle="tab">
								<i class="icon-link"></i> {{ trans('messages.system_urls') }}</a></li>
						@endif
							<li class="text-semibold"><a href="#top-cron_jobs" data-toggle="tab">
								<i class="icon-alarm"></i> {{ trans('messages.cron_jobs') }}</a></li>
					</ul>
	
					<div class="tab-content">
						
						@include("admin.settings._general")
						
						@include("admin.settings._smtp")
						
						@include("admin.settings._sending")
						
						@include("admin.settings._system_urls")						
						
						<div class="tab-pane" id="top-cron_jobs">						
							@include('elements._cron_jobs')							
						</div>
						
					</div>
				</div>
					
				
			</form>
				
			<script>
				function changeSelectColor() {
					$('.select2 .select2-selection__rendered, .select2-results__option').each(function() {							
						var text = $(this).html();
						if (text == '{{ trans('messages.default') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-teal-600'></i>");
							}
						}
						if (text == '{{ trans('messages.blue') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-blue'></i>");
							}
						}
						if (text == '{{ trans('messages.green') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-green'></i>");
							}
						}
						if (text == '{{ trans('messages.brown') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-brown'></i>");
							}
						}
						if (text == '{{ trans('messages.pink') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-pink'></i>");
							}
						}
						if (text == '{{ trans('messages.grey') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-grey'></i>");
							}
						}
						if (text == '{{ trans('messages.white') }}') {
							if($(this).find("i").length == 0) {
								$(this).prepend("<i class='icon-square text-white'></i>");
							}
						}
					});
				}
				
				function toogleMailer() {
					var value = $("select[name='env[MAIL_DRIVER]']").val();
					if(value == 'mail') {
						$('.smtp_box').hide();
					} else {
						$('.smtp_box').show();
					}
				}
				
				$(document).ready(function() {
					setInterval("changeSelectColor()", 100);
					
					@if (isset(request()->tab))			
                        $('a[href="#top-{{ request()->tab }}"]').trigger("click");
                    @endif
					
					// SMTP toogle
					toogleMailer();
					$("select[name='env[MAIL_DRIVER]']").change(function() {
						toogleMailer();
					});
				});
			</script>
@endsection
