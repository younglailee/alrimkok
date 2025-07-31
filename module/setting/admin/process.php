<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\SettingAdmin;

//error_reporting(E_ALL & ~E_WARNING);ini_set('display_errors', '1');
if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oSetting = new SettingAdmin();
$oSetting->init();
$pk = $oSetting->get('pk');

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$flag_json = ($_POST['flag_json']) ? $_POST['flag_json'] : $_GET['flag_json'];

if ($mode == 'update') {
    // 수정: 기본정보설정
    $oSetting->set('uid', $_POST['uid']);
    $result = $oSetting->updateData();
} elseif ($mode == 'update_payment') {
    // 수정: 결제정보
    $oSetting->set('uid', $_POST['uid']);
    $result = $oSetting->updateDataPay();
}
// 결과 처리
Html::postprocessFromResult($result, $flag_json);
