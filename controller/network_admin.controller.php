<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	update_site_option('ukm_livestream_username', $_POST['livestream_username'] );
	update_site_option('ukm_livestream_password', $_POST['livestream_password'] );
	$INFOS['error']['type'] = 'success';
	$INFOS['error']['title'] = 'Lagret!';
	$INFOS['error']['message'] = 'Livestream-detaljer er lagret';
}


$INFOS['livestream_password'] = get_site_option('ukm_livestream_password');
$INFOS['livestream_username'] = get_site_option('ukm_livestream_username');
