<?php

require_once('UKM/monstring.class.php');
require_once('UKM/innslag.class.php');
require_once('UKM/forestilling.class.php');

$monstring = new monstring( get_option('pl_id') );
$hendelser = $monstring->forestillinger();


$infos['hendelser'] = $hendelser;