<!DOCTYPE html>
<html>
  <head>
    <title>Driver main page</title>
    <link rel="stylesheet" type="text/css" href="dm.css"/>

    <?php
      require "functions.php";
      session_start();
      $ipboo;
      $triparr;
      $tripboo;
      $iparr;
      $username = $_SESSION['user'];
      $user = getLocation($dynamodb, $marshaler, $username);
      $lat = $marshaler->unmarshalValue($user['lat']);
      $lng = $marshaler->unmarshalValue($user['lng']);  
      $trip = pendingTrip($dynamodb, $marshaler, $username);
      $ip = in_progressDr($dynamodb, $marshaler, $username);
      
      if(empty($ip)){
        $ipboo = -1;
        $iparr = json_encode($ip);
      }else{
        $ipboo = 1;
        $iparr = json_encode($ip);
      }

      if(empty($trip)){
        $tripboo = -1;
        $triparr = json_encode($trip);
      }else{
        $tripboo = 1;
        $triparr = json_encode($trip);
      }
      
    ?>
      
    <script src="jquery-3.6.0.min.js"></script>

    <script type="text/javascript">
      
      function initMap() {
        var ipboo = <?php echo $ipboo;?>;
        var iparr = <?php echo $iparr;?>;
        var lat = <?php echo $lat;?>;
        var lng = <?php echo $lng;?>;
        const directionsRenderer = new google.maps.DirectionsRenderer();
        const directionsService = new google.maps.DirectionsService();
        const location = {lat: lat, lng: lng};
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 12,
          center: location,
        });
        
        directionsRenderer.setMap(map);
        directionsRenderer.setPanel(document.getElementById("directions-panel"));
        const marker = new google.maps.Marker({
          position: location,
          map: map,
        });
        
        if(ipboo == 1){
          window.alert("You have an incomplete trip...")
          calculateAndDisplayRoute(directionsService, directionsRenderer, iparr, lat, lng);
        }else{document.getElementById("right-panel").innerHTML = "<font size='5'>Waiting for a passenger..........<font>" + "<br /><br />"+ 
        "<input type='button' id='div_2' value='Searching' style='width:100px; height:25px'>";
        document.getElementById("div_2").addEventListener("click", () => {
          pop_up(directionsService, directionsRenderer, lat, lng);
        });}

        }
      
      function calculateAndDisplayRoute(directionsService, directionsRenderer, arr, lat, lng) {
        const waypts = [];
        var start = new google.maps.LatLng(lat, lng);
        var waypt = new google.maps.LatLng(arr[4], arr[5]);
        var end = new google.maps.LatLng(arr[6], arr[7]);
        waypts.push({
          location: waypt,
          stopover: true,
        });

        directionsService.route(
          {
            origin: start,
            destination: end,
            waypoints: waypts,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.DRIVING,
          },
          (response, status) => {
            if (status === "OK" && response) {
              directionsRenderer.setDirections(response);
            } else {
              window.alert("Directions request failed due to incorerect direction" );
            }
          }
        );

        var oDivNode = document.getElementById("div_1");
        oDivNode.innerHTML = "<input type='button' value='Complete Trip'>";
        document.getElementById("div_1").addEventListener("click", () => {
          completeTrip(arr);
        });

       }

      
       function pop_up(directionsService, directionsRenderer, lat, lng){
          var verify = <?php echo $tripboo;?>;
          if(verify == 1){
            var arr = <?php echo $triparr;?>;
            var a = confirm("A passenger wants to have a trip, do you want to help them?");
        if(a){
          arr[3] = "inprogress";
          $.ajax({
             headers: {
                "X-Api-Key": 'blablabla',
                "Content-Type": "application/json"
            },
            crossDomain: true,
            url: 'https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip',
            type: 'put',
            data: JSON.stringify({
                    "tripID": arr[0],
                    "driver": arr[1],
                    "passenger": arr[2],
                    "status": arr[3],
                    "startlat": arr[4],
                    "startlng": arr[5],
                    "deslat": arr[6],
                    "deslng": arr[7]
                }),
            dataType: 'JSON'
          });
          document.getElementById("right-panel").innerHTML="";
          calculateAndDisplayRoute(directionsService, directionsRenderer, arr, lat, lng);
        }else{
            $.ajax({
             headers: {
                "X-Api-Key": 'blablabla',
                "Content-Type": "application/json"
            },
            crossDomain: true,
            url: 'https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip',
            type: 'delete',
            data: JSON.stringify({
                    "tripID": arr[0],
                    "driver": arr[1]
                }),
            dataType: 'JSON'
          });
        }
          }else{
            window.alert("There is no passenger......");
          }
          
        }


       function completeTrip(arr){
          arr[3] = "complete";
          $.ajax({
             headers: {
                "X-Api-Key": 'blablabla',
                "Content-Type": "application/json"
            },
            crossDomain: true,
            url: 'https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip',
            type: 'put',
            data: JSON.stringify({
                    "tripID": arr[0],
                    "driver": arr[1],
                    "passenger": arr[2],
                    "status": arr[3],
                    "startlat": arr[4],
                    "startlng": arr[5],
                    "deslat": arr[6],
                    "deslng": arr[7]
                }),
            dataType: 'JSON'
          });
          $.ajax({
               headers: {
                  "X-Api-Key": 'blablabla',
                  "Content-Type": "application/json"
              },
              crossDomain: true,
              url: 'https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/location',
              type: 'put',
              data: JSON.stringify({
                      "username": arr[1],
                      "is_driving": true,
                      "lat": arr[6],
                      "lng": arr[7]
                  }),
              dataType: 'JSON'
            });
          $.ajax({
               headers: {
                  "X-Api-Key": 'blablabla',
                  "Content-Type": "application/json"
              },
              crossDomain: true,
              url: 'https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/location',
              type: 'put',
              data: JSON.stringify({
                      "username": arr[2],
                      "is_driving": false,
                      "lat": arr[6],
                      "lng": arr[7]
                  }),
              dataType: 'JSON'
            });
          window.location.href = "drivermain.php";
       }

    </script>
  </head>
  <body>
    <div id="directions-panel">
      <div id="right-panel"></div>
      <div id="div_1"></div>
    </div>
    <div id="map"></div>
  
<script
      src="https://maps.googleapis.com/maps/api/js?key=Your_API_Key&callback=initMap&libraries=&v=weekly"
      async
    ></script>
  </body>
</html>