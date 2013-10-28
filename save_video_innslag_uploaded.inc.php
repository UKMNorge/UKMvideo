<?php
// CRON ID IS PRIMARY AND UNIQUE IN ukm_related_video - safe to run only insert
$sql = new SQLins('ukm_related_video');
$sql->add('cron_id', $_POST['cron_id']);
$sql->add('b_id', $_POST['b_id']);
$sql->run();