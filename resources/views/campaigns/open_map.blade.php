@extends('layouts.frontend')

@section('title', $campaign->name)
	
@section('page_script')    
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyB4l5_NUAQhrLtmMfNDVmpNGqThWv4fGWw"></script>
@endsection

@section('page_header')
	
			@include("campaigns._header")

@endsection

@section('content')
                
            @include("campaigns._menu")
			
			<div class="map-container map-marker-simple"></div>

<script>

/* ------------------------------------------------------------------------------
*
*  # Basic markers
*
*  Specific JS code additions for maps_google_markers.html page
*
*  Version: 1.0
*  Latest update: Aug 1, 2015
*
* ---------------------------------------------------------------------------- */
var map;
var infowindows = {};
var markers = {};

$(function() {		
	// Setup map
	function initialize() {
        
		// Options
		var mapOptions = {
			zoom: 2,
			center: new google.maps.LatLng(52.374,4.898)
		};

		// Apply options
		map = new google.maps.Map($('.map-marker-simple')[0], mapOptions);
		
		
		@foreach ($campaign->locations()->get() as $key => $location)
			@if (!empty($location->latitude) && !empty($location->longitude))
				infowindows[{{ $key }}] = new google.maps.InfoWindow({
					content: '<table><tr><td><strong>{{ trans('messages.ip') }}&nbsp;&nbsp;</td><td></strong>{{ $location->ip_address }}</td></tr><tr><td><strong>{{ trans('messages.email') }} &nbsp;&nbsp;</td><td> </strong> {{ $location->email }}</td></tr><tr><td><strong>{{ trans('messages.opened_at') }} &nbsp;&nbsp;</td><td> </strong> {{ Acelle\Library\Tool::formatDateTime(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $location->open_at)) }}</td><tr><td><strong>{{ trans('messages.area') }}</strong>&nbsp;&nbsp;</td><td>{{ $location->name() }}</td></tr></tr><td/table>'
				});
				// Add marker
				markers[{{ $key }}] = new google.maps.Marker({
					position: new google.maps.LatLng({{ $location->latitude }},{{ $location->longitude }}),
					map: map,
					//title: '{{ trans('messages.opened_at') }}: {{ Acelle\Library\Tool::formatDateTime(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $location->open_at)) }}<br>{{ $location->name() }}'
				});
				markers[{{ $key }}].addListener('click', function() {
					Object.keys(infowindows).forEach(function (key) { 
						infowindows[key].close();
					})
					infowindows[{{ $key }}].open(map, markers[{{ $key }}]);
				});
			@endif
		@endforeach

	};

	// Initialize map on window load
	google.maps.event.addDomListener(window, 'load', initialize);

});

function loadMarkers() {

};

</script>
@endsection
