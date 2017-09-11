@extends('layouts.backend')

@section('title', trans('messages.upload_template'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
		
	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>		
@endsection

@section('page_header')

			<div class="page-title">				
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
					<li><a href="{{ action("Admin\TemplateController@index") }}">{{ trans('messages.language') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-upload"></i> {{ trans('messages.upload_language') }}</span>
				</h1>				
			</div>

@endsection

@section('content')
    
                <div class="row">
                    <div class="col-md-8">
					
						<div class="alert alert-info">
							{!! trans('messages.language_upload_guide') !!}
						</div>
                            
                        @if (count($errors) > 0)
                            <!-- Form Error List -->
                            <div class="alert alert-danger alert-noborder">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
					
                        <form enctype="multipart/form-data" action="{{ action('Admin\LanguageController@upload', $language->uid) }}" method="POST" class="form-validate-jquery">
                            {{ csrf_field() }}							
							
                            @include('helpers.form_control', ['required' => true, 'type' => 'file', 'label' => trans('messages.upload_file'), 'name' => 'file'])
								
							<div class="text-right">
                                <button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.upload') }}</button>
                                <a href="{{ action('Admin\LanguageController@index') }}" class="btn bg-grey-800"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
                            </div>
                            
                        </form>  
                        
                    </div>
                </div>
@endsection
