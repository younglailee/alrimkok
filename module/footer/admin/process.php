<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\FooterAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oFooter = new FooterAdmin();
$oFooter->init();
$pk = $oFooter->get('pk');

\sFramework\Log::debug($_POST);

if ($mode == 'insert') {
    // 등록
    $result = $oFooter->insertData();
} elseif ($mode == 'update') {
    // 수정
    $result = $oFooter->updateData();
} elseif ($mode == 'delete') {
    // 삭제
    $result = $oFooter->deleteData();
} elseif ($mode == 'change_order') {
    // 순서 변경
    $result = $oFooter->changeOrder();
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
