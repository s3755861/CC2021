<?php
require 'vendor/autoload.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucket = 's3755861-image';


$sdk = new Aws\Sdk([
    'region'   => 'us-east-1',
    'version'  => 'latest',
    'credentials' => [
        'key' => 'AKIATCROGQEFQJPY6VO7',
        'secret' => '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'
    ]
]);

$s3 = new S3Client([
    'region'  => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
        'key' => 'AKIATCROGQEFQJPY6VO7',
        'secret' => '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'
    ]
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();


function validateUser($dynamodb, $marshaler, $username, $type){

    $key = $marshaler->marshalJson('
    {
        "username": "'. $username.'" 
    }
    ');

    $params = [
    'TableName' => $type,
    'Key'=> $key
    ];

try {
    $result = $dynamodb->getItem($params);
    return $result['Item'];
} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}

}

function createUser($dynamodb, $marshaler, $username, $password, $type){
 	$item = $marshaler->marshalJson('
    {
        "username": "' . $username . '",
        "password": "' . $password . '"
    }
');

    $params = [
        'TableName' => $type,
        'Item' => $item
    ];

try {
    $result = $dynamodb->putItem($params);

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}    
} 


function getLocation($dynamodb, $marshaler, $username){
    $key = $marshaler->marshalJson('
    {
        "username": "'. $username.'" 
    }
    ');

    $params = [
    'TableName' => 'Location',
    'Key'=> $key
    ];

try {
    $result = $dynamodb->getItem($params);
    return $result['Item'];
} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function getLocations($dynamodb, $marshaler){
    $eav = $marshaler->marshalJson('
    {
        ":is_driving": true 
    }
    ');

    $params = [
    'TableName' => 'Location',
    'FilterExpression' => 'is_driving=:is_driving',
    'ExpressionAttributeValues'=> $eav
    ];

try {
    $result = $dynamodb->scan($params);
    return $result['Items'];
} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

 function createTrip($driver, $passenger, $status, $startlat, $startlng, $deslat, $deslng){
    $url = "https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip";
    $params = json_encode(
    array('tripID' => time(), 'driver' => $driver, 'passenger' => $passenger, 'status' => $status, 'startlat' => $startlat, 
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
 }

function editTrip($tripID, $driver, $passenger, $status, $startlat, $startlng, $deslat, $deslng){
    $url = "https://q7sqmxgvv6.execute-api.us-east-1.amazonaws.com/trip";
    $tripID = new time();
    $params = json_encode(
    array('tripID' => $tripID, 'driver' => $driver, 'passenger' => $passenger, 'status' => $status, 'startlat' => $startlat, 
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
 }
 

 function deleteTrip($tripID, $driver){
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
 }

 function pendingTrip($dynamodb, $marshaler, $driver){
    $eav = $marshaler->marshalJson('
    {
        ":driver":"'.$driver.'",
        ":sta":"pending"
    }
    ');

    $params = [
    'TableName' => 'Trip',
    'FilterExpression' => '#sta=:sta and driver=:driver',
    'ExpressionAttributeNames'=> [ '#sta' => 'status' ],
    'ExpressionAttributeValues'=> $eav
    ];

try {
    $result = $dynamodb->scan($params);
    if(empty($result['Items'])){
       return false;
    }else{
        foreach ($result['Items'] as $trip) {
        $array[0] = $marshaler->unmarshalValue($trip['tripID']);
        $array[1] = $marshaler->unmarshalValue($trip['driver']);
        $array[2] = $marshaler->unmarshalValue($trip['passenger']); 
        $array[3] = $marshaler->unmarshalValue($trip['status']); 
        $array[4] = $marshaler->unmarshalValue($trip['startlat']); 
        $array[5] = $marshaler->unmarshalValue($trip['startlng']);  
        $array[6] = $marshaler->unmarshalValue($trip['deslat']); 
        $array[7] = $marshaler->unmarshalValue($trip['deslng']);    
    }
        return $array;
    }
} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
 }

?>