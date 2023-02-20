<?php
use UKMNorge\OAuth2\HandleAPICall;

require_once('UKMconfig.inc.php');

// Hent videos fra CloudFlare Stream
// Basert på WP bruker id

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$livestreamId = 'c7876c8eaf798cc89a42c542e0719d3a';

$headers = array();
$headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
$headers[] = 'Content-Type: application/json';

$ch = curl_init();
$ch2 = curl_init();

// Get fra database
// Genererer det naar man oppretter streamen

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream/live_inputs/' . $livestreamId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch2, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream/live_inputs/' . $livestreamId . '/videos');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'GET');

$result2 = curl_exec($ch2);
curl_close($ch2);

$arrRes = ['info' => json_decode($result), 'videos' => json_decode($result2)];

$handleCall->sendToClient($arrRes);