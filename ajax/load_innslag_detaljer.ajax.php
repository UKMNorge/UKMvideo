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
			$tv->embed = $tv->embedcode(500);
			$videos[] = $tv;
		}
	} else {
		$videos = $related['tv'];
	}
	$AJAX_DATA = array( 'success' => true,	
						'id' => $_POST['innslag'],
						'related' => $videos,
					  );
}