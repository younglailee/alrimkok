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
global $layout_uri;

$menu[] = array(
    'title' => '회원관리',
    'sub' => array(
        array('title' => '회원리스트', 'uri' => $layout_uri . '/user/list.html', 'auth_code' => 'user')
    )
);
$menu[] = array(
    'title' => '공고관리',
    'sub' => array(
        array('title' => '공고리스트', 'uri' => $layout_uri . '/biz/list.html', 'auth_code' => 'biz_notice'),
        array('title' => '회원공고관리', 'uri' => $layout_uri . '/company/list.html', 'auth_code' => 'biz_notice')
    )
);
$menu[] = array(
    'title' => '고객센터',
    'sub' => array(
        array('title' => '공지사항', 'uri' => $layout_uri . '/notice/list.html', 'auth_code' => 'board'),
        array('title' => '1:1문의', 'uri' => $layout_uri . '/qna/list.html', 'auth_code' => 'board')
    )
);
