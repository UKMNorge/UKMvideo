<?php
use UKMNorge\OAuth2\HandleAPICall;

require_once('UKMconfig.inc.php');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$arrangement = new Arrangement(get_option('pl_id'));

if($arrangement->getMeta('cloudflare_live_id')) {
  $livestreamId = $arrangement->getMeta('cloudflare_live_id')->getValue();
}

$headers = array();
$headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
$headers[] = 'Content-Type: application/json';

$ch = curl_init();


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

$handleCall->sendToClient(['videos' => json_decode($result2)]);