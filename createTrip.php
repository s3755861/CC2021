<?php

$driver = $_POST['driver'];
$passenger = $_POST['passenger'];
$startlat = json_decode($_POST['startlat']);
$startlng = json_decode($_POST['startlng']);
$deslat = json_decode($_POST['deslat']);
$deslng = json_decode($_POST['deslat']);


    $url = "https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip";
    $params = json_encode(
    array('tripID' => time(), 'driver' => $driver, 'passenger' => $passenger, 'status' => 'pending', 'startlat' => $startlat, 
    'startlng' => $startlng, 'deslat' => $deslat, 'deslng' => $deslng)
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