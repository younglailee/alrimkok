<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\SettingAdmin;
use sFramework\Html;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oSetting = new SettingAdmin();
$oSetting->init();
$pk = $oSetting->get('pk');

/* check auth */
if (!$oSetting->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* list */
$list = $oSetting->selectList();
$cnt_total = $oSetting->get('cnt_total');

/* search condition */
$search_like_arr = $oSetting->get('search_like_arr');
$search_date_arr = $oSetting->get('search_date_arr');
$query_string = $oSetting->get('query_string');

/* pagination */
$page = $oSetting->get('page');
$page_arr = $oSetting->getPageArray();

/* code */
$mn_layout_arr = $oSetting->get('mn_layout_arr');
$mn_depth_arr = $oSetting->get('mn_depth_arr');
$year_arr = $oSetting->get('year_arr');
$month_arr = $oSetting->get('month_arr');
$date_arr = $oSetting->get('date_arr');
$flag_auth_arr = $oSetting->get('flag_auth_arr');
$bl_partner_arr = $oSetting->get('bl_partner_arr');
$mb_no_login_arr = $oSetting->get('mb_no_login_arr');
$cnt_rows_arr = $oSetting->get('cnt_rows_arr');
$order_arr = $oSetting->get('order_arr');

$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_flag_auth = $_GET['sch_flag_auth'];
$sch_date = $_GET['sch_date'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$sch_bl_partner = $_GET['sch_bl_partner'];
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>
<div id="<?= $module ?>">
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
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text" size="26"
                       maxlength="30" title="검색어"/>
            </td>
            <th><label for="sch_bl_partner">파트너</label></th>
            <td>
                <select name="sch_bl_partner" id="sch_bl_partner" class="select" title="환급구분">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($bl_partner_arr, $sch_bl_partner, 1) ?>
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
            <a href="./list.html" class="sButton" title="엑셀출력">엑셀출력</a>
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
        <col style="width:30px"/>
        <col style="width:50px"/>
        <col style="width:90px"/>
        <col style="width:100px"/>
        <col/>
        <col style="width:120px"/>
        <col style="width:110px"/>
        <col style="width:110px"/>
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
            <th>No</th>
            <th>권한구분</th>
            <th>이름</th>
            <th>제목</th>
            <th>금액</th>
            <th>지급일</th>
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
            // 환급구분
            $bl_partner = $list[$i]['bl_partner'];
            $txt_bl_partner = $bl_partner_arr[$bl_partner];
            // 차시
            $cnt_occasion = 17;
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                            class="list_checkbox" title="선택/해제"/></td>
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $txt_bl_partner ?></td>
                <td class="">
                    <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['cs_name'] ?></a>
                </td>
                <td><?= $cnt_occasion ?></td>
                <td><?= $list[$i]['cs_code'] ?></td>
                <td class="button">
                    <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"
                       class="sButton tiny" title="보기">보기</a>
                    <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"
                       class="sButton tiny" title="수정">수정</a>
                </td>
            </tr>
            <?php
        }
        echo !count($list) ? Html::makeNoTd(8) : null;
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
                <a href="./write.html?page=<?= $page ?><?= $query_string ?>" class="sButton small" title="엑셀등록">엑셀등록</a>
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
