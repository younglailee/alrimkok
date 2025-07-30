<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\NoticeTutor;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new NoticeTutor();
$oBoard->init();
$oBoard->downloadFile($fi_id);
