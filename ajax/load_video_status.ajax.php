<?php
$conv = new SQL("SELECT *
				 FROM `ukm_related_video`
				 WHERE `b_id` = '#bid'",
				array('bid' => $_POST['innslag']));
$conv = $conv->run();
$AJAX_DATA = array( 'num_working' => mysql_num_rows( $conv ),
					'id' => $_POST['innslag'],
					'check' => $_POST['check']);