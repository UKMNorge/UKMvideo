<?php
use UKMNorge\OAuth2\HandleAPICall;

require_once('UKMconfig.inc.php');

// Hent alle inputs
// Navn er place id

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$livestreamId = 'c7876c8eaf798cc89a42c542e0719d3a';

$headers = array();
$headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
$headers[] = 'Content-Type: application/json';

$ch = curl_init();

// Get fra database
// Genererer det naar man oppretter streamen

curl_setopt_array($ch, [
  CURLOPT_URL => "https://api.cloudflare.com/client/v4/accounts/account_identifier/stream/live_inputs",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "X-Auth-Email: "
  ],
]);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$arrRes = ['info' => json_decode($result), 'videos' => json_decode($result2)];

$handleCall->sendToClient($arrRes);