<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\VisitPartner;

if (!defined('_ALPHA_')) {
    exit;
}
global $module;

/* init Class */
$oVisit = new VisitPartner();
$oVisit->init();
$pk = $oVisit->get('pk');

/* check auth */
if (!$oVisit->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oVisit->selectList();
$cnt_total = $oVisit->get('cnt_total');

/* search condition */
$search_like_arr = $oVisit->get('search_like_arr');
$search_date_arr = $oVisit->get('search_date_arr');
$query_string = $oVisit->get('query_string');

/* pagination */
$page = $oVisit->get('page');
$page_arr = $oVisit->getPageArray();

/* code */
$date_arr = $oVisit->get('date_arr');

$layout_size = 'large';
/*
$us_name = array('홍길동', '이영래', '박금삼', '임꺽정', '이순신', '관리자', '최고관리자', '개발관리자', '부서관리자', '담당자');
$us_id = array('hong', 'lucas', 'sliva', 'limkkj', 'leess', 'admin', 'root', 'dept', 'part', 'charge');
$cp_name = array('알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀');
*/
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
        <input type="hidden" name="sch_date" value="reg_time"/>

        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_s_date">기간</label></th>
            <td>
                <input type="text" name="sch_s_date" value="<?= $sch_s_date ?>" class="text date" size="10"
                       maxlength="10" title="시작일"/>
                ~
                <input type="text" name="sch_e_date" value="<?= $sch_e_date ?>" class="text date" size="10"
                       maxlength="10" title="종료일"/>

                <?= Html::makePeriodAnchor($date_arr, $sch_s_date, $sch_e_date) ?>
            </td>
        </tr>
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
        <table class="list_table border odd" border="1">
        <colgroup>
        <col style="width:50px"/>
        <col style="width:100px"/>
        <col style="width:100px"/>
        <col style="width:250px"/>
        <col style="width:120px"/>
        <col style="width:100px"/>
        <col style="width:90px"/>
        <col style="width:110px"/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>아이디</th>
            <th>이름</th>
            <th>기업명</th>
            <th>아이피</th>
            <th>디바이스</th>
            <th>운영체제</th>
            <th>브라우저</th>
            <th>접속일시</th>
            <th>접속경로</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($list); $i++) {
            $uid = substr($list[$i][$pk], 2, 1);
            if (!$list[$i]['cp_name']) {
                if ($list[$i]['mb_level'] && $list[$i]['mb_level'] <= 2) {
                    $list[$i]['cp_name'] = '내일배움카드';
                } else {
                    $list[$i]['cp_name'] = '(주)알파에듀';
                }
            }
            $vs_device = $list[$i]['vs_device'];
            if ($vs_device == 'Desktop') {
                $vs_device = 'PC';
            }
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td>
                    <?= $list[$i]['reg_id'] ?>
                </td>
                <td>

                    <?= $list[$i]['reg_name'] ?>
                </td>
                <td class=""><?= $list[$i]['cp_name'] ?></td>
                <td><?= $list[$i]['vs_ip'] ?></td>
                <td><?= $vs_device ?></td>
                <td><?= $list[$i]['vs_os'] ?></td>
                <td><?= $list[$i]['vs_browser'] ?></td>
                <td><?= $list[$i]['reg_time'] ?></td>
                <td><?= Html::cutString($list[$i]['vs_referer'], 40) ?></td>
            </tr>
        <?php } ?>
        <?= !count($list) ? Html::makeNoTd(11) : null ?>
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
