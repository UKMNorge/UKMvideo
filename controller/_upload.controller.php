<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Query;

global $blog_id;
UKMvideo::addViewData('blog_id', $blog_id);

$arrangement = new Arrangement(intval(get_option('pl_id')));
UKMvideo::addViewData('arrangement', $arrangement);

global $blog_id;
if ($blog_id == 1) {
    $categories = array();
    $qry = new Query(
        "SELECT * 
        FROM `ukm_tv_categories`
        ORDER BY `c_name` ASC"
    );
} else {
    $basename = $arrangement->getNavn() . ' ' . $arrangement->getSesong();

    $categories = [
        $basename . ' videoreportasjer'
    ];
    $qry = new Query(
        "SELECT * 
        FROM `ukm_standalone_video`
        WHERE `pl_id` = '#plid'
        GROUP BY `video_category`
        ORDER BY `video_category` ASC",
        [
            'plid' => get_option('pl_id')
        ]
    );
}
$res = $qry->run();
while ($r = Query::fetch($res)) {
    if (!in_array($r['video_category'], $categories)) {
        $categories[] = utf8_encode($r['video_category']);
    }
}

UKMvideo::addViewData('set_basename', $basename);
UKMvideo::addViewData('sets', $categories);

if(isset($_GET['id'])) {	
	$sql = new SQL(
        "SELECT * FROM `ukm_standalone_video`
        WHERE `v_id` = '#vid'",
        [
            'vid' => $_GET['id']
        ]
    );
    $data = $sql->getArray();
    
    $video = new stdClass();
    $video->id = $_GET['id'];
    $video->name = $data['video_name'];
    $video->set =  $data['video_category'];
    $video->description = $data['video_description'];
}