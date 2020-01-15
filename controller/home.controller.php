<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Filmer\Write;

$arrangement = new Arrangement( intval(get_option('pl_id')));

if( isset($_GET['slett'] ) && isset($_GET['innslagId']) ) {
    $innslag = $arrangement->geTInnslag()->get($_GET['innslagId']);
    $film = $innslag->getFilmer()->get($_GET['slett']);

    if( $film->getTag('pl') != $arrangement->getId() ) {
        UKMvideo::getFlashbag()->error('Du kan kun slette filmer fra ditt eget arrangement');
        $film = false;
    }

    // If !$film == slettet allerede (?).
    if( $film ) {
        try {
            $res = Write::slett($film);
            $innslag->getFilmer()->reset();
            UKMvideo::getFlashbag()->success('Filmen er slettet');
        } catch( Exception $e ) {
            UKMvideo::getFlashbag()->error('Kunne ikke slette film. Systemet sa: '. $e->getMessage() );
        }
    }

}


UKMvideo::include('save/livestream.save.php');
UKMvideo::addViewData('arrangement', $arrangement);
UKMvideo::addViewData('is_superadmin', is_super_admin());
