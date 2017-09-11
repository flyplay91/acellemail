@extends('layouts.frontend')

@section('title', $list->name . ": " . trans('messages.create_subscriber'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

    @include("lists._header")

@endsection

@section('content')
    @include("lists._menu")
        <div class="row">
			<div class="col-sm-12 col-md-6 col-lg-6">
				<div class="sub-section">
					<h2 class="text-semibold text-teal-800"><i class="icon-plus2"></i> {{ trans('messages.create_subscriber') }}</h2>

					<form action="{{ action('SubscriberController@store', $list->uid) }}" method="POST" class="form-validate-jqueryz">
						{{ csrf_field() }}

						@include("subscribers._form")

						<div class="text-left">
							<button class="btn bg-teal mr-10"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
							<a href="{{ action('SubscriberController@index', $list->uid) }}" class="btn bg-grey-800"><i class="icon-cross2"></i> {{ trans('messages.cancel') }}</a>
						</div>
					<form>
				</div>
			</div>
		</div>
@endsection
