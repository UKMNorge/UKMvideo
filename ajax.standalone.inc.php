<?php
require_once('UKM/tv.class.php');
$tv = new tv(false, $_POST['cron_id']);

if(!$tv->id)
	die('Detaljer er ikke tilgjengelig mens filen er til konvertering');
else
	$tv->cron_id = $_POST['cron_id'];
?>
<div class="detailed video_data" data-tv-full-url="<?= $tv->full_url ?>">
	<div class="loaded">
		<div class="shareicons">
			<div class="icon-embed"><?= UKMN_icoButton('embed-simple', 24, 'bygg inn') ?></div>
			<div class="icon-tv"><?= UKMN_icoButton('UKMtv_small', 24, 'åpne i UKM-TV') ?></div>
			<div class="icon-face"><?= UKMN_icoButton('face', 24, 'del på facebook') ?></div>
			<div class="ikon-rediger"><strong><?=UKMN_icoButton('pencil',24,'rediger/flytt',11)?></strong></div>
			<div class="ikon-slett"><strong><?=UKMN_icoButton('trash',24,'slett',11)?></strong></div>
		</div>
		<div class="video_embedcode" style="display:none;"><?= $tv->embedcode() ?></div>
		<div class="video_preview"><?= $tv->embedcode('550px') ?></div>
		<div class="description"><?= $tv->description ?></div>
	</div>
</div>
<?php die(); ?>