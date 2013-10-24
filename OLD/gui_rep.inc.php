<?php
if(isset($_GET['id']) && $_GET['id'] == 'new') { ?>
	<h2>Last opp videoreportasje</h2>
	<input type="hidden" name="converter_data_pl_id" id="converter_data_pl_id" value="<?= get_option('pl_id') ?>" />
	<input type="hidden" name="converter_data_pl_type" id="converter_data_pl_type" value="<?= get_option('site_type') ?>" />
	<div class="videoopplaster">
		<form action="?page=<?= $_GET['page'] ?>&action=create&list=3" method="post">
			<div class="group" id="filechooser">
				<label>Velg fil</label>
				<input id="fileupload" type="file" name="files[]">
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
				<label>Tittel / navn</label>
				<input name="tittel" type="text" placeholder="Navn på videoreportasje..." />
			</div>			
			<div class="group">
				<label>Beskrivelse</label>
				<textarea name="beskrivelse" placeholder="Skriv en liten tekst om videoreportasjen"></textarea>
			</div>
			
			<div class="clear clearfix clear-fix"><br /></div>
			
			<div class="group">
				<label>Velg om du vil legge videoen i et eksisterende samling</label>
				<select name="kategori_velg">
					<option value="ikke_valgt" selected="selected" disabled="disabled">Velg samling</option>
				<?php
				foreach( UKMv_get_samlinger() as $id => $name ) { ?>
					<option name="<?= $id?>"><?= $name?></option>
				<?php
				} ?>
				</select>
			</div>

			<div class="group">
				<label>eller opprett ny samling</label>
				<input name="kategori" type="text" placeholder="Skriv navn på ny samling" />
			</div>

		
			<div class="group">
				<input name="submit_video" class="button-primary" id="submitbutton" type="submit" value="Lagre" disabled="disabled" />
			</div>
			
			<input type="hidden" id="ukm_id" name="ukm_id" />
		</form>
	</div>
	<div class="dropzone">
		<img src="http://ico.ukm.no/grafikk/dropzone.png" />
	</div>
	<div class="clear clearfix clear-fix"></div>
<?php } elseif(is_numeric($_GET['id'])) {
	$film = UKMv_get_film($_GET['id']);
?>
	<h2>Rediger</h2>
	<div class="videoopplaster">
		<form action="?page=<?= $_GET['page'] ?>&list=3" method="post">
			<div class="group">
				<label>Tittel / navn</label>
				<input name="tittel" type="text" value="<?= $film['video_name'] ?>" placeholder="Navn på videoreportasje..." />
			</div>

			<div class="group">
				<label>Beskrivelse</label>
				<textarea name="beskrivelse" placeholder="Skriv en liten tekst om videoreportasjen"><?= $film['video_description'] ?></textarea>
			</div>


			<div class="clear clearfix clear-fix"><br /></div>

			<div class="group">
				<label>Velg om du vil legge videoen i et eksisterende album</label>
				<select name="kategori_velg">
					
				<?php
				foreach( UKMv_get_samlinger() as $id => $name ) { ?>
					<option value="<?= $name?>" <?= $film['video_category']==$name ? 'selected="selected"':''?>><?= $name?></option>
				<?php
				} ?>
				</select>

			</div>

			<div class="group">
				<label>eller opprett nytt album</label>
				<input name="kategori" type="text" placeholder="Skriv navn på nytt album" />
			</div>




		
			<div class="group">
				<input name="submit_video" class="button-primary" id="submitbutton" type="submit" value="Lagre"  />
			</div>
			
			<input type="hidden" id="ukm_id" value="<?= $film['cron_id'] ?>" name="ukm_id" />
		</form>
	</div>
	<div class="dropzone">
		<iframe src="http://embed.ukm.no/<?= $film['video_file'] ?>" class="ukmtv" width="500" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
	<div class="clear clearfix clear-fix"></div><?php
} else { ?>
	<div class="wrap">
		<h2>Videoreportasjer
			<a href="?page=<?= $_GET['page'] ?>&list=<?= $_GET['list']?>&id=new" class="add-new-h2" style="font-weight: bold;">Legg til ny videoreportasje</a>
		</h2>
	<ul class="videoreportasjer">
	<?php
	if(!is_array(UKMV_get_filmer())) { ?>
		<li class="none">
			<h2>Du har ikke lagt til noen videoreportasjer enda</h2>
		</li>
	<?php 	
	} else {
		require_once('UKM/tv.class.php');
		foreach(UKMv_get_filmer() as $film) { 
		$tv = new tv(false, $film['cron_id']);

		if(!$tv->id)
			$tv = (object) UKMv_get_film($film['cron_id']);
		else
			$tv->cron_id = $film['cron_id'];
		?>
			<li class="reportasje" id="<?= $tv->cron_id?>">
				<div class="icons">
					<div class="group ">
						<div class="ikon_detaljer">
							<?=UKMN_icoButton('sirkel-pluss',18,'vis detaljer')?>
						</div>
						<div class="ikon_detaljer_skjul"><?=UKMN_icoButton('sirkel-minus',18,'skjul detaljer')?></div>
					</div>
				</div>
				<div class="basics">
					<div class="image"><img src="<?= $tv->image_url ?>" width="100" /></div>
					<div class="title"><?= $tv->title ?></div>
					<div class="set">Samling: 
						<a href="<?= $tv->set_url ?>" target="_blank"><?= $tv->set ?></a>
					</div>
					<div class="category">Kategori: 
						<a href="<?= $tv->category_url ?>" target="_blank"><?= $tv->category ?></a>
					</div>
					<div class="status"></div>
				</div>
				<div class="detailed" id="details_<?= $tv->cron_id ?>">
					<div class="loading">Vennligst vent, laster inn...</div>
					<div class="loaded"></div>
				</div>
				<div class="clear clearfix clear-fix"></div>
			</li>
		<?php
		} 
	} ?>
	</ul>
<?php	
} ?>