<?php

require_once('UKM/monstring.class.php');
require_once('UKM/innslag.class.php');

$pl = new monstring( get_option('pl_id') );
$monstring = new StdClass;
$monstring->navn = $pl->g('pl_name');
$monstring->season = $pl->g('season');
$monstring->pl_id = $pl->g('pl_id');
$INFOS['monstring'] = $monstring; 

$inn = new innslag( $_GET['innslag'] );
$innslag = new StdClass;
$innslag->b_id = $inn->g('b_id');
$innslag->navn = $inn->g('b_name');
$INFOS['innslag'] = $innslag;


global $blog_id;
$INFOS['blog_id'] = $blog_id;

$INFOS['filter'] = $_GET['filter'];
var_dump($INFOS);