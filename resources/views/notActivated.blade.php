@extends('layouts.clean')

@section('title', trans('messages.not_authorized'))

@section('content')
    <div class="alert bg-danger alert-styled-left">
        <span class="text-semibold">
            {{ trans('messages.you_are_not_activated') }}
        </span>
    </div>
    <a href='#back' onclick='history.back()' class='btn bg-grey-400'>{{ trans('messages.go_back') }}</a>
    <a href='{{ action('UserController@resendActivationEmail', ['uid' => $uid]) }}' class='btn bg-teal-800'>{{ trans('messages.resend_activation_email') }}</a>
@endsection