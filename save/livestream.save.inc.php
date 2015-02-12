<?php
if( isset($_GET['subaction']) && strpos($_GET['subaction'], 'aktiver') !== false ) {
	if( !is_super_admin() ) {
		$INFOS['error']['type'] = 'danger';
		$INFOS['error']['title'] = 'Ingen tilgang!';
		$INFOS['error']['message'] = 'Kun UKM Norge har tilgang til å aktivere/deaktivere livestream';
	} else {
		if( $_GET['subaction'] == 'aktiver' ) {
			update_option('livestream_aktiv', true);
			$INFOS['livestream_aktiv'] = true;
					
			$INFOS['error']['type'] = 'success';
			$INFOS['error']['title'] = 'Aktivert';
			$INFOS['error']['message'] = 'Livestream-fanen er nå aktivert, og lokale brukere har fått tilgang.';
		} else {
			update_option('livestream_aktiv', false);
			$INFOS['livestream_aktiv'] = false;
			
			$INFOS['error']['type'] = 'info';
			$INFOS['error']['title'] = 'Deaktivert';
			$INFOS['error']['message'] = 'Livestream-fanen er nå deaktivert';
		}
	}
}

update_option('ukm_live_link', $_POST['live_link'] );
update_option('ukm_live_embedcode', $_POST['live_embedcode'] );
$INFOS['error']['type'] = 'success';
$INFOS['error']['title'] = 'Lagret!';
$INFOS['error']['message'] = 'Livestream-detaljer er lagret';

do_action('UKMpush_to_front_generate_object');