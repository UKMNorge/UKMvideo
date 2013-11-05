<?php
require_once('UKM/sql.class.php');
require_once('UKM/tv.class.php');
require_once('UKM/tv_files.class.php');
require_once('UKM/monstring.class.php');


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


$INFOS['video_id'] = 'new';
$INFOS['video_title'] = '';
$INFOS['video_set'] = '';
$INFOS['video_description'] = '';