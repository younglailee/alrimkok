<?php
/**
 * 관리자 > 회원관리 > 개인정보동의자
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\UserAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
//error_reporting(E_ALL & ~E_WARNING);ini_set('display_errors', '1');

/* init Class */
$oUser = new UserAdmin();
$oUser->init();
$pk = $oUser->get('pk');

/* check auth */
if (!$oUser->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* set */
$layout_size = 'normal';
$oUser->set('mode', 'selection');
$search_date_arr = array(
    'privacy_time' => '동의일시'
);
$oUser->set('search_date_arr', $search_date_arr);

/* list */
$list = $oUser->selectListPersonal();
$cnt_total = $oUser->get('cnt_total');

/* search condition */
$search_like_arr = $oUser->get('search_like_arr_personal');
$search_date_arr = $oUser->get('search_date_arr');
$query_string = $oUser->get('query_string');

/* pagination */
$page = $oUser->get('page');
$page_arr = $oUser->getPageArray();

/* code */
$flag_use_arr = $oUser->get('flag_use_arr');
$mn_layout_arr = $oUser->get('mn_layout_arr');
$mn_depth_arr = $oUser->get('mn_depth_arr');
$year_arr = $oUser->get('year_arr');
$month_arr = $oUser->get('month_arr');
$date_arr = $oUser->get('date_arr');
$mb_level_arr = $oUser->get('mb_level_arr');
$mb_no_login_arr = $oUser->get('mb_no_login_arr');
$flag_auth_arr = $oUser->get('flag_auth_arr');
$cnt_rows_arr = $oUser->get('cnt_rows_arr');
$order_arr = $oUser->get('order_arr');

$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_like = $_GET['sch_like'];

$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];
$sch_date = $_GET['sch_date'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$sch_text = $_GET['sch_text'];
$sch_cp_name = $_GET['sch_cp_name'];
$sch_flag_auth = $_GET['sch_flag_auth'];
$sch_flag_use = $_GET['sch_flag_use'];

global $module;
$colspan = 9;
?>
<style type="text/css">
</style>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $("#cs_search_btn").click(function(e) {
        e.preventDefault();
        var sch_cp_name = $("#sch_cp_name").val();
        if (!sch_cp_name) {
            alert('기업명을 먼저 선택 해주세요.');
        } else {
            $(this).attr('href', "./popup.search_course.html?sch_cp_name=" + sch_cp_name);
            getContentsbyAjax(this);
        }
    })
    $("#cp_search_btn").click(function(e) {
        e.preventDefault();
        var sch_cp_name = $("#sch_cp_name").val();
        $(this).attr('href', "./popup.search_company.html?sch_cp_name=" + sch_cp_name);
        getContentsbyAjax(this);
    })
});
//]]>
</script>
<div id="<?= $module ?>">
    <div class="search">
        <form name="search_form" action="./assenter_personal.html" method="get"
              onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_order" value="<?= $sch_order ?>"/>
        <input type="hidden" name="sch_cnt_rows" value="<?= $sch_cnt_rows ?>"/>
        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90"/>
        <col width="*"/>
        <col width="90"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text"
                       size="30"
                       maxlength="30" title="검색어"/>
            </td>
            <th><label for="sch_cp_name">기업명</label></th>
            <td>
                <input type="text" name="sch_cp_name" id="sch_cp_name" value="<?= $sch_cp_name ?>"
                       class="text readonly"
                       size="30"
                       maxlength="30" title="검색어"/>
                <a href="./popup.search_company.html" class="btn_ajax sButton small" id="cp_search_btn"
                   target="layer_popup"
                   title="기업선택">기업선택</a>
            </td>
        </tr>
        <tr>
            <th><label for="sch_flag_use">사용여부</label></th>
            <td>
                <select name="sch_flag_use" id="sch_flag_use" class="select" title="재직여부">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($flag_use_arr, $sch_flag_use, 1) ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sch_s_date">기간</label></th>
            <td colspan="3">
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
            <a href="./assenter_personal.html" class="sButton" title="초기화">초기화</a>
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
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="delete"/>
        <fieldset>
        <legend>자료목록</legend>
        <!-- list_table -->
        <table class="list_table border odd" border="1">
        <colgroup>
        <col width="50"/>
        <col width="70"/>
        <col width="*"/>
        <col width="120"/>
        <col width="120"/>
        <col width="80"/>
        <col width="140"/>
        <col width="140"/>
        <col width="140"/>
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
            <th>No</th>
            <th>기업명</th>
            <th>아이디</th>
            <th>이름</th>
            <th>사용여부</th>
            <th>이용약관동의</th>
            <th>개인정보동의</th>
            <th>마케팅정보수신</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                // 마케팅정보수신
                $selection_time_text = '미동의';
                $selection_time = $list[$i]['selection_time'];
                if ($selection_time >= '0000-00-00 00:00:00') {
                    $selection_time_text = $selection_time;
                }
                ?>
                <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                    <td class="checkbox">
                        <input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                               class="list_checkbox" title="선택/해제"/>
                    </td>
                    <td><?= $list[$i]['no'] ?></td>
                    <td><?= $list[$i]['cp_name'] ?></td>
                    <td><?= $list[$i]['mb_id'] ?></td>
                    <td><?= $list[$i]['mb_name'] ?></td>
                    <td><?= $flag_use_arr[$list[$i]['flag_use']] ?></td>
                    <td><?= $list[$i]['policy_time'] ?></td>
                    <td><?= $list[$i]['privacy_time'] ?></td>
                    <td><?= $selection_time_text ?></td>
                </tr>
                <?php
            }
            echo !count($list) ? Html::makeNoTd($colspan) : null;
        } else {
            echo Html::makeNoTd($colspan);
        }
        ?>
        </tbody>
        </table>
        </form>
        <div class="pagination">
            <ul>
            <?= Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
    </div>
</div>
