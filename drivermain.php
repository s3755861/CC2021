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
      $boo;
      $arr;
      $username = $_SESSION['user'];
      $user = getLocation($dynamodb, $marshaler, $username);
      $lat = $marshaler->unmarshalValue($user['lat']);
      $lng = $marshaler->unmarshalValue($user['lng']);  
      $trip = pendingTrip($dynamodb, $marshaler, 'vcdgfdh');
      if(empty($trip)){
        $boo = -1;
      }else{
        $boo = 1;
        $arr = json_encode($trip);
      }

    ?>
      
    <script src="jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
    var abc = <?php echo $boo;?>;
    if(abc == 1){
      var a = confirm("A passenger wants to have a trip, do you want to help them?");
      var arr = <?php echo $arr;?>;
      arr[3] = "inprogress";
    if(a){
      $.ajax({
        url: 'editTrip.php',
        type: 'post',
        data: {tripID: arr[0],driver:arr[1],passenger:arr[2],status:arr[3],startlat:arr[4],startlng: arr[5],deslat:arr[6],deslng: arr[7]},
        dataType: 'JSON'
      });
    }else{
        $.ajax({
        url: 'deleteTrip.php',
        type: 'post',
        data: {tripID:arr[0],driver:arr[1]},
        dataType: 'JSON'
        })
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