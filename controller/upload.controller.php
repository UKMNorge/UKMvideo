<?php

use UKMNorge\Arrangement\Arrangement;

global $blog_id;
UKMvideo::addViewData('blog_id', $blog_id);

$arrangement = new Arrangement(intval(get_option('pl_id')));
UKMvideo::addViewData('arrangement', $arrangement);