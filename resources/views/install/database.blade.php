@extends('layouts.install')

@section('title', trans('messages.database'))

@section('page_script')    
    <script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
@endsection

@section('content')

<h3 class="text-teal-800"><i class="icon-database"></i> {{ trans('messages.database_configuration') }}</h3>

@if (isset($connect_error))
	<div class="alert alert-danger">
		{{ $connect_error }}
	</div>
@endif

<form action="{{ action('InstallController@database') }}" method="POST" class="form-validate-jqueryz">
	{!! csrf_field() !!}
	
		<div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'hostname',
                'value' => (isset($database["hostname"]) ? $database["hostname"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
        </div>
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'port',
                'value' => (isset($database["port"]) ? $database["port"] : "3306"),
								'placeholder' => '3306',
                'help_class' => 'install',
                'rules' => $rules
            ]) 
        </div>
    </div>
			
		<div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'username',
                'value' => (isset($database["username"]) ? $database["username"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
        </div>
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'password',
                'value' => (isset($database["password"]) ? $database["password"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
        </div>
    </div>
	
    <div class="row">
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'database_name',
                'value' => (isset($database["database_name"]) ? $database["database_name"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
                 
        </div>
        <div class="col-md-6">
            @include('helpers.form_control', [
                'type' => 'text',
                'name' => 'tables_prefix',
                'value' => (isset($database["tables_prefix"]) ? $database["tables_prefix"] : ""),
                'help_class' => 'install',
                'rules' => $rules
            ])
                 
        </div>
    </div>
    <hr >
    <div class="text-right">                                    
        <button type="submit" class="btn btn-primary bg-teal">{!! trans('messages.save') !!} <i class="icon-arrow-right14 position-right"></i></button>
    </div>
</form>
@endsection
