<?php
/**
 * User > Head 파일
 * @file    head.inc.php
 * @author  Alpha-Edu
 */

use sFramework\Db;
use sFramework\Html;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
// 자유게시판, 연수워크숍신청, 센터에바란다 및 예약이 아닐 경우, 작성자 세션 삭제 yllee 180831
global $module;
global $expansion;
global $service;
//echo $service;
if ($module != 'board' || ($expansion != 'free' && $expansion != 'reservation' && $expansion != 'request' && $expansion != 'proposal' && $expansion != 'receipt')) {
    Session::setSession('ss_bd_writer_name', null);
    Session::setSession('ss_bd_writer_tel', null);
}
// 입주신청이 아닐 경우, 신청자 세션 삭제
if ($module != 'occupancy') {
    Session::setSession('ss_oc_name', null);
    Session::setSession('ss_oc_tel', null);
}
// 온라인 접수가 아닐 경우, 담당자 세션 삭제
if ($module != 'application') {
    Session::setSession('ss_ap_crg_name', null);
    Session::setSession('ss_ap_crg_tel', null);
}
global $is_mobile;
/*if($is_mobile) {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    if ($user_ip != '218.146.221.53' && $user_ip != '118.39.175.195') {
        header("Location: /webuser/img/mobile_deny.jpg");
    }
}*/
// 에코웰 기업 코드 세션 저장 yllee 210727
$company_code = $_GET['company_code'];
if ($company_code == 'echowel') {
    Session::setSession('company_code', $company_code);
    //echo Session::getSession('company_code');
}
global $member;
$ss_id = session_id();
$mb_id = $member['mb_id'];

// DB 부하 발생으로 주석처리 yllee 221114
// 다시 활성화 yllee 230417
$concurrent = Db::selectOnce('tbl_concurrent_login', '*', "WHERE mb_id = '$mb_id' AND session_id = '$ss_id' AND cl_status = 0", '');
$request_uri_arr = explode('/', $_SERVER['REQUEST_URI']);
$page_name = $request_uri_arr[3];
$check_popup = explode('.', $page_name);
/*
if ($concurrent && $member['flag_test'] != 'Y') {
    if ($check_popup[0] == 'popup') {
        Html::closeWithRefresh('다른 장소에서 중복 로그인 하였습니다.');
    } else {
        Html::alert('다른 장소에서 중복 로그인 하였습니다. 로그아웃합니다.', '/webuser/member/process.html?mode=logout');
    }
}
*/
if ($member['mb_id']) {
    if ($member['flag_test'] != 'Y') {
        // 인터넷 연수원 수강생일 경우 해당 기업 인터넷 연수원으로 이동 처리 yllee 220531
        if ($member['mb_level'] == 1) {
            // 서원유통 수강생 해당 인터넷연수원 이동 yllee 210914
            if ($member['cp_id'] == '1628649560' || $member['cp_id'] == '1628649339') {
                //print_r($member);
                Session::setSession('ss_user_mb_id', '');
                $msg = '서원유통, 서원홀딩스 회원은 인터넷연수원 홈페이지로 이동됩니다.';
                $msg .= '\n다시 로그인하시기 바립니다.';
                Html::alert($msg, '//alpha-edu.co.kr/webcyber/page/seowon_intro.html');
                exit;
            } else if ($member['cp_id'] == '1569223802') {
                Session::setSession('ss_user_mb_id', '');
                $msg = 'the큰병원 회원은 인터넷연수원 홈페이지로 이동됩니다.';
                $msg .= '\n다시 로그인하시기 바립니다.';
                Html::alert($msg, '//alpha-edu.co.kr/webgrand/page/main.html');
                exit;
            } else if ($member['cp_id'] == '1590644638' || $member['cp_id'] == '1620917974' || $member['cp_id'] == '1620917974') {
                // 인생한방병원 수강생 포함 yllee 230525
                // 메디바이저 수강생 포함 yllee 220603
                Session::setSession('ss_user_mb_id', '');
                $msg = '미시안안과, 메디바이저, 인생한방병원 회원은 인터넷연수원 홈페이지로 이동됩니다.';
                $msg .= '\n다시 로그인하시기 바립니다.';
                Html::alert($msg, '//alpha-edu.co.kr/webmisian/page/main.html');
                exit;
            } else if ($member['cp_id'] == '1504660510' || $member['cp_id'] == '1504660247' || $member['cp_id'] == '1504659952') {
                // (주)삼광, (주)삼광원테크, 경진기업 minju 230309
                Session::setSession('ss_user_mb_id', '');
                $msg = '(주)삼광, (주)삼광원테크, 경진기업 회원은 인터넷연수원 홈페이지로 이동됩니다.';
                $msg .= '\n다시 로그인하시기 바립니다.';
                Html::alert($msg, '//alpha-edu.co.kr/websamkwang/page/main.html');
                exit;
            } else if ($member['cp_id'] == '1700532542' && $member['flag_cyber'] != 'N') {
                // 삼성웰스토리 yllee 231122
                Session::setSession('ss_user_mb_id', '');
                Session::setSession('ss_cyber_mb_id', '');
                $msg = '삼성웰스토리 회원은 인터넷연수원 홈페이지로 이동됩니다.';
                $msg .= '\n다시 로그인하시기 바립니다.';
                Html::alert($msg, '//alpha-edu.co.kr/webssws/page/main.html');
                exit;
            }
        }
    }
}
if (isset($_SESSION['LAST_ACTIVITY']) && ((time() - $_SESSION['LAST_ACTIVITY']) > 7200) && $member['mb_id']) {
    unset($_SESSION['LAST_ACTIVITY']);
    Html::alert('장시간(2시간 이상) 사용하지 않아 로그아웃됩니다.', '/webuser/member/process.html?mode=logout');
}
$_SESSION['LAST_ACTIVITY'] = time();
//echo $_SESSION['LAST_ACTIVITY'];

