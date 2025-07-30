<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */

use sFramework\BizAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBiz = new BizAdmin();
$oBiz->init();
$oBiz->downloadFile($fi_id);
