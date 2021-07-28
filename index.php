<?php

require('./vendor/autoload.php');

$photos = [];
$t1 = 0;
$t2 = 0;
$timetaken = -1;


$redis = new Predis\Client();

$t1 = microtime(true) * 1000;
$cachedEnty = $redis->get("photos");
$t2 = microtime(true) * 1000;

//Checking Redis cache
if ($cachedEnty) {

 echo "Displaying data from Redis <br/>";

 $photos = json_decode($cachedEnty);
} else {

 echo "Displaying data from REST API<br/>";

 $t1 = microtime(true) * 1000;
 //getting data from REST API
 $httpClinet = new GuzzleHttp\Client(['base_uri' => 'https://jsonplaceholder.typicode.com/', 'verify' => false]);

 $response = $httpClinet->request('GET', '/photos');
 $t2 = microtime(true) * 1000;
 $photos = json_decode($response->getBody());
 //setting photos key in Redis
 $redis->set("photos", json_encode($photos));

 //setting expiry time for photos key in Redis
 $redis->expire("photos", 10);
}

foreach ($photos as $photo) {
 echo "<strong>album id: " . $photo->albumId . "</strong><br/>";
 echo "<strong>id: " . $photo->id . "</strong><br/>";
 echo "<strong>title: " . $photo->title . "</strong><br/>";
 echo "<strong>url: " . $photo->url . "</strong><br/>";
 echo "<strong>thumbnailUrl: " . $photo->thumbnailUrl . "</strong><br/>";
}


$timetaken = $t2 - $t1;

print("time taken: " . $timetaken);
