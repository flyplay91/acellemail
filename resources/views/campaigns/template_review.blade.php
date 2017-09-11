@extends('layouts.frontend')

@section('title', $campaign->name)

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/chart.js') }}"></script>
@endsection

@section('page_header')

			@include("campaigns._header")

@endsection

@section('content')

            @include("campaigns._menu")

            <iframe class="preview_page_frame" src="{{ action('CampaignController@templateReviewIframe', $campaign->uid) }}" />

@endsection
