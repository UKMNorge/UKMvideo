<?php
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Meta\Write as WriteMeta;


require_once('UKMconfig.inc.php');

// Hent videos fra CloudFlare Stream
// Basert på WP bruker id

$handleCall = new HandleAPICall(['status'], [], ['GET', 'POST'], false);

$arrangement = new Arrangement(get_option('pl_id'));

$status = $handleCall->getArgument('status');

// Deaktiver direktesending på arrangement og stopp prosessen
if($status == 'false') {
    $meta = static::getArrangement()->getMeta('har_livestream')->set(false);
    WriteMeta::set($meta);
    $handleCall->sendToClient(['status' => false]);
    die;
} 
else {
    $meta = static::getArrangement()->getMeta('har_livestream')->set(true);
    WriteMeta::set($meta);
}
// Det finnes live link fra før, ikke opprett det på nytt
if($arrangement->getMeta('cloudflare_live_id')->getValue()) {
    $handleCall->sendToClient([
        'status' => true,
        'cfLiveId' => $arrangement->getMeta('cloudflare_live_id')->getValue(),
        'current_link' => $arrangement->getMeta('live_link')->getValue()
    ]);
}


$headers = array();
$headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
$headers[] = 'Content-Type: application/json';

$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"defaultCreator":"string","meta":{"name":"'. $arrangement->getNavn() .'"},"recording":{"mode":"automatic","requireSignedURLs":false,"timeoutSeconds":0}}');
curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream/live_inputs/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$res = json_decode($result);

// Generer lenker
$liveLink = 'https://'. UKM_CLOUDFLARE_CUSTOMER . '/' . $res->result->uid;
$liveEmbed = '<iframe src="'. $liveLink .'/iframe" style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;" allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;" allowfullscreen="true">';

$cloudflare_live_id = $arrangement->getMeta('cloudflare_live_id')->set($res->result->uid);
$meta_live_link = $arrangement->getMeta('live_link')->set($liveLink);
$meta_live_embed = $arrangement->getMeta('live_embed')->set(stripslashes($liveEmbed));
$rtmps_url = $arrangement->getMeta('rtmps_url')->set($res->result->rtmps->url);
$rtmps_key = $arrangement->getMeta('rtmps_key')->set($res->result->rtmps->streamKey);

// Lagre meta med lenk og iframe på db
WriteMeta::set($cloudflare_live_id);
WriteMeta::set($meta_live_link);
WriteMeta::set($meta_live_embed);
WriteMeta::set($rtmps_url);
WriteMeta::set($rtmps_key);

// returner liveLink til klient
$handleCall->sendToClient([
    'status' => true,
    'cfLiveId' => $arrangement->getMeta('cloudflare_live_id')->getValue(),
    'current_link' => $arrangement->getMeta('live_link')->getValue(),
    'rtmps_url' => $arrangement->getMeta('rtmps_url')->getValue(),
    'rtmps_key' => $arrangement->getMeta('rtmps_key')->getValue()
]);