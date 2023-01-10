<?php
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Database\SQL\Query;

// Det brukes POST fordi WP tillater POST bare
$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$retArray = [];

$retArray['test'] = 'ok';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/3fec674cb83e55d312b093cfd0f53482/stream/copy');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"url\":\"https://storage.googleapis.com/stream-example-bucket/video.mp4\",\"meta\":{\"name\":\"My First Stream Video\"}}");

$headers = array();
$headers[] = 'Authorization: Bearer NTduEqjl2Jg1Lyijr1qhi4btDnXBWSOrq10XD7nO';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
var_dump($response);





// Returner til klienten
$handleCall->sendToClient($retArray);

