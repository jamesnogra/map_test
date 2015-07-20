<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Restaurants in Cebu</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
    <style>	
		html, body {
			height: 100%;
			margin: 0;
		}
		
		#map-canvas {
			height: 90%;
			padding: 0;
		}
		#my-buttons {
			margin: 10px;
		}

    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places,visualization"></script>
    <script src="js/jquery.js"></script>
    <script>
		// This example adds a search box to a map, using the Google Place Autocomplete
		// feature. People can enter geographical searches. The search box will return a
		// pick list containing a mix of places and predicted search terms.

		function initialize() {

		  var markers = [];
		  var map = new google.maps.Map(document.getElementById('map-canvas'), {
			mapTypeId: google.maps.MapTypeId.ROADMAP
		  });

		  var defaultBounds = new google.maps.LatLngBounds(
			  new google.maps.LatLng(10.3207361,123.9025112));
		  map.fitBounds(defaultBounds);

		  // Create the search box and link it to the UI element.
		  var input = /** @type {HTMLInputElement} */(
			  document.getElementById('pac-input'));
		  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		  var searchBox = "restaurant";

		  // [START region_getplaces]
		  // Listen for the event fired when the user selects an item from the
		  // pick list. Retrieve the matching places for that item.
		  google.maps.event.addListener(searchBox, 'places_changed', function() {
			var places = searchBox.getPlaces();

			if (places.length == 0) {
			  return;
			}
			for (var i = 0, marker; marker = markers[i]; i++) {
			  marker.setMap(null);
			}

			// For each place, get the icon, place name, and location.
			markers = [];
			var bounds = new google.maps.LatLngBounds();
			for (var i = 0, place; place = places[i]; i++) {
			  var image = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			  };

			  // Create a marker for each place.
			  var marker = new google.maps.Marker({
				map: map,
				icon: image,
				title: place.name,
				position: place.geometry.location
			  });

			  markers.push(marker);

			  bounds.extend(place.geometry.location);
			}

			map.fitBounds(bounds);
		  });
		  // [END region_getplaces]

		  // Bias the SearchBox results towards places that are within the bounds of the
		  // current map's viewport.
		  google.maps.event.addListener(map, 'bounds_changed', function() {
			var bounds = map.getBounds();
			searchBox.setBounds(bounds);
		  });
		}

		google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
	<div id="my-buttons">
		<button onClick="clearMarkers();" type="button" class="btn btn-primary" id="hide-marker-button">Hide All Markers</button>
		<button onClick="showMarkers();" type="button" class="btn btn-primary" id="show-marker-button">Show All Markers</button>
	</div>
    <div id="map-canvas"></div>
  </body>
</html>