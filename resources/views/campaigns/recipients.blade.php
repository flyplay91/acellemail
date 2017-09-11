@extends('layouts.frontend')

@section('title', trans('messages.campaigns') . " - " . trans('messages.recipients'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li><a href="{{ action("CampaignController@index") }}">{{ trans('messages.campaigns') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-paperplane"></i> {{ $campaign->name }}</span>
        </h1>

        @include('campaigns._steps', ['current' => 1])
    </div>

@endsection

@section('content')
    <form action="{{ action('CampaignController@recipients', $campaign->uid) }}" method="POST" class="form-validate-jqueryz">
        {{ csrf_field() }}

        <h4 class="mb-20 mt-0">
            {{ trans('messages.choose_lists_segments_for_the_campaign') }}
        </h4>

        <div class="addable-multiple-form">
            <div class="addable-multiple-container">
                <?php $num = 0 ?>
                @foreach ($campaign->getListsSegmentsGroups() as $index =>  $lists_segment_group)
                    @include('campaigns._list_segment_form', [
                        'lists_segment_group' => $lists_segment_group,
                        'index' => $num,
                    ])
                    <?php $num++ ?>
                @endforeach
            </div>
            <br />
            <a
                sample-url="{{ action('CampaignController@listSegmentForm', $campaign->uid) }}"
                href="#add_condition" class="btn btn-info bg-info-800 add-form">
                <i class="icon-plus2"></i> {{ trans('messages.add_list_segment') }}
            </a>
        </div>

        <hr>

        <div class="text-right">
            <button class="btn bg-teal-800">{{ trans('messages.next') }} <i class="icon-arrow-right7"></i> </button>
        </div>
    <form>

@endsection
