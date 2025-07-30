<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();

$flag_json = ($_GET['flag_json']) ? $_GET['flag_json'] : $_POST['flag_json'];
$mode = $_GET['mode'];

if ($mode == 'search_company') {
    $result['code'] = 'success';
    $sch_like = $_GET['sch_like'];
    $sch_keyword = $_GET['sch_keyword'];
    ob_start();
    include_once _MODULE_PATH_ . '/company/admin/ajax.search_company.php';
    $content = ob_get_contents();
    ob_end_clean();
    $result['content'] = $content;
}
echo json_encode($result);
exit;
