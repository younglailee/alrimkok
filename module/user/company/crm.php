<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/

use sFramework\ContentsAdmin;
use sFramework\CourseAdmin;
use sFramework\Db;
use sFramework\ExamAdmin;
use sFramework\Html;
use sFramework\ProgressAdmin;
use sFramework\UserCompany;
use sFramework\VisitAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout, $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oUser = new UserCompany();
$oUser->init();
$pk = $oUser->get('pk');
$uid = $_GET['mb_id'];

/* check auth */
if (!$oUser->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
//$flag_use_head = false;
$flag_use_header = false;
$flag_use_footer = false;

/* data */
$uid = $oUser->get('uid');
$data = $oUser->selectDetail($uid);

/* search condition */
$search_like_arr = $oUser->get('search_like_arr');

/* code */
$mb_level_arr = $oUser->get('mb_level_arr');
$flag_tomocard_arr = $oUser->get('flag_tomocard_arr');
$flag_use_arr = $oUser->get('flag_use_arr');
$flag_auth_arr = $oUser->get('flag_auth_arr');

/* file */
$max_file = $oUser->get('max_file');
$file_list = $data['file_list'];
$profile_img = $data['profile_img'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'flag_use' => 'Y'
    );
} else {
    $mode = 'update';
}

$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];

// 수강생 리스트
/*
$oUser = new UserCompany();
$oUser->init();
$us_list = $oUser->selectStudentList($sch_like, $sch_keyword);
*/
// 기수 정보
$bt_list = $oUser->selectListMbId($uid);

/* pagination */
$page = $oUser->get('page');
$stu_page = $oUser->get('stu_page');
$page_arr = $oUser->getPageArray();
$stu_page_arr = $oUser->getPageArrayStu();

$book_list = $oUser->selectListBook($uid);

if ($page == 1) {
    $query_string_stu = '&mb_id=' . $uid;
} else {
    $query_string_stu = '&mb_id=' . $uid . '&page=' . $page;
}

if ($stu_page == 1) {
    $query_string = '&mb_id=' . $uid;
} else {
    $query_string = '&mb_id=' . $uid . '&stu_page=' . $stu_page;
}

if ($sch_like) {
    $query_string = $query_string . '&sch_like=' . $sch_like;
    $query_string_stu = $query_string_stu . '&sch_like=' . $sch_like;
}

if ($sch_keyword) {
    $query_string = $query_string . '&sch_keyword=' . $sch_keyword;
    $query_string_stu = $query_string_stu . '&sch_keyword=' . $sch_keyword;
}
// 수강 이력
$oProgress = new ProgressAdmin();
$oProgress->init();
$progress_list = $oProgress->selectProgress($uid);
//print_r($progress_list);
$flag_complete_arr = $oProgress->get('flag_complete_arr');
//print_r($flag_complete_arr);

// 과정 정보
$oCourse = new CourseAdmin();
$oCourse->init();
$bt_type_arr = $oCourse->get('cs_refund_type_arr');

// 시험 정보
$oExam = new ExamAdmin();
$oExam->init();

// 접속 이력
$oVisit = new VisitAdmin();
$oVisit->init();
// 부하 발생으로 주석처리 yllee 230712
// CRM 접속이력 조회 불가로 주석 해제 -> 데이터 갯수 제한 추가 minju 230713
$vs_list = $oVisit->selectVisit($uid);
//$vs_list = array();

