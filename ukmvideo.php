<?php  
/* 
Plugin Name: UKM Videoopplaster
Plugin URI: http://www.ukm-norge.no
Description: Opplasting av video til UKM-TV fra forestillinger (innslagsvideo) + UKM-TV Videoreportasjer
Author: UKM Norge / M Mandal 
Version: 3.0 
Author URI: http://www.ukm-norge.no
*/

if(is_admin()) {
	require_once('UKM/inc/handlebars.inc.php');
	add_action('admin_menu', 'UKMvideo_menu');
	
	add_action('wp_ajax_UKMvideo_load', 'UKMvideo_ajax_load');

}

function UKMvideo_menu() {
	$page = add_menu_page('UKM-TV Administrer innhold', 'Video', 'publish_posts', 'UKMvideo','UKMvideo', 'http://ico.ukm.no/video-16.png', 12);
	add_action( 'admin_print_styles-' . $page, 'UKMvideo_scripts_and_styles' );
}

function UKMvideo_ajax_load() {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	if(!isset( $_POST['load'] ) ) 
		die(0);
	
	require_once('ajax/load_'. $_POST['load'] .'.ajax.php');
	
	die( json_encode( $AJAX_DATA ) );
}


function UKMvideo() {
	if(!isset($_GET['action']))
		$_GET['action'] = 'innslag';
		
	require_once('UKM/related.class.php');
	require_once('UKM/innslag.class.php');
	require_once('UKM/monstring.class.php');
	$monstring = new monstring(get_option('pl_id'));

	switch( $_GET['action'] ) {
		case 'lastopp_innslag':
			require_once('controller_lastopp_innslag.inc.php');
			break;
		case 'innslag':
			if($_SERVER['REQUEST_METHOD'] == 'POST')
				require_once('save/save_video_innslag_uploaded.inc.php');
			require_once('controller_innslag.inc.php');
			break;
		case 'reportasje':
			require_once('controller_reportasje.inc.php');
			break;
	}
	$INFOS['active'] = $_GET['action'];
	echo TWIG($_GET['action'].'.twig.html', $INFOS, dirname(__FILE__));
	
	echo HANDLEBARS( dirname(__FILE__) );
}

function UKMvideo_scripts_and_styles(){
	wp_enqueue_script('handlebars_js');
	wp_enqueue_script('bootstrap_js');
	wp_enqueue_style('bootstrap_css');

	wp_enqueue_style('UKMresources_tabs');

	wp_enqueue_style( 'UKMvideo_css', plugin_dir_url( __FILE__ ) . 'UKMvideo.css');
	wp_enqueue_script( 'UKMvideo_js', plugin_dir_url( __FILE__ ) . 'UKMvideo.js');
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jqueryGoogleUI', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');

	wp_enqueue_style( 'blueimp-gallery-css', plugin_dir_url( __FILE__ ) . 'jqueryuploader/css/blueimp-gallery.min.css');

	// CSS to style the file input field as button and adjust the Bootstrap progress bars
	wp_enqueue_style( 'jquery-fileupload-css', plugin_dir_url( __FILE__ ) . 'jqueryuploader/css/jquery.fileupload.css');
	wp_enqueue_style( 'jquery-fileupload-ui-css', plugin_dir_url( __FILE__ ) . 'jqueryuploader/css/jquery.fileupload-ui.css');
	
	// The jQuery UI widget factory, can be omitted if jQuery UI is already included
	wp_enqueue_script('jquery_ui_widget', plugin_dir_url(__FILE__) . 'jqueryuploader/js/vendor/jquery.ui.widget.js');
	// The Load Image plugin is included for the preview images and image resizing functionality
	wp_enqueue_script('load-image', plugin_dir_url(__FILE__) . 'jqueryuploader/js/vendor/load-image.min.js');
	// The Canvas to Blob plugin is included for image resizing functionality
	wp_enqueue_script('canvas-to-blob', plugin_dir_url(__FILE__) . 'jqueryuploader/js/vendor/canvas-to-blob.min.js');
	// The Iframe Transport is required for browsers without support for XHR file uploads
	wp_enqueue_script('iframe-transport', plugin_dir_url(__FILE__) . 'jqueryuploader/js/jquery.iframe-transport.js');	
	// The basic File Upload plugin
	wp_enqueue_script('fileupload', plugin_dir_url(__FILE__) . 'jqueryuploader/js/jquery.fileupload.js');	
	// The File Upload user interface plugin
	wp_enqueue_script('fileupload-ui', plugin_dir_url(__FILE__) . 'jqueryuploader/js/jquery.fileupload-ui.js');
	// The File Upload processing plugin
	wp_enqueue_script('fileupload-process', plugin_dir_url(__FILE__) . 'jqueryuploader/js/jquery.fileupload-process.js');	
	// The File Upload image preview & resize plugin 
	wp_enqueue_script('fileupload-image', plugin_dir_url(__FILE__) . 'jqueryuploader/js/jquery.fileupload-image.js');	
	// The File Upload validation plugin
	wp_enqueue_script('fileupload-validate', plugin_dir_url(__FILE__) . 'jqueryuploader/js/jquery.fileupload-validate.js');	
}