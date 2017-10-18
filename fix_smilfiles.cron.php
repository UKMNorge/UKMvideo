<?php
	
require_once('UKM/sql.class.php');
require_once('UKM/curl.class.php');

$sql = new SQL("SELECT * 
				FROM `ukm_tv_files` 
				WHERE `file_exists_smil` = 'unknown'
				ORDER BY `tv_id` DESC
				LIMIT 100
				"
			);
$res = $sql->run();

while( $row = mysql_fetch_assoc( $res ) ) {
	echo '<h1>'. $row['tv_file'] .'</h1>';
	$request = urlencode( $row['tv_file'] );
	
	$curl = new UKMCurl();
	$curl->timeout( 5 );
	$create_smil = $curl->request( 'https://video.ukm.no/create_smil.php?file='. $request );
	

	if( !$create_smil->success ) {
		echo 'ERROR';
	} else {
		$ins = new SQLins('ukm_tv_files', array('tv_id' => $row['tv_id'] ) );
		$ins->add('file_exists_smil', ($create_smil->exists ? 'true' : 'false' ));
		echo $ins->debug();
		$ins->run();
	}
}