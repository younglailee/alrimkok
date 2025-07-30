<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\AdminAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oAdmin = new AdminAdmin();
$oAdmin->init();
$pk = $oAdmin->get('pk');

if ($mode == 'insert') {
    // 등록
    $result = $oAdmin->insertData();
} elseif ($mode == 'update') {
    // 수정
    $result = $oAdmin->updateData();
} elseif ($mode == 'delete') {
    // 삭제
    $result = $oAdmin->deleteData();
} elseif ($mode == 'validate_member_id') {
    // 아이디 유효성 검증
    $result = $oAdmin->validateMemberId($mb_id);
} elseif ($mode == 'validate_member_password') {
    // 패스워드 유효성 검증
    $result = $oAdmin->validateMemberPassword($mb_pw);
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
