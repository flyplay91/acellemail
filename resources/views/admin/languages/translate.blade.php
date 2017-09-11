@extends('layouts.backend')

@section('title', $language->name)
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ace/ace/ace.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ace/ace/theme-twilight.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ace/ace/mode-php.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ace/ace/mode-yaml.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ace/jquery-ace.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')
	
			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-share2"></i> {{ $language->name }}</span>
				</h1>
			</div>
				
@endsection

@section('content')
	
				@if (count($errors) > 0)
					<!-- Form Error List -->
					<div class="alert alert-danger alert-noborder">				
						<ul>
							<li>{{ $parse_error }}</li>
						</ul>
					</div>
				@endif
				
				<form enctype="multipart/form-data" action="{{ action('Admin\LanguageController@translate', ["id" => $language->uid, "file" => $filename]) }}" method="POST" class="form-validate-jqueryx">
					{{ csrf_field() }}
					
                    <div class="tabbable">
						<ul class="nav nav-tabs nav-tabs-top">
							<li class="{{ $filename == "messages" ? "active" : "" }} text-semibold">
								<a href="{{ action('Admin\LanguageController@translate', ["id" => $language->uid, "file" => "messages"]) }}">
									<i class="icon-menu6"></i> {{ trans('messages.messages') }}
								</a>
							</li>
							<li class="{{ $filename == "validation" ? "active" : "" }} text-semibold">
								<a href="{{ action('Admin\LanguageController@translate', ["id" => $language->uid, "file" => "validation"]) }}">
									<i class="icon-menu6"></i> {{ trans('messages.validation') }}
								</a>
							</li>
							<li class="{{ $filename == "pagination" ? "active" : "" }} text-semibold">
								<a href="{{ action('Admin\LanguageController@translate', ["id" => $language->uid, "file" => "pagination"]) }}">
									<i class="icon-menu6"></i> {{ trans('messages.pagination') }}
								</a>
							</li>
							<li class="{{ $filename == "passwords" ? "active" : "" }} text-semibold">
								<a href="{{ action('Admin\LanguageController@translate', ["id" => $language->uid, "file" => "passwords"]) }}">
									<i class="icon-menu6"></i> {{ trans('messages.passwords') }}
								</a>
							</li>
							<li class="{{ $filename == "auth" ? "active" : "" }} text-semibold">
								<a href="{{ action('Admin\LanguageController@translate', ["id" => $language->uid, "file" => "auth"]) }}">
									<i class="icon-menu6"></i> {{ trans('messages.auth') }}
								</a>
							</li>
						</ul>
							
						<div class="tab-content">
							<div class="tab-pane active" id="top-tab1">
								<textarea name="{{ $filename }}" class="my-code-messages" rows="20" style="width: 100%">{!! $content !!}</textarea>
							</div>							
						</div>
					</div>
					
					<hr>
					<div class="text-right">
						<button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
						<a href="{{ action('Admin\LanguageController@index') }}" type="button" class="btn bg-grey">
							<i class="icon-cross2"></i> {{ trans('messages.cancel') }}
						</a>
					</div>
					
				<form>
				
				<script>
					$('.my-code-messages').ace({ theme: 'twilight', lang: 'yaml' });
				</script>
	
@endsection