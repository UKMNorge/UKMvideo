<?php
$m = new monstring(get_option('pl_id'));

#### SAVE
if(isset($_POST['ukm_band_id'])) {
	$qry = new SQL("SELECT `cron_id` FROM `ukm_related_video` WHERE `cron_id` = '#ukmid'",
					array('ukmid' => $_POST['ukm_band_id']));
	$v_id = $qry->run('field','cron_id');
	
	if(!$v_id || !is_numeric($v_id))
		$sql = new SQLins('ukm_related_video');
	else
		$sql = new SQLins('ukm_related_video', array('v_id' => $v_id));

	$sql->add('cron_id', $_POST['ukm_band_id']);
	$sql->add('b_id', $_POST['b_id']);
	$sql->run();
}

if(isset($_GET['id'])) {
	require_once('gui_upload.inc.php');
} else {
	require_once('functions_bandrelated.inc.php');
	$bands = $m->innslag();
	?><ul class="forestillingsvideo"><?php
	foreach($bands as $band) {
		if($band['bt_id'] > 2)
			continue;
		UKMV_gui_band($band['b_id'], $m);
	}
	?></ul><?php
}