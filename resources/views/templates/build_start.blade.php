@extends('layouts.frontend')

@section('title', trans('messages.create_template'))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('tinymce/tinymce.min.js') }}"></script>
        
    <script type="text/javascript" src="{{ URL::asset('js/editor.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">				
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
                    <li><a href="{{ action("TemplateController@index") }}">{{ trans('messages.templates') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-pencil"></i> {{ trans('messages.select_a_template_layout') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
    
                <div class="row">
					@foreach(Acelle\Model\Template::templateStyles() as $name => $style)
						<div class="col-xxs-12 col-xs-6 col-sm-3 col-md-2">
							<a href="{{ action('TemplateController@build', ['style' => $name]) }}">
								<div class="panel panel-flat panel-template-style">
									<div class="panel-body">
										<img src="{{ url('images/template_styles/'.$name.'.png') }}" />
										<h5 class="mb-0 text-center">{{ trans('messages.'.$name) }}</h5>
									</div>
								</div>
							</a>
						</div>
					@endforeach
                </div>
@endsection
