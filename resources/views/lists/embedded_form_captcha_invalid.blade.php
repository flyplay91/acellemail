@extends('layouts.page')

@section('title', trans('messages.error'))
	


@section('content')
  <div class="row">
    <div class="col-md-12 tex-center">
        <div style="margin: 100px auto; width: 400px;text-align:center">
            <div class="alert alert-danger text-left">
                <ul>
                    @foreach ($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ $back_link }}" class="btn btn-primary">{{ trans('messages.try_again') }}</a>
        </div>
    </div>
  </div>

@endsection