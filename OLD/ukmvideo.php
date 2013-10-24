<?php  
/* 
Plugin Name: UKM Videoopplaster
Plugin URI: http://www.ukm-norge.no
Description: Opplasting av video til UKM-TV fra forestillinger (innslagsvideo) + UKM-TV Videoreportasjer
Author: UKM Norge / M Mandal 
Version: 1.0 
Author URI: http://www.ukm-norge.no
*/
/* UKM LOADER */ if(!defined('UKM_HOME')) define('UKM_HOME', '/home/ukmno/public_html/UKM/'); require_once(UKM_HOME.'loader.php');
add_action('wp_ajax_ukmtv_delete', 'ukmtv_delete');




function UKMvideoScripts() {
	wp_enqueue_script('UKMvideo_jwplayer', WP_PLUGIN_URL .'/UKMvideo/jwplayer.js');
}


## CREATE A MENU
function UKMvideos_menu() {
	global $UKMN;
	$page1 = add_menu_page('UKM-TV Administrer innhold', 'Video', 'publish_posts', 'UKM_videorep','UKM_videorep', 'http://ico.ukm.no/video-16.png', 12);
	$page2 = add_submenu_page('UKM_videorep', ' Gammel videomodul', 'Gammel videomodul', 'publish_posts', 'UKM_videos','UKM_videos');

	add_action( 'admin_print_styles-' . $page1, 'UKMvideoScripts' );
	add_action('admin_print_styles-'.$page1, 'UKMVideo_jQupload');
	add_action( 'admin_print_styles-' . $page2, 'UKMvideoScripts' );

}

function UKM_videorep(){
	if(!isset($_GET['list']))
		$_GET['list'] = 1;
	require_once('gui_tabs.inc.php');
	require_once('functions.inc.php');
	require_once('save_rep.inc.php');
	require_once('gui2013.inc.php');
	require_once('gui_footer.inc.php');
}

function UKM_videos(){
	require_once('gui.inc.php');
	UKMvideoGUI();
}

add_action('wp_ajax_UKMVideoreportasje_data', 'UKMVideoreportasje_data');

function UKMVideoreportasje_data() {
	$sql = new SQL("SELECT `video_image`, `video_file`
					FROM `ukm_standalone_video`
					WHERE `cron_id` = '#cronid'",
					array('cronid' => $_POST['cronid']));
	$row = $sql->run('array');
	if(empty($row['video_file']))
		die( json_encode(array('cron_id' => $_POST['cronid'],
							   'video' => false,
							   'image' => false)) );

	die( json_encode(array('cron_id' => $_POST['cronid'],
						   'video' => $row['video_file'],
						   'image' => $row['video_image'])) );
}

add_action('wp_ajax_UKMVideo_ajax', 'UKMVideo_ajax');
add_action('wp_ajax_loadBandVideos', 'loadBandVideos');
add_action('wp_ajax_load_rep_details', 'load_rep_details');
if(is_admin()) {
	add_action('admin_menu', 'UKMvideos_menu', 11000);
	add_action('admin_init', 'UKMVideo_scriptsandstyles',1000);
	add_action('admin_init', 'UKMTV_player');
}

function loadBandVideos() {
	require_once('ajax.bandrelated.inc.php');
}
function load_rep_details() {
	require_once('ajax.standalone.inc.php');
}

function ukmtv_delete() {
	require_once('ajax.ukmtv_delete.inc.php');
}

function UKMTV_player() {
	wp_enqueue_script('UKMTV_iframe','http://embed.ukm.no/iframe.js',10000);	
}

function UKMVideo_scriptsandstyles() {
	wp_enqueue_style('UKMVideo_css', WP_PLUGIN_URL .'/UKMvideo/stil.css');	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jqueryGoogleUI', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
	wp_enqueue_script('zoombox_js','/wp-content/plugins/UKMvisitorpages/zoombox/zoombox.js');
	wp_enqueue_script('UKMvideo_js', WP_PLUGIN_URL .'/UKMvideo/script.js');
}

function UKMVideo_jQupload() {
	wp_enqueue_script('jQu_iframe_transport', WP_PLUGIN_URL . '/UKMvideo/jQupload/js/jquery.iframe-transport.js');
	wp_enqueue_script('jQu_fileupload', WP_PLUGIN_URL . '/UKMvideo/jQupload/js/jquery.fileupload.js');
	wp_enqueue_script('jQu_fileupload-fp', WP_PLUGIN_URL . '/UKMvideo/jQupload/js/jquery.fileupload-fp.js');
	wp_enqueue_script('jQu_fileupload-ui', WP_PLUGIN_URL . '/UKMvideo/jQupload/js/jquery.fileupload-ui.js');
	wp_enqueue_script('jQu_main', WP_PLUGIN_URL . '/UKMvideo/jQupload/js/main.js');
	wp_enqueue_script('jQu_cors', WP_PLUGIN_URL . '/UKMvideo/jQupload/js/cors/jquery.xdr-transport.js');
	wp_enqueue_style('jQu_guistyle', WP_PLUGIN_URL .'/UKMvideo/style.gui_rep.css');

}


function UKMVideo_ajax() {
	require_once('ajax.php');
	die();
}
?>