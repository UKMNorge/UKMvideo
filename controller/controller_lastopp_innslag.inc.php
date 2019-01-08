<?php

require_once('UKM/monstring.class.php');
require_once('UKM/innslag.class.php');

$monstring = new monstring_v2( get_option('pl_id') );
$innslag = $monstring->getInnslag()->get( $_GET['innslag'] );

$data = new StdClass;
$data->navn = $monstring->getNavn();
$data->season = $monstring->getSesong();
$data->pl_id = $monstring->getId(); 
$INFOS['monstring'] = $data; 

$data = new StdClass;
$data->b_id = $innslag->getId();
$data->navn = $innslag->getNavn();
$data->personer = $innslag->getPersoner()->getAntall();
$data->harTitler = $innslag->getType()->harTitler();
$data->samtykke = new stdClass();
$data->samtykke->harNei = $innslag->getSamtykke()->harNei();
$data->samtykke->countNei = $innslag->getSamtykke()->getNeiCount();
$INFOS['innslag'] = $data;


global $blog_id;
$INFOS['blog_id'] = $blog_id;

$INFOS['filter'] = $_GET['filter'];