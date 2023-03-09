<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\UKMTV\CloudflareFilm;
use UKMNorge\Filmer\UKMTV\Filmer;
use UKMNorge\Filmer\UKMTV\Write as FilmWrite;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall(['cfId'], [], ['GET', 'POST'], false);

// hent cloudflare_id fra argumenter
$cfId = $handleCall->getArgument('cfId');


// Hent film med cloudflare_id
try{
    $film = Filmer::getByCFId($cfId);

    if($film == null || !$film instanceof CloudflareFilm) {
        throw new Exception('Filmen finnes ikke som Cloudflare film');
    }
}
catch(Exception $e) {
    throw new Exception($e);
}


// Slett filmen
try{
    FilmWrite::slett($film);
    deleteVideoCloudflare($cfId);
}
catch(Exception $e) {
    throw new Exception($e);
}

$handleCall->sendToClient($retArr);

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

