<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Restaurants in Cebu</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/my.css">
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places,drawing,visualization,geometry"></script>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
		var map;
		var infoWindow;
		var service;
		var markers = [];
		var restaurant_types = ["Fine Dining", "Fast Food", "Barbeque", "Coffee", "Chinese", "Pizza", "Italian", "Gourmet", "Japanese", "Filipino", "Chicken", "Vegetarian"];
		var add_keyword = ""
		var my_place = "Capitol, Cebu City"; //center
		var all_shapes = [];
		var shape_drawn = false;
		var new_markers = [];
		var directionsDisplay;
		

		function initialize() {
			map = new google.maps.Map(document.getElementById('map-canvas'), {
			zoom: 16,
			styles: [
				{
					stylers: [
						{ visibility: 'simplified' }
					]
				},
				{
					elementType: 'labels',
					stylers: [
						{ visibility: 'off' }
					]
				}
			]
			});
		  
			geocoder = new google.maps.Geocoder();
			var geocoder;			
			geocoder.geocode( {'address' : my_place}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
				} else {
					alert("Center not found");
				}
			});

			infoWindow = new google.maps.InfoWindow();
			service = new google.maps.places.PlacesService(map);
			
			var drawingToolFormatting = {
				fillColor: '#336600',
				fillOpacity: 0.4,
				strokeWeight: 0,
				clickable: false,
				editable: true,
				zIndex: 1
			};
			
			var drawingManager = new google.maps.drawing.DrawingManager({
				//drawingMode: google.maps.drawing.OverlayType.MARKER,
				drawingControl: true,
				drawingControlOptions: {
					position: google.maps.ControlPosition.TOP_CENTER,
					drawingModes: [
						//google.maps.drawing.OverlayType.MARKER,
						google.maps.drawing.OverlayType.CIRCLE,
						//google.maps.drawing.OverlayType.POLYGON,
						//google.maps.drawing.OverlayType.POLYLINE,
						google.maps.drawing.OverlayType.RECTANGLE
					]
				},
				circleOptions: drawingToolFormatting,
				rectangleOptions: drawingToolFormatting
			});
			drawingManager.setMap(map);
			
			google.maps.event.addListener(map, 'idle', performSearch);
			
			google.maps.event.addListener(drawingManager, "overlaycomplete", function(e) {
				//deleteAllShapes();
				all_shapes.push(e);
				shape_drawn = true;
				checkShapes();
				$("#shapes-span").show();
			});
		}
		
		function checkShapes() {
			for (var i in all_shapes) {
				e = all_shapes[i];
				if (e.type == google.maps.drawing.OverlayType.CIRCLE) {
					var circleRadius = e.overlay.getRadius();
					var circleCenter = e.overlay.getCenter();
					var cirleBounds = e.overlay.getBounds();
					for (var k in markers){
						//if (cirleBounds.contains(markers[k].getPosition())) {
						if (google.maps.geometry.spherical.computeDistanceBetween(circleCenter,markers[k].getPosition()) <= circleRadius){
							new_markers.push(k);
							console.log("Marker index is " + k +"\nDistance is " + google.maps.geometry.spherical.computeDistanceBetween(circleCenter,markers[k].getPosition()) + "\nRadius is " + circleRadius);
						}
					}
				} else if (google.maps.drawing.OverlayType.RECTANGLE) {
					var rectangle_bounds = e.overlay.getBounds();
					for (var k in markers){
						if (rectangle_bounds.contains(markers[k].getPosition())) {
							new_markers.push(k);
							console.log(k);
						}
					}
				}
			}
			drawNewMarkers();
		}

		function performSearch() {
			if (!shape_drawn) {
				//deleteAllShapes();
				showMarkers();
				var request = {
					bounds: map.getBounds(),
					keyword: 'restaurant ' + add_keyword
				};
				service.radarSearch(request, callback);
			}
		}
		
		function customPerformSearch(key) {
			deleteAllShapes();
			add_keyword = key;
			showMarkers();
			var request = {
				bounds: map.getBounds(),
				keyword: 'restaurant ' + key
			};
			console.log("Searching for " + request.keyword);			
			service.radarSearch(request, callback);
		}
		
		function callback(results, status) {
			//if (status == google.maps.places.PlacesServiceStatus.OK) {
				for (var i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
				}
				markers = [];
				for (var i = 0; i < results.length; i++) {
					createMarker(results[i]);
				}
			//}
		}
		
		function drawNewMarkers() {
			for (var i = 0; i < markers.length; i++) {
				markers[i].setMap(null);
			}
			for (var i = 0; i < new_markers.length; i++) {
				markers[new_markers[i]].setMap(map);
			}
			$("#num-restaurants-in-shape").html(new_markers.length);
			//alert(new_markers.length);
		}
		
		function deleteAllShapes() {
			$("#shapes-span").hide();
			new_markers = [];
			shape_drawn = false;
			for (var i=0; i < all_shapes.length; i++) {
				all_shapes[i].overlay.setMap(null);
			}
			all_shapes = [];
			performSearch();
		}
		
		function deleteAllDirections() {
			if (directionsDisplay !== undefined) {
				directionsDisplay.setMap(null);
			}
			$("#directions-button").hide();
		}
		
		function createMarker(place)
		{
			//var placeLoc = place.geometry.location;
			var marker = new google.maps.Marker({
				map: map,
				position: place.geometry.location
			});
			marker.setIcon({
				url: 'img/restaurant-icon.png',
				size: new google.maps.Size(40, 40),
				//anchor: new google.maps.Point(40, 40),
				scaledSize: new google.maps.Size(40, 40)
			});
			
			marker.setMap(map);
			
			//also insert to the database (no duplicates allowed)
			service.getDetails(place, function(place, status) {
				if (place !== null) {
					var temp_lat, temp_lng;
					temp_lat = place.geometry.location.A;
					temp_lng = place.geometry.location.F;
					$.post( "php/add_place.php", { id:place.place_id, name:place.name, lat:temp_lat, lng:temp_lng }).done(function( data ) {
						console.log(data);
					});
				}
			});
			
			google.maps.event.addListener(marker, 'click', function() {
				service.getDetails(place, function(place, status) {					
					if (status != google.maps.places.PlacesServiceStatus.OK) {
						alert(status);
						return;
					}
					console.log(place);
					infoWindow.setContent("<img src='img/small-loader.gif' width='16' />");
					infoWindow.open(map, marker);
					//get first the number of visits for this establishment
					$.post( "php/get_place_visits.php", { id:place.place_id }).done(function( num_visits ) {
						infoWindow.setContent(createWindowInfo(place.name, place.geometry.location, place.place_id, num_visits));
						infoWindow.open(map, marker);
						getSpecialtiesForInfo(place.name, place.place_id, place.geometry.location.A, place.geometry.location.F);
					});
				});
			});
			
			markers.push(marker);
		}
		
		function driveTo(name, id, lat, lng) {
			deleteAllDirections(); //delete existing directions
			if(navigator.geolocation) {
				browserSupportFlag = true;
				navigator.geolocation.getCurrentPosition(function(position) {
					initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
					//map.setCenter(initialLocation);
					//console.log(initialLocation);
					var directionsService = new google.maps.DirectionsService();
					directionsDisplay = new google.maps.DirectionsRenderer({
						suppressMarkers: true,
						polylineOptions: { 
							strokeColor: "#336600",
							strokeOpacity: 0.7,
							strokeWeight: 12 
						}
					});
					var to = new google.maps.LatLng(lat, lng);
					var request = {
						origin: initialLocation,
						destination:to,
						travelMode: google.maps.TravelMode.DRIVING
					};
					directionsService.route(request, function(response, status) {
						if (status == google.maps.DirectionsStatus.OK) {
							directionsDisplay.setDirections(response);
							directionsDisplay.setMap(map);
							$("#directions-button").show();
							addPlaceVisits(name, id, lat, lng); //we will increase visits when we geolocation
						}
					});
				}, function() {
					alert("Your browser doesn't support geolocation services.");
					//handleNoGeolocation(browserSupportFlag);
				});
			}
			// Browser doesn't support Geolocation
			else {
				browserSupportFlag = false;
				alert("Your browser doesn't support geolocation services.");
				//handleNoGeolocation(browserSupportFlag);
			}
		}
		
		function createWindowInfo(name, location, id, num_visits) {
			var temp_lat, temp_lng;
			temp_lat = location.A;
			temp_lng = location.F;
			name = name.replace("'", "");
			return 	"<div align='center'><span class=\"popup-title\">"+name+"</span>" +
						"<div id='specialty-list-in-info'></div>" +
						"<br /><a href='#'  class=\"btn btn-success btn-xs\" onClick=\"showSpecialtiesWindow('"+name+"', '"+id+"', '"+temp_lat+"', '"+temp_lng+"')\">Specialties</a> " +
						"<a href='#'  class=\"btn btn-info btn-xs\" onClick=\"showAddSpecialtyWindow('"+name+"', '"+id+"', '"+temp_lat+"', '"+temp_lng+"')\">Add Specialty</a> " +
						"<a href='#'  class=\"btn btn-warning btn-xs\" onClick=\"driveTo('"+name+"', '"+id+"', '"+temp_lat+"', '"+temp_lng+"')\">Drive to Here</a> " +
						"<button onClick=\"addPlaceVisits('"+name+"', '"+id+"', '"+temp_lat+"', '"+temp_lng+"', '"+num_visits+"')\" class=\"btn btn-primary btn-xs\" type=\"button\">Visit <span class=\"badge\" id=\"number-visits\">"+num_visits+"</span></button>" +
					"</div>";
		}
		
		function addPlaceVisits(establishment_name, establishment_id, my_lat, my_lng, num_visits) {
			num_visits = $("#number-visits").text();
			num_visits++;
			$("#number-visits").html(num_visits);
			$.post("php/add_place_visits.php", { id:establishment_id, name:establishment_name, lat:my_lat, lng:my_lng }).done(function( data ) {
				//console.log(establishment_id + "\n" + establishment_name + "\n" + my_lat + "\n" + my_lng);
			});
		}
		
		function showSpecialtiesWindow(establishment_name, establishment_id, my_lat, my_lng) {
			$("#specialties-container").show();	
			$("#specialty-list").html("<img src='img/small-loader.gif' width='16' />");
			$("#establishment-name-specialties").html("<img src='img/small-loader.gif' width='16' />");
			$.post("php/get_specialties_db.php", { id:establishment_id, name:establishment_name, lat:my_lat, lng:my_lng }).done(function( data ) {
				console.log(data);
				$("#establishment-name-specialties").html(establishment_name);
				var temp_arr_specialty = data.split("|");
				var temp_str = "";
				for (var i=0; i<temp_arr_specialty.length; i++) {
					temp_str += ' <span class="label label-info">'+temp_arr_specialty[i]+'</span> ';
				}
				console.log(temp_arr_specialty);
				$("#specialty-list").html(temp_str);
			});
		}
		
		function getAllSpecialties(establishment_name, establishment_id, my_lat, my_lng) {
			$("#specialty-list-in-add").html("<img src='img/small-loader.gif' width='16' />");
			$.post("php/get_specialties_db.php", { id:establishment_id, name:establishment_name, lat:my_lat, lng:my_lng }).done(function( data ) {
				//console.log(data);
				$("#establishment-name-specialties").html(establishment_name);
				var temp_arr_specialty = data.split("|");
				var temp_str = "";
				for (var i=0; i<temp_arr_specialty.length; i++) {
					temp_str += ' <span class="label label-info">'+temp_arr_specialty[i]+'</span> ';
				}
				console.log(temp_arr_specialty);
				$("#specialty-list-in-add").html(temp_str);
			});
		}
		
		function getSpecialtiesForInfo(establishment_name, establishment_id, my_lat, my_lng) {
			$("#specialty-list-in-info").html("<img src='img/small-loader.gif' width='16' />");
			$.post("php/get_specialties_db.php", { id:establishment_id, name:establishment_name, lat:my_lat, lng:my_lng }).done(function( data ) {
				//console.log(data);
				$("#establishment-name-specialties").html(establishment_name);
				var temp_arr_specialty = data.split("|");
				var temp_str = "<span class='label label-info'>";
				for (var i=0; i<temp_arr_specialty.length; i++) {
					temp_str += temp_arr_specialty[i] + ", ";
				}
				temp_str = temp_str.substring(0, temp_str.length-2);
				temp_str += '</span>';
				console.log(temp_arr_specialty);
				$("#specialty-list-in-info").html(temp_str);
			});
		}
		
		function closeSpecialtyList() {
			$("#specialties-container").hide();	
		}
		
		function showAddSpecialtyWindow(name, id, my_lat, my_lng) {
			getAllSpecialties(name, id, my_lat, my_lng);
			$("#add-specialty-container").show();
			$("#establishment-name").html(name);
			$("#establishment-id").val(id);
			$("#establishment-name-input").val(name);
			$("#establishment-lat").val(my_lat);
			$("#establishment-lng").val(my_lng);
		}
		
		function addSpecialtyToDB() {
			var establishment_specialty = $("#specialty").val();
			if (establishment_specialty.length < 1) { alert("Please enter a specialty menu."); return; }
			closeSpecialtyContainer();
			var establishment_id = $("#establishment-id").val();
			var establishment_name = $("#establishment-name-input").val();
			var establishment_lat = $("#establishment-lat").val();
			var establishment_lng = $("#establishment-lng").val();
			$("#specialty").val("");
			$.post( "php/add_specialty_db.php", { id:establishment_id, specialty:establishment_specialty, name:establishment_name, lat:establishment_lat, lng:establishment_lng }).done(function( data ) {
				console.log(data);
				getSpecialtiesForInfo(establishment_name, establishment_id, establishment_lat, establishment_lng);
				//alert(data);
			});
		}
		
		function closeSpecialtyContainer() {
			$("#add-specialty-container").hide();
		}
		
		// Sets the map on all markers in the array.
		function setAllMap(map) {
			for (var i = 0; i < markers.length; i++) {
				markers[i].setMap(map);
			}
		}
		// Removes the markers from the map, but keeps them in the array.
		function clearMarkers() {
			$("#show-marker-button").show();
			$("#hide-marker-button").hide();
			setAllMap(null);
			console.log("Total: " + markers.length);
		}
		
		// Shows any markers currently in the array.
		function showMarkers() {
			$("#show-marker-button").hide();
			$("#hide-marker-button").show();
			setAllMap(map);
		}
		
		// Deletes all markers in the array by removing references to them.
		function deleteMarkers() {
			console.log("Total: " + markers.length)
			markers = [];
		}
		

		$(document).ready(function() {
			$("#show-marker-button").hide();
			$("#directions-button").hide();
			$("#shapes-span").hide();
			closeSpecialtyContainer();		
			closeSpecialtyList();		
			//show the map
			google.maps.event.addDomListenerOnce(window, 'load', initialize);
		});
    </script>
  </head>
  <body>
	<div id="my-buttons">
		<button onClick="customPerformSearch('');$('#my-key').val('');" type="button" class="btn btn-primary" id="reset-button">Reset</button>
		<button onClick="clearMarkers();" type="button" class="btn btn-primary" id="hide-marker-button">Hide All Markers</button>
		<button onClick="showMarkers();" type="button" class="btn btn-primary" id="show-marker-button">Show All Markers</button>
		<span id="shapes-span">
			<button onClick="deleteAllShapes();" type="button" class="btn btn-danger">Delete All Shapes</button>
			Number of Filtered Restaurants: <b><span id="num-restaurants-in-shape">0</span></b>
		</span>
		<button onClick="deleteAllDirections();" type="button" class="btn btn-danger" id="directions-button">Delete Directions</button>
		<div class="btn-group">
			<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Filter Restaurants <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li class="clickable_link"><a onClick="customPerformSearch('');">All</a></li>
				<script>
					restaurant_types.sort();
					for (var x=0; x<restaurant_types.length; x++) {
						document.write("<li class=\"clickable_link\"><a onClick=\"customPerformSearch('"+restaurant_types[x]+"');\">"+restaurant_types[x]+"</a></li>");
					}
				</script>
			</ul>
		</div>
		<button onClick="window.location='php/more-info.php';" type="button" class="btn btn-success" id="show-marker-button">More Info</button>
		<span style="margin-left:10px;" id="shapes-span">
			<span>Search: </span><input type="text" id="my-key" name="my-key" />
			<button onClick="customPerformSearch($('#my-key').val());" type="button" class="btn btn-success">Search</button>
		</span>
	</div>
    <div id="map-canvas"><img src='img/small-loader.gif' width='16' /></div>
	
	<div class="center-text" id="add-specialty-container">
		<h2>Add Specialty for <strong id="establishment-name"></strong></h2>
		<div style="margin-bottom:10px;" id="specialty-list-in-add"></div>
		<label for="specialty">New Specialty: </label>
		<input name="specialty" id="specialty" type="text" class="my-text-input" />
		<input name="establishment-id" id="establishment-id" type="hidden" class="my-text-input" />
		<input name="establishment-name-input" id="establishment-name-input" type="hidden" class="my-text-input" />
		<input name="establishment-lat" id="establishment-lat" type="hidden" class="my-text-input" />
		<input name="establishment-lng" id="establishment-lng" type="hidden" class="my-text-input" />
		<br /><br />
		<button type="button" class="btn btn-info dropdown-toggle" onClick="addSpecialtyToDB();">Add</button>
		<button type="button" class="btn btn-danger dropdown-toggle" onClick="closeSpecialtyContainer();">Close</button>
	</div>
	
	<div class="center-text" id="specialties-container">
		<h2>Specialties in <strong id="establishment-name-specialties"></strong></h2>
		<div id="specialty-list"></div>
		<button type="button" class="btn btn-danger dropdown-toggle" onClick="closeSpecialtyList();">Close</button>
	<div>
	
  </body>
</html>