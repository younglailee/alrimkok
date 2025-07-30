<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */
use sFramework\PopupAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oPopup = new PopupAdmin();
$oPopup->init();
$pk = $oPopup->get('pk');

if ($mode == 'insert') {
    // 등록
    $result = $oPopup->insertData();
} elseif ($mode == 'update') {
    // 수정
    $result = $oPopup->updateData();
} elseif ($mode == 'delete') {
    // 삭제
    $result = $oPopup->deleteData();
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
