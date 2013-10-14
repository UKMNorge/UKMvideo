<?php
/// FUNKSJONER SOM BRUKES AV VIDEOREPORTASJER
global $blog_id;
#require_once(UKM_HOME.'/subdomains/tv/inc/config.inc.php');

function UKMv_get_film($v_id) {
	$sql = new SQL("SELECT *, 
						   `video_name` AS `title`,
						   `video_category` AS `set`
					FROM `ukm_standalone_video` 
					WHERE `cron_id` = '#vid'",
				array('vid' => $v_id));
	return $sql->run('array');
}

function UKMv_get_filmer() {
	$sql = new SQL("SELECT `cron_id` FROM `ukm_standalone_video` 
					WHERE `pl_id` = '#plid'
					ORDER BY `video_name` ASC",
					array('plid' => UKMv_plid()));
	$res = $sql->run();
	while( $r = mysql_fetch_assoc( $res )) {
		$films[] = UKMv_get_film( $r['cron_id'] );
	}
	return $films;
}

function UKMv_plid() {
	# Brukes av UKMV_get_filmer + gui_rep.inc.php
	global $blog_id;
	return $blog_id == 1 ? 0 : $blog_id;
}
/*
function UKMv_get_kategorier() {
	$qry = new SQL("SELECT * 
					FROM `ukm_tv_category_folders`
					ORDER BY `f_name` ASC");
	$res = $qry->run();
	while( $r = mysql_fetch_assoc($res) ) {
		$cats[$r['f_id']] = utf8_encode($r['f_name']);
	}
	return $cats;
}
*/
function UKMv_get_samlinger() {
	// UKM NORGE - SELECT ALL CATEGORIES
	if(UKMv_plid() == 0) {
		$qry = new SQL("SELECT * 
						FROM `ukm_tv_categories`
						ORDER BY `c_name` ASC");
	} else {
		$m = new monstring(get_option('pl_id'));
		$qry = new SQL("SELECT * 
						FROM `ukm_tv_categories`
						WHERE `c_name` LIKE '#name%'
						ORDER BY `c_name` ASC",
						array('name' => $m->g('pl_name').' '. $m->g('season')));
	}
	$res = $qry->run();
	while( $r = mysql_fetch_assoc($res) ) {
		$cats[$r['c_id']] = $r['c_name'];
	}
	return $cats;
}
