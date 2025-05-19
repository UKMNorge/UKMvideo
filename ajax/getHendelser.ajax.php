<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$arrangement = new Arrangement(get_option('pl_id'));

$retArr = [];
foreach($arrangement->getProgram()->getAbsoluteAll() as $hendelse) {
    $hendelse->getInnslag()->getAll();
    $titler = [];
    $samtykker = [];

    foreach($hendelse->getInnslag()->getAll() as $innslag) {
        try{
            $titler[$innslag->getId()] = $innslag->getTitler()->getAll();
            $samtykker[$innslag->getId()] = $innslag->getSamtykke()->getAll();
        }
        catch(Exception $e) {
            // No title
        }
        $innslag->antallFilmer = $innslag->getFilmer($arrangement->getId())->getAntall();

    }
    $retArr[$hendelse->getId()]['hendelse'] = $hendelse;
    $retArr[$hendelse->getId()]['titler'] = $titler;
    $retArr[$hendelse->getId()]['samtykker'] = $samtykker;
}

$handleCall->sendToClient($retArr);