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

///////////////////////////////////////////////////////////////
// CATEGORIES
global $blog_id;
if($blog_id == 1) {
	$categories = array();
	$qry = new SQL("SELECT * 
					FROM `ukm_tv_categories`
					ORDER BY `c_name` ASC");
} else {
	$pl = new monstring(get_option('pl_id'));
	$basename = $pl->g('pl_name') .' '. $pl->g('season');
	
	$categories = array( $basename .' videoreportasjer' );
	$qry = new SQL("SELECT * 
					FROM `ukm_tv_categories`
					WHERE `c_name` LIKE '#name%'
					ORDER BY `c_name` ASC",
				array('name' => $basename));
}
$res = $qry->run();
while( $r = mysql_fetch_assoc($res) ) {
	if(!in_array( $r['c_name'] , $categories))
		$categories[] = $r['c_name'];
}


$INFOS['set_basename'] = $basename;
$INFOS['sets']	= $categories;

$INFOS['reportasjer'] = $films;