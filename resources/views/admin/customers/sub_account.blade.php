@extends('layouts.backend')

@section('title', trans('messages.contact_information'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>

    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

	<div class="page-title">
		<ul class="breadcrumb breadcrumb-caret position-right">
			<li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
			<li class="active">{{ trans('messages.customer.sub_account') }}</li>
		</ul>
		<h1>
            <span class="text-semibold"><i class="icon-profile"></i> {{ $customer->displayName() }}</span>
        </h1>
	</div>

@endsection

@section('content')

	@include('admin.customers._tabs')
    <div class="sub-section">
        <h3 class="text-semibold">{{ trans('messages.customer.sub_account.title') }}</h3>

        <p>{{ trans('messages.customer.sub_account.wording') }}</p>

        <div class="row">
            <div class="col-md-10">
                <ul class="modern-listing big-icon no-top-border-list mt-0">
                    @foreach ($customer->subAccounts as $key => $account)
                        <li>
                            @if (Auth::user()->admin->can('delete', $account))
                                <a href="{{ action('Admin\SubAccountController@delete', $account->uid) }}"
                                    data-popup="tooltip" title="{{ trans('messages.subaccount.delete.tooltip') }}"
                                    type="button" class="btn btn-danger reload_page"
                                    data-method="delete"
                                    list-delete-confirm="{{ action('Admin\SubAccountController@deleteConfirm', $account->uid) }}"
                                >
                                        <i class="icon-cross2"></i> {{ trans('messages.subaccount.delete') }}
                                </a>
                            @endcan
                            <div>
                                <span class="">
                                    <i class="icon-drive text-grey-800"></i>
                                </span>
                            </div>
                            <h4><span class="text-teal-800">{{ $account->username }}</span></h4>
                            <p>
                                {{ $account->sendingServer->name }} ({{ trans('messages.' . $account->sendingServer->type) }})
                            </p>
                        </li>
                    @endforeach

                </ul>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

@endsection
