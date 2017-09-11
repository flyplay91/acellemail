@extends('layouts.page')

@section('title', $page->subject)
	
@section('page_script')
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script type="text/javascript" src="{{ URL::asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
		
    <script type="text/javascript" src="{{ URL::asset('js/validate.js') }}"></script>
		
	
@endsection

@section('content')

	<form action="" method="POST" class="form-validate-jqueryz">
		{{ csrf_field() }}
		
		{!! $page->content !!}
	
	</form>
		
@endsection