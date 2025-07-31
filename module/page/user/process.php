<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\BizUser;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oBiz = new BizUser();
$oBiz->init();
$pk = $oBiz->get('pk');

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$flag_json = ($_POST['flag_json']) ? $_POST['flag_json'] : $_GET['flag_json'];

if ($mode == 'biz_like') {
    //print_r($_POST);
    //exit;
    // 등록
    $bz_id = $_GET['bz_id'];
    $state = $_GET['state'];
    $result = $oBiz->bizLike($bz_id, $state);
} elseif ($mode == 'popup_cookie') {
    setcookie('popup_' . $_GET['pu_id'], 'Y', strtotime("+1 day 000000"));
}
// 결과 처리
Html::postprocessFromResult($result, $flag_json);
