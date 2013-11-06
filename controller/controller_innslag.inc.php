<?php

require_once('UKM/monstring.class.php');
require_once('UKM/innslag.class.php');
require_once('UKM/forestilling.class.php');

$monstring = new monstring( get_option('pl_id') );

if( isset( $_GET['filter'] ) && is_numeric( $_GET['filter'] ) ) {
	$hendelse = new forestilling( $_GET['filter'] );
	$INFOS['program'] = $hendelse->innslag();
	$INFOS['sortering'] = $_GET['filter'];
} else {
	$INFOS['program'] = $monstring->innslag();
	$INFOS['sortering'] = 'alfa';
}

$hendelser = $monstring->forestillinger();
$INFOS['hendelser'] = $hendelser;


if( isset( $INFOS['program'] ) ) {
	foreach($INFOS['program'] as $innslag) {
		$inn = new innslag($innslag['b_id']);
		if($inn->tittellos())
			continue;
		$related = $inn->related_items();

		$unique_id = array();
		if(is_array($related['tv'])) {
			foreach($related['tv'] as $key => $tv) {
				$unique_id[] = $tv->file;
			}
		}

		$conv = new SQL("SELECT *
						 FROM `ukm_related_video`
						 WHERE `b_id` = '#bid'",
						array('bid' => $innslag['b_id']));
		$conv = $conv->run();
		while( $r = mysql_fetch_assoc( $conv ) ) {
			if( !in_array( $r['file'], $unique_id) )
				$coming[] = $r;
		}
		
		$innslagdata = array('name' => $inn->g('b_name'),
						 'id' => $inn->g('b_id'),
						 'num_videos' => sizeof($related['tv']) + sizeof($coming),
						 'converting' => $converting,
						 'moving' => $moving,
						);
		if(isset($_POST['b_id']) && $_POST['b_id'] == $inn->g('b_id'))
			$innslagdata['recentlyUploaded'] = true;

		$alle_innslag[] = $innslagdata;
	}
	$INFOS['program'] = $alle_innslag;
}