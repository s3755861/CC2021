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

function getUrl($s3, $bucket, $key){
    
    $result = $s3->getObjectUrl($bucket, $key);
    return $result;
}



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


function showSub($s3, $dynamodb, $marshaler, $email){

    $eav = $marshaler->marshalJson('
    {
        ":email":"'.$email.'"
    }
');
    $params = [
    'TableName' => 'subscription',
    'KeyConditionExpression' => 'email=:email',
    'ExpressionAttributeValues'=> $eav
];
    try {

    $result = $dynamodb->query($params);
    foreach ($result['Items'] as $subscription) {
        $title = $marshaler->unmarshalValue($subscription['title']);
        $artist = $marshaler->unmarshalValue($subscription['artist']);
        $year = $marshaler->unmarshalValue($subscription['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title."</td>";
        echo "<td>". $artist."</td>";
        echo "<td>". $year ."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='remove.php?title=".$title."'>remove</a></td>";
        echo "<br />";
        echo "</tr>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}

}

function createSub($dynamodb, $marshaler, $email, $title, $artist, $year){

    $item = $marshaler->marshalJson('
        {
            "email": "' . $email . '",
            "title": "' . $title . '",
            "artist": "' . $artist . '",
            "year": "' . $year . '"
        }
    ');

    $params = [
        'TableName' => 'subscription',
        'Item' => $item
    ];

    try {
    $result = $dynamodb->putItem($params);
    header("Location:main.php");

    } catch (DynamoDbException $e) {
        echo "Unable to add item:\n";
        echo $e->getMessage() . "\n";
    }

}


function remove($dynamodb, $marshaler, $email, $title){

    $key = $marshaler->marshalJson('
        {
            "email": "' . $email . '", 
            "title": "' . $title . '"
        }
    ');

    $params = [
        'TableName' => 'subscription',
        'Key' => $key
    ]; 
    try {
        $result = $dynamodb->deleteItem($params);
        header("Location:main.php");

    } catch (DynamoDbException $e) {
        echo "Unable to delete item:\n";
        echo $e->getMessage() . "\n";
    }
}

function scan($s3, $dynamodb, $marshaler, $title, $artist, $year){
    $eav = $marshaler->marshalJson('
        {
            ":title":"'.$title.'", 
            ":artist":"'.$artist.'", 
            ":yyyy":"'.$year.'" 
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => '#yr=:yyyy and title=:title and artist=:artist',
        'ExpressionAttributeNames'=> [ '#yr' => 'year' ],
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);
    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";
        
    }
         echo "</table>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function scanbytitle($s3, $dynamodb, $marshaler, $title){
    $eav = $marshaler->marshalJson('
        {
            ":title":"'.$title.'" 
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => 'title=:title',
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);

    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";
    }
         echo "</table>";
    }
} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function scanbyyear($s3, $dynamodb, $marshaler, $year){
    $eav = $marshaler->marshalJson('
        {
            ":yyyy":"'.$year.'" 
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => '#yr=:yyyy',
        'ExpressionAttributeNames'=> [ '#yr' => 'year' ],
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);
    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";

    }
        echo "</table>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function scanbyartist($s3, $dynamodb, $marshaler, $artist){
    $eav = $marshaler->marshalJson('
        {
            ":artist":"'.$artist.'"
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => 'artist=:artist',
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);
    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";
    }
        echo "</table>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function scanbyta($s3, $dynamodb, $marshaler, $title, $artist){
    $eav = $marshaler->marshalJson('
        {
            ":title":"'.$title.'", 
            ":artist":"'.$artist.'"
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => 'title=:title and artist=:artist',
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);
    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";
    }
        echo "</table>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function scanbyty($s3, $dynamodb, $marshaler, $title, $year){
    $eav = $marshaler->marshalJson('
        {
            ":title":"'.$title.'", 
            ":yyyy":"'.$year.'" 
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => '#yr=:yyyy and title=:title',
        'ExpressionAttributeNames'=> [ '#yr' => 'year' ],
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);
    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";
    }
        echo "</table>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}

function scanbyay($s3, $dynamodb, $marshaler, $artist, $year){
    $eav = $marshaler->marshalJson('
        {
            ":artist":"'.$artist.'", 
            ":yyyy":"'.$year.'" 
        }
    ');

    $params = [
        'TableName' => 'music',
        'FilterExpression' => '#yr=:yyyy and artist=:artist',
        'ExpressionAttributeNames'=> [ '#yr' => 'year' ],
        'ExpressionAttributeValues'=> $eav
    ];
    try {

    $result = $dynamodb->scan($params);
    if (empty($result['Items'])) {
        header("Location:main.php?err=1");
    }else{
        foreach ($result['Items'] as $music) {
        $title = $marshaler->unmarshalValue($music['title']);
        $artist = $marshaler->unmarshalValue($music['artist']);
        $year = $marshaler->unmarshalValue($music['year']);
        $url = getUrl($s3, 's3755861-image', $artist);
        echo "<tr>";
        echo "<td>". $title ."</td>";
        echo "<td>". $artist ."</td>";
        echo "<td>". $year."</td>";
        echo "<td><img src=".$url.".jpg width=70px height=70px alt=''/></td>";
        echo "<td> <a href='subscribe.php?title=".$title."&artist=".$artist."&year=".$year."'>subscribe</a></td>";
        echo "<br />";
        echo "</tr>";
    }
        echo "</table>";
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}
}


?>