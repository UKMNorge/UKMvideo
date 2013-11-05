<?php
require_once('UKM/sql.class.php');
require_once('UKM/tv.class.php');
require_once('UKM/tv_files.class.php');
require_once('UKM/monstring.class.php');

function UKMv_get_film($v_id) {
	$sql = new SQL("SELECT *, 
						   `video_name` AS `title`,
						   `video_category` AS `set`
					FROM `ukm_standalone_video` 
					WHERE `cron_id` = '#vid'",
				array('vid' => $v_id));
	return $sql->run('array');
}



///////////////////////////////////////////////////////////////

$sql = new SQL("SELECT `cron_id` FROM `ukm_standalone_video` 
				WHERE `pl_id` = '#plid'
				ORDER BY `video_name` ASC",
				array('plid' => get_option('pl_id')));
$res = $sql->run();
while( $r = mysql_fetch_assoc( $res )) {
	$films[] = UKMv_get_film( $r['cron_id'] );
}

$INFOS['reportasjer'] = $films;