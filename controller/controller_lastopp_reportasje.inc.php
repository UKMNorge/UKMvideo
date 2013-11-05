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
					WHERE `pl_id` = '#plid'
					GROUP BY `c_name`
					ORDER BY `c_name` ASC",
				array('plid' => get_option('pl_id')));
}
$res = $qry->run();
while( $r = mysql_fetch_assoc($res) ) {
	if(!in_array( $r['c_name'] , $categories))
		$categories[] = $r['c_name'];
}


$INFOS['set_basename'] = $basename;
$INFOS['sets']	= $categories;

if(isset($_GET['id'])) {
	$INFOS['video_id'] = $_GET['id'];
	
	$sql = new SQL("SELECT * FROM `ukm_standalone_video`
					WHERE `v_id` = '#vid'",
					array('vid' => $_GET['id']));
	$data = $sql->run('array');
	
	$INFOS['video_title'] = $data['video_name'];
	$INFOS['video_set'] = $data['video_category'];
	$INFOS['video_description'] = $data['video_description'];
} else {	
	$INFOS['video_id'] = 'new';
	$INFOS['video_title'] = '';
	$INFOS['video_set'] = '';
	$INFOS['video_description'] = '';
}