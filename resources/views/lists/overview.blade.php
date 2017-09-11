@extends('layouts.frontend')

@section('title', $list->name . " - " . number_with_delimiter($list->readCache('SubscriberCount')) . " " . trans('messages.subscribers'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('page_header')

    @include("lists._header")

@endsection

@section('content')

    @include("lists._menu")

    <h3 class="text-semibold text-teal-800">{{ trans('messages.list_performance') }}</h3>

    @include("lists._stat")

    <h3 class="text-semibold text-teal-800">{{ trans('messages.list_growth') }}</h3>

    @include("lists._growth_chart")
@endsection
