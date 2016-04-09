<?php
	
$site_type = get_option('site_type');

if( $site_type == 'land' || $site_type == 'fylke' ) {
	$INFOS['livestream_aktiv'] = true;
} else {
	$INFOS['livestream_aktiv'] = get_option('livestream_aktiv');
}

$INFOS['livestream_password'] = get_site_option('ukm_livestream_password');
$INFOS['livestream_username'] = get_site_option('ukm_livestream_username');

$INFOS['site_type'] = $site_type;
$INFOS['is_superadmin'] = is_super_admin();

$INFOS['live_link'] = get_option('ukm_live_link');
$INFOS['live_embedcode'] = get_option('ukm_live_embedcode');

// Rensk opp i URLer
if( is_string( $INFOS['live_link'] ) ) {
	$INFOS['live_link'] = stripslashes( $INFOS['live_link'] );
}
if( is_string( $INFOS['live_embedcode'] ) ) {
	$INFOS['live_embedcode'] = stripslashes( $INFOS['live_embedcode'] );
}

$hendelser = get_option('ukm_hendelser_perioder' );

$INFOS['hendelser'] = $hendelser;

$pl = new monstring( get_option('pl_id') );
$monstring = new StdClass();
$monstring->navn = $pl->g('pl_name');
$monstring->season = $pl->g('season');
$monstring->pl_id = $pl->g('pl_id');
$monstring->start = $pl->get('pl_start');
$monstring->stopp = $pl->get('pl_stop');

$INFOS['monstring'] = $monstring;