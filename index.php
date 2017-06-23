<?php 
	require('vendor/autoload.php');

	$response1 =  \GeometryLibrary\SphericalUtil::computeHeading(
              ['lat' => 19.27612477442321, 'lng' => -103.72971539315176], 
              ['lat' => 19.273355, 'lng' => -103.737446]);
    
  	echo $response1;
	
  	$response =  \GeometryLibrary\SphericalUtil::computeOffset(['lat' => 19.27612477442321, 'lng' => -103.72971539315176], 200, $response1);
     
    echo "<br>";
  	echo json_encode($response);
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
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
    <div id="map"></div>
    <script>
      var map;
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: <?php echo json_encode($response); ?>,
          //center: {lat: 19.27612477442321, lng: -103.72971539315176},
          zoom: 18,
          //scrollwheel: false
        });
        var marker = new google.maps.Marker({
        	position: <?php echo json_encode($response); ?>,
		    //position: {lat: 19.27612477442321, lng: -103.72971539315176},
		    map: map,
		    draggable: true
		});
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApY37tU3ps7UhPKXW6_Flaw2iHqIbA04w&callback=initMap"
    async defer></script>
  </body>
</html>