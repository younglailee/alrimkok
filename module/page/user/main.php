<?php
/**
 * @file    main.php
 * @author  Alpha-Edu
 */
/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/
use sFramework\Html;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
global $is_mobile;
global $member;
//if (!$is_mobile) {
$request_ip = $_SERVER['REMOTE_ADDR'];

// http 사용자를 https 로 redirect 처리
Html::httpsRedirect();

$is_main = true;
$body_class = 'main';

global $js_uri;
$ck_save_id = Session::getCookie('ck_save_id_user');
?>
<script type="text/javascript">
//<![CDATA[
//]]>
</script>
<div id="new_cmm_sub">
메인
</div>
<a href="../notice/list.html">공지사항</a>
