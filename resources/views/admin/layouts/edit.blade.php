@extends('layouts.backend')

@section('title', trans('messages.' . $layout->alias))

@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('tinymce/tinymce.min.js') }}"></script>
        
    <script type="text/javascript" src="{{ URL::asset('js/editor.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">				
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
                    <li><a href="{{ action("Admin\LayoutController@index") }}">{{ trans('messages.page_form_layout') }}</a></li>
				</ul>
				<h1>
					<span class="text-semibold"><i class="icon-pencil"></i> {{ trans('messages.' . $layout->alias) }}</span>
				</h1>				
			</div>

@endsection

@section('content')
    
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ action('Admin\LayoutController@update', $layout->uid) }}" method="POST" class="ajax_upload_form form-validate-jquery">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PATCH">
                            
							@include('admin.layouts._form') 
                            
                        </form>  
                        
                    </div>
                </div>
@endsection
