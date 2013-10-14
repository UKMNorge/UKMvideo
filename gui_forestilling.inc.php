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

if(!isset($_GET['forestilling'])) {?>
	<h3>Velg forestilling</h3>
	<?php
	$forestillinger = $m->forestillinger();
	if(is_array($forestillinger)) {
		foreach( $forestillinger as $forestilling ) {?>
			<a href="?page=UKM_videorep&list=1&forestilling=<?= $forestilling['c_id']?>"><?= $forestilling['c_name']?></a><br />
			<?php
		}
	} else {?>
		<strong>Du må legge til hendelser og sette opp program for din mønstring før du kan bruke denne funksjonen</strong>
	<?php
	}
} elseif(isset($_GET['id'])) {
	require_once('gui_upload.inc.php');
} else {
	require_once('UKM/forestilling.class.php');
	require_once('functions_bandrelated.inc.php');
	$c = new forestilling($_GET['forestilling']);
	$bands = $c->innslag();
	?>
	<h3>Hendelsen har følgende innslag</h3>
	<?php
	if(is_array($bands)) { ?>
		<ul class="forestillingsvideo"><?php
		foreach($bands as $band) {
			UKMV_gui_band($band['b_id'], $m);
		}
		?></ul><?php
	} else { ?>
		<strong>Det er ingen innslag i denne hendelsen. Ønsker du å legge til innslag gjør du dette under menyvalget "program"</strong>
	<?php	
	}
}