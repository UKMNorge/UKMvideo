<?php
require_once('UKM/tv.class.php');

$TV = new tv(false, $_POST['cron_id']);

$AJAX_DATA = array('success' => true,
				   'reload' => $TV->id !== false ? 'true' : 'false',
				   'cron_id' => $_POST['cron_id']);