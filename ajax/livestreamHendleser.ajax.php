<?php

use UKMNorge\Filmer\UKMTV\Direkte\Write as WriteDirekte;
use UKMNorge\Meta\Collection;
use UKMNorge\Meta\ParentObject;
use UKMNorge\Meta\Write as WriteMeta;

$arrangement = UKMvideo::getArrangement();
$livestream = new Collection(
    new ParentObject('livestream', 0)
);
//Check if livestream input exists for this arrangement
if( $arrangement->getMetaValue('har_livestream') )
    $input = true;
UKMvideo::addViewData(
    [
        'arrangement' => $arrangement,
        'livestream' => $livestream,
        'input' => $input
    ]
);

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
                        'Sending for ' . $hendelse->getNavn() . ' ble ikke opprettet! ' .
                            'Systemet sa: ' . $e->getMessage()
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
                        'Kunne ikke slette sending for ' . $hendelse->getNavn() . '! ' .
                            'Systemet sa: ' . $e->getMessage()
                    );
                }
            }
        }
    }
}

die(true);