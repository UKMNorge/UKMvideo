<?php
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Filmer\UKMTV\WriteFilmCloudflare;
use UKMNorge\Filmer\UKMTV\CloudflareFilm as Film;
use UKMNorge\Arrangement\Arrangement;

require_once('UKMconfig.inc.php');

$handleCall = new HandleAPICall(
    [
        'tittel', 
        'description', 
        'cloudFlareId',
        'innslagId',
    ], [], ['GET', 'POST'], false);

$arrangement_id = get_option( 'pl_id' );


if(!$arrangement_id) {
    $handleCall->sendErrorToClient('pl_id finnes ikke. Kalle ble kjørt utenfor et arrangement', 400);
    die;
}

$arrangement = new Arrangement($arrangement_id);
$tittel = $handleCall->getArgument('tittel'); 
$description = $handleCall->getArgument('description'); 
$cloudFlareId = $handleCall->getArgument('cloudFlareId'); 
$innslagId = $handleCall->getArgument('innslagId');

$cloudFlareVideo = getFromCloudFlare($cloudFlareId);

$cloudFlareLink = $cloudFlareVideo->result->preview;
$cloudFlareThumbnail = $cloudFlareVideo->result->thumbnail;

$film = new Film(-1, $tittel, $description, $cloudFlareId, $cloudFlareLink, $cloudFlareThumbnail, $arrangement_id, $innslagId, $arrangement->getSesong());
$res = WriteFilmCloudflare::createOrUpdate($film);


$handleCall->sendToClient(json_decode('true'));


// Det trengs å hente video data fra CloudFlare etter at id ble hentet
function getFromCloudFlare($videoId) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID . '/stream/' . $videoId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    $headers = array();
    $headers[] = 'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY;
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    return json_decode($result);
}