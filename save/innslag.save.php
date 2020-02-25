<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\Upload\Uploaded;

$arrangement = new Arrangement(intval(get_option('pl_id')));
$innslag = $arrangement->getInnslag()->get($_POST['b_id'], true);

try {
    Uploaded::registrerInnslag(
        intval($_POST['cron_id']),
        $innslag,
        $arrangement
    );
    UKMvideo::getFlashbag()->success(
        'Film av '. $innslag->getNavn() .' er lastet opp og lagt i konverteringskø'
    );
} catch( Exception $e ) {
    UKMvideo::getFlashbag()->error(
        'Kunne ikke laste opp filmen. Systemet sa: '. $e->getMessage()
    );
}