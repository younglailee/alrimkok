<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\UserCompany;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oUser = new UserCompany();
$oUser->init();
$pk = $oUser->get('pk');

/* check auth */
if (!$oUser->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oUser->selectList();
$cnt_total = $oUser->get('cnt_total');

/* search condition */
$search_like_arr = $oUser->get('search_like_arr');
$search_date_arr = $oUser->get('search_date_arr');
$query_string = $oUser->get('query_string');

/* pagination */
$page = $oUser->get('page');
$page_arr = $oUser->getPageArray();

/* code */
$mn_layout_arr = $oUser->get('mn_layout_arr');
$mn_depth_arr = $oUser->get('mn_depth_arr');
$year_arr = $oUser->get('year_arr');
$month_arr = $oUser->get('month_arr');
$date_arr = $oUser->get('date_arr');
$flag_auth_arr = $oUser->get('flag_auth_arr');
$mb_level_arr = $oUser->get('mb_level_arr');
$mb_no_login_arr = $oUser->get('mb_no_login_arr');
$cnt_rows_arr = $oUser->get('cnt_rows_arr');
$order_arr = $oUser->get('order_arr');
$flag_use_arr = $oUser->get('flag_use_arr');
$flag_test_arr = $oUser->get('flag_test_arr');
$flag_book_arr = $oUser->get('flag_book_arr');
$flag_live_arr = $oUser->get('flag_live_arr');
$flag_tomocard_arr = $oUser->get('flag_tomocard_arr');

$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_flag_auth = $_GET['sch_flag_auth'];
$sch_date = $_GET['sch_date'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$sch_mb_level = $_GET['sch_mb_level'];
$sch_flag_test = $_GET['sch_flag_test'];
$sch_flag_use = $_GET['sch_flag_use'];
$sch_flag_book = $_GET['sch_flag_book'];
$sch_flag_live = $_GET['sch_flag_live'];
$sch_company = $_GET['sch_company'];
$sch_flag_tomocard = $_GET['sch_flag_tomocard'];

global $member;
$mb_id = $member['mb_id'];
// 경남TP 시험성적서 발급을 위해 주석처리 yllee 230718
//$mb_flag_test = $member['flag_test'];

if ($mb_flag_test == 'Y') {
    $list = array(
        '0' => array(
            'no' => '5',
            'mb_level' => '1',
            'mb_id' => 'alpha',
            'mb_name' => '홍길동',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_book' => 'Y',
            'flag_live' => '',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '1' => array(
            'no' => '4',
            'mb_level' => '1',
            'mb_id' => 'alpha_test2',
            'mb_name' => '임꺽정',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_book' => '',
            'flag_live' => 'Y',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '2' => array(
            'no' => '3',
            'mb_level' => '1',
            'mb_id' => 'alpha_test3',
            'mb_name' => '이순신',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_book' => '',
            'flag_live' => '',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '3' => array(
            'no' => '2',
            'mb_level' => '1',
            'mb_id' => 'alpha_test4',
            'mb_name' => '정약용',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_book' => 'Y',
            'flag_live' => 'Y',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '4' => array(
            'no' => '1',
            'mb_level' => '1',
            'mb_id' => 'alpha_test5',
            'mb_name' => '유관순',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_book' => 'Y',
            'flag_live' => 'Y',
            'bt_reg_date' => '2020-10-14 19:02:23'
        )
    );
    $cnt_total = 1;
    //print_r($member);
}
if ($sch_flag_book) {
    $query_string .= '&sch_flag_book=' . $sch_flag_book;
}
if ($sch_flag_live) {
    $query_string .= '&sch_flag_live=' . $sch_flag_live;
}
if ($sch_company) {
    $query_string .= '&sch_company=' . $sch_company;
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $("#resetBtn").click(function() {
        if (confirm('비밀번호를 1234로 초기화하시겠습니까?')) {
            var mb_id_arr = Array();
            var send_cnt = 0;
            var chkbox = $(".list_checkbox");

            for (var i = 0; i < chkbox.length; i++) {
                if (chkbox[i].checked === true) {
                    mb_id_arr[send_cnt] = chkbox[i].value;
                    send_cnt++;
                }
            }
            $.ajax({
                url: "process.html",
                type: "GET",
                dataType: "json",
                data: {
                    flag_json: '1',
                    mode: 'reset_pw',
                    mb_id_arr: mb_id_arr
                },
                success: function(result) {
                    alert('초기화에 성공하였습니다.');
                }
            });
        }
    });

    $(".authBtn").click(function() {
        var auth = $(this).attr('data-auth');

        var confirmMsg = '';

        if (auth === 'A') {
            confirmMsg = '본인인증을 수기인증으로 일괄 수정하시겠습니까?';
        } else if (auth === 'N') {
            confirmMsg = '본인인증을 미인증으로 일괄 수정하시겠습니까?';
        }
        if (confirm(confirmMsg)) {
            var mb_id_arr = Array();
            var send_cnt = 0;
            var chkbox = $(".list_checkbox");

            for (var i = 0; i < chkbox.length; i++) {
                if (chkbox[i].checked === true) {
                    mb_id_arr[send_cnt] = chkbox[i].value;
                    send_cnt++;
                }
            }
            $.ajax({
                url: "process.html",
                type: "GET",
                dataType: "json",
                data: {
                    flag_json: '1',
                    mode: 'update_auth',
                    mb_id_arr: mb_id_arr,
                    auth: auth
                },
                success: function(result) {
                    alert('변경을 완료했습니다.');
                    location.reload();
                }
            });
        }
    });

    $(document).on("click", "a.btn_send_sms", function(e) {
        var f = document.list_form;
        if ($("input.list_checkbox", f).filter(":checked").length === 0) {
            alert("문자를 발송할 수강생을 선택해주세요.");
            return false;
        }
        //console.log($("input.list_checkbox", f).filter(":checked"));
        var mb_ids = "";
        console.log(mb_ids);
        $("input.list_checkbox", f).filter(function() {
            if ($(this).is(":checked")) {
                mb_ids = mb_ids + $(this).val() + "|";
                console.log(mb_ids);
            }
        });
        $(this).attr("href", "./popup.send_sms.html?mb_ids=" + mb_ids);
        getContentsbyAjax(this);
        e.preventDefault();
    });
    // 테스트 환경 알림
    $('.ready').click(function(e) {
        alert('테스트 환경에서는 이용할 수 없습니다.');
        e.preventDefault();
        return false;
    });
});
//]]>
</script>
<div id="<?= $module ?>" style="width: 1300px">
    <div class="search">
        <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_order" value="<?= $sch_order ?>"/>
        <input type="hidden" name="sch_cnt_rows" value="<?= $sch_cnt_rows ?>"/>
        <table class="search_table">
        <caption>검색조건</caption>
        <colgroup>
        <col style="width:90px"/>
        <col style="width:322px"/>
        <col style="width:90px"/>
        <col style="width:220px"/>
        <col style="width:90px"/>
        <col style="width:180px"/>
        <col style="width:90px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_mb_level">회원구분</label></th>
            <td>
                <select name="sch_mb_level" id="sch_mb_level" class="select" title="회원구분">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($mb_level_arr, $sch_mb_level, 1) ?>
                </select>
            </td>
            <th><label for="sch_mb_auth">본인인증</label></th>
            <td>
                <select name="sch_flag_auth" id="sch_mb_auth" class="select" title="본인인증">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($flag_auth_arr, $sch_flag_auth, 1) ?>
                </select>
            </td>
            <th><label for="sch_flag_use">재직여부</label></th>
            <td>
                <select name="sch_flag_use" id="sch_flag_use" class="select" title="재직여부">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($flag_use_arr, $sch_flag_use, 1) ?>
                </select>
            </td>
            <th><label for="sch_flag_book">북러닝</label></th>
            <td>
                <select name="sch_flag_book" id="sch_flag_book" class="select" title="북러닝여부">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($flag_book_arr, $sch_flag_book, 1) ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text"
                       size="26"
                       maxlength="30" title="검색어"/>
            </td>
            <th><label for="sch_s_date">기간</label></th>
            <td colspan="5">
                <select name="sch_date" class="select" title="기간컬럼">
                <?= Html::makeSelectOptions($search_date_arr, $sch_date, 1) ?>
                </select>
                <select name="sch_year" id="sch_year" class="select" title="년">
                <option value="">년</option>
                <?= Html::makeSelectOptions($year_arr, $sch_year, 1) ?>
                </select>
                <select name="sch_month" id="sch_month" class="select" title="월">
                <option value="">월</option>
                <?= Html::makeSelectOptions($month_arr, $sch_month, 1) ?>
                </select>
                <input type="text" name="sch_s_date" value="<?= $sch_s_date ?>" class="text date" size="10"
                       maxlength="10" title="시작일"/>
                ~
                <input type="text" name="sch_e_date" value="<?= $sch_e_date ?>" class="text date" size="10"
                       maxlength="10" title="종료일"/>
                <?= Html::makePeriodAnchor($date_arr, $sch_s_date, $sch_e_date) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <button type="submit" class="sButton info" title="검색">검 색</button>
            <a href="./list.html" class="sButton" title="초기화">초기화</a>
        </div>
        </form>
    </div>
    <div class="list">
        <div class="list_header">
            <div class="left">
                <i class="xi-file-text-o"></i> Total : <strong><?= number_format($cnt_total) ?></strong> 건, 현재 :
                <strong><?= number_format($page) ?></strong> 페이지
            </div>
            <div class="right">
                <select name="sch_order" class="select change_order" title="정렬순서">
                <?= Html::makeSelectOptions($order_arr, $sch_order, 1) ?>
                </select>
                <select name="sch_cnt_rows" class="select change_order" title="출력갯수">
                <?= Html::makeSelectOptions($cnt_rows_arr, $sch_cnt_rows, 1) ?>
                </select>
            </div>
        </div>
        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="delete"/>
        <fieldset>
        <legend>자료목록</legend>
        <table class="list_table border odd">
        <colgroup>
        <col style="width:50px"/>
        <col style="width:100px"/>
        <col style="width:100px"/>
        <col style="width:100px"/>
        <col/>
        <col style="width:100px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:100px"/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>구분</th>
            <th>아이디</th>
            <th>이름</th>
            <th>기업명</th>
            <th>휴대폰번호</th>
            <th>본인인증</th>
            <th>재직여부</th>
            <th>북러닝</th>
            <th>화상교육</th>
            <th>가입일</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($list); $i++) {
            // 회원 구분
            $mb_level = $list[$i]['mb_level'];
            $txt_level = $mb_level_arr[$mb_level];
            // 본인인증
            $flag_auth = $list[$i]['flag_auth'];
            $txt_auth = $flag_auth_arr[$flag_auth];
            if (!$flag_auth) {
                $txt_auth = '-';
            }

            if($list[$i]['flag_tomocard'] == 'Y' && $list[$i]['cp_name'] != '내일배움'){
                $list[$i]['cp_name'] = $list[$i]['cp_name'] . "|내일배움";
            }

            $cp_name_arr = explode('|', $list[$i]['cp_name']);

            $mb_id_arr = explode('@',$list[$i][$pk]);

            $txt_mb_id =  $list[$i][$pk];

            if(count($mb_id_arr) == 2){
                $txt_mb_id = $mb_id_arr[0] . '<br/>@' . $mb_id_arr[1];
            }

            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $txt_level ?></td>
                <td>
                    <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $txt_mb_id ?></a>
                </td>
                <td>

                    <a href="./crm.html?mb_id=<?= $list[$i][$pk] ?>"
                       target="_blank"><?= $list[$i]['mb_name'] ?></a>
                </td>
                <td class=""><? for ($j = 0; $j < count($cp_name_arr); $j++) { ?>
                        <?= $cp_name_arr[$j] ?><br/>
                    <? } ?></td>
                <td><?= Html::beautifyTel($list[$i]['mb_tel']) ?></td>
                <td><?= $txt_auth ?></td>
                <td><?= $flag_use_arr[$list[$i]['flag_use']] ?></td>
                <td><?= $list[$i]['flag_book'] == 'Y' ? '사용' : '미사용' ?></td>
                <td><?= $list[$i]['flag_live'] == 'Y' ? '사용' : '미사용' ?></td>
                <td><?= $list[$i]['bt_reg_date'] ?></td>
            </tr>
            <?php
        }
        echo !count($list) ? Html::makeNoTd(11) : null;
        ?>
        </tbody>
        </table>
        <div class="list_footer">
            <div class="left">
            </div>
            <div class="right">
            </div>
        </div>
        </form>
        <div class="pagination">
            <ul>
            <?= Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
    </div>
</div>
