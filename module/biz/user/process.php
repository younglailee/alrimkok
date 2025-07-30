<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\BizUser;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oBiz = new BizUser();
$oBiz->init();
$pk = $oBiz->get('pk');

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$flag_json = ($_POST['flag_json']) ? $_POST['flag_json'] : $_GET['flag_json'];

if ($mode == 'biz_like') {
    $bz_id = $_GET['bz_id'];
    $state = $_GET['state'];
    $result = $oBiz->bizLike($bz_id, $state);
} elseif ($mode == 'save_memo'){
    \sFramework\Log::debug($_GET);
    $bz_id = $_GET['bz_id'];
    $memoText = $_GET['memoText'];
    $result = $oBiz->saveMemo($bz_id, $memoText);
}
// 결과 처리
Html::postprocessFromResult($result, $flag_json);
