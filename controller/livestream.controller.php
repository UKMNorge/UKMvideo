<?php

use UKMNorge\Filmer\UKMTV\Direkte\Write as WriteDirekte;
use UKMNorge\Meta\Write as WriteMeta;

$arrangement = UKMvideo::getArrangement();
UKMvideo::addViewData('arrangement', $arrangement);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meta_live_link = $arrangement->getMeta('live_link')->set($_POST['live_link']);
    $meta_live_embed = $arrangement->getMeta('live_embed')->set(stripslashes($_POST['live_embed']));

    WriteMeta::set($meta_live_link);
    WriteMeta::set($meta_live_embed);

    UKMvideo::getFlashbag()->success('Livestream-detaljer er lagret');


    foreach ($_POST as $key => $value) {
        if (strpos($key, 'livestream_') === 0) {
            $hendelse = $arrangement->getProgram()->get(
                intval(
                    str_replace('livestream_', '', $key)
                )
            );

            // Fra nå skal hendelsen sendes
            if ($value == 'true') {
                // Hent og oppdater sending
                try {
                    $sending = $hendelse->getSending();
                    $sending->setStartOffset(intval($_POST['sending_' . $hendelse->getId() . '_start_for']));
                    $sending->setVarighet(intval($_POST['sending_' . $hendelse->getId() . '_varighet']));
                    WriteDirekte::lagre($sending);
                } catch (Exception $e) {
                    // Hvis sendingen ikke finnes, opprett den nå
                    if ($e->getCode() == 144001) {
                        $sending = WriteDirekte::opprett(
                            $hendelse->getId(),
                            intval($_POST['sending_' . $hendelse->getId() . '_start_for']),
                            intval($_POST['sending_' . $hendelse->getId() . '_varighet'])
                        );
                    } else {
                        UKMvideo::getFlashbag()->error(
                            'Sending for '. $hendelse->getNavn() .' ble ikke opprettet! '.
                            'Systemet sa: '. $e->getMessage()
                        );
                    }
                }
            }
            // Fra nå skal hendelsen ikke sendes
            else {
                try {
                    $sending = $hendelse->getSending();
                    WriteDirekte::slett($hendelse, $sending);
                } catch (Exception $e) {
                    // sendinger som ikke finnes trenger ikke sletting
                    // evt andre errorer kastes
                    if ($e->getCode() != 144001) {
                        UKMvideo::getFlashbag()->error(
                            'Kunne ikke slette sending for '. $hendelse->getNavn() .'! '.
                            'Systemet sa: '. $e->getMessage()
                        );
                    }
                }
            }
        }
    }
}