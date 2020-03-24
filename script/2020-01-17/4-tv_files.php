<?php
header('Content-Type: text/html; charset=utf-8');
echo '<meta charset="utf-8" />';

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;
use UKMNorge\Database\SQL\Update;

require_once('UKM/Autoloader.php');
require_once('utf8.php');
error_reporting(E_ALL);
ini_set('display_errors', true);

$query = new Query(
    "SELECT *
    FROM `ukm_tv_files`
    WHERE `b_id` = 0
    "
);
$res = $query->run();

echo Query::numRows($res);
while ($row = Query::fetch($res)) {
    echo '<h1>' . $row['tv_file'] . '</h1>';
    if ($row['tv_file'] == 'ukmno/videos/') {
        echo 'continue';
        continue;
    }
    if( strpos($row['tv_tags'], 'pl') === false ) {
        echo 'hopp over den ene uten pl-tag';
        continue;
    }
    
    $finnes = new Query(
        "SELECT `id`
        FROM `ukm_uploaded_video`
        WHERE `file` = '#file'",
        [
            'file' => $row['tv_file']
        ]
    );
    $id = $finnes->getField();
    
    if( $id ) {
        $modify = new Update(
            'ukm_uploaded_video',
            [
                'file' => $row['tv_file']
            ]
        );
    } else {
        $modify = new Insert('ukm_uploaded_video');
        $modify->add('cron_id', NULL);

        $data = explode('/', $row['tv_file']);

        $season = $data[2];
        
        $matches = [];
        preg_match("/\|pl_([0-9]+)\|/", $row['tv_tags'], $matches);
        $modify->add('file', $row['tv_file']);
        $modify->add('arrangement_id', $matches[1]);
        $modify->add('season', $season);
    }

    $cron_match = [];
    preg_match("/cronid_([0-9]+)/", $row['tv_file'], $cron_match);
    if( sizeof( $cron_match ) == 0 ) {
        preg_match("/cron_([0-9]+)/", $row['tv_file'], $cron_match);
    }
    if( sizeof( $cron_match ) > 0 ) {
        $modify->add('cron_id', $cron_match[1]);
    }

    $modify->add('tv_id', $row['tv_id']);
    $modify->add('title', makesurethisisutf8($row['tv_title']));
    $modify->add('description', makesurethisisutf8($row['tv_description']) );
    $modify->add('innslag_id', $row['b_id']);
    $modify->add('converted', 'true');
    echo $modify->debug();
    try {
        $db_res = $modify->run();
    } catch (Exception $e) {
        // Finnes allerede-error
        if ($e->getCode() == 901001) {
            continue;
        }
        echo '<h2>ERROR:</h2>';
        var_dump($modify->getError());
        var_dump($e);
        die();
    }
}

echo 'done';