// 기수 클래스 호출(총점 계산 메소드 사용하기 위해) yllee 240104
$oContents = new ContentsAdmin();
$oContents->init();
?>
<script type="text/javascript">
//<![CDATA[
// 날짜 형식 변환 메소드 yllee 210603
Date.prototype.format = function(f) {
    if (!this.valueOf()) return " ";
    var weekKorName = ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"];
    var weekKorShortName = ["일", "월", "화", "수", "목", "금", "토"];
    var weekEngName = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var weekEngShortName = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    var d = this;
    return f.replace(/(yyyy|yy|MM|dd|KS|KL|ES|EL|HH|hh|mm|ss|a\/p)/gi, function($1) {
        switch ($1) {
            case "yyyy":
                return d.getFullYear(); // 년 (4자리)
            case "yy":
                return (d.getFullYear() % 1000).zf(2); // 년 (2자리)
            case "MM":
                return (d.getMonth() + 1).zf(2); // 월 (2자리)
            case "dd":
                return d.getDate().zf(2); // 일 (2자리)
            case "KS":
                return weekKorShortName[d.getDay()]; // 요일 (짧은 한글)
            case "KL":
                return weekKorName[d.getDay()]; // 요일 (긴 한글)
            case "ES":
                return weekEngShortName[d.getDay()]; // 요일 (짧은 영어)
            case "EL":
                return weekEngName[d.getDay()]; // 요일 (긴 영어)
            case "HH":
                return d.getHours().zf(2); // 시간 (24시간 기준, 2자리)
            case "hh":
                return ((h = d.getHours() % 12) ? h : 12).zf(2); // 시간 (12시간 기준, 2자리)
            case "mm":
                return d.getMinutes().zf(2); // 분 (2자리)
            case "ss":
                return d.getSeconds().zf(2); // 초 (2자리)
            case "a/p":
                return d.getHours() < 12 ? "오전" : "오후"; // 오전/오후 구분
            default:
                return $1;
        }
    });
};
String.prototype.string = function(len) {
    var s = '', i = 0;
    while (i++ < len) {
        s += this;
    }
    return s;
};
String.prototype.zf = function(len) {
    return "0".string(len - this.length) + this;
};
Number.prototype.zf = function(len) {
    return this.toString().zf(len);
};

$(function() {
    $(".cs_info").on('click', function(e) {
        e.preventDefault();

        var cs_id = $(this).attr('data-id');
        var mb_id = $(this).attr('data-mb');
        var no = $(this).attr('data-no');
        var bt_code = $(this).attr('data-bt');
        var oc_tr_no = $("#oc_tr_" + no);

        if (oc_tr_no.attr('class') === 'on') {
            oc_tr_no.html('');
            oc_tr_no.removeClass('on')
        } else {
            $.ajax({
                url: "process.html",
                type: "GET",
                dataType: "json",
                data: {
                    flag_json: '1',
                    mode: 'occasion_progress',
                    cs_id: cs_id,
                    mb_id: mb_id,
                    bt_code: bt_code
                },
                success: function(rs) {
                    //console.log(rs);
                    //console.log(rs.data);
                    var oc_data = rs.data;
                    var html = "";
                    html += "<td colspan='11'>";
                    html += "<table>";
                    html += "<colgroup>";
                    html += "<col style='width:50px'/>";
                    html += "<col style='*'/>";
                    html += "<col style='width:70px'/>";
                    html += "<col style='width:70px'/>";
                    html += "<col style='width:90px'/>";
                    html += "<col style='width:95px'/>";
                    html += "<col style='width:125px'/>";
                    html += "</colgroup>";
                    html += "<thead>";
                    html += "<tr>";
                    html += "<th>차시</th>";
                    html += "<th>차시명(" + oc_data.length + ")</th>";
                    html += "<th>학습여부</th>";
                    html += "<th>진도체크</th>";
                    html += "<th>학습시간(분)</th>";
                    html += "<th>학습IP</th>";
                    html += "<th>최초/최종접속</th>";
                    html += "</tr>";
                    html += "</thead>";
                    html += "<tbody>";

                    var j = 0;
                    var tm_data = "";
                    var tm_pg_num = 0;
                    var oc_totaltime = 0;
                    var oc_time = 0;
                    var progress_oc = "";
                    var tm_ip = "";
                    var reg_time = "";
                    var first_time = "";
                    var last_time = "";
                    var total_second = 0;
                    var first_time_var = "";

                    for (var i = 0; i < oc_data.length; i++) {
                        j = i + 1;
                        tm_data = oc_data[i].tm_data;
                        tm_pg_num = 0;
                        oc_totaltime = parseInt(oc_data[i].oc_time / 60);
                        oc_time = 0;
                        progress_oc = 'X';
                        tm_ip = '';
                        reg_time = '';
                        first_time = '';
                        last_time = '';
                        total_second = 0;

                        if (tm_data) {
                            if (tm_data.tm_pg_num) {
                                //console.log(tm_data);
                                tm_pg_num = tm_data.tm_pg_num;
                                oc_time = parseInt(tm_data.tm_totaltime / 60);
                            }
                            if (tm_data.tm_oc_check === 'Y') {
                                progress_oc = 'O';
                            }
                            if (tm_data.tm_ip) {
                                tm_ip = tm_data.tm_ip
                            }
                            if (tm_data.reg_time) {
                                reg_time = tm_data.reg_time
                            }
                            if (tm_data.first_time) {
                                first_time = tm_data.first_time
                            }
                            if (tm_data.last_time) {
                                last_time = tm_data.last_time
                            }
                            // 시작일시가 종료일시 보다 크면 등록일시 출력
                            if (first_time > last_time) {
                                first_time = reg_time;
                            }
                            // 최초 접속 일시로 변경 yllee 210304
                            first_time = reg_time;
                        }
                        // 차시 전체 페이지가 1페이지이면 최종접속시간 재계산 yllee 210603
                        if (parseInt(oc_data[i].oc_page) === 1) {
                            total_second = parseInt(tm_data.tm_totaltime);
                            first_time_var = new Date(first_time.replace("-", "/"));
                            last_time = new Date(first_time_var.setSeconds(first_time_var.getSeconds() + total_second));
                            last_time = last_time.format("yyyy-MM-dd HH:mm:ss");
                        }
                        html += "<tr>";
                        html += "<td>" + j + "차</td>";
                        html += "<td>" + oc_data[i].oc_name + "</td>";
                        html += "<td>" + progress_oc + "</td>";
                        html += "<td>" + tm_pg_num + "/" + oc_data[i].oc_page + "</td>";
                        html += "<td>" + oc_time + "/" + oc_totaltime + "</td>";
                        html += "<td>" + tm_ip + "</td>";
                        html += "<td>" + first_time + "<br/>" + last_time + "</td>";
                        html += "</tr>";
                    }
                    html += "</tbody>";
                    html += "</table>";
                    html += "</td>";

                    $("#oc_tr_" + no).html(html);
                    $("#oc_tr_" + no).addClass("on");
                }
            });
        }
    });
});

