<?php
/**
 * Admin > 메뉴 파일
 * @file    menu.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}
global $member;
$menu = array();

$sch_month = date("Y-m", strtotime("-1 month"));
$sch_date = date("Y-m-d", strtotime("-1 month"));
$last_day = date('t', strtotime($sch_date));
$sch_s_date = $sch_month . '-01';
$sch_e_date = $sch_month . '-' . $last_day;
$sch_period = '?sch_date=reg_time&sch_s_date=' . $sch_s_date . '&sch_e_date=' . $sch_e_date;

$menu[] = array(
    'title' => '회원관리',
    'sub' => array(
        array('title' => '회원리스트', 'uri' => '/webadmin/user/list.html', 'auth_code' => 'user'),
        array('title' => '개인정보동의', 'uri' => '/webadmin/user/assenter_personal.html', 'auth_code' => 'user')
    )
);
$menu[] = array(
    'title' => '공고관리',
    'sub' => array(
        array('title' => '공고리스트', 'uri' => '/webadmin/biz/list.html', 'auth_code' => 'biz_notice'),
        array('title' => '회원공고관리', 'uri' => '/webadmin/company/list.html', 'auth_code' => 'biz_notice')
    )
);
$menu[] = array(
    'title' => '고객센터',
    'sub' => array(
        array('title' => '공지사항', 'uri' => '/webadmin/notice/list.html', 'auth_code' => 'board'),
        array('title' => '1:1문의', 'uri' => '/webadmin/qna/list.html', 'auth_code' => 'board')
    )
);
$menu[] = array(
    'title' => '환경설정',
    'sub' => array(
        array('title' => '기본정보설정', 'uri' => '/webadmin/setting/base_info.html', 'auth_code' => 'setting'),
        array('title' => '약관정보설정', 'uri' => '/webadmin/setting/terms_info.html', 'auth_code' => 'setting'),
        array('title' => '결제정보', 'uri' => '/webadmin/setting/payment.html', 'auth_code' => 'setting'),
        array('title' => '팝업관리', 'uri' => '/webadmin/popup/list.html', 'auth_code' => 'setting'),
        array('title' => '메인배너관리', 'uri' => '/webadmin/carousel/list.html', 'auth_code' => 'setting'),
        array('title' => '하단배너관리', 'uri' => '/webadmin/footer/list.html', 'auth_code' => 'setting'),
        array('title' => '공고사이트', 'uri' => '/webadmin/crawl/list.html', 'auth_code' => 'crawl'),
        array('title' => '계정관리', 'uri' => '/webadmin/admin/list.html', 'auth_code' => 'member')
    )
);
