<?php
$place = new monstring(get_option('pl_id'));
?>
<div class="wrap" id="tvadmin_wrap">

	<div class="error" id="videoupgradetip">
		<strong>OBS:</strong>
		Videoopplasteren kan kreve en relativt ny nettleser.
		<br />
		Vi anbefaler <a href="https://www.google.com/intl/no/chrome/browser/">Google Chrome</a>, 
					<a href="http://www.mozilla.org/nb-NO/firefox/new/">Mozilla Firefox</a> eller 
					<a href="http://windows.microsoft.com/en-US/internet-explorer/downloads/ie-9/worldwide-languages">Internet Explorer 9</a>.
		<br />
		Problemer med opplasting? <a href="mailto:support@ukm.no">Ta kontakt med support!</a>
	</div>	


	<img src="http://tv.ukm.no/img/ukmtv_logo.png" id="tvadmin_logo" />
	<h2>Administrer innhold</h2>
		<div class="clear clearfix clear-fix"></div>
</div>
<div class="video_tabs">
	<a href="?page=<?=$_GET['page']?>&list=1" <?=((!isset($_GET['list'])||$_GET['list']=='1')?' class="active"':'')?>>
		<div>
			<span class="tab_header">Video fra din mønstring</span>
			<?= UKMN_icoAlt('chart', 'Følg forestilling', 25) ?><br>
			<span class="tab_description">Sortert etter program</span>
		</div>
	</a>
	<a href="?page=<?=$_GET['page']?>&list=2" <?=((isset($_GET['list'])&&$_GET['list']=='2')?' class="active"':'')?>>
		<div>
			<span class="tab_header">Video fra din mønstring</span>
			<?= UKMN_icoAlt('people', 'Alle innslag på mønstringen', 25) ?><br>
			<span class="tab_description">Sortert alfabetisk</span>
		</div>
	</a>
	<a href="?page=<?=$_GET['page']?>&list=3" <?=((isset($_GET['list'])&&$_GET['list']=='3')?' class="active"':'')?>>
		<div>
			<span class="tab_header">Videoreportasjer</span>
			<?= UKMN_icoAlt('video', 'Videoreportasjer', 25) ?><br>
			<span class="tab_description">Videoer uten tilknytning til innslag</span>
		</div>
	</a>
</div>

<div class="video_tabs_desc">
	<span>
	</span>
</div>
<div class="ukmvideo_wrapper">
