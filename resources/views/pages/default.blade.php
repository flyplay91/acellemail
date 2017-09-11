@extends('layouts.page')

@section('title', $page->subject)

@section('content')
    {!! $page->content !!}
@endsection