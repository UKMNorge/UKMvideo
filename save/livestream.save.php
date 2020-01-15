<?php

if( isset($_GET['subaction']) && strpos($_GET['subaction'], 'livestream-') !== false ) {
	if( !is_super_admin() ) {
        UKMvideo::getFlashbag()->error('Kun UKM Norge har tilgang til å aktivere/deaktivere livestream');
	} else {
		if( $_GET['subaction'] == 'livestream-aktiver' ) {
			update_option('livestream_aktiv', true);
			UKMvideo::getViewData()['livestream']->aktiv = true;
            UKMvideo::getFlashbag()->success('Livestream-fanen er nå aktivert, og lokale brukere har fått tilgang.');
		} else {
			update_option('livestream_aktiv', false);
			UKMvideo::getViewData()['livestream']->aktiv = false;
			UKMvideo::getFlashbag()->success('Livestream-fanen er nå deaktivert');
		}
	}
}