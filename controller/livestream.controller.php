<?php

$arrangement = UKMvideo::getArrangement();
UKMvideo::addViewData('arrangement', $arrangement);

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    update_option('ukm_live_link', $_POST['live_link'] );
    update_option('ukm_live_embedcode', $_POST['live_embedcode'] );
    UKMvideo::getFlashbag()->success('Livestream-detaljer er lagret');
    UKMvideo::getViewData()['livestream']->link = $_POST['live_link']; 
}