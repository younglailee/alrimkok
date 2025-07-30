<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\NoticeAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new NoticeAdmin();
$oBoard->init();
$oBoard->downloadFile($fi_id);
