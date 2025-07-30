<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */
use sFramework\AdminAdmin;
use sFramework\Html;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oAdmin = new AdminAdmin();
$oAdmin->init();
$pk = $oAdmin->get('pk');

/* check auth */
if (!$oAdmin->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oAdmin->selectList();
$cnt_total = $oAdmin->get('cnt_total');

/* search condition */
$search_like_arr = $oAdmin->get('search_like_arr');
$search_date_arr = $oAdmin->get('search_date_arr');
$query_string = $oAdmin->get('query_string');

/* pagination */
$page = $oAdmin->get('page');
$page_arr = $oAdmin->getPageArray();

/* code */
$mn_layout_arr =$oAdmin->get('mn_layout_arr');
$mn_depth_arr = $oAdmin->get('mn_depth_arr');
$year_arr = $oAdmin->get('year_arr');
$month_arr = $oAdmin->get('month_arr');
$date_arr = $oAdmin->get('date_arr');

$mb_level_arr = $oAdmin->get('mb_level_arr');
$mb_no_login_arr = $oAdmin->get('mb_no_login_arr');

$cnt_rows_arr = $oAdmin->get('cnt_rows_arr');
$order_arr = $oAdmin->get('order_arr');

global $module;
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

});
//]]>
</script>
<div id="<?=$module?>">
    <div class="search">
        <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_order" value="<?=$sch_order?>" />
        <input type="hidden" name="sch_cnt_rows" value="<?=$sch_cnt_rows?>" />

        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90" />
        <col width="*" />
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?=Html::makeSelectOptions($search_like_arr, $sch_like, 1)?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?=$sch_text?>" class="text" size="30" maxlength="30" title="검색어" />
            </td>
        </tr>
        <tr>
            <th><label for="sch_s_date">기간</label></th>
            <td>
                <select name="sch_date" class="select" title="기간컬럼">
                <?=Html::makeSelectOptions($search_date_arr, $sch_date, 1)?>
                </select>

                <select name="sch_year" id="sch_year" class="select" title="년">
                <option value="">년</option>
                <?=Html::makeSelectOptions($year_arr, $sch_year, 1)?>
                </select>

                <select name="sch_month" id="sch_month" class="select" title="월">
                <option value="">월</option>
                <?=Html::makeSelectOptions($month_arr, $sch_month, 1)?>
                </select>

                <input type="text" name="sch_s_date" value="<?=$sch_s_date?>" class="text date" size="10" maxlength="10" title="시작일" />
                ~
                <input type="text" name="sch_e_date" value="<?=$sch_e_date?>" class="text date" size="10" maxlength="10" title="종료일" />

                <?=Html::makePeriodAnchor($date_arr, $sch_s_date, $sch_e_date)?>
            </td>
        </tr>
        <?php /*
        <tr>
            <th>권한</th>
            <td>
                <?=Html::makeRadio('sch_mb_level', $mb_level_arr, $sch_mb_level, 1)?>
            </td>
        </tr>
        <tr>
            <th>로그인허용</th>
            <td>
                <?=Html::makeCheckbox('sch_mb_no_login', $mb_no_login_arr, $sch_mb_no_login, 1)?>
            </td>
        </tr> */ ?>
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
                <i class="xi-file-text-o"></i> Total : <strong><?=number_format($cnt_total)?></strong> 건, 현재 : <strong><?=number_format($page)?></strong> 페이지
            </div>
            <div class="right">
                <select name="sch_order" class="select change_order" title="정렬순서">
                <?=Html::makeSelectOptions($order_arr, $sch_order, 1)?>
                </select>
                <select name="sch_cnt_rows" class="select change_order" title="출력갯수">
                <?=Html::makeSelectOptions($cnt_rows_arr, $sch_cnt_rows, 1)?>
                </select>
            </div>
        </div>
        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?=$query_string?>" />
        <input type="hidden" name="page" value="<?=$page?>" />
        </fieldset>

        <fieldset>
        <legend>자료목록</legend>
        <input type="hidden" name="mode" value="delete" />

        <table class="list_table border odd" border="1">
        <colgroup>
        <col width="30" />
        <col width="50" />
        <col width="130" />
        <col width="130" />
        <col width="*" />
        <col width="130" />
        <col width="150" />
        <col width="100" />
        <col width="80" />
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택" /></th>
            <th>No</th>
            <th>아이디</th>
            <th>이름</th>
            <th>이메일</th>
            <th>등록일</th>
            <th>최근접속일시</th>
            <th>등급</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i = 0; $i < count($list); $i++) { ?>
            <tr class="list_tr_<?=$list[$i]['odd']?>">
                <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?=$list[$i][$pk]?>" class="list_checkbox" title="선택/해제" /></td>
                <td><?=$list[$i]['no']?></td>
                <td><a href="./write.html?<?=$pk?>=<?=$list[$i][$pk]?>&page=<?=$page?><?=$query_string?>"><?=$list[$i]['mb_id']?></a></td>
                <td><a href="./write.html?<?=$pk?>=<?=$list[$i][$pk]?>&page=<?=$page?><?=$query_string?>"><?=$list[$i]['mb_name']?></a></td>
                <td><?=Format::getWithoutNull($list[$i]['mb_email'])?></td>
                <td><?=$list[$i]['bt_reg_date']?></td>
                <td><?=Format::getWithoutNull($list[$i]['mb_login_time'])?></td>
                <td><?=$list[$i]['txt_mb_level']?></td>
                <td class="button"><a href="./write.html?<?=$pk?>=<?=$list[$i][$pk]?>&page=<?=$page?><?=$query_string?>" class="sButton tiny" title="수정">수정</a></td>
            </tr>
        <?php } ?>
        <?=!count($list) ? Html::makeNoTd(9) : null?>
        </tbody>
        </table>

        <div class="list_footer">
            <div class="left">
                <button type="submit" class="sButton small">선택삭제</button>
            </div>
            <div class="right">
                <a href="./write.html?page=<?=$page?><?=$query_string?>" class="sButton small primary" title="등록">등록</a>
            </div>
        </div>
        </form>

        <div class="pagination">
            <ul>
            <?=Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
    </div>
</div>
