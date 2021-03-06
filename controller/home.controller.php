<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\UKMTV\Write;
use UKMNorge\Filmer\Upload\Queue;

$arrangement = new Arrangement( intval(get_option('pl_id')));

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    UKMvideo::include('save/reportasje.save.php');
}

if( isset($_GET['slett'] ) ) {
    if( isset($_GET['innslagId']) ) {
        $filmer = $arrangement->getInnslag()->get(intval($_GET['innslagId']))->getFilmer();
    } else {
        $filmer = $arrangement->getFilmer();
    }
    $film = $filmer->get(intval($_GET['slett']));

    if( $film && $film->getTag('arrangement')->getValue() != $arrangement->getId() ) {
        UKMvideo::getFlashbag()->error('Du kan kun slette filmer fra ditt eget arrangement');
        $film = false;
    }
    
    // If !$film == slettet allerede (?).
    if( $film ) {
        try {
            $res = Write::slett($film);
            $filmer->reset();
            UKMvideo::getFlashbag()->success('Filmen er slettet');
        } catch( Exception $e ) {
            UKMvideo::getFlashbag()->error('Kunne ikke slette film. Systemet sa: '. $e->getMessage() );
        }
    }
}

UKMvideo::addViewData('arrangement', $arrangement);
UKMvideo::addViewData('convertQueue', Queue::getByArrangement($arrangement) );