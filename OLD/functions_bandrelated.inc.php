<?php
function UKMV_gui_band($b_id, &$m) {
	$inn = new innslag($b_id);
	$inn->loadBTIMG();
	$related = $inn->related_items();
	
	$queue = new SQL("SELECT COUNT(`cron_id`) AS `count`
					  FROM `ukm_related_video`
					  WHERE `b_id` = '#bid'
					  AND `file` IS NULL",
					  array('bid' => $b_id));
	$queue = (int)$queue->run('field', 'count');
	
	$num_related = $queue + sizeof($related['tv']);
	
?>
	<li class="innslag <?= isset($_GET['uploaded'])&&$_GET['uploaded']==$inn->g('b_id') ? 'active':''?>" id="<?= $inn->g('b_id')?>">
		<div class="icons">
			<div class="ico"><?=UKMN_icoButton('number-'.$num_related,18,($num_related==1?' &nbsp;':'').'video'.($num_related==1?' &nbsp;':'er'))?></div>

			<div class="ikon_rediger"><a href="?page=<?=$_GET['page']?>&list=<?=$_GET['list']?>&forestilling=<?=$_GET['forestilling']?>&b_id=<?=$inn->g('b_id')?>&id=new"><?=UKMN_icoButton('video-upload',18,'legg til video')?></a></div>
			<div class="group">
				<div class="ikon_detaljer">
					<?=UKMN_icoButton('sirkel-pluss',18,'vis detaljer')?>
				</div>
				<div class="ikon_detaljer_skjul"><?=UKMN_icoButton('sirkel-minus',18,'skjul detaljer')?></div>
			</div>
		</div>
		<div class="basics">
			<div class="binfo">
				<div class="kategori"><?= ucfirst($inn->g('b_kategori')) ?></div>
				<div class="sjanger"><?= $inn->g('b_sjanger')?></div>
			</div>
			<div class="title"><?= $inn->g('b_name') ?></div>
			<div class="category"><strong>Titler:</strong>
			<?php
			$i=0;
			foreach($inn->titler($m->g('pl_id')) as $tittel) {
				$i++;
				echo $tittel->g('tittel');
				if($i < sizeof($inn->titler($m->g('pl_id'))))
					echo ', ';
			}
			?>
			</div>
			<div class="status"></div>
		</div>
		<div class="detailed" id="details_<?= $inn->g('b_id')?>">
			<div class="loading">Vennligst vent, laster inn...</div>
			<div class="loaded"></div>
		</div>
		<div class="clear clearfix clear-fix"></div>
	</li>
	
<?php
}?>