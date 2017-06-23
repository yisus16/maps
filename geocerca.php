<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Geocerca</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApY37tU3ps7UhPKXW6_Flaw2iHqIbA04w&callback=initMap"
    async defer></script>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
</head>
<body>
	<center><button Onclick="limpiar_marcas();">Limpiar Ruta</button><button Onclick="geocerca();">Geocerca</button></center><br>
	<div id="map"></div>
    <script>
      var map;
      var marker = null;
      var marcas_a_b = [];
      var latlng_marcas = [];
      var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  	  var labelIndex = 0;
  	  var directionsDisplay = null;
  	  var directionsService = null;
  	  var puntos_intermedio_ruta = [];
  	  var left  = [];
  	  var right = [];
      var punto_origen_destino_intermedio = [];
      var unir_puntos = [];

      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 19.27612477442321, lng: -103.72971539315176},
          zoom: 14
        });

        google.maps.event.addListener(map, 'click', function(event) {
	        var configDS = {
	        	map:map,
	        	suppressMarkers: true
	        }

	        directionsDisplay = new google.maps.DirectionsRenderer(configDS);
        	if (labelIndex < 2) {
		  		agregar_marca(event.latLng, map);
        	} else {
        		alert("Solo 2 puntos");
        	}
        	if(labelIndex == 2){
        		trazar_ruta(directionsDisplay);
        		labelIndex++;
        	}
		    }); 
      }

      function agregar_marca(location, map){
      	marker = new google.maps.Marker({
  	    	position: location,
  	    	label: labels[labelIndex++ % labels.length],
  		    map: map
  		  });
    		marcas_a_b.push(marker);
    		latlng_marcas.push(location);
      }

      function limpiar_marcas(){
      	for (var i = 0; i < marcas_a_b.length; i++) {
      		marcas_a_b[i].setMap(null);
      	}

      	limpiar_ruta();
      	labelIndex = 0;
      	latlng_marcas.length = 0;
      }

      function trazar_ruta(ds){
      	var request = {
    			origin: latlng_marcas[0],
    			destination: latlng_marcas[1],
    			travelMode: google.maps.TravelMode.DRIVING
    		}

  	    directionsService = new google.maps.DirectionsService();
  	    directionsService.route(request, function(response, status) {
  		    if (status == google.maps.DirectionsStatus.OK) {
  		        ds.setDirections(response);
  		        for (var i = 0; i < response.routes[0].overview_path.length; i++) {
  			        //console.log(response.routes[0].overview_path[i].lat());
  			        //console.log(response.routes[0].overview_path[i].lng());
                if (i == 0) {
                  punto_origen_destino_intermedio.push({lat: response.routes[0].overview_path[i].lat(), lng: response.routes[0].overview_path[i].lng()});
                } else if (i == response.routes[0].overview_path.length -1){
                  punto_origen_destino_intermedio.push({lat: response.routes[0].overview_path[i].lat(), lng: response.routes[0].overview_path[i].lng()});
                }
  			        puntos_intermedio_ruta[i] = {lat: response.routes[0].overview_path[i].lat(), lng: response.routes[0].overview_path[i].lng()}
  			        trazar_puntos_intermedios(puntos_intermedio_ruta[i]);
  			        //console.log(puntos_intermedio_ruta[i]);
  		        }
  		    } else {
  		        alert("No existen rutas entre ambos puntos");
  		    }
		    });
      }

      function limpiar_ruta(){
      	directionsDisplay.setMap(null);
      }

      function trazar_puntos_intermedios(location){
        marker = new google.maps.Marker({
    	    position: location,
    		  map: map
    		});
      }

      function geocerca(){
      	var lat_origen = null;
      	var lng_origen = null;
      	var lat_destin = null;
      	var lng_destin = null;
      	for (var i = 0; i < puntos_intermedio_ruta.length; i++) {
	      	lat_origen = puntos_intermedio_ruta[i].lat;
	      	lng_origen = puntos_intermedio_ruta[i].lng;
	      	if(i != puntos_intermedio_ruta.length - 1){
		      	lat_destin = puntos_intermedio_ruta[i+1].lat;
		      	lng_destin = puntos_intermedio_ruta[i+1].lng;
	      	} 
	      	//console.log(lat_destin);
	      	$.ajax({
            async: false,
	      		url:"direccion.php",
	      		type:"POST",
	      		dataType:"json",
	      		data:{
	      			'O_lat':lat_origen,
	      			'O_lng':lng_origen,
	      			'D_lat':lat_destin,
	      			'D_lng':lng_destin
	      		}, success:function(data){
	      			//console.log({lat: data[0].lat, lng: data[0].lng});
	      			trazar_geocerca_puntos(data[0]);
	      			trazar_geocerca_puntos(data[1]);
              left.push({lat: data[0].lat, lng: data[0].lng});
              right.push({lat: data[1].lat, lng: data[1].lng});
	      		}
	      	});
      	}
        crear_puntos_geocerca();
      	dibujar();
      }

      function trazar_geocerca_puntos(location){
        marker = new google.maps.Marker({
    	    position: location,
    		  map: map,
    		  icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
    		});
      }

      function dibujar(){
      	//console.log(right);
      	//console.log(left);
      	var poly = new google.maps.Polygon({
          paths: unir_puntos,
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 3,
          fillColor: '#FF0000',
          fillOpacity: 0.35
        });
        poly.setMap(map);
      }

      function crear_puntos_geocerca(){
        unir_puntos.push(punto_origen_destino_intermedio[0]);//punto origen de la ruta intermedia
        for (var i = 0; i < left.length; i++) {
          unir_puntos.push(left[i]);//puntos izquierdos del inicio al ultimo
        }
        unir_puntos.push(punto_origen_destino_intermedio[1]);//punto destino de la ruta intermedia
        for (var i = right.length - 1; i >= 0; i--) {
          unir_puntos.push(right[i]);//puntos derechos del ultimo al inicio
        }
      }
    </script>
</body>
</html>