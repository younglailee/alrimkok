<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\NoticeAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new NoticeAdmin();
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
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
