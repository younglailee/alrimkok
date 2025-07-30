<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\Log;
use sFramework\Visit;

if ($mode == 'login') {
    // 로그인 검증
    $result = $oMember->login();
    if ($result['code'] == 'success') {
        // 로그인 성공 시 방문 DB 기록
        $member = $oMember->getLoginMember();
        $oVisit = new Visit();
        $oVisit->init();
        $oVisit->insertData();
        //Log::debug($_POST);
        Log::debug($member);
    }
} elseif ($mode == 'logout') {
    // 로그아웃
    $result = $oMember->logout();
} else if ($mode == 'update_password') {
    $result = $oMember->updatePassword();
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
