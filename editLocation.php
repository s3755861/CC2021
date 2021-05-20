<?php
$username = $_POST['username'];
$is_driving = $_POST['is_driving'];
$lat = json_decode($_POST['lat']);
$lng = json_decode($_POST['lng']);


    $url = "https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/location";
    $params = json_encode(
    array('username' => $username, 'is_driving' => $is_driving, 'lat' => $lat, 'lng' => 'lng')
    );
     
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($params)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
     
    $res = curl_exec($ch);
    curl_close($ch);

?>