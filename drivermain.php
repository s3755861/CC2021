<!DOCTYPE html>
<html>
  <head>
    <title>Main Page</title>

    <style type="text/css">
      #map {
        height: 400px;
        width: 100%;
      }
    </style>

    <?php
    require "functions.php";
    session_start();
    $username = 'driver1';
    $user = getLocation($dynamodb, $marshaler, $username);
    $lat = $marshaler->unmarshalValue($user['lat']);
    $lng = $marshaler->unmarshalValue($user['lng']);
    ?>

    <script>
      
      function initMap() {
        
        var lat = <?php echo $lat;?>;
        var lng = <?php echo $lng;?>;
        const location = {lat: lat, lng: lng};
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 4,
          center: location,
        });
        
        const marker = new google.maps.Marker({
          position: location,
          map: map,
        });
      }
    </script>
  </head>
  <body>
    <h3>My Google Maps Demo</h3>
    <div id="map"></div>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4Sdn-OA_q2brPyvGXKyrU5kcpzeEJmSY&callback=initMap&libraries=&v=weekly"
      async
    ></script>
  </body>
</html>