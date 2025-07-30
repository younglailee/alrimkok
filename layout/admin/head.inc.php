<?php
/**
 * Admin > Head 파일
 * @file    head.inc.php
 * @author  Alpha-Edu
 */

use sFramework\Db;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
// http 사용자를 https 로 redirect 처리
Html::httpsRedirect();

global $html_title;
global $layout_uri;
global $js_uri;
global $layout;
/*
 * 서비스 변수 확인
global $member;
if ($member['mb_id'] == 'lucas') {
    global $service;
    echo $service;
}
*/
// 비상 서비스 장애 접속 허용 로직 yllee 220728
$emergency = $_SESSION['emergency'] ?: $_GET['emergency'];
if ($emergency == 'incident') {
    if (!$_SESSION['emergency']) {
        $_SESSION['emergency'] = $emergency;
    }
} else {
    // 관리자 모드는 알파에듀, 그린랩 사무실, 회의실 노트북에서만 접속 가능 yllee 210712
    // 봉암동 센터 IP 210.91.181.46 허용 yllee 210929
    // 밀양 경남지식인인재개발원 IP 222.97.83.58 허용 yllee 210930
    // 회의실 인턴 2명 PC IP 추가 silva 211005
    // KT 인터넷 IP 61.76.26.31
    // LG U+로 인터넷 계약 외부 IP 변경  yllee 220408
    // 한국인터넷진흥원 테스트 IP 예외 처리 박금삼 220623
    // 대찬빌딩 503호로 사무실 이전 IP 변경 182.208.82.12 yllee 221024
    $user_ip = $_SERVER['REMOTE_ADDR'];
    if ($user_ip != '118.130.3.171' && $user_ip != '222.96.8.88' && $user_ip != '121.146.88.176' && $user_ip != '210.91.181.46' && $user_ip != '222.97.83.58' && $user_ip != '61.76.26.122' && $user_ip != '61.76.26.84' && $user_ip != '59.22.162.171' && $user_ip != '222.236.76.218' && $user_ip != '222.236.76.219' && $user_ip != '115.90.194.83' && $user_ip != '182.208.82.12') {
        // 콘텐츠 미리보기 페이지 허용 yllee 220510
        global $service;
        if ($service != 'popup.course_preview') {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
    }
}
global $member;
$mb_id = $member['mb_id'];
// 중복 로그인 차단 로직 적용 yllee 210430
$ss_id = session_id();
$db_where = "WHERE mb_id = '$mb_id' AND session_id = '$ss_id' AND cl_status = 0";
$concurrent = Db::selectOnce('tbl_concurrent_login', '*', $db_where, '');
$request_uri_arr = explode('/', $_SERVER['REQUEST_URI']);
$page_name = $request_uri_arr[3];
$check_popup = explode('.', $page_name);
if ($concurrent) {
    if ($check_popup[0] == 'popup') {
        Html::closeWithRefresh('다른 장소에서 중복 로그인 하였습니다.');
    } else {
        $url = $layout_uri . '/member/process.html?mode=logout';
        Html::alert('다른 장소에서 중복 로그인 하였습니다. 로그아웃합니다.', $url);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta name="robots" content="noindex"/>
<!--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=11,chrome=1"/>
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta name="author" content="Alpha-Edu(alpha@alpha-edu.co.kr)"/>
<meta name="copyright" content="COPYRIGHT &copy; 2016 alpha-edu.co.kr ALL RIGHT RESERVED."/>
<meta name="language" content="ko"/>
<title><?= $html_title ?></title>
<?php
//print_r($_POST);
global $service;
//echo $service;
if ($service == 'crm') {
    ?>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">-->
    <?php
}
?>
<link rel="stylesheet" type="text/css" href="<?= $layout_uri ?>/layout.css"/>
<link rel="stylesheet" type="text/css" href="<?= $js_uri ?>/jquery-ui-1.11.4/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="<?= $js_uri ?>/uniform-2.1.2/alpha.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/xeicon/2/xeicon.min.css"/>
<script type="text/javascript" src="<?= $js_uri ?>/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/uniform-2.1.2/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/jquery.smenu-0.2.1.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.util.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.ajax.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.validate.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.common.js"></script>
<script type="text/javascript" src="<?= $layout_uri ?>/layout.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    initContent(document);

});
var layout = "<?=$layout?>";
var base_uri = "<?=_BASE_URI_?>";
var layout_uri = "<?=$layout_uri?>";
//]]>
</script>
