<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;

require_once('UKM/Autoloader.php');

error_reporting(E_ALL);
ini_set('display_errors', true);

$query = new Query(
    "SELECT *
    FROM `ukm_related_video`
    WHERE `file` IS NOT NULL"
);
$res = $query->run();

echo Query::numRows($res);
while ($row = Query::fetch($res)) {
    echo '<h1>' . $row['file'] . '</h1>';
    $data = explode('/', $row['file']);

    $sesong = $data[2];

    $_sted = explode('_', $data[3]);
    if ($data[4] == 'innslag') {
        $_fil = explode('_', $data[5]);
    } else {
        $_fil = explode('_', $data[4]);
    }

    $arrangement_id = $_sted[0];
    if (isset($_sted[1])) {
        $innslag = $_fil[1];
    } else {
        $innslag = $data[3];
    }

    $matches = [];
    preg_match("/_cronid_([0-9]+)./", $row['file'], $matches);
    // Noen gamle filer mangler cron_id. De kan vi drite i
    if (!isset($matches[1])) {
        preg_match("/_cron_([0-9]+)./", $row['file'], $matches);
        if (!isset($matches[1])) {
            continue;
        }
    }
    $cron_id = $matches[1];
    if (empty($cron_id) || empty($row['file']) || empty($arrangement_id) || empty($innslag) || empty($sesong)) {
        $donotskip = false;
        if( empty( $sesong ) ) {
            $sesong = 0;
            $donotskip = true;
        }
        if( empty( $arrangement_id ) ) {
            $arrangement_id = 0;
            $donotskip = true;
        }
        if( !$donotskip ) {
            echo ('Noe mangler!');
            die();
        }
    } else {
        echo 'letsgo:<br />';
    }
    $insert = new Insert(
        'ukm_uploaded_video'
    );
    $insert->add('cron_id', $cron_id);
    $insert->add('file', $row['file']);
    $insert->add('arrangement_id', $arrangement_id);
    $insert->add('innslag_id', $innslag);
    $insert->add('title_id', NULL);
    $insert->add('season', $sesong);
    $insert->add('converted', 'true');
    echo $insert->debug();
    try {
        $db_res = $insert->run();
    } catch (Exception $e) {
        // Finnes allerede-error
        if ($e->getCode() == 901001) {
            continue;
        }
        echo '<h2>ERROR:</h2>';
        var_dump($insert->getError());
        var_dump($e);
        die();
    }
}

echo 'done';