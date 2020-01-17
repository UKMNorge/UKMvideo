<?php
header('Content-Type: text/html; charset=utf-8');
echo '<meta charset="utf-8" />';

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Insert;
use UKMNorge\Database\SQL\Query;


require_once('UKM/Autoloader.php');
require_once('utf8.php');
error_reporting(E_ALL);
ini_set('display_errors', true);

$query = new Query(
    "SELECT *
    FROM `ukm_standalone_video`
    WHERE `video_file` IS NOT NULL
    "
);
$res = $query->run();

echo Query::numRows($res);
while ($row = Query::fetch($res)) {
    echo '<h1>' . $row['video_file'] . '</h1>';
    if ($row['video_file'] == 'ukmno/videos/') {
        echo 'continue';
        continue;
    }
    $data = explode('/', $row['video_file']);

    $sesong = $data[2];

    $_sted = explode('_', $data[3]);
    if ($data[4] == 'innslag') {
        $_fil = explode('_', $data[5]);
    } else {
        $_fil = explode('_', $data[4]);
    }

    $arrangement_id = $row['pl_id'];

    $cron_id = $row['cron_id'];

    $skip = true;

    if (empty($cron_id)) {
        echo 'Mangler cron_id';
        $skip = false;
    }
    if (empty($row['video_file'])) {
        echo 'Mangler video_fil';
        $skip = false;
    }
    if ($skip && empty($sesong)) {
        $sesong = 0;
        $skip = true;
    }
    if (!$skip  && empty($arrangement_id)) {
        $arrangement_id = 0;
        $skip = true;
    }

    if (!$skip) {
        echo ('Noe mangler!');
        die();
    }

    $title = makesurethisisutf8($row['video_name']);
    $description = makesurethisisutf8($row['video_description']);

    $insert = new Insert(
        'ukm_uploaded_video'
    );
    $insert->charset('utf8mb4');
    $insert->add('cron_id', $cron_id);
    $insert->add('file', $row['video_file']);
    $insert->add('arrangement_id', $arrangement_id);
    $insert->add('innslag_id', NULL);
    $insert->add('title_id', NULL);
    $insert->add('season', $sesong);
    $insert->add('converted', 'true');
    $insert->add('title', $title);
    $insert->add('description', $description);
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