<?php

    $tripID = json_decode($_POST['tripID']);
    $driver = $_POST['driver'];
    
    $url = "https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip";
    $params = json_encode(
    array('tripID' => $tripID, 'driver' => $driver)
    );
     
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($params)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
     
    $res = curl_exec($ch);
    curl_close($ch);

?>