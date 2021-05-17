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
      $username = $_SESSION['user'];
      $user = getLocation($dynamodb, $marshaler, $username);
      $lat = $marshaler->unmarshalValue($user['lat']);
      $lng = $marshaler->unmarshalValue($user['lng']);  
      $trip = pendingTrip($dynamodb, $marshaler, $username);
    ?>
      
    <script>
    var boo = <?php echo validate($trip);?>;
    if(boo == 1){
      accept=window.confirm("A passenger wants to have a trip, do you want to help them?");
      if(accept == true){
          <?php 
          $trip[3] = 'inprogress';
          editTrip($trip[0], $trip[1], $trip[2], $trip[3], $trip[4], $trip[5], $trip[6], $trip[7]);
          ?>
      }else{
          <?php 
          deleteTrip($trip[0], $trip[1]);
          ?>
          }
      }
    </script>

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