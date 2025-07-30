<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\BizUser;
use sFramework\Html;
use sFramework\Session;
use sFramework\Db;

if (!defined('_ALPHA_')) {
    exit;
}

/* variable */
$fi_id = $_GET['fi_id'];

/* init Class */
$oBiz = new BizUser();
$oBiz->init();

/* Download File */
$oBiz->downloadFile($fi_id);
