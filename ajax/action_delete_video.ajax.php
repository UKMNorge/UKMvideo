<?php
require_once('UKM/tv.class.php');
$TV = new tv( $_POST['tv_id'] );
$TV->delete();

if(isset($_POST['video_id']) && (int)$_POST['video_id'] > 0) {
	$sqlDel = new SQLdel('ukm_standalone_video', array('v_id' => $_POST['video_id']));
	$sqlDel->run();
}

$AJAX_DATA = array( 'success' => true,	
					'id' => $_POST['tv_id'],
					'cron_id' => $_POST['cron_id'],
					'video_id' => $_POST['video_id']);
