<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\Upload\Queue;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    UKMvideo::include('save/innslag.save.php');
}


$arrangement = new Arrangement( intval(get_option('pl_id')));

global $blog_id;
UKMvideo::addViewData('blog_id', $blog_id);

// Last opp film av innslag
if( isset($_GET['innslag'])) {
    $innslag = $arrangement->getProgram()->get($_GET['hendelse'])->getInnslag()->get($_GET['innslag']);
    UKMvideo::addViewData('innslag', $innslag);
}
// Vis alle innslag i hendelsen
if( isset($_GET['hendelse'] )) {

    foreach( $arrangement->getProgram()->get($_GET['hendelse'])->getInnslag()->getAll() as $innslag ) {
        $innslag->setAttr('convertQueue', Queue::getByInnslag( $innslag ));
    }

    UKMvideo::addViewData('hendelse', $arrangement->getProgram()->get($_GET['hendelse']));
}

UKMvideo::addViewData('arrangement', $arrangement);