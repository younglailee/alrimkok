<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\UserAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserAdmin();
$oUser->init();
$pk = $oUser->get('pk');

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$flag_json = ($_POST['flag_json']) ? $_POST['flag_json'] : $_GET['flag_json'];

if ($mode == 'insert') {
    //print_r($_POST);
    //exit;
    // 등록
    $result = $oUser->insertData();
} elseif ($mode == 'update') {
    // 수정
    //$result = $oUser->updateData();
    //print_r($_POST);print_r($_FILES);exit;
    $result = $oUser->updateUserData();
} elseif ($mode == 'delete') {
    // 삭제
    $result = $oUser->deleteData();
} elseif ($mode == 'validate_member_id') {
    // 아이디 유효성 검증
    $mb_id = $_POST['mb_id'];
    $result = $oUser->validateMemberId($mb_id);
} elseif ($mode == 'validate_member_password') {
    // 패스워드 유효성 검증
    $mb_pw = $_POST['mb_pw'];
    $result = $oUser->validateMemberPassword($mb_pw);
} elseif ($mode == 'search_company') {
    $result['code'] = 'success';
    $sch_like = $_GET['sch_like'];
    $sch_keyword = $_GET['sch_keyword'];
    //Log::debug($_POST);
    //Log::debug($_GET);
    //Log::debug(_MODULE_PATH_);
    ob_start();
    include_once _MODULE_PATH_ . '/user/admin/ajax.search_company.php';
    $content = ob_get_contents();
    ob_end_clean();
    $result['content'] = $content;
    //Log::debug($result);
    echo json_encode($result);
    exit;
} elseif ($_GET['mode'] == 'reset_pw') {
    $result = $oUser->resetPW();
} elseif ($_GET['mode'] == 'occasion_progress') {
    $result = $oUser->progressOc();
} elseif ($_GET['mode'] == 'update_auth') {
    $result = $oUser->updateAuth();
} elseif ($_GET['mode'] == 'send_sms') {
    $result = $oUser->sendSms();
} elseif ($_GET['mode'] == 'delete_progress_crm') {
    //$pr_id = $_GET['pr_id'];
    $bt_code = $_GET['bt_code'];
    $cs_code = $_GET['cs_code'];
    $mb_id = $_GET['mb_id'];
    $result = $oUser->deleteProgressCrm($bt_code, $cs_code, $mb_id);
} elseif ($_GET['mode'] == 'delete_progress_crm_book') {
    //$pr_id = $_GET['pr_id'];
    $bt_code = $_GET['bt_code'];
    $cs_code = $_GET['cs_code'];
    $mb_id = $_GET['mb_id'];
    $result = $oUser->deleteProgressCrmBook($bt_code, $cs_code, $mb_id);
}
// 결과 처리
Html::postprocessFromResult($result, $flag_json);
