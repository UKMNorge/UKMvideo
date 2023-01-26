<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\OAuth2\HandleAPICall;

$handleCall = new HandleAPICall(['arrangementId'], [], ['GET', 'POST'], false);

$arrangementId = $handleCall->getArgument('arrangementId');

$arrangement = new Arrangement($arrangementId);

$retArr = [];
foreach($arrangement->getProgram()->getAbsoluteAll() as $hendelse) {
    $retArr[] = $hendelse;
}

$handleCall->sendToClient($retArr);