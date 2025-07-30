<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\AdminAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oAdmin = new AdminAdmin();
$oAdmin->init();
$oAdmin->downloadFile($fi_id);
