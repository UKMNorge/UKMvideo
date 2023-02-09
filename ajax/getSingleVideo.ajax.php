<?php
use UKMNorge\OAuth2\HandleAPICall;

require_once('UKMconfig.inc.php');

// Hent videos fra CloudFlare Stream
// Basert på WP bruker id

$handleCall = new HandleAPICall(['videoId'], [], ['GET', 'POST'], false);

$ch = curl_init();

$videoId = $handleCall->getArgument('videoId');
$wp_user_id = get_current_user_id();

curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream/' . $videoId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$handleCall->sendToClient(json_decode($result));