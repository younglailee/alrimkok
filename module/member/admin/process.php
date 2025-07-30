<?php
global $oMember;

/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\Html;
use sFramework\Visit;

$mode = ($_POST['mode']) ?: $_GET['mode'];
$flag_json = ($_POST['flag_json']) ?: $_GET['flag_json'];

if ($mode == 'login') {
    // 로그인 검증
    $result = $oMember->login();
    if ($result['code'] == 'success') {
        // 로그인 성공 시 방문 DB 기록
        $member = $oMember->getLoginMember();
        $oVisit = new Visit();
        $oVisit->init();
        $oVisit->insertData();
    }
} elseif ($mode == 'logout') {
    // 로그아웃
    $result = $oMember->logout();
} else if($mode == 'update_password') {
    $result = $oMember->updatePassword();
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
