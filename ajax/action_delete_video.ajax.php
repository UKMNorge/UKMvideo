<?php
require_once('UKM/tv.class.php');
$TV = new tv( $_POST['tv_id'] );
//$TV->delete();

$AJAX_DATA = array( 'success' => true,	
					'id' => $_POST['tv_id'] );
