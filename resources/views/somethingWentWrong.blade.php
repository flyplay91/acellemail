@extends('layouts.clean')

@section('title', trans('messages.something_went_wrong'))

@section('content')
    <div class="alert bg-danger alert-styled-left">
        <span class="text-semibold">
            {!! $message !!}
        </span>
    </div>
    <a href='#back' onclick='history.back()' class='btn bg-grey-400'>{{ trans('messages.go_back') }}</a>
@endsection
