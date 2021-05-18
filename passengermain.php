<!DOCTYPE html>
<html>
  <head>
    <title>Places Search Box</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style type="text/css">
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }

      /* Optional: Makes the sample page fill the window. */
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
      }

      #infowindow-content .title {
        font-weight: bold;
      }

      #infowindow-content {
        display: none;
      }

      #map #infowindow-content {
        display: inline;
      }

      .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Roboto;
      }

      #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
      }

      .pac-controls {
        display: inline-block;
        padding: 5px 11px;
      }

      .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
      }

      #target {
        width: 345px;
      }
    </style>

    <?php
    require "functions.php";
    session_start();
    $username = 'passenger1';
    $user = getLocation($dynamodb, $marshaler, 'passenger1');
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

          
        });

        

        

        //Add marker version 2
        function addMarker2(latLng, name, title){

        var marker = new google.maps.Marker({
            map:map,
            position:latLng,
            icon: 'images/car-placeholder.png',
            title: title
        });

        
        var infowindow = new google.maps.InfoWindow({
          content:'<h1>click me</h1>'
        });

        google.maps.event.addListener(marker,'click',function(mev){
                var div = document.createElement('div');
                div.innerHTML = name;
                div.onclick = function(){iwClick(name)};
                infowindow.setContent(div);
                infowindow.setPosition(mev.latLng);
                infowindow.open(map);

            });

        }

        function iwClick(str){
            //alert(str);
            result = window.confirm(str);
            if(result)
             var username = <?php echo $username;?>;
            {
              $.ajax({
              url: 'createTrip.php',
              type: 'post',
              data: {driver:marker.tittle, passenger:username, startlat:lat, startlng:lng ,deslat:place.geometry.location.lat(),deslng:place.geometry.location.lng() },
              dataType: 'JSON'
              });
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

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4Sdn-OA_q2brPyvGXKyrU5kcpzeEJmSY&callback=initAutocomplete&libraries=places&v=weekly"
      async
    ></script>
  </body>
</html>