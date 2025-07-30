<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\BizUser;
use sFramework\Format;
use sFramework\Html;
use sFramework\MemberUser;
use sFramework\UserUser;
use sFramework\Visit;

$oMember = new MemberUser();
$oMember->init();
$oUser = new UserUser();
$oUser->init();
$oBiz = new BizUser();
$oBiz->init();

// XSS 필터링 적용 yllee 220811
$_POST['mail1'] = Format::filterXss($_POST['mail1']);
$_POST['mail2'] = Format::filterXss($_POST['mail2']);
$_POST['mb_addr'] = Format::filterXss($_POST['mb_addr']);
$_POST['mb_addr2'] = Format::filterXss($_POST['mb_addr2']);

//error_reporting(E_ALL & ~E_WARNING);ini_set('display_errors', '1');print_r($_POST);

$mode = ($_POST['mode']) ?: $_GET['mode'];
if ($mode == 'login') {
    // 로그인 검증
    $result = $oMember->login();
    if ($result['code'] == 'success') {
        // 로그인 성공 시 방문 DB 기록
        $member = $oMember->getLoginMember();
        $oVisit = new Visit();
        $oVisit->init();
        $result_visit = $oVisit->insertData();
    }
} elseif ($mode == 'logout') {
    // 로그아웃
    $result = $oMember->logout();

} else if ($mode == 'update_password') {
    $result = $oMember->updatePassword();
    //$result['uri'] = $_SERVER['HTTP_REFERER'];
} else if ($mode == 'update_member_info') {
    //print_r($_FILES);print_r($_POST);exit;
    $result = $oMember->updateMemberData();
    $result['uri'] = $_SERVER['HTTP_REFERER'];
} else if ($mode == 'findId') {
    $result = $oMember->findId();
} else if ($mode == 'find_password') {
    $result = $oMember->findPassword();
} else if ($mode == 'cancel_application') {
    $result = $oMember->cancelApplication();
} else if ($mode == 'search_id') {
    $result = $oMember->checkId();
} elseif ($mode == 'validate_member_password') {
    // 패스워드 유효성 검증
    $mb_pw = $_POST['mb_pw'];
    $result = $oMember->validateMemberPassword($mb_pw);
} elseif ($mode == 'join') {
    /*
    print_r($_POST);
    $jon_post = array
    (
        'mode' => 'join',
        'cp_name' => '주식회사 오스비스',
        'mb_id' => '1638102287',
        'mb_pw' => 'bestone6508^^',
        'flag_mb_pw' => 1,
        'mb_pw2' => 'bestone6508^^',
        'mb_name' => '홍길동',
        'mb_depart' => '영업팁',
        'mb_hp' => '01012345678',
        'mb_email' => 'test',
        'mb_email2' => 'manual',
        'managerEmailCustomDomain' => 'alpha-edu.co.kr',
        'mail1' => '',
        'mail2' => '',
        'mb_addr' => '',
        'mb_addr2' => ''
    );
    exit;
    */
    $result = $oMember->join();
} elseif ($mode == 'sub_popup') {
    $result['code'] = 'success';
    $mb_id = $_GET['mb_id'];
    $sub_mode = $_GET['sub_mode'];
    ob_start();
    include_once _MODULE_PATH_ . '/member/user/ajax.sub_popup.php';
    $content = ob_get_contents();
    ob_end_clean();
    $result['content'] = $content;
    echo json_encode($result);
    exit;
} elseif ($mode == 'sub_insert') {
    //print_r($_POST);exit;
    $result = $oMember->joinSub();
} elseif ($mode == 'sub_update') {
    //print_r($_POST);exit;
    $result = $oMember->joinSub();
} elseif ($mode == 'delete') {
    // 삭제
    //print_r($_POST);exit;
    $result = $oUser->deleteData();
} elseif ($mode == 'insert') {
    $result = $oMember->join();
} elseif ($mode == 'method_bank') {
    $ap_ids = $_GET['ap_ids'];
    $result = $oMember->updateMethod($ap_ids);
} else if ($mode == 'cert_password') {
    $result = $oMember->certification();
} else if ($mode == 'check_auth') {
    $result = $oMember->checkPassAuth();
} else if ($mode == 'update_password_find') {
    $result = $oMember->updatePasswordFind();
} else if ($mode == 'save_addr') {
    $result = $oMember->saveAddr();
} elseif ($mode == 'save_memo'){
    \sFramework\Log::debug($_GET);
    $bz_id = $_GET['bz_id'];
    $memoText = $_GET['memoText'];
    $result = $oBiz->saveMemo($bz_id, $memoText);
} elseif ($mode == 'biz_like') {
    $bz_id = $_GET['bz_id'];
    $state = $_GET['state'];
    $result = $oBiz->bizLike($bz_id, $state);
}
// 결과 처리
$flag_json = ($_GET['flag_json']) ?: $_POST['flag_json'];
Html::postprocessFromResult($result, $flag_json);
