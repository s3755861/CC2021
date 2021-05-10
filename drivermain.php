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


    <script>
      
      function initMap() {
        
        const uluru = { lat: -25.344, lng: 131.036 };
        
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 4,
          center: uluru,
        });
        
        const marker = new google.maps.Marker({
          position: uluru,
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