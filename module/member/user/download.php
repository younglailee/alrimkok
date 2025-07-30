<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */

use sFramework\MemberUser;

if (!defined('_ALPHA_')) {
    exit;
}
$flag_use_head = false;
$flag_use_header = false;
$flag_use_footer = false;

/* variable */
$fi_id = $_GET['fi_id'];

/* init Class */
$oMember = new MemberUser();
$oMember->init();

/* Download File */
$oMember->downloadFile($fi_id);
