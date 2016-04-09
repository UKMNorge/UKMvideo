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
	$kategori = utf8_encode($_POST['reportasje_category']);

$sql->add('video_name', utf8_encode($_POST['reportasje_title']));
if((int)$_POST['cron_id'] > 0)
	$sql->add('cron_id', $_POST['cron_id']);
$sql->add('video_description', utf8_encode($_POST['reportasje_description']));
$sql->add('video_category', trim($kategori));
$sql->add('pl_id', get_option('pl_id'));

//echo $sql->debug();
$sql->run();


//  UPDATE WITH UKM-TV IF EXISTS
	if((int)$_POST['cron_id'] > 0)
		$cron_id = $_POST['cron_id'];
	else {
		$select = new SQL("SELECT `cron_id`
						   FROM `ukm_standalone_video`
						   WHERE `v_id` = '#vid'",
						   array('vid' => $_POST['video_id'] ));
		$cron_id = $select->run('field','cron_id');
	}
	$TV = new tv(false, $cron_id);	

	if( $TV->id != false) {
		$register = new UKMCURL();
		$register->post( array('type' => 'standalone') );
		$register->request('http://api.ukm.no/video:tv_update/'.$cron_id);
	}

// Hvis filen er konvertert ferdig før bruker trykker lagre vil den stoppe opp
// Dette scriptet på videoconverteren sjekker om det har skjedd, og vil i tilfelle resette status	
$reset_converter = new UKMCURL();
$reset_converter->request('http://videoconverter.ukm.no/api/reset_on_upload.php?cron_id='.$cron_id);
