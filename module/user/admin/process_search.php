<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\UserAdmin;
use sFramework\Html;
use sFramework\Log;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserAdmin();
$oUser->init();

$flag_json = ($_GET['flag_json']) ? $_GET['flag_json'] : $_POST['flag_json'];
$mode = $_GET['mode'];

if ($mode == 'search_user') {
    $result['code'] = 'success';
    $sch_like = $_GET['sch_like'];
    $sch_keyword = $_GET['sch_keyword'];
    ob_start();
    include_once _MODULE_PATH_ . '/user/admin/ajax.search_user.php';
    $content = ob_get_contents();
    ob_end_clean();
    $result['content'] = $content;
}
echo json_encode($result);
exit;
