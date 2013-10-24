<?php
// SLETT 
if(isset($_POST['del_rel_id'])) {
	if((int)$_POST['del_rel_id'] == 0)
		die('Error, manglende ID');
	
	$slett = new SQLdel('ukmno_wp_related', array('rel_id'=>(int)$_POST['del_rel_id']));
	mail('support@ukm.no', 'Slettet relasjoner fra UKMVideo-ajax.php', $slett->debug());
	$slett = $slett->run();
} elseif(isset($_POST['data'])){
	$ch = curl_init();  
	// set URL and other appropriate options  
	curl_setopt($ch, CURLOPT_URL, 'http://videoconverter.ukm.no/video/ffmpegadmin_save.php?'.$_POST['data']);
	curl_setopt($ch, CURLOPT_HEADER, false);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	echo curl_exec($ch);
	curl_close($ch);
	die();
} else {
	$ch = curl_init();  
	// set URL and other appropriate options  
	curl_setopt($ch, CURLOPT_URL, 'http://videoconverter.ukm.no/video/ffmpegadmin.php?file='.$_POST['file'].'&ext='.$_POST['ext']);  
	curl_setopt($ch, CURLOPT_HEADER, false);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	echo curl_exec($ch);
	curl_close($ch);
	die();
}
?>