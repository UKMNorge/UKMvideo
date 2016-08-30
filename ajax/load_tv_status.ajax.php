<?php
require_once('UKM/tv.class.php');

$TV = new tv(false, $_POST['cron_id']);
$convert_failed = false;
$reload = false;

if( false == $TV->id ) {
	require_once('UKM/curl.class.php');
	$curl_converter = new UKMCURL();
	$curl_converter->timeout(2);
	$status_converter = $curl_converter->request('http://videoconverter.'. UKM_HOSTNAME .'/api/convertStatus.php?cron_id='. $_POST['cron_id']);
	$status_convert = $status_converter->data;

	// CHECK CONVERTER STATUS
	if( in_array( $status_convert->status_progress, array('does_not_exist','crashed') ) ) {
		$convert_failed = true;
	}
} else {
	$reload = true;
	$convert_failed = false;
}

$AJAX_DATA = array('success' => true,
				   'reload' => $reload,
				   'cron_id' => $_POST['cron_id'],
				   'convert_failed' => $convert_failed);