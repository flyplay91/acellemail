@extends('layouts.clean')

@section('title', trans('messages.app.notice.title'))

@section('content')
    <div class="alert bg-info alert-styled-left">
        <span class="text-semibold">
            {!! $message !!}
        </span>
    </div>
    <a href='#back' onclick='history.back()' class='btn bg-grey-400'>{{ trans('messages.go_back') }}</a>
@endsection
