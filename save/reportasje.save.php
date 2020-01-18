<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\Upload\Uploaded;

$arrangement = new Arrangement(intval(get_option('pl_id')));

try {
    Uploaded::registrerReportasje(
        intval($_POST['cron_id']),
        $_POST['reportasje_title'],
        $_POST['reportasje_description'],
        $arrangement
    );
    UKMvideo::getFlashbag()->success(
        'Filmen er lastet opp og lagt i konverteringskø.'
    );
} catch( Exception $e ) {
    UKMvideo::getFlashbag()->error(
        'Kunne ikke laste opp filmen. Systemet sa: '. $e->getMessage()
    );
}