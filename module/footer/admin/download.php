<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\CarouselAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oCarousel = new CarouselAdmin();
$oCarousel->init();
$oCarousel->downloadFile($fi_id);
