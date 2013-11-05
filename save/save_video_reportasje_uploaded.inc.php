<?php
require_once('UKM/tv.class.php');
require_once('UKM/curl.class.php');

if($_POST['video_id'] == 'new')
	$sql = new SQLins('ukm_standalone_video');
else 
	$sql = new SQLins('ukm_standalone_video', array('v_id' => $_POST['video_id']) );



$pl = new monstring(get_option('pl_id'));
$basename = $blog_id == 1 ? '' : $pl->g('pl_name') .' '. $pl->g('season');
	

if($_POST['reportasje_category'] == 'new' && !empty($_POST['reportasje_new_album']))
	$kategori = $basename .' '. $_POST['reportasje_new_album'];
else
	$kategori = $_POST['reportasje_category'];

$sql->add('video_name', $_POST['reportasje_title']);
if((int)$_POST['cron_id'] > 0)
	$sql->add('cron_id', $_POST['cron_id']);
$sql->add('video_description', $_POST['reportasje_description']);
$sql->add('video_category', $kategori);
$sql->add('pl_id', get_option('pl_id'));

//echo $sql->debug();
$sql->run();


//  UPDATE WITH UKM-TV IF EXISTS
	$TV = new tv(false, $_POST['cron_id']);
	if($TV->id) {
		$register = new UKMCURL();
		$register->post( array('type' => 'standalone') );
		$register->request('http://api.ukm.no/video:tv_update/'.$_POST['cron_id']);
	}