<?php

require_once('UKM/monstring.class.php');
require_once('UKM/innslag.class.php');
require_once('UKM/forestilling.class.php');

$monstring = new monstring( get_option('pl_id') );

if( isset( $_GET['filter'] ) && is_numeric( $_GET['filter'] ) ) {
	$hendelser = new forestilling( $_GET['filter'] );
	$INFOS['program'] = $hendelser->innslag();
} elseif( isset( $_GET['filter'] ) ) {
	$INFOS['program'] = $monstring->innslag();
} else {
	$hendelser = $monstring->forestillinger();
	$INFOS['hendelser'] = $hendelser;
}

if( isset( $INFOS['program'] ) ) {
	foreach($INFOS['program'] as $innslag) {
		$inn = new innslag($innslag['b_id']);
		$related = $inn->related_items();
		
		$innslagdata = array('name' => $inn->g('b_name'),
						 'id' => $inn->g('b_id'),
						 'num_images' => sizeof($related['image']),
						);
		$alle_innslag[] = $innslagdata;
	}
	$INFOS['program'] = $alle_innslag;
}