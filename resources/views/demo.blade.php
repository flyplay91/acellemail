@extends('layouts.demo')

@section('title', trans('messages.demo'))

@section('content')

                <style>
                    .demo_pic {
                        opacity: 0.85;
                    }
                    .demo_pic:hover, .demo_pic:focus {
                        opacity: 1;
                    }
                </style>

                <div class="text-center login-header" style="margin-bottom: 10px">
                    <a class="main-logo-big" href="{{ action('HomeController@index') }}">
                        <img src="{{ URL::asset('images/logo_big.png') }}" alt="">
                    </a>
                    <h3 class="text-center text-muted2" style="color: #aaa; margin-left: -20px">{{ trans('messages.demo') }}</h3>
                </div>

                <div class="row">
                    <div class="col-md-1">

                    </div>
                    <div class="col-md-5">
                        <h3 class="mb-0 text-center">{{ trans("messages.frontend") }}</h3>
                        <a target="_blank" class="demo_pic" href="{{ action("Controller@demoGo", ['view' => 'frontend']) }}">
                            <img style="max-width: 100%" src="{{ url("/images/demo_frontend.png") }}" />
                        </a>
                    </div>
                    <div class="col-md-5">
                        <h3 class="mb-0 text-center">{{ trans("messages.backend") }}</h3>
                        <a target="_blank" class="demo_pic" href="{{ action("Controller@demoGo", ['view' => 'backend']) }}">
                            <img style="max-width: 100%" src="{{ url("/images/demo_backend.png") }}" />
                        </a>
                    </div>
                    <div class="col-md-1">

                    </div>
                </div>

@endsection