$html_title = _HOMEPAGE_TITLE_;
$html_description = '알림콕 사업공고 매칭 서비스';
//$html_title .= ' | ' . $html_description;
global $js_uri;
global $layout_uri;
global $layout;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta name="NaverBot" content="All"/>
<meta name="NaverBot" content="index,follow"/>
<meta name="Yeti" content="All"/>
<meta name="Yeti" content="index,follow"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Expires" content="-1">
<meta http-equiv="imagetoolbar" content="no"/>
<meta name="author" content="Alpha-Edu(alpha@alpha-edu.co.kr)"/>
<meta name="copyright" content="COPYRIGHT &copy; 2024 Alpha-Edu ALL RIGHT RESERVED."/>
<meta name="language" content="ko"/>
<meta name="description" content="<?= $html_description ?>"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="<?= $html_title ?>"/>
<meta property="og:description" content="<?= $html_description ?>"/>
<title><?= $html_title ?></title>
<link rel="canonical" href="http://alpha-edu.co.kr/index.php">
<link rel="shortcut icon" href="/favicon.ico"/>
<link rel="stylesheet" type="text/css" href="<?= $layout_uri ?>/layout.css"/>
<link rel="stylesheet" type="text/css" href="<?= $js_uri ?>/jquery-ui-1.11.4/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="<?= $js_uri ?>/uniform-2.1.2/alpha.css"/>
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
$(function() {
    initContent(document);
    // 토글 탭
    $("a.tab_title").on("click", function(e) {
        $(this).parent("li").addClass("on").siblings().removeClass("on");
        e.preventDefault();
    });
});
var layout = "<?=$layout?>";
var base_uri = "<?=_BASE_URI_?>";
var layout_uri = "<?=$layout_uri?>";
//]]>
</script>
<link rel="stylesheet" type="text/css" href="/common/css/user.css"/>

<!-- swiper -->
<link rel="stylesheet" href="<?= $js_uri ?>/swiper/swiper-bundle.min.css" />
<script src="<?= $js_uri ?>/swiper/swiper-bundle.min.js"></script>