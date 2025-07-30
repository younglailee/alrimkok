<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */
use sFramework\VisitAdmin;
use sFramework\Html;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oVisit = new VisitAdmin();
$oVisit->init();
$pk = $oVisit->get('pk');

/* check auth */
if (!$oVisit->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list_mode_arr = array(
    'day' => '일별',
    'month' => '월별',
    'year' => '연별'
);
if (!$sch_list_mode || !array_key_exists($sch_list_mode, $list_mode_arr)) {
    $sch_list_mode = 'month';
}

$oVisit->set('list_mode', $sch_list_mode);
$result = $oVisit->selectListByDate();

/* search condition */
$search_like_arr = $oVisit->get('search_like_arr');
$search_date_arr = $oVisit->get('search_date_arr');
$query_string = $oVisit->get('query_string');

/* code */
$date_arr = $oVisit->get('date_arr');
//$layout_size = 'normal';
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    $("#sch_list_mode").on("change", function() {
        var f = document.search_form;
        f.sch_list_mode.value = $(this).val();
        f.submit();
    });
});
//]]>
</script>

<div id="<?=$module?>">

    <!-- search -->
    <div class="search">
        <form name="search_form" action="./statistics.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_date" value="reg_time" />
        <input type="hidden" name="sch_list_mode" value="<?=$sch_list_mode?>" />

        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90" />
        <col width="*" />
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_s_date">기간</label></th>
            <td>
                <input type="text" name="sch_s_date" value="<?=$sch_s_date?>" class="text date" size="10" maxlength="10" title="시작일" />
                ~
                <input type="text" name="sch_e_date" value="<?=$sch_e_date?>" class="text date" size="10" maxlength="10" title="종료일" />

                <?=Html::makePeriodAnchor($date_arr, $sch_s_date, $sch_e_date)?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton info" title="검색">검 색</button>
            <a href="./statistics.html" class="sButton" title="초기화">초기화</a>
        </div>
        </form>
    </div>
    <!-- //search -->

    <div class="statistics_area">
        <table class="list_table border statistics" border="1">
        <colgroup>
        <col width="*" />
        <col width="240" />
        <col width="240" />
        <col width="380" />
        <thead>
        <tr>
            <th>총방문자수</th>
            <th>최대 방문자</th>
            <th>최소 방문자</th>
            <th>최대·최소 편차</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="total">
                <strong class="failed"><?= Format::beautifyNumber($result['total']) ?></strong>명
            </td>
            <td>
                <strong><?= Format::beautifyNumber($result['max']) ?></strong>명
                <!--<span>2016.01</span>-->
            </td>
            <td>
                <strong><?= Format::beautifyNumber($result['min']) ?></strong>명
                <!--<span>2016.01</span>-->
            </td>
            <td class="deviation">
                <strong><?= Format::beautifyNumber($result['max'] - $result['min']) ?></strong>명
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <!-- list -->
    <div class="list">

        <!-- list_header -->
        <div class="list_header">
            <div class="left">
                <label for="sch_list_mode">집계기준 : </label>
                <select name="sch_list_mode" id="sch_list_mode" class="select">
                <?= Html::makeSelectOptions($list_mode_arr, $sch_list_mode, 1) ?>
                </select>
            </div>

            <div class="right">

            </div>
        </div>
        <!-- //list_header -->

        <!-- list_table -->
        <table class="stats_table">
        <colgroup>
        <col width="200">
        <col width="*">
        <col width="100">
        </colgroup>
        <thead>
        <tr>
            <th>기간</th>
            <th>방문자수</th>
            <th>합계</th>
        </tr>
        </thead>
        <tbody>
        <? if (is_array($result['list'])) {
        foreach ($result['list'] as $key => $arr) { ?>
        <tr>
            <td class="sub_th"><?=$arr['txt']?></td>
            <td>
                <div class="visit_bar">
                    <span style="width:<?= $arr['per'] ?>%"></span>
                    <span class="percent" style="left:<?= $arr['per'] ?>%"><?= Format::beautifyNumber($arr['cnt']) ?>명</span>
                </div>
            </td>
            <td class="number"><?= Format::beautifyNumber($arr['cnt']) ?>명</td>
        </tr>
        <?php }
        }?>
        </tbody>
        <tfoot>
        <tr class="sum">
            <th class="sub_th">합계</th>
            <td class="number"></td>
            <td class="number"><?= Format::beautifyNumber($result['total']) ?>명</td>
        </tr>
        </tfoot>
        </table>
        <!-- //list_table -->

    </div>
    <!-- //list -->
</div>
