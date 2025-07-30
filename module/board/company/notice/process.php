<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\UserAdmin;
use sFramework\Html;
use sFramework\NoticeAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new NoticeAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');

$oUser = new UserAdmin();
$oUser->init();

if ($mode == 'insert') {
    // 등록
    $result = $oBoard->insertData();
} elseif ($mode == 'update') {
    // 수정
    $result = $oBoard->updateData();
} elseif ($mode == 'delete') {
    // 삭제
    $result = $oBoard->deleteData();
}elseif($_GET['mode'] == 'sch_partner'){
    $result = $oUser->selectPartnerListAjax();
}elseif($_GET['mode'] == 'sch_tutor'){
    $result = $oUser->selectTutorListAjax();
}elseif($_GET['mode'] == 'sch_company'){
    $result = $oUser->selectCompanyListAjax();
} elseif ($mode == 'search_course') {
    $result['code'] = 'success';
    $sch_like = $_GET['sch_like'];
    $sch_keyword = $_GET['sch_keyword'];
    ob_start();
    include_once _MODULE_PATH_ . '/board/admin/notice/ajax.search_course.php';
    $content = ob_get_contents();
    ob_end_clean();
    $result['content'] = $content;
    echo json_encode($result);
    exit;
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
