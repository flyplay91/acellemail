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
        
		// Add info window
		var infowindow = new google.maps.InfoWindow({
			content: 'Amsterdam'
		});
        
		// Add marker
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(52.374,4.898),
			map: map,
			title: 'Hello World!'
		});

		// Attach click event
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open(map,marker);
		});

	};

	// Initialize map on window load
	google.maps.event.addDomListener(window, 'load', initialize);

});