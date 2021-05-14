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


?>