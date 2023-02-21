<?php
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Arrangement;


require_once('UKMconfig.inc.php');

// Hent videos fra CloudFlare Stream
// Basert på WP bruker id

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$arrangement = new Arrangement(get_option('pl_id'));

if($arrangement->getMeta('har_livestream')->getValue() == true && $arrangement->getMeta('cloudflare_live_id')->getValue()) {
  $livestreamId = $arrangement->getMeta('cloudflare_live_id')->getValue();
}
else {
  $arrRes = [
      'status' => false,
      'current_iframe' => null,
      'rtmps_url' => null,
      'rtmps_key' => null,
      'videos' => []
  ];
    $handleCall->sendToClient($arrRes);
    die;
}

$headers = array();
$headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
$headers[] = 'Content-Type: application/json';

$ch2 = curl_init();

curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch2, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream/live_inputs/' . $livestreamId . '/videos');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'GET');

$result2 = curl_exec($ch2);
curl_close($ch2);

$arrRes = [
    'status' => true,
    'current_link' => $arrangement->getMeta('live_link')->getValue(), 
    'current_iframe' => $arrangement->getMeta('live_embed')->getValue(),
    'rtmps_url' => $arrangement->getMeta('rtmps_url')->getValue(),
    'rtmps_key' => $arrangement->getMeta('rtmps_key')->getValue(),
    'videos' => json_decode($result2)
];

$handleCall->sendToClient($arrRes);