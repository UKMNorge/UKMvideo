<?php
require_once('UKM/curl.class.php');

$status_videoconverter = new UKMCURL();
$status_videoconverter1 = $status_videoconverter->request('http://videoconverter.ukm.no/api/status.php');

var_dump($status_videoconverter);
var_dump($status_videoconverter1);
