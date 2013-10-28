<?php
require_once('UKM/curl.class.php');

$curl_videoconverter = new UKMCURL();
$status_videoconverter = $curl_videoconverter->request('http://videoconverter.ukm.no/api/status.php');

var_dump($curl_videoconverter);
var_dump($status_videoconverter);

echo 'zbhaøsokd';

$curl_videoconverter = new UKMCURL();
$status_videoconverter = $curl_videoconverter->request('http://videoconverterrerr.ukm.no/api/status.php');
var_dump($curl_videoconverter);
var_dump($status_videoconverter);
