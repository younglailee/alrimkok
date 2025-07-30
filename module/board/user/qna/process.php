<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Format;
use sFramework\Html;
use sFramework\QnaUser;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oBoard = new QnaUser();
$oBoard->init();
$pk = $oBoard->get('pk');

$mode = ($_POST['mode']) ?: $_GET['mode'];
$flag_json = ($_POST['flag_json']) ?: $_GET['flag_json'];
// XSS 필터링 적용 yllee 220811
$_POST['bd_subject'] = Format::filterXss($_POST['bd_subject']);

if ($mode == 'insert') {
    // 등록
    $result = $oBoard->insertData();
} elseif ($mode == 'update') {
    // 수정
    $result = $oBoard->updateData();
} elseif ($mode == 'delete') {
    // 삭제
    $result = $oBoard->deleteData();
} elseif ($mode == 'send_auth_no') {
    // 인증번호 발송
    $result = $oBoard->sendAuthNo();
} elseif ($mode == 'validate_auth_no') {
    // 인증번호 검사
    $result = $oBoard->validateAuthNo();
}
// 결과 처리
Html::postprocessFromResult($result, $flag_json);
