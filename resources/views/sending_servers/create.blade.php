@extends('layouts.frontend')

@section('title', trans('messages.create_sending_server'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-plus-circle2"></i> {{ trans('messages.create_sending_server') }}</span>
        </h1>
    </div>

@endsection

@section('content')
	<p>{{ trans('messages.sending_server.wording') }}</p>

    <form action="{{ action('SendingServerController@store', ["type" => request()->type]) }}" method="POST" class="form-validate-jquery">
        {{ csrf_field() }}

        @include('sending_servers._form')
    <form>

@endsection
