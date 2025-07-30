<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\UserCompany;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserCompany();
$oUser->init();
$pk = $oUser->get('pk');

$mode = ($_POST['mode']) ?: $_GET['mode'];
$flag_json = ($_POST['flag_json']) ?: $_GET['flag_json'];
$result = array();

if ($_GET['mode'] == 'occasion_progress') {
    $result = $oUser->progressOc();
}
// 결과 처리
Html::postprocessFromResult($result, $flag_json);
