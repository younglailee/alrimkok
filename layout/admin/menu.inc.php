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
        array('title' => '회원리스트', 'uri' => '/webadmin/user/list.html', 'auth_code' => 'user')
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
        array('title' => '시험유의사항', 'uri' => '/webadmin/setting/exam_info.html', 'auth_code' => 'setting'),
        array('title' => '결제정보', 'uri' => '/webadmin/setting/payment.html', 'auth_code' => 'setting'),
        array('title' => '팝업관리', 'uri' => '/webadmin/popup/list.html', 'auth_code' => 'setting'),
        array('title' => '메인배너관리', 'uri' => '/webadmin/carousel/list.html', 'auth_code' => 'setting'),
        array('title' => '하단배너관리', 'uri' => '/webadmin/footer/list.html', 'auth_code' => 'setting'),
        array('title' => '공고사이트', 'uri' => '/webadmin/crawl/list.html', 'auth_code' => 'crawl'),
        array('title' => '관리자계정', 'uri' => '/webadmin/admin/list.html', 'auth_code' => 'member')
    )
);
/*$menu[] = array(
    'title' => '평가관리',
    'sub' => array(
        array('title' => '중간평가응시', 'uri' => '/webadmin/exam/exam_middle.html', 'auth_code' => 'exam'),
        array('title' => '최종시험응시', 'uri' => '/webadmin/exam/exam.html', 'auth_code' => 'exam'),
        array('title' => '레포트응시', 'uri' => '/webadmin/exam/report.html', 'auth_code' => 'exam'),
        array('title' => '강의평가등록', 'uri' => '/webadmin/evaluate/list.html', 'auth_code' => 'evaluate'),
        array('title' => '강의평가결과', 'uri' => '/webadmin/evaluate/result.html', 'auth_code' => 'evaluate'),
        array('title' => '강의평가결과(기수)', 'uri' => '/webadmin/evaluate/result_batch.html', 'auth_code' => 'evaluate'),
        array('title' => '강의평가결과(과정)', 'uri' => '/webadmin/evaluate/result_course.html', 'auth_code' => 'evaluate')
    )
);
$menu[] = array(
    'title' => '통계관리',
    'sub' => array(
        array('title' => '개인정보동의자', 'uri' => '/webadmin/statistics/assenter_personal.html', 'auth_code' => 'statistics'),
        array('title' => '선택정보동의자', 'uri' => '/webadmin/statistics/assenter_choice.html', 'auth_code' => 'statistics'),
        array('title' => '입과시본인인증', 'uri' => '/webadmin/statistics/once_certify.html', 'auth_code' => 'statistics'),
        array('title' => '시험응시동의자', 'uri' => '/webadmin/statistics/exam_taker.html', 'auth_code' => 'statistics'),
        array('title' => '모사율결과', 'uri' => '/webadmin/statistics/imitation_rate.html', 'auth_code' => 'statistics'),
        array('title' => 'OTP인증', 'uri' => '/webadmin/otp/list.html', 'auth_code' => 'otp')
    )
);
$menu[] = array(
    'title' => '콘텐츠관리',
    'sub' => array(
        array('title' => '과정리스트', 'uri' => '/webadmin/course/list.html', 'auth_code' => 'contents'),
        array('title' => '차시리스트', 'uri' => '/webadmin/course/occasion.html', 'auth_code' => 'contents'),
        array('title' => '페이지리스트', 'uri' => '/webadmin/course/page.html', 'auth_code' => 'contents'),
        array('title' => '시험문제은행', 'uri' => '/webadmin/bank/list.html', 'auth_code' => 'contents'),
        array('title' => '레포트문제은행', 'uri' => '/webadmin/bank/report.html', 'auth_code' => 'contents'),
        array('title' => '심사코드', 'uri' => '/webadmin/audit/list.html', 'auth_code' => 'contents')
    )
);
$menu[] = array(
    'title' => '내부게시판',
    'sub' => array(
        array('title' => '알파게시판', 'uri' => '/webadmin/notepad/list.html', 'auth_code' => 'board'),
        array('title' => '알파클라우드', 'uri' => '/webadmin/cloud/list.html', 'auth_code' => 'board'),
        array('title' => '캘린더', 'uri' => '/webadmin/schedule/list.html', 'auth_code' => 'board'),
        array('title' => '알리미', 'uri' => '/webadmin/alerts/list.html', 'auth_code' => 'board'),
        array('title' => '소식함', 'uri' => '/webadmin/news/list.html', 'auth_code' => 'board')
    )
);
$menu[] = array(
    'title' => '외부게시판',
    'sub' => array(
        array('title' => '1:1문의', 'uri' => '/webadmin/qna/list.html', 'auth_code' => 'board'),
        array('title' => '과정문의', 'uri' => '/webadmin/owner/list.html', 'auth_code' => 'board'),
        array('title' => '수강후기', 'uri' => '/webadmin/review/list.html', 'auth_code' => 'board'),
        array('title' => '공지사항', 'uri' => '/webadmin/notice/list.html', 'auth_code' => 'board'),
        array('title' => '자주묻는질문', 'uri' => '/webadmin/faq/list.html', 'auth_code' => 'board'),
        array('title' => '이벤트', 'uri' => '/webadmin/event/list.html', 'auth_code' => 'board'),
        array('title' => '작품과힐링', 'uri' => '/webadmin/gallery/list.html', 'auth_code' => 'board'),
        array('title' => '과정자료실', 'uri' => '/webadmin/reference/list.html', 'auth_code' => 'board'),
        array('title' => '정보바다', 'uri' => '/webadmin/info/list.html', 'auth_code' => 'board'),
        array('title' => '의견게시판', 'uri' => '/webadmin/opinion/list.html', 'auth_code' => 'board')
    )
);
// 안전보건게시판 분리 220628 박금삼
$menu[] = array(
    'title' => '안전보건게시판',
    'sub' => array(
        array('title' => '안전보건공지사항', 'uri' => '/webadmin/snotice/list.html', 'auth_code' => 'board'),
        array('title' => '안전보건자료실', 'uri' => '/webadmin/safe/list.html', 'auth_code' => 'board'),
        array('title' => '관리감독자 교육 신청', 'uri' => '/webadmin/application/super_safe_list.html', 'auth_code' => 'board'),
        array('title' => '위험성평가 교육 신청', 'uri' => '/webadmin/application/risk_safe_list.html', 'auth_code' => 'board'),
        array('title' => '교육 일정 관리', 'uri' => '/webadmin/application/schedule management.html', 'auth_code' => 'board'),
        array('title' => '근로자 교육 신청', 'uri' => '/webadmin/application/worker_safe_list.html', 'auth_code' => 'board'),
        array('title' => '고객사 요청 교육 신청', 'uri' => '/webadmin/application/customer_safe_list.html', 'auth_code' => 'board')
    )
);
// 정산결과 열람은 윤지현, 박민주, 장혜진만 가능. 박민주 요청 yllee 231020
if ($member['mb_id'] == 'lucas' || $member['mb_id'] == 'berry' || $member['mb_id'] == 'julie' || $member['mb_id'] == 'alphaC') {
    $menu[] = array(
        'title' => '정산관리',
        'sub' => array(
            array('title' => '정산조회', 'uri' => '/webadmin/balance/enquiry_list.html', 'auth_code' => 'balance'),
            array('title' => '정산결과', 'uri' => '/webadmin/balance/list.html', 'auth_code' => 'balance')
        )
    );
} else {
    $menu[] = array(
        'title' => '정산관리',
        'sub' => array(
            array('title' => '정산조회', 'uri' => '/webadmin/balance/enquiry_list.html', 'auth_code' => 'balance')
        )
    );
}
$menu[] = array(
    'title' => '근태관리',
    'sub' => array(
        array('title' => '연차관리', 'uri' => '/webadmin/leave/list.html', 'auth_code' => 'balance'),
        array('title' => '연차설정', 'uri' => '/webadmin/leave/setting.html', 'auth_code' => 'balance'),
        array('title' => '출퇴근기록표', 'uri' => '/webadmin/commute/list.html', 'auth_code' => 'balance')
    )
);
$menu[] = array(
    'title' => '환경설정',
    'sub' => array(
        array('title' => '기본정보설정', 'uri' => '/webadmin/setting/base_info.html', 'auth_code' => 'setting'),
        array('title' => '약관정보설정', 'uri' => '/webadmin/setting/terms_info.html', 'auth_code' => 'setting'),
        array('title' => '시험유의사항', 'uri' => '/webadmin/setting/exam_info.html', 'auth_code' => 'setting'),
        array('title' => '결제정보', 'uri' => '/webadmin/setting/payment.html', 'auth_code' => 'setting'),
        array('title' => '팝업관리', 'uri' => '/webadmin/popup/list.html', 'auth_code' => 'setting'),
        array('title' => '메인배너관리', 'uri' => '/webadmin/carousel/list.html', 'auth_code' => 'setting'),
        array('title' => '하단배너관리', 'uri' => '/webadmin/footer/list.html', 'auth_code' => 'setting'),
        array('title' => '관리자계정', 'uri' => '/webadmin/admin/list.html', 'auth_code' => 'member')
    )
);*/
