<!DOCTYPE html>
<html>
  <head>
    <title>Passenger main page</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <link rel="stylesheet" type="text/css" href="pm.css"/>

    <?php
    require "functions.php";
    session_start();
    $username = $_SESSION['user'];
    $user = getLocation($dynamodb, $marshaler, $username);
    $ip = in_progressPa($dynamodb, $marshaler, $username);
    $pending = pendingPa($dynamodb, $marshaler, $username);
    $ipboo;
    $pendingboo;
     
     if(empty($pending)){
        $pendingboo = -1;
      }else{
        $pendingboo = 1;
      }
      
     if(empty($ip)){
        $ipboo = -1;
      }else{
        $ipboo = 1;
      }
      
    $users = getLocations($dynamodb, $marshaler);
    $lat = $marshaler->unmarshalValue($user['lat']);
    $lng = $marshaler->unmarshalValue($user['lng']);
    ?>

    <script src="jquery-3.6.0.min.js"></script>
    <script>
      // This example adds a search box to a map, using the Google Place Autocomplete
      // feature. People can enter geographical searches. The search box will return a
      // pick list containing a mix of places and predicted search terms.
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      var latitude;
      var longitude;
      function initAutocomplete() {
        const map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: -37.8, lng: 144.9666 },
          zoom: 13,
          mapTypeId: "roadmap",
        });
        // Create the search box and link it to the UI element.
        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // Bias the SearchBox results towards current map's viewport.
        map.addListener("bounds_changed", () => {
          searchBox.setBounds(map.getBounds());
        });
        let markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener("places_changed", () => {
          const places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }
          // Clear out the old markers.
          markers.forEach((marker) => {
            marker.setMap(null);
          });
          markers = [];
          // For each place, get the icon, name and location.
          const bounds = new google.maps.LatLngBounds();
          places.forEach((place) => {
            latitude = place.geometry.location.lat();
            longitude = place.geometry.location.lng();
            if (!place.geometry || !place.geometry.location) {
              console.log("Returned place contains no geometry");
              return;
            }
            const icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25),
            };
            // Create a marker for each place.
            markers.push(
              new google.maps.Marker({
                map,
                icon,
                title: place.name,
                position: place.geometry.location,
              })
            );

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          
          map.fitBounds(bounds);
        });
        //Add passenger marker
        
        var lat = <?php echo $lat;?>;
        var lng = <?php echo $lng;?>;

        var marker = new google.maps.Marker({
          position: { lat: lat, lng: lng },
          map: map,
          icon: 'https://createbucket-py.s3.us-east-2.amazonaws.com/Child_9.png'
          
        });

        

        

        //Add marker version 2
        function addMarker2(latLng, name, title){

        var marker = new google.maps.Marker({
            map:map,
            position:latLng,
            icon: 'https://createbucket-py.s3.us-east-2.amazonaws.com/Car_2.png',
            title: title
        });

        
        var infowindow = new google.maps.InfoWindow({
          content:'<h1>click me</h1>'
        });

        google.maps.event.addListener(marker,'click',function(mev){
                var div = document.createElement('div');
                var driverTitle = marker.getTitle();
                div.innerHTML = name;
                div.onclick = function(){iwClick(name,driverTitle)};
                //div.onclick = function(){on()};
                infowindow.setContent(div);
                infowindow.setPosition(mev.latLng);
                infowindow.open(map);

            });

        }

        function iwClick(str, title){
            //alert(str);
            result = window.confirm(str);
            if(result)
            {
              var username = "<?php echo $username;?>";
              var tripID = <?php echo time();?>;
              $.ajax({
                 headers: {
                    "X-Api-Key": 'blablabla',
                    "Content-Type": "application/json"
                },
                crossDomain: true,
                url: 'https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip',
                type: 'put',
                data: JSON.stringify({
                        "tripID":tripID,
                        "driver": title,
                        "passenger": username,
                        "status":"pending",
                        "startlat": lat,
                        "startlng": lng,
                        "deslat": latitude,
                        "deslng": longitude
                    }),
                dataType: 'JSON'
              });
              document.getElementById("overlay").style.display = "block";
            }
        };
        
         function getLocation(){
          var arr = <?php echo json_encode($users);?>;
          var aarr = eval(arr);
          for(i = 0; i < aarr.length; i++){
            var marker = new google.maps.LatLng(aarr[i]['lat'].N, aarr[i]['lng'].N);
            addMarker2(marker,'Do you want to have a free drive?', aarr[i]['username'].S);
            
        }
        
        };
        
        var pendingboo = <?php echo $pendingboo;?>;
        var ipboo = <?php echo $ipboo;?>;
        if(ipboo == 1 || pendingboo == 1){
          document.getElementById("overlay").style.display = "block";
        };

        getLocation();

      }

      
    </script>
  </head>
  <body>
    <input
      id="pac-input"
      class="controls"
      type="text"
      placeholder="Search Box"
    />
    <div id="map"></div>
    <div id="overlay">
    <div id="text">Trip in progress</div>
    </div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
  
<script
      src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initAutocomplete&libraries=places&v=weekly"
      async
    ></script>
  </body>
</html>