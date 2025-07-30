<?php
/**
 * Admin > Header 파일
 * @file    header.inc.php
 * @author  Alpha-Edu
 */
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
if (!$layout_size) {
    $layout_size = 'small';
}
global $member;
global $gnb;
global $page_no_arr;

$mb_level = $member['mb_level'];
if ($mb_level != 6) {
    Html::alert('권한이 없습니다.');
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
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
    // 좌측 메뉴 모두 펄침 yllee 200622
    $("li.depth-1").addClass("on");
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
            echo '<a href="' . $layout_uri . '/news/list.html">';
            echo _HOMEPAGE_TITLE_ . ' ' . ucfirst($layout) . ' Mode';
            echo '</a>';
            ?>
        </h1>
        <h2 class="hidden">상단영역</h2>
        <ul class="header_qm">
        <li><a href="<?= _BASE_URI_ ?>/webuser/page/main.html" target="_blank" title="새창">사용자홈으로</a></li>
        <li><a href="<?= $layout_uri ?>/user/partner_info.html">개인정보수정</a></li>
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
            <div class="inplus">
                <ul>
                <li><a href="http://www.alpha-edu.co.kr" target="_blank"><i class="xi-home "></i>알파에듀 홈페이지</a></li>
                </ul>
            </div>
        </div>
        <!-- //aside -->

        <!-- content -->
        <div id="content">
            <h3><?= $doc_title ?></h3>
