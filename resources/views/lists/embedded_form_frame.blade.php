@extends('layouts.none')

@section('content')
    <div style="padding:20px">
        <?php
            $params = request()->all();
            $params["preview"] = true;
        ?>
        @include("lists._embedded_form_content", $params)
    </div>
@endsection
