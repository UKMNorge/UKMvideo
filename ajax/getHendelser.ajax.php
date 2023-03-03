<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall(['arrangementId'], [], ['GET', 'POST'], false);

$arrangementId = $handleCall->getArgument('arrangementId');

$arrangement = new Arrangement($arrangementId);

$retArr = [];
foreach($arrangement->getProgram()->getAbsoluteAll() as $hendelse) {
    $hendelse->getInnslag()->getAll();
    foreach($hendelse->getInnslag()->getAll() as $innslag) {
        $innslag->antallFilmer = $innslag->getFilmer()->getAntall();

    }
    $retArr[] = $hendelse;
}

$handleCall->sendToClient($retArr);