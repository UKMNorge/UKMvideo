<?php
require_once('UKM/innslag.class.php');

if( !isset( $_POST['innslag'] ) ) {
	$AJAX_DATA = array( 'success' => false,
						'message' => 'Mangler innslags-ID'
					  );
} else {
	$innslag = new innslag( $_POST['innslag'] );
	$related = $innslag->related_items();
	
	
	if(is_array($related['tv'])) {
		$videos = array();
		foreach($related['tv'] as $key => $tv) {
			$tv->embed = $tv->embedcode(600);
			$videos[] = $tv;
		}
	} else {
		$videos = $related['tv'];
	}
	
	
	$converting = array();
	$moving = array();
	$conv = new SQL("SELECT *
					 FROM `ukm_related_video`
					 WHERE `b_id` = '#bid'",
					array('bid' => $innslag->get('b_id')));
	$conv = $conv->run();
	while( $r = mysql_fetch_assoc( $conv ) ) {
		if(!empty($r['file'])) {
			$moving[] = $r;
		} else {
			$converting[] = $r;
		}
	}

	
	$AJAX_DATA = array( 'success' => true,	
						'id' => $_POST['innslag'],
						'related' => $videos,
						'converting' => $converting,
						'moving' => $moving,
					  );
}