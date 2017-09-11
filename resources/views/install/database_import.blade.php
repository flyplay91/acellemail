@extends('layouts.install')

@section('title', trans('messages.database'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('content')

<h3 class="text-teal-800"><i class="icon-database"></i> {{ trans('messages.database_configuration') }}</h3>

    <h5 class="">
        The settings was successfully configured! Click <span class="text-semibold">{!! trans('messages.setup_database') !!}</span> button to start importing data to database '{{ $database["database_name"] }}'.
    </h5>

	@if ($tables_exist)
		<div class="alert alert-danger">
			Acelle Mail is going to initialize your database, all existing data will be erased
		</div>
	@endif

    <div class="text-right">
        <a href="{{ action('InstallController@import') }}" class="btn btn-info bg-info-800">{!! trans('messages.setup_database') !!} <i class="icon-arrow-right14 position-right"></i></a>
		<a href="{{ action('InstallController@database') }}" class="btn btn-info bg-grey-600"><i class="icon-undo2"></i> {!! trans('messages.back') !!}</a>
    </div>

@endsection
