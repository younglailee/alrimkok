<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Format;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();
$pk = $oCompany->get('pk');

/* check auth */
if (!$oCompany->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oCompany->selectList();
$cnt_total = $oCompany->get('cnt_total');

/* search condition */
$search_like_arr = $oCompany->get('search_like_arr');
$search_date_arr = $oCompany->get('search_date_arr');
$query_string = $oCompany->get('query_string');

/* pagination */
$page = $oCompany->get('page');
$page_arr = $oCompany->getPageArray();

/* code */
$mn_layout_arr = $oCompany->get('mn_layout_arr');
$mn_depth_arr = $oCompany->get('mn_depth_arr');
$year_arr = $oCompany->get('year_arr');
$month_arr = $oCompany->get('month_arr');
$date_arr = $oCompany->get('date_arr');
$flag_auth_arr = $oCompany->get('flag_auth_arr');
$mb_level_arr = $oCompany->get('mb_level_arr');
$mb_no_login_arr = $oCompany->get('mb_no_login_arr');
$cnt_rows_arr = $oCompany->get('cnt_rows_arr');
$order_arr = $oCompany->get('order_arr');
$cp_type_arr = $oCompany->get('cp_type_arr');
$flag_book_arr = $oCompany->get('flag_book_arr');
$flag_live_arr = $oCompany->get('flag_live_arr');

$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_flag_auth = $_GET['sch_flag_auth'];
$sch_date = $_GET['sch_date'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$sch_mb_level = $_GET['sch_mb_level'];
$sch_flag_book = $_GET['sch_flag_book'];
$sch_flag_live = $_GET['sch_flag_live'];
$sch_partner = $_GET['sch_partner'];
$sch_like = $_GET['sch_like'];
$sch_text = $_GET['sch_text'];

$query_string .= '&sch_partner=' . $sch_partner;
if ($sch_flag_book) {
    $query_string .= '&sch_flag_book=' . $sch_flag_book;
}
if ($sch_flag_live) {
    $query_string .= '&sch_flag_live=' . $sch_flag_live;
}
global $member;
$mb_id = $member['mb_id'];
if ($mb_id == 'alphatest') {
    $list = array();
    $cnt_total = 0;
}
// 테스트 배열
$mb_flag_test = $member['flag_test'];
if ($mb_flag_test == 'Y') {
    $list = array(
        '0' => array(
            'no' => '3',
            'cp_id' => '1502260229',
            'cp_type' => 'priority_support',
            'cp_name' => '(주)알파에듀',
            'cp_number' => '4928100339',
            'cp_tel' => '0552556364',
            'staff_name' => '서혜미',
            'staff_position' => '대리',
            'partner_name' => '장재선'
        ),
        '1' => array(
            'no' => '2',
            'cp_id' => '1602908890',
            'cp_type' => 'priority_support',
            'cp_name' => '혜미컴퍼니',
            'cp_number' => '1234567890',
            'cp_tel' => '055-111-1112',
            'staff_name' => '혜미기업관리자',
            'staff_position' => '',
            'partner_name' => '장재선',
            'flag_live' => 'Y'
        ),
        '2' => array(
            'no' => '1',
            'cp_id' => '1482940026',
            'cp_type' => '',
            'cp_name' => '직업능력심사평가원',
            'cp_number' => '1234567890',
            'cp_tel' => '1644-5113',
            'staff_name' => '김민경',
            'staff_position' => '담당',
            'partner_name' => '',
            'flag_book' => 'Y'
        )
    );
}

global $module;
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $(".cp_pw_reset_btn").click(function(e) {
        e.preventDefault();
        if (confirm('비밀번호를 1234로 초기화하시겠습니까?')) {
            var cp_id = $(this).attr('data-id');
            $.ajax({
                url: "process.html",
                type: "GET",
                dataType: "json",
                data: {
                    flag_json: '1',
                    mode: 'reset_pw',
                    cp_id: cp_id
                },
                success: function(rs) {
                    alert(rs.msg);
                }
            });
        }
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
<div id="<?= $module ?>" style="width: 1400px">
    <div class="search">
        <form name="search_form" action="./list.ip.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_order" value="<?= $sch_order ?>"/>
        <input type="hidden" name="sch_cnt_rows" value="<?= $sch_cnt_rows ?>"/>
        <table class="search_table">
        <caption>검색조건</caption>
        <colgroup>
        <col style="width:90px"/>
        <col style="width:400px"/>
        <col style="width:90px"/>
        <col style="width:250px"/>
        <col style="width:70px"/>
        <col/>
        </colgroup>
        <tbody>
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
            <th><label for="sch_partner">파트너</label></th>
            <td>
                <?= Html::makeInputText('sch_partner', '파트너', $sch_partner, '', 30, 10) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <button type="submit" class="sButton info" title="검색">검 색</button>
            <a href="./list.ip.html" class="sButton" title="초기화">초기화</a>
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
        <col style="width:90px"/>
        <col/>
        <col style="width:120px"/>
        <col style="width:110px"/>
        <col style="width:80px"/>
        <col style="width:100px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:100px"/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>기업코드</th>
            <th>기업구분</th>
            <th>기업명</th>
            <th>사업자등록번호</th>
            <th>대표번호</th>
            <th>담당자명</th>
            <th>직급</th>
            <th>파트너명</th>
            <th>북러닝</th>
            <th>화상교육</th>
            <th>관리</th>
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
            //echo $list[$i]['cp_tel'];
            $view_url = './view.ip.html?' . $pk . '=' . $list[$i][$pk] . $query_string;
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $list[$i]['cp_id'] ?></td>
                <td><?= $cp_type_arr[$list[$i]['cp_type']] ?></td>
                <td class="">
                    <a href="<?= $view_url ?>"><?= $list[$i]['cp_name'] ?></a>
                </td>
                <td><?= $list[$i]['cp_number'] ?></td>
                <td><?= Html::beautifyTel($list[$i]['cp_tel']) ?></td>
                <td><?= Format::getWithoutNull($list[$i]['staff_name']) ?></td>
                <td><?= Format::getWithoutNull($list[$i]['staff_position']) ?></td>
                <td><?= Format::getWithoutNull($list[$i]['partner_name']) ?></td>
                <td><?= $list[$i]['flag_book'] == 'Y' ? '사용' : '미사용' ?></td>
                <td><?= $list[$i]['flag_live'] == 'Y' ? '사용' : '미사용' ?></td>
                <td class="button">
                    <a href="<?= $view_url ?>"
                       class="sButton tiny" title="접속IP현황">접속IP현황</a>
                </td>
            </tr>
            <?php
        }
        echo !count($list) ? Html::makeNoTd(12) : null;
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
