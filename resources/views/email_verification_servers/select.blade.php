@extends('layouts.frontend')

@section('title', trans('messages.sending_servers'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

	<script type="text/javascript" src="{{ URL::asset('js/listing.js') }}"></script>
@endsection

@section('page_header')

			<div class="page-title">
				<ul class="breadcrumb breadcrumb-caret position-right">
					<li><a href="{{ action("Admin\HomeController@index") }}">{{ trans('messages.home') }}</a></li>
				</ul>

				<h1>
					<span class="text-semibold"><i class="icon-plus2"></i> {{ trans('messages.select_sending_servers_type') }}</span>
				</h1>
			</div>

@endsection

@section('content')
							<div class="row">
								<div class="col-md-1"></div>
								<div class="col-md-10">
                                    <ul class="modern-listing big-icon no-top-border-list mt-0">

										@foreach (Acelle\Model\SendingServer::types() as $key => $type)

											<li>
												<a href="{{ action('Admin\SendingServerController@create', ["type" => $key]) }}" class="btn btn-info bg-info-800">{{ trans('messages.choose') }}</a>
                                                <a href="{{ action('Admin\SendingServerController@create', ["type" => $key]) }}">
                                                    <span class="server-avatar server-avatar-{{ $key }}">
                                                        <i class="icon-server"></i>
                                                    </span>
                                                </a>
												<h4><a href="{{ action('Admin\SendingServerController@create', ["type" => $key]) }}">{{ trans('messages.' . $key) }}</a></h4>
												<p>
													{{ trans('messages.sending_server_intro_' . $key) }}
												</p>
											</li>

										@endforeach

									</ul>
									<div class="pull-right">
										<a href="{{ action('Admin\SendingServerController@index') }}" type="button" class="btn bg-grey">
											<i class="icon-cross2"></i> {{ trans('messages.cancel') }}
										</a>
									</div>
								</div>
								<div class="col-md-1"></div>
							</div>
@endsection