function popupOcInfo(mb_id, oc_num, bt_code, cs_id) {
    window.open("./crm_oc_info.html?mb_id=" + mb_id + "&oc_num=" + oc_num + "&bt_code=" + bt_code + "&cs_id=" + cs_id, 'crm', 'scrollbars=yes,width=920,height=600');
}

function showTab(id, obj) {
    $("#crm_list").hide();
    $("#visit_list").hide();
    $("#consult_list").hide();
    $("#sms_list").hide();
    $("#read_list").hide();
    //alert(id);
    $("#" + id).show();
    $("button.nav-link").removeClass("active");
    $(obj).addClass("active");
}

function toggleConsultView(no) {
    var bd_content_tr = $("#bd_content" + no);
    if (bd_content_tr.hasClass("on")) {
        bd_content_tr.removeClass("on");
        bd_content_tr.hide();
    } else {
        bd_content_tr.addClass("on");
        bd_content_tr.show();
    }
}

<?php
// CRM 수강이력 삭제 기능: pr_id -> bt_code, cs_code, mb_id 파라미터 전송 yllee 220628
?>
//function deleteProgressCRM(obj, pr_id) {
//pr_id: pr_id
function deleteProgressCRM(obj, bt_code, cs_code, mb_id) {
    var cs_name = $(obj).attr('data-cs');
    if (confirm(cs_name + ' 기수를 삭제하시겠습니까?')) {
        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'delete_progress_crm',
                bt_code: bt_code,
                cs_code: cs_code,
                mb_id: mb_id
            },
            success: function(result) {
                alert('삭제완료하였습니다.');
                location.reload();
            }
        });
    }
}

