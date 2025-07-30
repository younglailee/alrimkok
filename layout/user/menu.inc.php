<?php
/**
 * Admin > 메뉴 파일
 * @file    menu.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}
$menu = array();

$menu[] = array(
    'title' => '마이페이지',
    'sub' => array(
        array('title' => '본인인증', 'uri' => '/webuser/member/certify.html'),
        array('title' => '회원가입', 'uri' => '/webuser/member/join.html'),
        array('title' => '정보수정', 'uri' => '/webuser/member/modify_info.html'),
        array('title' => '수강신청현황', 'uri' => '/webuser/member/apply_state.html'),
        array('title' => '로그인', 'uri' => '/webuser/member/login.html')
    )
);

$menu[] = array(
    'title' => '나의강의실',
    'sub' => array(
        array('title' => '나의수강현황', 'uri' => '/webuser/page/classroom.html'),
        array('title' => '나의문의현황', 'uri' => '#')
    )
);

$menu[] = array(
    'title' => '과정안내/신청',
    'sub' => array(
        array('title' => '교육과정안내', 'uri' => '/webuser/course/list.html'),
        array('title' => '수강신청', 'uri' => '/webuser/course/apply.html')
    )
);

$menu[] = array(
    'title' => '학습지원센터',
    'sub' => array(
        array('title' => '학습매뉴얼', 'uri' => '/webuser/page/study_manual.html'),
        array('title' => '학습환경설정', 'uri' => '/webuser/page/study_setting.html')
    )
);

$menu[] = array(
    'title' => '커뮤니티',
    'sub' => array(
        array('title' => '공지사항', 'uri' => '/webuser/notice/list.html'),
        array('title' => '자주묻는질문', 'uri' => '/webuser/faq/list.html'),
        array('title' => '1:1문의하기', 'uri' => '/webuser/qna/list.html'),
        array('title' => '수강후기', 'uri' => '/webuser/review/list.html'),
        array('title' => '이벤트', 'uri' => '/webuser/event/list.html'),
        array('title' => '작품과힐링', 'uri' => '/webuser/gallery/list.html')
    )
);
/*
if ($_SERVER['REMOTE_ADDR'] == '61.76.26.31') {
    $menu[4]['sub'][] = array('title' => '안전보건자료실', 'uri' => '/webuser/safe/list.html');
}
*/
$menu[] = array(
    'title' => '기타',
    'sub' => array(
        array('title' => '개인정보처리방침', 'uri' => '/webuser/page/privacy.html'),
        array('title' => '사이트맵', 'uri' => '/webuser/page/sitemap.html')
    ),
    'is_etc' => true
);
