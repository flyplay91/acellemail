@extends('layouts.frontend')

@section('title', trans('messages.contact_information'))

@section('page_script')
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('page_header')

            <div class="page-title">
                <ul class="breadcrumb breadcrumb-caret position-right">
                    <li><a href="{{ action("HomeController@index") }}">{{ trans('messages.home') }}</a></li>
                    <li class="active">{{ trans('messages.contact_information') }}</li>
                </ul>
                <h1>
                    <span class="text-semibold"><i class="icon-address-book3"></i> {{ $customer->displayName() }}</span>
                </h1>
            </div>

@endsection

@section('content')

                @include("account._menu")

                <form enctype="multipart/form-data" action="{{ action('AccountController@contact') }}" method="POST" class="form-validate-jqueryz">
                    {{ csrf_field() }}

                    <h2 class="text-semibold text-teal-800">{{ trans('messages.primary_account_contact') }}</h2>

                    <div class="row">
                        <div class="col-md-6">

                            <div class="row">
                                <div class="col-md-6">
                                    @include('helpers.form_control', ['type' => 'text', 'name' => 'first_name', 'value' => $contact->first_name, 'rules' => Acelle\Model\Contact::$rules])
                                </div>
                                <div class="col-md-6">
                                    @include('helpers.form_control', ['type' => 'text', 'name' => 'last_name', 'value' => $contact->last_name, 'rules' => Acelle\Model\Contact::$rules])
                                </div>
                            </div>

                            @include('helpers.form_control', ['type' => 'text', 'label' => trans('messages.email_at_work'), 'name' => 'email', 'value' => $contact->email, 'help_class' => 'customer_contact', 'rules' => Acelle\Model\Contact::$rules])

                            @include('helpers.form_control', ['type' => 'text', 'name' => 'address_1', 'value' => $contact->address_1, 'rules' => Acelle\Model\Contact::$rules])

                            <div class="row">
                                <div class="col-md-6">
                                    @include('helpers.form_control', ['type' => 'text', 'name' => 'city', 'value' => $contact->city, 'rules' => Acelle\Model\Contact::$rules])
                                </div>
                                <div class="col-md-6">
                                    @include('helpers.form_control', ['type' => 'text', 'label' => trans('messages.zip_postal_code'), 'name' => 'zip', 'value' => $contact->zip, 'rules' => Acelle\Model\Contact::$rules])
                                </div>
                            </div>

                            @include('helpers.form_control', ['type' => 'text', 'label' => trans('messages.website_url'), 'name' => 'url', 'value' => $contact->url, 'rules' => Acelle\Model\Contact::$rules])

                        </div>
                        <div class="col-md-6">

                            @include('helpers.form_control', ['type' => 'select', 'name' => 'country_id', 'label' => trans('messages.country'), 'value' => $contact->country_id, 'options' => Acelle\Model\Country::getSelectOptions(), 'include_blank' => trans('messages.choose'), 'rules' => Acelle\Model\Contact::$rules])

                            @include('helpers.form_control', ['type' => 'text', 'label' => trans('messages.company_organization'), 'name' => 'company', 'value' => $contact->company, 'rules' => Acelle\Model\Contact::$rules])

                            @include('helpers.form_control', ['type' => 'text', 'label' => trans('messages.office_phone'), 'name' => 'phone', 'value' => $contact->phone, 'rules' => Acelle\Model\Contact::$rules])

                            @include('helpers.form_control', ['type' => 'text', 'name' => 'address_2', 'value' => $contact->address_2, 'rules' => Acelle\Model\Contact::$rules])

                            @include('helpers.form_control', ['type' => 'text', 'label' => trans('messages.state_province_region'), 'name' => 'state', 'value' => $contact->state, 'rules' => Acelle\Model\Contact::$rules])

                        </div>
                    </div>

                    <h2 class="text-semibold text-teal-800">{{ trans('messages.billing_information') }}</h2>

                    <div class="row">
                        <div class="col-md-6">

                            @include('helpers.form_control', [
                                'type' => 'text',
                                'name' => 'tax_number',
                                'value' => $contact->tax_number,
                                'help_class' => 'customer_contact',
                                'rules' => Acelle\Model\Contact::$rules]
                            )

                        </div>
                        <div class="col-md-6">

                            @include('helpers.form_control', [
                                'type' => 'text',
                                'name' => 'billing_address',
                                'value' => $contact->billing_address,
                                'help_class' => 'customer_contact',
                                'rules' => Acelle\Model\Contact::$rules]
                            )

                        </div>
                    </div>

                    <div class="text-right">
                        <button class="btn bg-teal"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
                    </div>

                <form>

@endsection
