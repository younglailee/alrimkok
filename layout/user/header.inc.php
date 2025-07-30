<?php
/**
 * User > Header 파일
 * @file    header.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}
global $page_no_arr;
global $gnb;
global $snb;
global $group_title;
global $is_mobile;
global $tomorrow;
global $body_class;
global $title_path;
$title_path = str_replace('><a ', '><i class="xi-angle-right-min arrow"></i><a ', $title_path);

// 로그인 확인 yllee 250709
global $member;
$login_class = 'log-in';
$logoff_class = 'log-off';
if ($member['mb_id']) {
    $login_class = 'log-off';
    $logoff_class = 'log-in';
}
?>
<meta charset="UTF-8">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, shrink-to-fit=no, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<title>알림콕</title>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="/common/css/user/style.css"/>
<link rel="stylesheet" type="text/css" href="/common/css/user/swiper.css"/>

<script src="/common/js/user/swiper.jquery.min.js"></script>

<script src="/common/js/user/mobile.js"></script>
<script src="/common/js/user/layout.js"></script>

<meta property="og:type" content="website" />
<meta property="og:site_name" content="알림콕">
<meta property="og:url" content="http://notipop.nayo.kr">
<meta property="og:title" content="알림콕">
<meta property="og:image" content="http://notipop.nayo.kr/images/og.png">
<meta name="twitter:url" content="http://notipop.nayo.kr/">
<meta name="twitter:title" content="알림콕">
<meta name="twitter:card" content="photo">
<meta name="twitter:image" content="http://notipop.nayo.kr/images/og.png">
<link rel="canonical" href="http://notipop.nayo.kr/">
<link rel="apple-touch-icon-precomposed" href="http://notipop.nayo.kr/images/favi/apple-icon-72x72.png">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://notipop.nayo.kr/images/favi/apple-icon-144x144.png">
<link rel="shortcut icon" href="http://notipop.nayo.kr/images/favi/favicon.ico">
<link rel="icon" href="http://notipop.nayo.kr/images/favi/favicon.ico">
<meta property="og:description" content="알림콕 사업공고 매칭 서비스" />
<meta name="description" content="알림콕 사업공고 매칭 서비스" />
<link rel="canonical" href="http://notipop.nayo.kr/" />
</head>

<body>

<div id="body_wrap">

    <!-- Header -->
    <header id="header" class="logout">
        <div class="container">
            <h1 class="logo">
                <a href="/"><img class="of-ct" src="/common/img/user/logo.svg" alt="알림콕"></a>
            </h1>
            <nav class="pc">
                <ul class="gnb">
                    <li class=""><a href="/webuser/biz/list.html">맞춤 공고</a></li>
                    <li class="<?= $logoff_class ?>"><a href="/webuser/member/login.html">로그인</a></li>
                    <li class="<?= $logoff_class ?>"><a href="/webuser/member/join.html">회원가입</a></li>
                    <li class="<?= $login_class ?>"><a href="/webuser/member/apply_list.html">마이페이지</a></li>
                    <li class="<?= $login_class ?>"><a href="/webuser/member/process.html?mode=logout">로그아웃</a></li>
                    <li class="dropDown">
                        <a href="" class="dropBtn">고객센터</a>
                        <ul class="lnb dropCon">
                            <li><a href="/webuser/notice/list.html">공지사항</a></li>
                            <li><a href="/webuser/qna/list.html">1:1문의</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <!-- 모바일 메뉴 -->
            <div class="mobile" id="menu">
                <img src="/common/img/user/icon/menu.svg" class="of-ct menuBtn" alt="메뉴"><img src="/common/img/user/icon/close-00.svg" class="of-ct closeBtn" alt="메뉴">
            </div>
            <div id="openMenu" class="mobile">
                <div class="top-btn">
                    <div class="btn <?= $logoff_class ?> btn02"><a href="/webuser/member/login.html">로그인</a></div>
                    <div class="btn <?= $logoff_class ?>"><a href="/webuser/member/join.html">회원가입</a></div>
                    <div class="btn <?= $login_class ?> btn02"><a href="/webuser/member/apply_list.html">마이페이지</a></div>
                    <div class="btn <?= $login_class ?>"><a href="/webuser/member/process.html?mode=logout">로그아웃</a></div>
                </div>
                <ul class="m-ul">
                    <li><a href="/webuser/biz/list.html">맞춤 공고</a></li>
                    <li><a href="/webuser/notice/list.html">공지사항</a></li>
                    <li><a href="/webuser/qna/list.html">1:1문의</a></li>
                </ul>
            </div>

            <!-- // 모바일 메뉴 -->
        </div>
    </header>
    <!-- // Header -->
