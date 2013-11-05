<?php
require_once('UKM/sql.class.php');
require_once('UKM/tv.class.php');
require_once('UKM/tv_files.class.php');
require_once('UKM/monstring.class.php');

///////////////////////////////////////////////////////////////

$sql = new SQL("SELECT `cron_id` FROM `ukm_standalone_video` 
				WHERE `pl_id` = '#plid'
				ORDER BY `video_name` ASC",
				array('plid' => get_option('pl_id')));
$res = $sql->run();
while( $r = mysql_fetch_assoc( $res )) {

	$film = new SQL("SELECT *
					FROM `ukm_standalone_video` 
					WHERE `cron_id` = '#cron_id'",
				array('cron_id' => $r['cron_id']));
	$film = $film->run('array');
	
	$TV = new tv(false, $film['cron_id']);
	var_dump($TV);
	
	$film['converting'] = !$TV->id;
	
	$films[] = $film;
}

$INFOS['reportasjer'] = $films;