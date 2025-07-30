<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\FreeUser;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new FreeUser();
$oBoard->init();
$pk = $oBoard->get('pk');

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
} elseif($mode == 'select_detail'){
    $result = $oBoard->selectDetail($_GET['uid']);
}


// 결과 처리
Html::postprocessFromResult($result, $flag_json);
