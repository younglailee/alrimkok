<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\NoticeAdmin;
use sFramework\Html;
use sFramework\Format;
use sFramework\Db;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new NoticeAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');

/* check auth */
if (!$oBoard->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* search keyword */
$search_columns = 'fi_name, dw_name, dw_tel';
$oBoard->set('search_columns', $search_columns);

/* list */
$oBoard->set('data_table', 'tbl_download');
$oBoard->set('select_columns', '*');
$oBoard->set('bd_code', '');
$oBoard->set('flag_use_reg', false);
$list = $oBoard->selectList();
//print_r($list);
$cnt_total = $oBoard->get('cnt_total');

/* search condition */
$search_like_arr = array(
    'fi_name' => '파일명',
    'dw_name' => '이름',
    'dw_tel' => '연락처'
);
$search_date_arr = $oBoard->get('search_date_arr');
$query_string = $oBoard->get('query_string');
$sch_like = $_GET['sch_like'];
$sch_text = $_GET['sch_text'];

/* pagination */
$page = $oBoard->get('page');
$page_arr = $oBoard->getPageArray();

/* colspan */
$colspan = 7;
?>
<script type="text/javascript">
//<![CDATA[
$(function() {

});
//]]>
</script>
<div id="<?= $module ?>">
    <div class="search">
        <form name="search_form" action="./list_download.html" method="get" onsubmit="return submitSearchForm(this)">
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
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text" size="30"
                       maxlength="30" title="검색어"/>
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
    <!-- //search -->

    <!-- list -->
    <div class="list">

        <!-- list_header -->
        <div class="list_header">
            <div class="left">
                <i class="xi-file-text-o"></i> Total : <strong><?= number_format($cnt_total) ?></strong> 건, 현재 :
                <strong><?= number_format($page) ?></strong> 페이지
            </div>

            <div class="right">

            </div>
        </div>
        <!-- //list_header -->

        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        </fieldset>

        <fieldset>
        <legend>자료목록</legend>
        <input type="hidden" name="mode" value="delete"/>

        <!-- list_table -->
        <table class="list_table border odd" border="1">
        <colgroup>
        <col width="60"/><!-- 번호 -->
        <col width="120"/><!-- 구분 -->
        <col width="*"/><!-- 파일명 -->
        <col width="120"/><!-- 이름 -->
        <col width="120"/><!-- 연락처 -->
        <col width="160"/><!-- 다운로드 일시 -->
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>구분</th>
            <th>파일명</th>
            <th>이름</th>
            <th>연락처</th>
            <th>다운로드 일시</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($list); $i++) {
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $list[$i]['bd_category'] ?></td>
                <td class="subject"><?= $list[$i]['fi_name'] ?></td>
                <td><?= $list[$i]['dw_name'] ?></td>
                <td><?= $list[$i]['dw_tel'] ?></td>
                <td><?= $list[$i]['reg_time'] ?></td>
            </tr>
        <?php } ?>
        <?= (!count($list)) ? Html::makeNoTd($colspan) : null ?>
        </tbody>
        </table>
    </div>
</div>
