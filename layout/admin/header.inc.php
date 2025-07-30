<?php
/**
 * Admin > Header 파일
 * @file    header.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}
global $layout_size, $layout, $layout_uri, $page_no_arr, $sitemap, $doc_title;
if (!$layout_size) {
    $layout_size = 'small';
}
global $member;

// 알리지 SSO용 토큰 생성
function makeIdToken($id, $ip, $key)
{
    return md5($id . $ip . date("Y-m-d") . $key);
}
$login_id = $member['mb_id'];
$ip = $_SERVER['REMOTE_ADDR'];
$key = 'aleasy';
$make_id_token = makeIdToken($login_id, $ip, $key);
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    // GNB
    $("#gnb").sMenu({
        on_menu1: "<?=$page_no_arr[0]?>",
        on_menu2: "<?=$page_no_arr[1]?>",
        hover_class: "hover"
    });

    $("#snb").sMenu({
        on_menu1: "<?=$page_no_arr[0]?>",
        on_menu2: "<?=$page_no_arr[1]?>",
        hover_class: "hover",
        is_snb: true,
        hoverCall: function(obj, depth) {
            obj.removeClass("hover");
            if (!obj.hasClass("on")) {
                obj.addClass("on");
            } else {
                obj.removeClass("on");
            }
        }
    });
});
//]]>
</script>
</head>
<body class="layout-<?= $layout_size ?>">
<div id="skipToContainer">
    <a href="#container" class="accessibility">본문 바로가기</a>
</div>
<div id="wrap">
    <div id="header">
        <h1>
            <?php
            // 메인 페이지 이동(레벨 7 제외) yllee 190313
            // 모든 레벨 메인 페이지 이동 가능 yllee 210120
            echo ($member['mb_level'] != 77) ? '<a href="' . $layout_uri . '/page/main.html">' : '<a>';
            echo _HOMEPAGE_TITLE_ . ' ' . ucfirst($layout) . 'Mode';
            echo ($member['mb_level'] != 77) ? '</a>' : '</a>';
            ?>
        </h1>
        <h2 class="hidden">상단영역</h2>
        <ul class="header_qm">
        <li><a href="//alpha.aleasy.co.kr/webadmin/page/api.login.html?get_mb_id=<?=$member['mb_id']?>&token=<?=$make_id_token?>" target="_blank" title="새창">알리지관리자</a>
        </li>
        <li><a href="<?= _BASE_URI_ ?>/webuser/page/main.html" target="_blank" title="새창">사용자홈으로</a></li>
        <li><a href="<?= $layout_uri ?>/member/modify_password.html">비밀번호변경</a></li>
        <li><a href="<?= $layout_uri ?>/member/process.html?mode=logout"><strong>로그아웃</strong></a></li>
        </ul>

        <div id="gnb">
            <ul>
            <?= $gnb ?>
            </ul>
        </div>
    </div>
    <!-- //header -->

    <!-- container -->
    <div id="container">

        <h2 class="hidden">본문영역</h2>

        <!-- aside -->
        <div id="aside">
            <div class="member_info">
                <p>
                    <em><strong><?= $member['mb_name'] ?></strong>님,</em>
                    환영합니다.<br/>
                    회원등급 : <?= $member['txt_mb_level'] ?><br/>
                    접속아이피 : <span><?= _USER_IP_ ?></span>
                </p>
            </div>
            <div id="snb">
                <ul>
                <?= $sitemap ?>
                </ul>
                <?php
                // 레벨 7 이상일 때만 SMS 관리 출력 yllee 190313
                if ($member['mb_level'] > 7) {
                    ?>
                    <ul>
                    <li><a href="https://www.munjasin.co.kr/" target="_blank">SMS관리</a>
                    </li>
                    </ul>
                    <?php
                }
                ?>
            </div>
            <div class="alpha">
                <ul>
                <li><a href="http://www.alpha-edu.co.kr" target="_blank"><i class="xi-home "></i>알파에듀 홈페이지</a></li>
                <li><a href="http://www.alpha-edu.co.kr" target="_blank"><i class="xi-desktop "></i>알파에듀 CRM</a></li>
                <li><a href="http://367.co.kr" target="_blank"><i class="xi-network-public"></i>원격지원 요청하기</a></li>
                </ul>
            </div>
        </div>
        <!-- //aside -->

        <!-- content -->
        <div id="content">
            <h3><?= $doc_title ?></h3>
