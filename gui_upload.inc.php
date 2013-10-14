<?php
$inn = new innslag($_GET['b_id']);
global $blog_id;
?>
<h2>Last opp ny film</h2>
<input type="hidden" name="converter_data_pl_id" id="converter_data_pl_id" value="<?= get_option('pl_id')?>" />
<input type="hidden" name="converter_data_pl_id" id="converter_data_pl_type" value="<?= get_option('site_type') ?>" />
<input type="hidden" name="converter_data_b_id" id="converter_data_b_id" value="<?= $_GET['b_id']?>" />
<input type="hidden" name="converter_data_blog_id" id="converter_data_blog_id" value="<?= $blog_id?>" />
<div class="videoopplaster">
	<form action="?page=<?= $_GET['page'] ?>&list=<?= $_GET['list']?>&forestilling=<?=$_GET['forestilling']?>&action=create&uploaded=<?=$_GET['b_id']?>" method="post">
		<div class="group" id="filechooser">
			<label>Velg fil</label>
			<input id="fileupload_band" type="file" name="files[]">
		</div>
		<div class="group" id="uploading">
			<label>Laster opp fil</label>
			<progress value="0" max="100" id="uploadprogress"></progress>
		</div>
		<div class="group" id="uploaded">
			<h2>Filen er lastet opp!</h2>
			<strong>Du kan nå trykke lagre, filmen er sendt til konvertering</strong>
		</div>	
		<div class="group">
			<label>Navn på innslag</label>
			<?= $inn->g('b_name');?>
		</div>

		<div class="group">
			<label>Album</label>
			<?= $m->g('pl_name').' '.get_option('season') ?>
		</div>
		<input type="hidden" id="b_id" value="<?= $_GET['b_id']?>" name="b_id" />	
		<input type="hidden" id="ukm_band_id" value="<?= $_GET['b_id']?>" name="ukm_band_id" />	
		<div class="group">
			<input name="submit_video" class="button-primary" id="submitbutton" type="submit" value="Lagre" disabled="disabled" />
		</div>
	</form>
</div>
<div class="dropzone">
	<img src="http://ico.ukm.no/grafikk/dropzone.png" />
</div>
<div class="clear clearfix clear-fix"></div>