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
global $member;

if ($member['flag_book'] == 'Y') {
    $menu[] = array(
        'title' => '수강관리',
        'sub' => array(
            array('title' => '수강현황', 'uri' => '/webcompany/progress/list.html', 'auth_code' => 'progress'),
// 기수리스트 필요 시 다시 살리기 20241007 박금삼
//            array('title' => '기수리스트', 'uri' => '/webcompany/contents/batch_list.html', 'auth_code' => 'progress'),
            array('title' => '북러닝 수강현황', 'uri' => '/webcompany/read/list.html', 'auth_code' => 'read'),
        )
    );
} else {
    $menu[] = array(
        'title' => '수강관리',
        'sub' => array(
            array('title' => '수강현황', 'uri' => '/webcompany/progress/list.html', 'auth_code' => 'progress')
        )
    );
}
$menu[] = array(
    'title' => 'HRD-Flex',
    'sub' => array(
        array('title' => '기수리스트', 'uri' => '/webcompany/flex/flex_subs_batch_list.html', 'auth_code' => ''),
        array('title' => '수강신청관리', 'uri' => '/webcompany/flex/flex_appl_application_list.html', 'auth_code' => ''),
        array('title' => '수강신청승인관리', 'uri' => '/webcompany/flex/flex_stud_userapplication_list.html', 'auth_code' => '')
    )
);
$menu[] = array(
    'title' => '북러닝(환급)',
    'sub' => array(
        array('title' => '북러닝기수현황', 'uri' => '/webcompany/book/batch_list.html', 'auth_code' => ''),
        array('title' => '북러닝성적정보', 'uri' => '/webcompany/book/record.html', 'auth_code' => '')
    )
);
// 신흥만 회원리스트 메뉴 출력(윤지현 요청) yllee 240617
if ($member['cp_id'] == '1717479266') {
    $menu[0]['sub'][] = array('title' => '회원리스트', 'uri' => '/webcompany/user/list.html', 'auth_code' => 'progress');
}
if ($member['mb_id'] == '6038203952') {
    $menu[] = array(
        'title' => '커뮤니티',
        'sub' => array(
            array('title' => '소식함', 'uri' => '/webcompany/news/list.html', 'auth_code' => 'board'),
            array('title' => '공지사항', 'uri' => '/webcompany/notice/list.html', 'auth_code' => 'board')
        )
    );
} else {
    // 기업관리자 모드에서 알리미 메뉴 빼기: 박민주 요청 yllee 240424
    //array('title' => '알리미', 'uri' => '/webcompany/alerts/list.html', 'auth_code' => 'board')
    $menu[] = array(
        'title' => '커뮤니티',
        'sub' => array(
            array('title' => '소식함', 'uri' => '/webcompany/news/list.html', 'auth_code' => 'board')
        )
    );
}
