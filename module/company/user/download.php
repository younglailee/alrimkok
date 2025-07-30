<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyUser;

if (!defined('_ALPHA_')) {
    exit;
}
$flag_use_head = false;
$flag_use_header = false;
$flag_use_footer = false;

/* variable */
$fi_id = $_GET['fi_id'];

/* init Class */
$oCompany = new CompanyUser();
$oCompany->init();

/* Download File */
$oCompany->downloadFile($fi_id);
