<?php
require_once('UKM/tv.class.php');
$TV = new tv( $_POST['tv_id'] );
$TV->delete();

$AJAX_DATA = array( 'success' => true,	
					'id' => $_POST['tv_id'],
					'cron_id' => $_POST['cron_id'],
					'video_id' => $_POST['video_id']);
