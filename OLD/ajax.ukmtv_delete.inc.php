<?php
require_once('UKM/tv.class.php');
if(is_numeric($_POST['cron_id'])) {
	$tv = new tv(false, $_POST['cron_id']);
	$tv->delete();
	
	$standalone = new SQLdel('ukm_standalone_video', array('cron_id'=>$_POST['cron_id']));
	$standalone->run();	
	
//	DELETE FROM STANDALONE
}