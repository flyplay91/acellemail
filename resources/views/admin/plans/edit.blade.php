@extends('layouts.backend')

@section('title', $plan->name)

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li><a href="{{ action("Admin\PlanController@index") }}">{{ trans('messages.plans') }}</a></li>
            <li class="active">{{ trans('messages.update') }}</li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-profile"></i> {{ $plan->name }}</span>
        </h1>
    </div>

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <p>{{ trans('messages.plan_create_message') }}</p>
            <form enctype="multipart/form-data" action="{{ action('Admin\PlanController@update', $plan->uid) }}" method="POST" class="form-validate-jqueryx">
                {{ csrf_field() }}
                <input type="hidden" name="_method" value="PATCH">
                @include('admin.plans._form')
            <form>
        </div>
    </div>
@endsection
