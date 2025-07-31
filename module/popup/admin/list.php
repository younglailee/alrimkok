<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\PopupAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oPopup = new PopupAdmin();
$oPopup->init();
$pk = $oPopup->get('pk');

/* check auth */
if (!$oPopup->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oPopup->selectList();
$cnt_total = $oPopup->get('cnt_total');

/* search condition */
$search_like_arr = $oPopup->get('search_like_arr');
$search_date_arr = $oPopup->get('search_date_arr');
$query_string = $oPopup->get('query_string');

/* search code */
$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_like = $_GET['sch_like'];
$sch_text = $_GET['sch_text'];
$sch_date = $_GET['sch_date'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];

/* pagination */
$page = $oPopup->get('page');
$page_arr = $oPopup->getPageArray();

/* code */
$mn_layout_arr = $oPopup->get('mn_layout_arr');
$mn_depth_arr = $oPopup->get('mn_depth_arr');
$year_arr = $oPopup->get('year_arr');
$month_arr = $oPopup->get('month_arr');
$date_arr = $oPopup->get('date_arr');

$pu_is_display_arr = $oPopup->get('pu_is_display_arr');

$mb_level_arr = $oPopup->get('mb_level_arr');
$mb_no_login_arr = $oPopup->get('mb_no_login_arr');

$cnt_rows_arr = $oPopup->get('cnt_rows_arr');
$order_arr = $oPopup->get('order_arr');

$sch_pu_is_display = $_GET['sch_pu_is_display'];
global $module;
$colspan = 9;
$page = $oPopup->get('page');
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

});
//]]>
</script>

<div id="<?= $module ?>">
    <div class="search">
        <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <option value="all">통합검색</option>
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text" size="30"
                       maxlength="30" title="검색어"/>
            </td>
            <th><label for="sch_pu_is_display">출력여부</label></th>
            <td>
                <select name="sch_pu_is_display" class="select" title="검색컬럼">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($pu_is_display_arr, $sch_pu_is_display, 1) ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="sch_s_date">기간검색</label></th>
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
            <a href="./list.html" class="sButton" title="초기화">초기화</a>
        </div>
        </form>
    </div>

    <div class="list">
        <div class="list_header">
            <div class="left">
                <i class="xi-file-text-o"></i> Total : <strong><?= number_format($cnt_total) ?></strong> 건
            </div>
            <div class="right">
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
        <col width="30"/>
        <col width="50"/>
        <col width="100"/>
        <col width="*"/>
        <col width="80"/>
        <col width="80"/>
        <col width="80"/>
        <col width="80"/>
        <col width="60"/>
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
            <th>No</th>
            <th>출력여부</th>
            <th>제목</th>
            <th>시작일</th>
            <th>종료일</th>
            <th>작성일</th>
            <th>작성자</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody id="carousel_tbody">
        <?php
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                ?>
                <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                    <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                                class="list_checkbox" title="선택/해제"/></td>
                    <td><?= $list[$i]['no'] ?></td>
                    <td><?= $pu_is_display_arr[$list[$i]['pu_is_display']] ?></td>
                    <td class="">
                        <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['pu_subject'] ?></a>
                    </td>
                    <td>
                        <?= $list[$i]['pu_bgn_date'] ?>
                    </td>
                    <td>
                        <?= $list[$i]['pu_end_date'] ?>
                    </td>
                    <td><?= $list[$i]['reg_date'] ?></td>
                    <td><?= $list[$i]['writer_name'] ?></td>
                    <td class="button"><a
                            href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"
                            class="sButton tiny" title="수정">수정</a></td>
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

        <div class="list_footer">
            <div class="left">
                <button type="submit" class="sButton small">선택삭제</button>
            </div>
            <div class="right">
                <a href="./write.html?page=<?= $page ?><?= $query_string ?>" class="sButton small primary"
                   title="등록">등록</a>
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

<form name="order_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value="change_order"/>
<input type="hidden" name="direction" value=""/>
<input type="hidden" name="<?= $pk ?>" value=""/>
</form>
