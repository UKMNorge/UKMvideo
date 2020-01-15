<?php  
/* 
Plugin Name: UKM Videoopplaster
Plugin URI: http://www.ukm-norge.no
Description: Opplasting av video til UKM-TV fra forestillinger (innslagsvideo) + UKM-TV Videoreportasjer
Author: UKM Norge / M Mandal 
Version: 4.0 
Author URI: http://www.ukm-norge.no
*/

use UKMNorge\Wordpress\Modul;

require_once('UKM/Autoloader.php');

class UKMvideo extends Modul
{
    public static $action = 'home';
    public static $path_plugin = null;

    /**
     * Register hooks
     */
    public static function hook()
    {
        // Kun mønstringssider skal ha bilder
        if (is_numeric(get_option('pl_id'))) {
            add_action(
                'admin_menu',
                ['UKMvideo', 'meny']
            );
        }
    }

    /**
     * Rendre meny
     */
    public static function meny()
    {
        $page = add_submenu_page(
            'edit.php',
            'Filmer',
            'Filmer',
            'superadmin',#'edit_posts',
            'UKMvideo',
            ['UKMvideo','renderAdmin']
        );
        add_action(
            'admin_print_styles-' . $page,
            ['UKMvideo','scripts_and_styles']
        );
    }

    public static function renderAdmin() {
        $livestream = new stdClass();
        $livestream->aktiv = in_array( get_option('pl_eier_type'), ['land','fylke']) ? true : get_option('livestream_aktiv');
        $livestream->passord = get_site_option('ukm_livestream_password');
        $livestream->brukernavn = get_site_option('ukm_livestream_username');
        $livestream->url = get_option('ukm_live_link');
        $livestream->embed = get_option('ukm_live_embedcode');
        UKMvideo::addViewData('livestream', $livestream);

        parent::renderAdmin();
    }

    /**
     * Scripts og styles
     */
    public static function scripts_and_styles() {
        require_once('UKM/inc/twig-js.inc.php');
        wp_enqueue_script('TwigJS');

        wp_enqueue_script('WPbootstrap3_js');
        wp_enqueue_style('WPbootstrap3_css');
        wp_enqueue_style( 'UKMvideo_css', static::getPluginUrl() . 'UKMvideo.css');
        wp_enqueue_script( 'UKMvideo_js', static::getPluginUrl() . 'js/video.js');
        if( isset($_GET['action'] ) && $_GET['action'] == 'flerkamera' ) {
            wp_enqueue_script( 'UKMvideo_js_upload_innslag', static::getPluginUrl() . 'js/upload_innslag.js');
        } else {
            wp_enqueue_script( 'UKMvideo_js_upload', static::getPluginUrl() . 'js/upload.js');
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('jqueryGoogleUI', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
    
        wp_enqueue_style( 'blueimp-gallery-css', static::getPluginUrl() . 'jqueryuploader/css/blueimp-gallery.min.css');
    
        // CSS to style the file input field as button and adjust the Bootstrap progress bars
        wp_enqueue_style( 'jquery-fileupload-css', static::getPluginUrl() . 'jqueryuploader/css/jquery.fileupload.css');
        wp_enqueue_style( 'jquery-fileupload-ui-css', static::getPluginUrl() . 'jqueryuploader/css/jquery.fileupload-ui.css');
        
        // The jQuery UI widget factory, can be omitted if jQuery UI is already included
        wp_enqueue_script('jquery_ui_widget', static::getPluginUrl() . 'jqueryuploader/js/vendor/jquery.ui.widget.js');
        // The Load Image plugin is included for the preview images and image resizing functionality
        wp_enqueue_script('load-image', static::getPluginUrl() . 'jqueryuploader/js/vendor/load-image.min.js');
        // The Canvas to Blob plugin is included for image resizing functionality
        wp_enqueue_script('canvas-to-blob', static::getPluginUrl() . 'jqueryuploader/js/vendor/canvas-to-blob.min.js');
        // The Iframe Transport is required for browsers without support for XHR file uploads
        wp_enqueue_script('iframe-transport', static::getPluginUrl() . 'jqueryuploader/js/jquery.iframe-transport.js');	
        // The basic File Upload plugin
        wp_enqueue_script('fileupload', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload.js');	
        // The File Upload user interface plugin
        wp_enqueue_script('fileupload-ui', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-ui.js');
        // The File Upload processing plugin
        wp_enqueue_script('fileupload-process', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-process.js');	
        // The File Upload image preview & resize plugin 
        wp_enqueue_script('fileupload-image', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-image.js');	
        // The File Upload validation plugin
        wp_enqueue_script('fileupload-validate', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-validate.js');	
    }

}
UKMvideo::init(__DIR__);
UKMvideo::hook();