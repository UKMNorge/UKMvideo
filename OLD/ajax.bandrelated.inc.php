<?php
$sql = new SQL("SELECT * FROM `ukm_related_video`
				WHERE `b_id` = '#bid'
				AND `file` IS NULL",
				array('bid' => $_POST['b_id']));
$sql = $sql->run();
$printed = false;
if(mysql_num_rows( $sql ) > 0) {?>
	<h3>Filer til konvertering</h3>
	<?php
	while( $r = mysql_fetch_assoc( $sql ) ) { ?>
		<div class="convert_job" id="<?= $r['cron_id']?>">
			<div class="filename">Film nr <?= $r['cron_id']?> er til konvertering. Trykk skjul/vis detaljer for å laste inn status på nytt.</div>
			<div class="status"></div>
		</div>
	<?php
	}
	$printed = true;
}
	
$inn = new innslag( $_POST['b_id'] );
$related = $inn->related_items();
$videos = $related['tv'];

if(is_array($videos)) {
	foreach( $videos as $tv_id => $tv ) {?>
		<div class="video_data" data-tv-full-url="<?= $tv->full_url ?>" id="cron_<?= $tv->cron_id ?>" data-cron="<?= $tv->cron_id ?>">
			<div class="shareicons_related">
				<div class="related-icon-embed"><?= UKMN_icoButton('embed-simple', 22, 'bygg inn') ?></div>
				<div class="related-icon-tv"><?= UKMN_icoButton('UKMtv_small', 22, 'åpne i UKM-TV') ?></div>
				<div class="related-icon-face"><?= UKMN_icoButton('face', 22, 'del på facebook') ?></div>
				<div class="ikon-slett"><strong><?=UKMN_icoButton('trash',24,'slett',11)?></strong></div>
			</div>
			<div class="video_embedcode" style="display:none;"><?= $tv->embedcode() ?></div>
			<div class="video_preview"><?= $tv->embedcode('490px') ?></div>
		</div>
		<div class="clear clearfix clear-fix"></div>
	<?php
	}
	$printed = true;
}


if(!$printed) {?>
	<div class="error" style="width:510px;">Dette innslaget har ingen tilknyttede videofiler<div>
	<?php
}
die();
?>