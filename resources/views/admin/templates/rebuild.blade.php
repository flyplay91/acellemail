@extends('layouts.builder')

@section('title', trans('messages.create_template'))

@section('content')

        <div class="left">
            <form action="{{ action('Admin\TemplateController@update', $template->uid) }}" method="POST" class="ajax_upload_form form-validate-jquery">
                {{ csrf_field() }}
                <input type="hidden" name="_method" value="PATCH">
                
                <input type="text" name="name" value="{{ $template->name }}" class="required" />
                <textarea class="hide template_content" name="content">{{ $template->content }}</textarea>
                <button class="btn btn-primary mr-5">{{ trans('messages.save') }}</button>
                <a href="{{ action('Admin\TemplateController@index') }}" class="btn bg-slate">{{ trans('messages.cancel') }}</a>
            </form>
        </div>
        <div class="right">
            
        </div>
    
@endsection

@section('template_content')

    {!! $template->content !!}
    
@endsection