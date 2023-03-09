<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\UKMTV\CloudflareFilm;
use UKMNorge\Filmer\UKMTV\Filmer;
use UKMNorge\Filmer\UKMTV\Write as FilmWrite;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall(['cfId'], [], ['GET', 'POST'], false);

$arrangement = new Arrangement(get_option('pl_id'));
// hent cloudflare_id fra argumenter
$cfId = $handleCall->getArgument('cfId');

if(!checkPermissions($cfId, $arrangement)) {
    throw new Exception('Du har ikke tilgang til filmen!');
}

// Hent film med cloudflare_id
try{
    $film = Filmer::getByCFId($cfId);
}
catch(Exception $e) {
    // Det finnes tilfeller når filmen er ikke publisert (ikke lagret på db) og kun finnes på Cloudflare og derfor er det ikke obligatorisk å hente CloudflareFilm
}

// Slett filmen DB
if($film) {
    FilmWrite::slett($film);
}

// Slett filmen på Cloudlfare Stream
try{
    deleteVideoCloudflare($cfId);
}
catch(Exception $e) {
    throw new Exception($e);
}

$handleCall->sendToClient(['result' => true]);

/**
 * Slett filmen på Cloudflare
 *
 * @return boolean
 */
function deleteVideoCloudflare(String $cfId) {
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID . '/stream/' . $cfId,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "DELETE",
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY
      ],
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);
    
    if ($err) {
        throw new Exception('Filmen ble ikke fjernet på Cloudflare');
    } else {
        return true;
    }
}

/**
 * Sjekk om brukeren har tilgang til filmen
 * Det sjekkes arrangement på creator, hvis brukeren har tilgang til arrangement så har brukeren tilgang på filmen
 *
 * @return boolean
 */
function checkPermissions(String $cfId, $arrangement) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID .'/stream/' . $cfId);
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

    if($result) {
        $resObj = json_decode($result);
        if($resObj && $resObj->result->creator) {
            $creatorExploded = explode("-", $resObj->result->creator);
            return $creatorExploded[0] == $arrangement->getId() ? true : false;
        }
    }

    return false;
}
