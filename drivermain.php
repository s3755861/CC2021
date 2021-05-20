<!DOCTYPE html>
<html>
  <head>
    <title>Main Page</title>

    <style type="text/css">
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      #map {
        height: 100%;
        float: left;
        width: 80%;
        height: 100%;
      }
      
      #directions-panel {
        height: 100%;
        float: right;
        width: 20%;
        overflow: auto;
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
    
    function pop_up(directionsService, directionsRenderer){
      var abc = <?php echo $boo;?>;
    if(abc == 1){
      var a = confirm("A passenger wants to have a trip, do you want to help them?");
      var arr = <?php echo $arr;?>;
      arr[3] = "inprogress";
    if(a){
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
      calculateAndDisplayRoute(directionsService, directionsRenderer, arr);
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
    }
    }


</script>

    <script>
      
      function initMap() {
        var verify = <?php echo $boo;?>; 
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
        
        if(verify == 1){
          pop_up(directionsService, directionsRenderer);
        } 

        }
      
      function calculateAndDisplayRoute(directionsService, directionsRenderer, arr) {
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
    <h3>My Google Maps Demo</h3>
    <div id="map"></div>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4Sdn-OA_q2brPyvGXKyrU5kcpzeEJmSY&callback=initMap&libraries=&v=weekly"
      async
    ></script>
  </body>
</html>