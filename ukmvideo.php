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
use UKMNorge\Meta\Write as MetaWrite;
use UKMNorge\Arrangement\Arrangement;



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
        add_action('wp_ajax_UKMvideo_ajax', ['UKMvideo', 'ajax']);

    }

    /**
     * Håndterer alle ajax-kall
     *
     * @return void
     */
    public static function ajax()
    {   
        $reques_method = $_SERVER['REQUEST_METHOD'];
        $subAction = $_REQUEST['subaction'];

        if(!$subAction) {
            $subAction = 'getCloudflareUrl';
        }

        try {
            require_once('ajax/' . $subAction . '.ajax.php');

        // Noe gikk galt
        } catch (Exception $e) {
            static::addResponseData('success', false);
            static::addResponseData('message', $e->getMessage());
            static::addResponseData('code', $e->getCode());
        }

        $data = json_encode(static::getResponseData());
        echo $data;
        die();
    }

    /**
     * Rendre meny
     */
    public static function meny()
    {
        $arrangement = new Arrangement( get_option( 'pl_id ') );

        if(!$arrangement->erKunstgalleri()) {
            $film = add_submenu_page(
                'edit.php',
                'Filmer',
                'Filmer',
                'edit_posts',
                'UKMvideo',
                ['UKMvideo','renderAdmin']
            );
        // }

        // // static::_checkForLivestreamActivation();

        // // if( static::getArrangement()->getMetaValue('har_livestream') || in_array(static::getArrangement()->getEierType(), ['fylke','land']) )
        // {
            // $live = add_submenu_page(
            //     'edit.php',
            //     'Direktesending',
            //     'Direktesending',
            //     'edit_posts',
            //     'UKMlive',
            //     ['UKMvideo','renderAdminDirekte']
            // );
        }

        add_action(
            'admin_print_styles-' . $film,
            ['UKMvideo','scripts_and_styles']
        );
        add_action(
            'admin_print_styles-' . $live,
            ['UKMvideo','scripts_and_styles']
        );
    }

    public static function renderAdminDirekte() {
        static::setAction('livestream');
        return static::renderAdmin();
    }

    public static function _checkForLivestreamActivation() {
        // Aktiver direktesending
        if( ! in_array($_GET['page'], ['UKMvideo','UKMlive'])) {
            return false;
        }

        if( isset($_GET['subaction']) && in_array($_GET['subaction'], ['livestream-aktiver', 'livestream-deaktiver']) ) {
            if( is_super_admin() ) {
                if( $_GET['subaction'] == 'livestream-aktiver') {
                    $meta = static::getArrangement()->getMeta('har_livestream')->set(true);
                    MetaWrite::set($meta);
                    UKMvideo::getFlash()->success('Direktesending er aktivert, og menyvalget er lagt til.');
                } else {
                    $meta = static::getArrangement()->getMeta('har_livestream')->set(false);
                    MetaWrite::delete($meta);
                    UKMvideo::getFlash()->success('Direktesending er deaktivert, og menyvalget er skjult.');
                }
            } else {
                UKMvideo::getFlash()->error('Kun superadmins kan aktivere direktesending');
            }
        }
    }
    public static function _checkForStreamInput() {
        // Aktiver direktesending
        if( ! in_array($_GET['page'], ['UKMvideo','UKMlive'])) {
            return false;
        }

        if( isset($_GET['cmd']) && in_array($_GET['cmd'], ['act', 'deact']) ) {
            if( $_GET['cmd'] == 'act') {
                $meta = static::getArrangement()->getMeta('har_livestream')->set(true);
                MetaWrite::set($meta);
                UKMvideo::getFlash()->success('Direktesending er aktivert, og menyvalget er lagt til.');
            } else {
                $meta = static::getArrangement()->getMeta('har_livestream')->set(false);
                MetaWrite::delete($meta);
                UKMvideo::getFlash()->success('Direktesending er deaktivert, og menyvalget er skjult.');
            }
        }
    }

    /**
     * Scripts og styles
     */
    public static function scripts_and_styles() {

        wp_enqueue_script('mainUKMvideoJS', plugin_dir_url(__FILE__) . '/dist/build.js','','',true);

        wp_enqueue_script('WPbootstrap3_js');
        
        wp_enqueue_style('WPbootstrap3_css');
        // require_once('UKM/inc/twig-js.inc.php');
        wp_enqueue_script('TwigJS');

    	wp_enqueue_style('UKMvideoStyle', plugin_dir_url(__FILE__) . '/style/styles.css');
    	wp_enqueue_style('UKMvideoStyleHeader', plugin_dir_url(__FILE__) . '/style/header.css');
    	wp_enqueue_style('UKMvideoStyleHendelser', plugin_dir_url(__FILE__) . '/style/videoHendelser.css');

    	
        wp_enqueue_style('UKMvideoArrSysStyle', '//assets.' . UKM_HOSTNAME . '//css/arr-sys.css');
        
        // wp_enqueue_style( 'UKMvideo_css', static::getPluginUrl() . 'UKMvideo.css');
        // wp_enqueue_script( 'UKMvideo_js', static::getPluginUrl() . 'js/video.js');
        // if( isset($_GET['action'] ) && $_GET['action'] == 'flerkamera' ) {
        //     wp_enqueue_script( 'UKMvideo_js_upload_innslag', static::getPluginUrl() . 'js/upload_innslag.js');
        // } else {
        //     wp_enqueue_script( 'UKMvideo_js_upload', static::getPluginUrl() . 'js/upload.js');
        // }
        
        // wp_enqueue_script('jquery');
        // wp_enqueue_script('jqueryGoogleUI', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
    
        // wp_enqueue_style( 'blueimp-gallery-css', static::getPluginUrl() . 'jqueryuploader/css/blueimp-gallery.min.css');
    
        // // CSS to style the file input field as button and adjust the Bootstrap progress bars
        // wp_enqueue_style( 'jquery-fileupload-css', static::getPluginUrl() . 'jqueryuploader/css/jquery.fileupload.css');
        // wp_enqueue_style( 'jquery-fileupload-ui-css', static::getPluginUrl() . 'jqueryuploader/css/jquery.fileupload-ui.css');
        
        // // The jQuery UI widget factory, can be omitted if jQuery UI is already included
        // wp_enqueue_script('jquery_ui_widget', static::getPluginUrl() . 'jqueryuploader/js/vendor/jquery.ui.widget.js');
        // // The Load Image plugin is included for the preview images and image resizing functionality
        // wp_enqueue_script('load-image', static::getPluginUrl() . 'jqueryuploader/js/vendor/load-image.min.js');
        // // The Canvas to Blob plugin is included for image resizing functionality
        // wp_enqueue_script('canvas-to-blob', static::getPluginUrl() . 'jqueryuploader/js/vendor/canvas-to-blob.min.js');
        // // The Iframe Transport is required for browsers without support for XHR file uploads
        // wp_enqueue_script('iframe-transport', static::getPluginUrl() . 'jqueryuploader/js/jquery.iframe-transport.js');	
        // // The basic File Upload plugin
        // wp_enqueue_script('fileupload', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload.js');	
        // // The File Upload user interface plugin
        // wp_enqueue_script('fileupload-ui', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-ui.js');
        // // The File Upload processing plugin
        // wp_enqueue_script('fileupload-process', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-process.js');	
        // // The File Upload image preview & resize plugin 
        // wp_enqueue_script('fileupload-image', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-image.js');	
        // // The File Upload validation plugin
        // wp_enqueue_script('fileupload-validate', static::getPluginUrl() . 'jqueryuploader/js/jquery.fileupload-validate.js');	
    }

}
UKMvideo::init(__DIR__);
UKMvideo::hook();