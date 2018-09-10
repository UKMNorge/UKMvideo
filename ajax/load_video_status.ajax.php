<?php
$conv = new SQL("SELECT *
				 FROM `ukm_related_video`
				 WHERE `b_id` = '#bid'
				 AND `file` = ''",
				array('bid' => $_POST['innslag']));
$conv = $conv->run();
$AJAX_DATA = array( 'num_working' => SQL::fetch( $conv ),
					'id' => $_POST['innslag'],
					'check' => $_POST['check']);