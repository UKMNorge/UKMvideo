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
		
		$conv = new SQL("SELECT *
						 FROM `ukm_related_video`
						 WHERE `b_id` = '#bid'",
						array('bid' => $innslag['b_id']));
		$conv = $conv->run();
		while( $r = mysql_fetch_assoc( $conv ) ) {
			$coming[] = $r;
		}
		
		$innslagdata = array('name' => $inn->g('b_name'),
						 'id' => $inn->g('b_id'),
						 'num_videos' => sizeof($related['tv']) + sizeof($coming),
						 'converting' => $converting,
						 'moving' => $moving,
						);
		$alle_innslag[] = $innslagdata;
	}
	$INFOS['program'] = $alle_innslag;
}