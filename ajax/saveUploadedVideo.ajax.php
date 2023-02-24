<?php
use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Filmer\UKMTV\WriteFilmCloudflare;
use UKMNorge\Filmer\UKMTV\CloudflareFilm;
use UKMNorge\Filmer\UKMTV\Write as FilmWrite;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Innslag\Context\Context;
use UKMNorge\Innslag\Innslag;

require_once('UKMconfig.inc.php');

$handleCall = new HandleAPICall(
    [
        'tittel', 
        'description', 
        'cloudFlareId',
        'erReportasje',
    ], 
    [
        'innslagId',
    ], ['GET', 'POST'], false);

$arrangement_id = get_option( 'pl_id' );

if(!$arrangement_id) {
    $handleCall->sendErrorToClient('pl_id finnes ikke. Kalle ble kjørt utenfor et arrangement', 400);
    die;
}

$arrangement = new Arrangement($arrangement_id);

$tittel = $handleCall->getArgument('tittel'); 
$description = $handleCall->getArgument('description'); 
$cloudFlareId = $handleCall->getArgument('cloudFlareId'); 
$erReportasje = $handleCall->getArgument('erReportasje'); 

$erReportasje = $erReportasje == 'false' ? false : true;

// Hvis det ikke er reportasje, da kan innslag data hentes
if(!$erReportasje) {
    $context = Context::createMonstring(
        $arrangement->getId(),
        $arrangement->getType(),
        $arrangement->getSesong(),
        $arrangement->getFylke()->getId(),
        $arrangement->getKommuner()->getIdArray()
    );

    $innslag = new Innslag($handleCall->getOptionalArgument('innslagId'));
    $innslag->setContext($context);
}
$innslagId = $innslag ? $innslag->getId() : null;

$cloudFlareVideo = saveInfoToCloudflare($cloudFlareId, $arrangement->getId(), $innslagId, $tittel, $description);

$cloudFlareLink = $cloudFlareVideo->result->preview;
$cloudFlareThumbnail = $cloudFlareVideo->result->thumbnail;

const ARRANGEMENT_TYPER = ['kommune' => 1, 'fylke' => 2, 'land' => 3];

$data = [
    'id' => -1,
    'title' => $tittel,
    'description' => $description,
    'cloudflare_id' => $cloudFlareId,
    'cloudflare_lenke' => $cloudFlareLink,
    'cloudflare_thumbnail' => $cloudFlareThumbnail,
    'arrangement' => $arrangement_id,
    'innslag' => $innslagId,
    'sesong' => $arrangement->getSesong(),
    'arrangement_type' => ARRANGEMENT_TYPER[$arrangement->getType()],
    'fylke' => $arrangement->getFylke()->getId(),
    'deleted' => 0,
    'erReportasje' => $erReportasje,
];

$film = new CloudflareFilm($data);
$res = WriteFilmCloudflare::createOrUpdate($film);

// ["arrangement", "arrangement_type", "fylke", "innslag", "kommune", "person", "sesong"]

// Arrangement
$film->getTags()->opprett('arrangement', $arrangement->getId());

// arrangement_type
$film->getTags()->opprett('arrangement_type', ARRANGEMENT_TYPER[$arrangement->getType()]);

// fylke
$film->getTags()->opprett('fylke', $arrangement->getFylke()->getId());

// sesong
$film->getTags()->opprett('sesong', $arrangement->getSesong());

// sesong
$film->getTags()->opprett('sesong', $arrangement->getSesong());

// Kommuner
foreach($arrangement->getKommuner()->getAll() as $kommune) {
    $film->getTags()->opprett('kommune', $kommune->getId());
}

// Hvis det ikke er reportasje, da kan innslag data legges til på tag
if(!$erReportasje) {
    // innslag
    $film->getTags()->opprett('innslag', $innslag->getId());
        
    // Personer
    foreach($innslag->getPersoner()->getAll() as $person) {
        $film->getTags()->opprett('person', $person->getId());
    }
}

FilmWrite::saveTags($film);

$handleCall->sendToClient(json_decode(true));

/**
 * Lagrer info på Cloudflare og returnerer objektet
 *
 * @return {} - Cloudflare film objekt
 */
function saveInfoToCloudflare($videoId, $arrangement, $innslag, $title, $description) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/accounts/'. UKM_CLOUDFLARE_ACCOUNT_ID . '/stream/' . $videoId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . UKM_CLOUDFLARE_VIDEO_KEY
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"meta\": {\"arrangement\": \"". $arrangement ."\", \"innslag\": \"". $innslag ."\", \"title\": \"". $title ."\", \"description\": \"". $description ."\"}\n}");

    $result = curl_exec($ch);

    curl_close($ch);

    return json_decode($result);    
}