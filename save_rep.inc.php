<?php
if(isset($_POST['ukm_id'])) {
	require_once(WP_PLUGIN_DIR.'/UKMvideo/functions.inc.php');
	$qry = new SQL("SELECT `v_id` FROM `ukm_standalone_video` WHERE `cron_id` = '#ukmid'",
					array('ukmid' => $_POST['ukm_id']));
	$v_id = $qry->run('field','v_id');
	
	if(!$v_id || !is_numeric($v_id)) {
		$sql = new SQLins('ukm_standalone_video');
		$updateTV = false;
	} else {
		$sql = new SQLins('ukm_standalone_video', array('v_id' => $v_id));
		$updateTV = true;
	}

	if(empty($_POST['kategori']) && $_POST['kategori_velg'] == 'ikke_valgt') {
		$m = new monstring(get_option('pl_id'));
		$_POST['kategori'] = $m->g('pl_name').' '. $m->g('season');
	}
	if(empty($_POST['kategori'])) {
		$_POST['kategori'] = $_POST['kategori_velg'];
	}

	$sql->add('cron_id', $_POST['ukm_id']);
	$sql->add('video_name', utf8_encode($_POST['tittel']));
	$sql->add('video_description', utf8_encode($_POST['beskrivelse']));
	$sql->add('video_category', utf8_encode($_POST['kategori']));
	$sql->add('pl_id', UKMv_plid());
	$res = $sql->run();
	
	
	if(!$v_id)
		$v_id = $sql->insId();

	#if($_GET['action'] == 'create')
	#	$_GET['id'] = $v_id;
	
	if( $updateTV ) {
		file_get_contents('http://ukm.no/UKM/video/regVideo_TV.php?type=standalone&file='.$_POST['ukm_id']);
	}
}