function deleteProgressCRMBook(obj, bt_code, cs_code, mb_id) {
    var cs_name = $(obj).attr('data-name');
    if (confirm(cs_name + ' 기수를 삭제하시겠습니까?')) {
        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'delete_progress_crm_book',
                bt_code: bt_code,
                cs_code: cs_code,
                mb_id: mb_id
            },
            success: function(result) {
                alert('삭제완료하였습니다.');
                location.reload();
            }
        });
    }
}
//]]>
</script>
<style>
.nav {
    display:flex;
    flex-wrap:wrap;
    padding-left:0;
    margin-bottom:0;
    list-style:none;
}
.nav-link {
    display:block;
    padding:0.5rem 1rem;
}
.nav-link:hover, .nav-link:focus {
    text-decoration:none;
}
.nav-link.disabled {
    color:#6c757d;
    pointer-events:none;
    cursor:default;
}
.nav-tabs {
    border-bottom:1px solid #dee2e6;
}
.nav-tabs .nav-item {
    margin-bottom:-1px;
}
.nav-tabs .nav-link {
    border:1px solid transparent;
    border-top-left-radius:0.25rem;
    border-top-right-radius:0.25rem;
}
.nav-tabs .nav-link:hover, .nav-tabs .nav-link:focus {
    border-color:#e9ecef #e9ecef #dee2e6;
}
.nav-tabs .nav-link.disabled {
    color:#6c757d;
    background-color:transparent;
    border-color:transparent;
}
.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color:#495057;
    background-color:#fff;
    border-color:#dee2e6 #dee2e6 #fff;
}
.nav-tabs .dropdown-menu {
    margin-top:-1px;
    border-top-left-radius:0;
    border-top-right-radius:0;
}
.nav-pills .nav-link {
    border-radius:0.25rem;
}
.nav-pills .nav-link.active,
.nav-pills .show > .nav-link {
    color:#fff;
    background-color:#007bff;
}
.nav-fill .nav-item {
    flex:1 1 auto;
    text-align:center;
}
.nav-justified .nav-item {
    flex-basis:0;
    flex-grow:1;
    text-align:center;
}
.tab-content > .tab-pane {
    display:none;
}
.tab-content > .active {
    display:block;
}
ul.nav {
    margin-top:30px;
    width:90%;
}
ul.nav li a.active {
    font-weight:bold;
}
</style>
<div id="<?= $module ?>" style="padding:20px;">
    <?php
    global $us_list;
    if ($us_list) {
        ?>
        <div style="float:left;width:300px">
            <form name="search_form" action="./crm.html" method="get">
            <input type="hidden" name="mb_id" value="<?= $uid ?>"/>

            <select name="sch_like" class="select" title="검색컬럼">
            <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
            </select>
            <input type="text" name="sch_keyword" id="sch_keyword" value="<?= $sch_keyword ?>" class="text" size="15"
                   maxlength="30" title="검색어"/>
            <button type="submit" class="sButton info small" title="검색">검 색</button>
            </form>
            <table class="search_table" id="search_user_list" style="width:100%">
            <caption>검색조건</caption>
            <colgroup>
            <col width="50%"/>
            <col width="*"/>
            </colgroup>
            <tbody>
            <?php
            // 테스트 계정 임시 배열
            global $member;
            $mb_flag_test = $member['flag_test'];
            if ($mb_flag_test == 'Y') {
                $us_list = array(
                    '0' => array(
                        'mb_id' => 'alpha',
                        'mb_name' => '홍길동'
                    ),
                    '1' => array(
                        'mb_id' => 'alpha_test2',
                        'mb_name' => '임꺽정'
                    ),
                    '2' => array(
                        'mb_id' => 'alpha_test3',
                        'mb_name' => '이순신'
                    ),
                    '3' => array(
                        'mb_id' => 'alpha_test4',
                        'mb_name' => '정약용'
                    ),
                    '4' => array(
                        'mb_id' => 'alpha_test5',
                        'mb_name' => '유관순'
                    )
                );
                $stu_page_arr = [];
            }
            for ($i = 0; $i < count($us_list); $i++) {
                ?>
                <tr>
                    <td><a href="./crm.html?mb_id=<?= $us_list[$i]['mb_id'] ?>"
                           title="<?= $us_list[$i]['mb_tel'] ?>"><?= $us_list[$i]['mb_id'] ?></a></td>
                    <td><a href="./crm.html?mb_id=<?= $us_list[$i]['mb_id'] ?>"
                           title="<?= $us_list[$i]['mb_tel'] ?>"><?= $us_list[$i]['mb_name'] ?></a></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
            </table>
            <div class="pagination">
                <ul>
                <?= $oUser->makePaginationStu($stu_page_arr, $query_string_stu); ?>
                </ul>
            </div>
        </div>
        <?php
    }
    // 외부 DB 연동 기업 수강생 정보 출력 yllee 220126
    if (!$data['mb_name']) {
        $data['mb_name'] = $progress_list[0]['us_name'];
        $data['mb_id'] = $progress_list[0]['us_id'];
        $data['cp_name'] = $progress_list[0]['cp_name'];
    }
    // 동아대의료원 등 외부 DB 연동 기업 수강생 정보가 출력 되도록 위치 변경 yllee 230404
    // 문서 제목 출력 yllee 221202
    $html_title = $data['mb_name'] . '(' . $data['cp_name'] . ')';

    // 페이지 넓이 990 -> 1300 수정 yllee 221202
    ?>
    <div class="write" style="float:left;width:1300px">
        <fieldset>
        <legend>기본 정보</legend>
        <h4>기본 정보</h4>
        <table class="write_table">
        <caption>CRM 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th>이름</th>
            <td><?= $data['mb_name'] ?></td>
            <th>연락처</th>
            <td>
                <?= $data['mb_tel'] ?>
            </td>
        </tr>
        <tr>
            <th>아이디</th>
            <td><?= $data['mb_id'] ?></td>
            <th>생년월일</th>
            <td><?= $data['mb_birthday'] ?></td>
        </tr>
        <tr>
            <th>소속기업</th>
            <td><?= $data['cp_name'] ?></td>
            <th>이메일</th>
            <td><?= $data['mb_email'] ?></td>
        </tr>
        <tr>
            <th>회원가입일시</th>
            <td><?= $data['reg_time'] ?></td>
            <th>본인인증</th>
            <td><?= $flag_auth_arr[$data['flag_auth']] ?></td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <ul class="nav nav-tabs" style="position:relative;z-index:99;">
        <li class="nav-item">
            <button class="nav-link active" onclick="showTab('crm_list', this)">수강이력</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" onclick="showTab('visit_list', this)">접속이력</button>
        </li>
        </ul>

        <fieldset class="" id="crm_list">
        <legend>수강 이력</legend>
        <table class="list_table border odd">
        <colgroup>
        <col style="width:50px"/>
        <col style="width:70px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col/>
        <col style="width:70px"/>
        <col style="width:70px"/>
        <col style="width:70px"/>
        <col style="width:70px"/>
        <col style="width:70px"/>
        <col style="width:70px"/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>구분</th>
            <th>시작일</th>
            <th>종료일</th>
            <th>과정명</th>
            <th>진도율</th>
            <th>중간평가</th>
            <th>최종시험</th>
            <th>레포트</th>
            <th>합계</th>
            <th>수료</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($bt_list); $i++) {
            $bt_code = $bt_list[$i]['bt_code'];
            $cs_code = $bt_list[$i]['cs_code'];
            $bt_type = $bt_list[$i]['bt_type'];
            $txt_bt_type = $bt_type_arr[$bt_type];
            $cs_data = $bt_list[$i]['cs_data'];
            // 시험 정보
            /*
            $oExam->set('bn_division', 'exam_midterm');
            $oExam->set('bt_code', $bt_code);
            $er_data = $oExam->selectResult($uid);
            */
            //print_r($bt_list[$i]);
            $bt_end_date = $bt_list[$i]['bt_e_date'];
            $bt_end_date_arr = explode('-', $bt_end_date);
            $bt_end_year = $bt_end_date_arr[0];

            // 미응시
            $txt_mid = '미응시';
            $txt_fin = '미응시';
            $txt_report = '미응시';

            $mid_data = $bt_list[$i]['mid_data'];
            $fin_data = $bt_list[$i]['fin_data'];
            $rep_data = $bt_list[$i]['report_data'];
            $total_score = 0;

            $calc_middle_score = 0;
            $calc_final_score = 0;
            $calc_report_score = 0;

            if ($mid_data) {
                $link_mid = '../exam/view.html?er_id=' . $mid_data['er_id'] . '&er_type=exam_middle&exam_year=';
                $er_score = $mid_data['er_score'];
                // 중간평가 0점 표시 되도롯 er_score 연산자 변경 yllee 220725
                if ($mid_data['er_id'] && $er_score == '') {
                    $er_score = '응시중';
                }
                $txt_mid = $er_score;
            } else {
                // 시험(중간평가) 응시 데이터 연도별 추출 yllee 220210
                $mb_id = ($_GET['mb_id']) ? $_GET['mb_id'] : $_POST['mb_id'];
                $db_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code = '$bt_code' AND er_type = 'exam_midterm' AND flag_delete IS NULL";
                $exam_data['mid'] = Db::selectOnce('tbl_exam_result_' . $bt_end_year, '*', $db_where, '');
                $link_mid = '../exam/view.html?er_id=' . $exam_data['mid']['er_id'] . '&er_type=exam_middle&exam_year=';
                $er_score = $exam_data['mid']['er_score'];
                if ($exam_data['mid']['er_id'] && !$er_score) {
                    $er_score = '응시중';
                }
                // CRM 중간평가 미응시 출력 X 아래 코드 주석처리 minju 230210
                //$txt_mid = '<a href="' . $link_mid . '" target="_blank">' . $er_score . '</a>';
            }
            /*
            if ($i == 6) {
                print_r($fin_data);
            }
            */
            if ($fin_data) {
                // 최종시험 2020년 이전 데이터 확인 링크 적용 yllee 220603
                $link_fin = '../exam/view.html?er_id=' . $fin_data['er_id'] . '&er_type=exam_final&exam_year=';
                // 교육 종료일 2021년부터 체크 yllee 220711
                // 시험 제출일시가 있으면 채점중 표시 추가 (박금삼) 220714
                if (($bt_end_year >= '2021') && (!$fin_data['er_scoring_time'] || $fin_data['er_scoring_time'] == '0000-00-00 00:00:00') && (strpos($fin_data['er_bn_type'], 'sa') || strpos($fin_data['er_bn_type'], 'ds')) && $fin_data['er_submit_time']) {
                    $er_score = '채점중';
                    $txt_fin = '<a href="' . $link_fin . '" target="_blank">' . $er_score . '</a>';
                    $calc_final_score = 0;
                } else {
                    // 채점 완료된 최종시험 점수 대신 [응시중]으로 나오는 문제 해결: 최종시험 점수 배열 값 출력 yllee 210730
                    //print_r($fin_data);
                    //$txt_fin = $bt_list[$i]['fin_score'];;
                    $er_score = $fin_data['er_score'];
                    //print_r($fin_data);
                    if ($fin_data['er_id'] && !$er_score && $er_score != 0) {
                        $er_score = '응시중';
                    }
                    if ($er_score == '') {
                        $er_score = '미제출';
                    }
                    $txt_fin = '' . $er_score . '';
                    $calc_final_score = $bt_list[$i]['fin_data']['er_score'] * $bt_list[$i]['cs_data']['cs_rate_exam'] / 100;
                }
            } else {
                // 시험(최종시험) 응시 데이터 연도별 추출 yllee 220210
                $mb_id = ($_GET['mb_id']) ? $_GET['mb_id'] : $_POST['mb_id'];
                $db_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code = '$bt_code' AND er_type = 'exam_final' AND flag_delete IS NULL";
                $exam_data['fin'] = Db::selectOnce('tbl_exam_result_' . $bt_end_year, '*', $db_where, '');
                $er_score = $exam_data['fin']['er_score'];
                /*
                print_r($exam_data);
                echo $cs_code;
                echo $txt_fin;
                echo $er_score;
                */
                // 최종시험 점수 0점도 포함 yllee 220711
                if ($er_score && $er_score >= 0) {
                    $txt_fin = $er_score;
                    $calc_final_score = $er_score * $bt_list[$i]['cs_data']['cs_rate_exam'] / 100;
                }
            }
            if ($rep_data) {
                if (!$rep_data['rr_scoring_time']) {
                    $txt_report = '채점중';
                } else {
                    $txt_report = $rep_data['rr_score'];
                }
            }
            // 합계
            $calc_middle_score = $bt_list[$i]['mid_data']['er_score'] * $cs_data['cs_rate_exam_middle'] / 100;
            $calc_report_score = $bt_list[$i]['report_data']['rr_score'] * $cs_data['cs_rate_report'] / 100;

            if (!$cs_data['cs_rate_exam_middle'] || $cs_data['cs_rate_exam_middle'] == 0) {
                $txt_mid = '-';
                $calc_middle_score = 0;
            }
            if (!$cs_data['cs_rate_exam'] || $cs_data['cs_rate_exam'] == 0) {
                $txt_fin = '-';
                $calc_final_score = 0;
            }
            if (!$cs_data['cs_rate_report'] || $cs_data['cs_rate_report'] == 0) {
                $txt_report = '-';
                $calc_report_score = 0;
            }
            //$total_score = $calc_middle_score + $calc_final_score + $calc_report_score;
            // 총점 계산 메소드 적용 yllee 240104
            $score_data = array(
                'cs_rate_progress' => $cs_data['cs_rate_progress'],
                'cs_rate_exam_middle' => $cs_data['cs_rate_exam_middle'],
                'cs_rate_exam' => $cs_data['cs_rate_exam'],
                'cs_rate_report' => $cs_data['cs_rate_report'],
                'rate_progress' => $bt_list[$i]['rate_progress'],
                'mid_score' => $bt_list[$i]['mid_data']['er_score'],
                'fin_score' => $er_score,
                'rep_score' => $bt_list[$i]['report_data']['rr_score']
            );
            $total_score = $oContents->totalScore($score_data);

            if ($txt_mid == '') {
                $txt_mid = '응시중';
            }
            //echo '[' . $txt_fin  . ']';
            if ($txt_fin == '') {
                $txt_fin = '응시중';
            }
            if ($txt_report == '') {
                $txt_report = '응시중';
            }
            // CRM에서 해당 수강생의 기수 진도 데이터 삭제 파라미터 수정 yllee 220628
            $bt_code = $bt_list[$i]['bt_code'];
            $cs_code = $bt_list[$i]['cs_code'];
            ?>
            <tr class="list_tr_<?= $bt_list[$i]['odd'] ?>">
                <td><?= $bt_list[$i]['no'] ?></td>
                <td><?= $txt_bt_type ?></td>
                <td><?= $bt_list[$i]['bt_s_date'] ?></td>
                <td><?= $bt_list[$i]['bt_e_date'] ?></td>
                <td class="subject">
                    <a href="./crm.html?mb_id=<?= $uid ?>&cs_code=<?= $bt_list[$i]['cs_code'] ?>" class="cs_info"
                       data-id="<?= $bt_list[$i]['bt_course'] ?>"
                       data-mb="<?= $uid ?>"
                       data-no="<?= $i ?>"
                       data-bt="<?= $bt_code ?>"><?= $cs_data['cs_name'] ?></a>
                </td>
                <td><?= $bt_list[$i]['rate_progress'] ?>%</td>
                <td><?= $txt_mid ?></td>
                <td><?= $txt_fin ?></td>
                <td><?= $txt_report ?></td>
                <td><?= $total_score ?></td>
                <td><?= $flag_complete_arr[$bt_list[$i]['flag_complete']] ?></td>
            </tr>
            <tr id="oc_tr_<?= $i ?>">
            </tr>
            <?php
        }
        echo !count($bt_list) ? Html::makeNoTd(11) : null;
        ?>
        </tbody>
        </table>
        <div class="pagination">
            <ul>
            <?= $oUser->makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
        </fieldset>


        <fieldset class="" id="visit_list" style="display:none">
        <legend>접속 이력</legend>
        <table class="list_table border odd">
        <colgroup>
        <col style="width:50px"/>
        <col style="width:140px"/>
        <col style="width:120px"/>
        <col style="width:90px"/>
        <col style="width:90px"/>
        <col style="width:100px"/>
        <col/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>접속일시</th>
            <th>아이피</th>
            <th>디바이스</th>
            <th>운영체제</th>
            <th>브라우저</th>
            <th>접속경로</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($vs_list); $i++) {
            $vs_device = $vs_list[$i]['vs_device'];
            if ($vs_device == 'Desktop') {
                $vs_device = 'PC';
            }
            ?>
            <tr class="list_tr_<?= $vs_list[$i]['odd'] ?>">
                <td><?= $vs_list[$i]['no'] ?></td>
                <td><?= $vs_list[$i]['reg_time'] ?></td>
                <td>
                    <?= $vs_list[$i]['vs_ip'] ?>
                </td>
                <td><?= $vs_device ?></td>
                <td><?= $vs_list[$i]['vs_os'] ?></td>
                <td><?= $vs_list[$i]['vs_browser'] ?></td>
                <td class="subject"><?= $vs_list[$i]['vs_referer'] ?></td>
            </tr>
            <?php
        }
        echo !count($vs_list) ? Html::makeNoTd(7) : null;
        ?>
        </tbody>
        </table>
        </fieldset>
    </div>
</div>
<form name="check_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="mb_id" value=""/>
<input type="hidden" name="mb_pw" value=""/>
</form>
<div id="layer_back"></div>
<div id="layer_popup">
    <div id="layer_header">
        <h1>레이어팝업</h1>
        <button type="button" onclick="closeLayerPopup()" title="닫기"><i class="xi-close-square"></i></button>
    </div>
    <div id="layer_content">
        레이어팝업 내용
    </div>
</div>
<div id="layer_loading">
    <p>
        <i class="xi-spinner-4 xi-spin"></i>
        <br/>
        <strong id="loading_state">잠시만 기다려주세요.</strong>
    </p>
</div>
