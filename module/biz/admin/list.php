<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\BizAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBiz = new BizAdmin();
$oBiz->init();
$pk = $oBiz->get('pk');

/* check auth */
if (!$oBiz->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oBiz->selectList();
$cnt_total = $oBiz->get('cnt_total');

/* search condition */
$search_like_arr = $oBiz->get('search_like_arr');
$search_date_arr = $oBiz->get('search_date_arr');
$query_string = $oBiz->get('query_string');

/* pagination */
$page = $oBiz->get('page');
$page_arr = $oBiz->getPageArray();

/* code */
?>
<script type="text/javascript">
    //<![CDATA[
    $(function () {

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
                        <th><label for="sch_text">검색어</label></th>
                        <td>
                            <select name="sch_like" class="select" title="검색컬럼">
                                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                            </select>
                            <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text"
                                   size="26"
                                   maxlength="30" title="검색어"/>
                        </td>
                        <th><label for="sch_company">기업명</label></th>
                        <td>
                            <input type="text" name="sch_company" id="sch_company" value="<?= $sch_company ?>"
                                   class="text"
                                   size="26"
                                   maxlength="30" title="검색어"/>
                        </td>
                        <th><label for="sch_flag_test">테스트용</label></th>
                        <td>
                            <select name="sch_flag_test" id="sch_flag_test" class="select" title="테스트용">
                                <option value="">전체</option>
                                <?= Html::makeSelectOptions($flag_test_arr, $sch_flag_test, 1) ?>
                            </select>
                        </td>
                        <th><label for="sch_flag_live">화상교육</label></th>
                        <td>
                            <select name="sch_flag_live" id="sch_flag_live" class="select" title="북러닝여부">
                                <option value="">전체</option>
                                <?= Html::makeSelectOptions($flag_live_arr, $sch_flag_live, 1) ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
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
                        <th><label for="sch_flag_tomocard">내일배움</label></th>
                        <td>
                            <select name="sch_flag_tomocard" id="sch_flag_tomocard" class="select" title="북러닝여부">
                                <option value="">전체</option>
                                <?= Html::makeSelectOptions($flag_tomocard_arr, $sch_flag_tomocard, 1) ?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
            <div class="button">
                <button type="submit" class="sButton info" title="검색">검 색</button>
                <a href="./list.html" class="sButton" title="초기화">초기화</a>
                <a href="./user_list_excel.html?page=<?= $page ?><?= $query_string ?>"
                   class="sButton" title="엑셀출력">엑셀출력</a>
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
                        <col style="width:30px"/>
                        <col style="width:50px"/>
                        <col style="width:100px"/>
                        <col style="width:100px"/>
                        <col style="width:100px"/>
                        <col/>
                        <col style="width:100px"/>
                        <col style="width:80px"/>
                        <col style="width:80px"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
                        <th>No</th>
                        <th>유형</th>
                        <th>분야</th>
                        <th>지역</th>
                        <th>공고명</th>
                        <th>출력여부</th>
                        <th>등록일</th>
                        <th>관리</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i = 0; $i < count($list); $i++) {
                        ?>
                        <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                            <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                                        class="list_checkbox" title="선택/해제"/></td>
                            <td><?= $list[$i]['no'] ?></td>
                            <td><?= $list[$i]['bz_category'] ?></td>
                            <td><?= $list[$i]['bz_field'] ?></td>
                            <td><?= $list[$i]['bz_region'] ?></td>
                            <td><?= $list[$i]['bz_title'] ?></td>
                            <td><?= $list[$i]['bz_region'] ?></td>
                            <td><?= $list[$i]['reg_date'] ?></td>
                            <td>
                                <a href="./write.html?bz_id=<?= $list[$i]['bz_id'] ?>" target="_blank"
                                   class="sButton tiny" title="수정">수정</a>
                            </td>
                        </tr>
                        <?php
                    }
                    echo !count($list) ? Html::makeNoTd(9) : null;
                    ?>
                    </tbody>
                </table>
                <div class="list_footer">
                    <div class="left">
                        <button type="button" class="sButton small ready">선택삭제</button>
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
