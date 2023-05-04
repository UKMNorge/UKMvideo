<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$arrangement = new Arrangement(get_option('pl_id'));

$retArr = [];
foreach($arrangement->getProgram()->getAbsoluteAll() as $hendelse) {
    $hendelse->getInnslag()->getAll();
    foreach($hendelse->getInnslag()->getAll() as $innslag) {
        $innslag->antallFilmer = $innslag->getFilmer()->getAntall();

    }
    $retArr[] = $hendelse;
}

$handleCall->sendToClient($retArr);