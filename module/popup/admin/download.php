<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\PopupAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oPopup = new PopupAdmin();
$oPopup->init();
$oPopup->downloadFile($fi_id);
