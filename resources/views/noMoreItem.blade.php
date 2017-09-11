@extends('layouts.clean')

@section('title', trans('messages.not_authorized'))

@section('content')
                <div class="alert bg-danger alert-styled-left">
                    <span class="text-semibold">
                        {{ trans('messages.no_more_item') }}
                    </span>
                </div>
                <a href='#back' onclick='history.back()' class='btn bg-grey-400'>{{ trans('messages.go_back') }}</a>
@endsection