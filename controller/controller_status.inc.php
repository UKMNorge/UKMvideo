<?php
$WARN = new stdClass;
$WARN->diskspace = 100;

$STATUS = new stdClass;
$STATUS->critical = new stdClass;
$STATUS->critical->status = false;
$STATUS->critical->message = null;

$STATUS->warning = new stdClass;
$STATUS->warning->status = false;
$STATUS->warning->message = null;

$STATUS->info = new stdClass;
$STATUS->info->status = false;
$STATUS->info->message = null;

require_once('UKM/curl.class.php');

// VIDEOCONVERTEREN
$curl_videoconverter = new UKMCURL();
$curl_videoconverter->timeout(2);
$status_videoconverter = $curl_videoconverter->request('https://videoconverter.' . UKM_HOSTNAME . '/api/status.php');

if(!$status_videoconverter) {
    $STATUS->critical->status = true;
    $STATUS->critical->message = "Webserveren får ikke kontakt med videoconverteren.
                                  Dette gjør at det ikke er mulig å laste opp video akkurat nå.
                                  Opplastede videoer vil heller ikke bli konvertert mens videoconverteren er nede";
} elseif( $status_videoconverter->diskspace < $WARN->diskspace*1024*1024) {
    $disk_human = $status_videoconverter->diskspace / (1024 * 1024);
    $STATUS->warning->status = true;
    $STATUS->warning->message = 'Videoconverteren har kun '. $disk_human .'GB ledig plass';
}
$INFOS['STATUS'] = $STATUS;