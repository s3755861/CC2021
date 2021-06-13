<?php
require 'vendor/autoload.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;


$sdk = new Aws\Sdk([
    'region'   => 'us-east-1',
    'version'  => 'latest',
    'credentials' => [
        'key' => Your_key,
        'secret' => Your_secret
    ]
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();


function validateUser($dynamodb, $marshaler, $username){

    $key = $marshaler->marshalJson('
    {
        "username": "'. $username.'" 
    }
    ');

    $params = [
    'TableName' => 'User',
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

function createUser($dynamodb, $marshaler, $username, $password){
 	$item = $marshaler->marshalJson('
    {
        "username": "' . $username . '",
        "password": "' . $password . '"
    }
');

    $params = [
        'TableName' => 'User',
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
            return $result['Items'];
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

 function validate($array){
    if (empty($array)) {
        return -1;
    }else{
        return 1;
    }
 }

 function isDriving($dynamodb, $marshaler, $username){
    $key = $marshaler->marshalJson('
    {
        "username": "'. $username.'" 
    }
    ');


       
    $eav = $marshaler->marshalJson('
    {
        ":is_driving": true
    }
    ');


    $params = [
    'TableName' => 'Location',
    'Key'=> $key,
    'UpdateExpression' => 
        'set is_driving = :is_driving',
    'ExpressionAttributeValues'=> $eav ,
    ];
    
    try {
        $result = $dynamodb->updateItem($params);

    } catch (DynamoDbException $e) {
        echo "Unable to get item:\n";
        echo $e->getMessage() . "\n";
    }

    }
 
 function isNotDriving($dynamodb, $marshaler, $username){
    $key = $marshaler->marshalJson('
    {
        "username": "'. $username.'" 
    }
    ');


       
    $eav = $marshaler->marshalJson('
    {
        ":is_driving": false
    }
    ');


    $params = [
    'TableName' => 'Location',
    'Key'=> $key,
    'UpdateExpression' => 
        'set is_driving = :is_driving',
    'ExpressionAttributeValues'=> $eav ,
    ];
    
    try {
        $result = $dynamodb->updateItem($params);

    } catch (DynamoDbException $e) {
        echo "Unable to get item:\n";
        echo $e->getMessage() . "\n";
    }



    }

  function in_progressPa($dynamodb, $marshaler, $passenger){
    $eav = $marshaler->marshalJson('
    {
        ":passenger":"'.$passenger.'",
        ":sta":"inprogress"
    }
    ');

    $params = [
    'TableName' => 'Trip',
    'FilterExpression' => '#sta=:sta and passenger=:passenger',
    'ExpressionAttributeNames'=> [ '#sta' => 'status' ],
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




   function pendingPa($dynamodb, $marshaler, $passenger){
    $eav = $marshaler->marshalJson('
    {
        ":passenger":"'.$passenger.'",
        ":sta":"pending"
    }
    ');

    $params = [
    'TableName' => 'Trip',
    'FilterExpression' => '#sta=:sta and passenger=:passenger',
    'ExpressionAttributeNames'=> [ '#sta' => 'status' ],
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



  function in_progressDr($dynamodb, $marshaler, $driver){
    $eav = $marshaler->marshalJson('
    {
        ":driver":"'.$driver.'",
        ":sta":"inprogress"
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
            return $result['Items'];
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