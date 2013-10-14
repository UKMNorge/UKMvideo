<?php
function UKMvideoGUI() {
	if(isset($_GET['band']))
		UKMvideoGUIupload();
	else
		UKMvideoGUIform();
}

function UKMvideoGUIform() {	
	echo '<h2>Last opp UKM-video</h2>'
		.'Velg hvilket innslag du &oslash;nsker &aring; laste opp video for<br /><br/>'
		
		.'<div class="feedbacktext" id="video_harikke">'
			.'Innslaget har ikke video, men du kan laste opp en ved &aring; klikke p&aring; &quot;last opp ny video&quot; til h&oslash;yre for innslaget'
		.'</div>'
		
		.'<ul class="forestilling">';

	$m = new monstring(get_option('pl_id'));
	$alle_innslag = $m->innslag_alpha();
	
	foreach($alle_innslag as $i => $info) {
		if(!in_array($info['bt_id'],array(1,2,6))) continue;
		$innslag = new innslag($info['b_id']);
		$innslag->loadGeo();
		
		$items = $innslag->related_items();
		$videoer = $items['video'];

		$videoKnapp = '<div class="feedback" rel="video_harikke">'
					.  UKMN_icoButton('error_utrop', 16, 'ingen video')
					.  '</div>';

		if(is_array($videoer)) {
			$videoKnapp = '<div class="video_har">'
						.  UKMN_icoButton('check', 16, 'vis video(er)')
						.  '</div>';	
		}

		echo '<li class="innslag" id="container'.$innslag->g('b_id').'">'
			

			.	'<div class="innslag_left first">'
			.		'<div class="videostatus">'.$videoKnapp.'</div>'
			.		'<span class="kommune">'.utf8_decode($innslag->g('kommune')).'</span>'
			.		'<div class="type">'.$innslag->g('bt_name').'</div>'
			.		'<div class="navn">'.$innslag->g('b_name').'</div>'
			.	'</div>'

			.	'<div class="innslag_right">'
			.		'<div class="lastoppNyVideo">'
			.			'<a href="?page='.$_GET['page'].'&band='.$innslag->g('b_id').'">'
			.				UKMN_icobutton('video-upload',20,'Last opp ny video')
			.			'</a>'
			.		'</div>'
			.	'</div>'
			;
			
		if(is_array($videoer))	
			foreach($videoer as $i => $video) {
				$antall++;
				$unikt_navn = substr($video['post_meta']['file'], strrpos($video['post_meta']['file'],'/')+1);
				$filnavn = substr($video['post_meta']['file'], 0, strrpos($video['post_meta']['file'],'.'));
				$etternavn = substr($video['post_meta']['file'], strrpos($video['post_meta']['file'],'.')+1);
				$bilde = $filnavn.'.jpg';
#				$bilde = $video['post_meta']['file'].'.jpg'; # gammel bildestandard
				$navn = isset($video['post_meta']['title']) 
						? 'Video fra ' . strtolower($video['post_meta']['title'])
						: 'Video uten navn';
				
				#echo '<pre>'; var_dump($video);echo '</pre>';
				
				echo '<div class="innslag_left" rel="video" style="display:none;">'
					.	'<div class="videoContainer">'
					.		'<img class="image" src="http://video.ukm.no/'.$bilde.'" />'
					.		' <span class="navn" title="Unik ID: '.$video['rel_id'].'">'
					.			$navn.'sm&oslash;nstring'
					.		' </span>'
					. 		' <a class="sepa" id="'.$video['rel_id'].'" href="#">'
					.			UKMN_icobutton('geek',20,'se p&aring;')
					.		'</a>'

					
					. 		' <a class="videodetaljer" id="'.$unikt_navn.'" name="'.str_replace('.'.$etternavn,'',$unikt_navn).'" rel="'.$etternavn.'" href="#">'
					.			UKMN_icobutton('pencil',20,'endre')
					.		'</a>'
					
					. 		' <a class="videoslett" id="'.$video['rel_id'].'" href="#">'
					.			UKMN_icobutton('trash',20,'slett')
					.		'</a>'
					
					. 		'<br clear="all" />'
					
					. 		'<div class="detaljvindu" id="detaljvindu_'.str_replace('.','',$unikt_navn).'"></div>'
		
					. 	'<div class="UKMvideoplayer" id="video_'.$video['rel_id'].'" rel="'.$video['post_meta']['file'].'"></div>'

					.	'</div>'
					. 	'<br clear="all" />'
					.'</div>';
			}

		echo '<div class="innslag_left last"></div>';
			
		echo '<br clear="all" />'
		.	'</li>'
			;
	}
	
	echo '</ul>';





/*



/*
	echo '<h2>Last opp UKM-video</h2>'
		.'Velg hvilket innslag du &oslash;nsker &aring; laste opp video for<br /><br/>';	
	$m = new monstring(get_option('pl_id'));
	$alle_innslag = $m->innslag_alpha();
	?>
	
	<script type="text/javascript" language="javascript">
	</script>
	<ul class="forestilling"><?php
	
	
	
	
	
	foreach($alle_innslag as $i => $info) {
		if(!in_array($info['bt_id'],array(1,2,6))) continue;
		$innslag = new innslag($info['b_id']);
		?>
		<a style="display:inline-block;"></a>
		<div class="outerBorder">
			<li id="i<?=$info['b_id']?>" class="band">
				<span class="bandThumbs" style="float:right;">
					<a href="?page=<?=$_GET['page']?>&band=<?=$innslag->g('b_id')?>"><?=UKMN_icobutton('video-upload',32,'Last opp ny video')?></a>
				</span>

				<p style="margin: 0px;"><?=$innslag->g('b_sjanger')?></p>
				<span class="bBasicInfo">
					<h3><?= $innslag->g('b_name') ?></h3>
				<?php
					$items = $innslag->related_items();
					$videoer = $items['video'];
					$antall = 0;
					if(is_array($videoer)) {
						echo '<span class="video">';
							echo '<h4>Tilknyttede videoer</h4>';
						foreach($videoer as $i => $video) {
							$antall++;
							$unikt_navn = substr($video['post_meta']['file'], strrpos($video['post_meta']['file'],'/')+1);
							$navn = isset($video['post_meta']['title']) ? 'Video fra ' . strtolower($video['post_meta']['title']) : 'Video uten navn';
							echo $antall . ': ' . $navn;
							echo ' <a class="videodetails" id="'.$unikt_navn.'" href="#">verktoy / detaljer</a>';

						}
						echo '</span>';
					}
				
				
				?>
				</span>
				<span class="break"></span>
				<div style="clear:both;"></div>	<!-- slutt deltakere/titler -->		
			</li>		
			<div style="clear:both;"></div>	
		</div>
<?php } ?>
	</ul>
	<div id="videodetaljer">
		<h2>Detaljer</h2>
		<div>Ingen video valgt</div>
	</div>

<div style="clear:both;"></div>	
<?php
*/
}
function UKMvideoGUIupload() {
	global $blog_id;
	?>
	<!-- Load Queue widget CSS and jQuery -->
	<style type="text/css">@import url(/wp-content/plugins/UKMNorge/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">google.load("jquery", "1.3");</script>
	<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
	<script type="text/javascript" src="/wp-content/plugins/UKMNorge/plupload/js/plupload.full.js"></script>
	<script type="text/javascript" src="/wp-content/plugins/UKMNorge/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js"></script>

	<script type="text/javascript">
	plupload.addI18n({
    	    'Select files' : 'Last opp videofil',
        	'Add files to the upload queue and click the start button.' : 'Legg til filer i ved &aring; klikke &quot;Legg til filer&quot;, og klikk deretter &quot;Start opplasting&quot;<br />TIPS: Skal du velge flere videoer kan du bruke shift-tasten, eller ctrl-tasten p&aring; tastaturet.',
	        'Filename' : 'Filnavn',
    	    'Status' : 'Status',
        	'Size' : 'St&oslash;rrelse',
	        'Add files' : 'Legg til filer',
    		'Start upload':'Start opplasting',
        	'Stop current upload' : 'Avbryt',
	        'Start uploading queue' : 'Start opplastingsk&oslash;',
    	    'Drag files here.' : 'Dra filer hit'
	});
	</script>

	<script type="text/javascript">
	// Convert divs to queue widgets when the DOM is ready
	$(function() {

		function serializeArray(array){
			var serialized = "";
			var size = 0;
	
			for (var key in array){
				++size;
				serialized = serialized + "s:" +
						unescape(encodeURIComponent(String(key))).length + ":\"" + String(key) + "\";s:" +
						unescape(encodeURIComponent(String(array[key]))).length + ":\"" + String(array[key]) + "\";";
			}
			serialized = "a:" + size + ":{" + serialized + "}";
			return serialized;
		}

		function attachCallbacks(Uploader) {
			var i = 1;
			var filesarr = new Array();
		    Uploader.bind('FileUploaded', function(Up, File, Response) {
	    		filesarr[i] = File.name;
	    		i++;
		        if( (Uploader.total.uploaded + 1) == Uploader.files.length) {
					<?php if (isset($_GET['return'])) { ?>
							window.location = 'admin.php?page=UKMVideresending&videoupload=<?=$_GET['band'];?>&steg=3#b_<?=$_GET['band'];?>';
					<?php }
					else { ?>
		      	          	window.location = 'admin.php?page=UKM_videos&uploaded='+serializeArray(filesarr);
					<?php } ?>
	    	    }
		    });
		}

		$("#uploader").pluploadQueue({
			// General settings
			runtimes : 'flash,silverlight,browserplus'/*'silverlight,gears,flash'*/,
			url : 'http://videoconverter.ukm.no/videoUploadWP.php',
			max_file_size : '3000mb',
			unique_names : false,
			preinit: attachCallbacks,
			multipart_params : <?php echo json_encode(array('wp_id'=>get_option('pl_id'),'b_id'=>$_GET['band'],'pl_type'=>get_option('site_type'),'nicename'=>'blog_'.$blog_id, 'season'=>get_option('season'))); ?>,
			multipart: true,
			file_data_name : 'async-upload',

			// Resize images on clientside if we can
			resize : {width : 3600, height : 3600, quality : 100}, // 100%,A3@220DPI

			// Specify what files to browse for
			filters : [
				{title : "Media files", extensions : "mp4,mov,avi,flv,wmv,m2ts"  },
			],
			
			max_file_count : 1,

			// Flash settings
			flash_swf_url: 'http://videoconverter.ukm.no/plupload.flash.swf',
//			flash_swf_url : '/wp-content/plugins/UKMNorge/plupload/js/plupload.flash.swf',

			// Silverlight settings
			silverlight_xap_url : '/wp-content/plugins/UKMNorge/plupload/js/plupload.silverlight.xap'
		});

		// Client side form validation
		$('form').submit(function(e) {
			var uploader = $('#uploader').pluploadQueue();
			// Validate number of uploaded files
			if (uploader.total.uploaded == 0) {
				// Files in queue upload them first
				if (uploader.files.length > 0) {
					// When all files are uploaded submit form
					uploader.bind('UploadProgress', function() {
						if (uploader.total.uploaded == uploader.files.length)
							$('form').submit();
					});
					uploader.start();
				} else
					alert('Du m&aring; velge minst en fil.');
				e.preventDefault();
			}
		});
	});
	</script>			

	<form>
    <?php
		UKM_loader('api/innslag.api');
		$innslag = new innslag($_GET['band']);
		$navn = $innslag->g('b_name');
		echo '<h2>Last opp video for: "'.$navn.'"</h2>';
#		Nicename: <input type="text" id="nicename" />
	?>

		<div id="uploader">
			<p>Vennligst vent, laster inn opplaster...<br /><br />
			Hvis opplasteren ikke dukker opp i l&oslash;pet av kort tid, betyr det at nettleseren din har ikke st&oslash;tte for Silverlight, Gears eller Flash.<br />
			<a href="http://www.mozilla.org/nb-NO/firefox/new/">P&aring; tide med noe nytt?</a></p>
		</div>
	</form>
<?php
}
?>