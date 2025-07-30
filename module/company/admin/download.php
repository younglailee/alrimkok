<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\CompanyAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();

$fi_id = $_GET['fi_id'];

$oCompany->downloadFile($fi_id);
