@extends('layouts.frontend')

@section('title', trans('messages.blacklist.import'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    <div class="page-title">
        <ul class="breadcrumb breadcrumb-caret position-right">
            <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
            <li><a href="{{ action("BlacklistController@index") }}">{{ trans('messages.blacklist') }}</a></li>
        </ul>
        <h1>
            <span class="text-semibold"><i class="icon-download4"></i> {{ trans('messages.blacklist.import') }}</span>
        </h1>
    </div>

@endsection

@section('content')
    @if (is_object($system_job))
        <div class="sub-section">
            <h3 class="text-semibold mt-0">{{ trans('messages.blacklist.import_process') }}</h3>

            <div class="progress-box" data-url="{{ action('BlacklistController@importProcess', ['system_job_id' => $system_job->id]) }}">
            </div>

        </div>
    @else
        <div class="sub-section">
            <h3 class="text-semibold mt-0">{{ trans('messages.blacklist.upload_list_from_file') }}</h3>

            <form action="{{ action('BlacklistController@import') }}" method="POST" class="form-validate-jquery" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-md-6">
                        <p>{!! trans('messages.blacklist.import_file_help', [
                            'max' => \Acelle\Library\Tool::maxFileUploadInBytes()
                        ]) !!}</p>

                        @include('helpers.form_control', [
                            'required' => true,
                            'type' => 'file',
                            'label' => '',
                            'name' => 'file',
                            'value' => ''
                        ])

                        <div class="text-left">
                            <button class="btn bg-teal mr-10 click-effect"><i class="icon-check"></i> {{ trans('messages.import') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

@endsection
