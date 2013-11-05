<?php
if($_POST['video_id'] == 'new')
	$sql = new SQLins('smartukm_standalone_video');
else 
	$sql = new SQLins('smartukm_standalone_video', array('v_id' => $_POST['video_id']) );



$pl = new monstring(get_option('pl_id'));
$basename = $blog_id == 1 ? '' : $pl->g('pl_name') .' '. $pl->g('season');
	

if($_POST['reportasje_category'] == 'new' && !empty($_POST['reportasje_new_album']))
	$kategori = $basename .' '. $_POST['reportasje_new_album'];
else
	$kategori = $_POST['reportasje_category'];

$sql->add('video_name', $_POST['reportasje_title']);
$sql->add('cron_id', $_POST['cron_id']);
$sql->add('video_description', $_POST['reportasje_description']);
$sql->add('video_category', $kategori);
$sql->add('pl_id', get_option('pl_id'));

$sql->run();