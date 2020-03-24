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
    FROM `ukmno_wp_related`
    WHERE `post_type` = 'video'
    "
);
$res = $query->run();

echo Query::numRows($res);
while ($row = Query::fetch($res)) {
    $metadata = unserialize($row['post_meta']);
    if (!$metadata) {
        $row['post_meta'] = rtrim($row['post_meta'], ';}') . '}';
        $metadata = unserialize($row['post_meta']);
        if (!$metadata) {
            echo 'ERROR i metadata. Continue';
            continue;
        }
    }

    echo '<h1>' . $metadata['file'] . '</h1>';
    if ($metadata['file'] == 'ukmno/videos/') {
        echo 'continue';
        continue;
    }

    $finnes = new Query(
        "SELECT `id`
        FROM `ukm_uploaded_video`
        WHERE `file` = '#file'",
        [
            'file' => $metadata['file']
        ]
    );
    $id = $finnes->getField();

    if ($id) {
        $modify = new Update(
            'ukm_uploaded_video',
            [
                'file' => $metadata['file']
            ]
        );
    } else {
        $modify = new Insert('ukm_uploaded_video');

        $modify->add('file', $metadata['file']);

        if ($row['pl_type'] == 'kommune') {
            $pl_qry = new Query(
                "SELECT `pl`.`pl_id`
                FROM `smartukm_place` AS `pl`
                LEFT JOIN `smartukm_rel_pl_k` AS `pl_k`
                    ON (`pl_k`.`pl_id` = `pl`.`pl_id`)
                WHERE `pl_k`.`k_id` = '#kommune'
                AND `pl_k`.`season` = '#season'",
                [
                    'kommune' => $row['b_kommune'],
                    'season' => $row['b_season']
                ]
            );
        } elseif ($row['pl_type'] == 'fylke') {
            if (strlen($row['b_kommune'] == 3)) {
                $fylke = substr($row['b_kommune'], 0, 1);
            } else {
                $fylke = substr($row['b_kommune'], 0, 2);
            }
            $pl_qry = new Query(
                "SELECT `pl_id`
                FROM `smartukm_place`
                WHERE `old_pl_fylke` = '#fylke'
                AND `old_pl_kommune` = '0'
                AND `season` = '#season'",
                [
                    'fylke' => $fylke,
                    'season' => $row['b_season']
                ]
            );
        } elseif ($row['pl_type'] == 'land') {
            $pl_qry = new Query(
                "SELECT `pl_id`
                FROM `smartukm_place`
                WHERE `old_pl_fylke` = '123456789'
                AND `old_pl_kommune` = '123456789'
                AND `season` = '#season'",
                [
                    'season' => $row['b_season']
                ]
            );
        } else {
            die('Støtter ikke pl_type::' . $row['pl_type']);
        }
        $pl_id = $pl_qry->getField();

        $modify->add('arrangement_id', $pl_id);
    }

    $matches = [];
    preg_match("/_cronid_([0-9]+)./", $metadata['file'], $matches);
    // Noen gamle filer mangler cron_id. De kan vi drite i
    if (!isset($matches[1])) {
        preg_match("/_cron_([0-9]+)./", $metadata['file'], $matches);
        if (!isset($matches[1])) {
            continue;
        }
    }
    if( isset( $matches[1] ) && is_numeric( $matches[1] ) ) {
        $modify->add('cron_id', $matches[1]);
    }

    $modify->add('innslag_id', $row['b_id']);
    $modify->add('season', $row['b_season']);
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