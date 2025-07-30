<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\NoticePartner;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oBoard = new NoticePartner();
$oBoard->init();
$pk = $oBoard->get('pk');

/* check auth */
if (!$oBoard->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oBoard->selectList();
$cnt_total = $oBoard->get('cnt_total');

/* search condition */
$search_like_arr = $oBoard->get('search_like_arr');
$search_date_arr = $oBoard->get('search_date_arr');
$query_string = $oBoard->get('query_string');

/* pagination */
$page = $oBoard->get('page');
$page_arr = $oBoard->getPageArray();

/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_bgn = $oBoard->get('flag_use_bgn');
$flag_use_end = $oBoard->get('flag_use_end');
$flag_use_state = $oBoard->get('flag_use_state');

/* code */
$bd_is_notice_arr = $oBoard->get('bd_is_notice_arr');

if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}

if ($flag_use_state) {
    $bd_state_arr = $oBoard->get('bd_state_arr');
}

/* colspan */
$colspan = 6;
if ($bd_code == 'article') {
    $colspan = 8;
}

if ($flag_use_category) {
    $colspan++;
}

if ($flag_use_bgn) {
    $colspan++;
}
if ($flag_use_state) {
    $colspan++;
}
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
        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <?php if ($flag_use_category) { ?>
            <tr>
                <th>분류</th>
                <td>
                    <input type="radio" name="sch_bd_category" id="sch_bd_category_all" value=""
                           class="radio" <?= (!$sch_bd_category) ? 'checked="checked"' : null ?> title="전체"/>
                    <label for="sch_bd_category_all">전체</label>
                    <?= Html::makeRadio('sch_bd_category', $bd_category_arr, $sch_bd_category, 0) ?>
                </td>
            </tr>
        <?php } ?>
        <?php if ($flag_use_state) { ?>
            <tr>
                <th>상태</th>
                <td>
                    <input type="radio" name="sch_bd_state" id="sch_bd_state_all" value=""
                           class="radio" <?= (!$sch_bd_state) ? 'checked="checked"' : null ?> title="전체"/>
                    <label for="sch_bd_state_all">전체</label>
                    <?= Html::makeRadio('sch_bd_state', $bd_state_arr, $sch_bd_state, 1) ?>
                </td>
            </tr>
        <?php } ?>
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
        <?= ($flag_use_category) ? '<col width="120" />' : '' ?><!-- 진행기간 -->
        <col width="*"/><!-- 제목 -->
        <?php if ($bd_code == 'article') { ?>
            <col width="100"/>
            <col width="260"/>
        <?php } ?>
        <col width="90"/><!-- 첨부파일 -->
        <col width="120"/><!-- 작성자 -->
        <col width="90"/><!-- 작성일 -->
        <?= ($flag_use_bgn) ? '<col width="160" />' : '' ?><!-- 진행기간 -->
        <?= ($flag_use_state) ? '<col width="60" />' : '' ?><!-- 상태 -->
        <?php if ($bd_code != 'article') { ?>
            <col width="60"/><!-- 조회수 -->
        <?php } ?>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <?= ($flag_use_category) ? '<th>분류</th>' : '' ?>
            <th>제목</th>
            <?php if ($bd_code == 'article') { ?>
                <th>언론사</th>
                <th>링크주소</th>
            <?php } ?>
            <th>첨부파일</th>
            <th>작성자</th>
            <th>작성일</th>
            <?= ($flag_use_bgn) ? '<th>진행기간</th>' : '' ?>
            <?= ($flag_use_state) ? '<th>상태</th>' : '' ?>
            <?php if ($bd_code != 'article') { ?>
                <th>조회수</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                $file_list = $list[$i]['file_list'];
                ?>
                <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                    <td><?= $list[$i]['no'] ?></td>
                    <?= ($flag_use_category) ? '<td>' . $list[$i]['bd_category'] . '</td>' : '' ?>
                    <?php if ($bd_code == 'article') { ?>
                        <td class="">
                            <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['bd_subject'] ?></a>
                            <?= ($list[$i]['cnt_file']) ? '<i class="xi-save file"></i>' : '' ?>
                            <?= ($list[$i]['is_new']) ? '<i class="xi-new new"></i>' : '' ?>
                        </td>
                        <td><?= $list[$i]['bd_etc1'] ?></td>
                        <td class="overflow"><?= $list[$i]['bd_content'] ?></td>
                    <?php } else { ?>
                        <td class="">
                            <a href="./view.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['bd_subject'] ?></a>
                            <?= ($list[$i]['cnt_file']) ? '<i class="xi-save file"></i>' : '' ?>
                            <?= ($list[$i]['is_new']) ? '<i class="xi-new new"></i>' : '' ?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php
                        if ($file_list[0]['fi_id']) {
                            ?>
                            <a href="./download.html?fi_id=<?= $file_list[0]['fi_id'] ?>" class="btn_download"
                               target="_blank" title="새창 다운로드">
                                <img src="/common/img/exam/lms_download_icon.png"/>
                            </a>
                            <?php
                        }
                        ?>
                    </td>
                    <td><?= $bd_code == 'news' && $list[$i]['bd_writer_mode'] == 'admin' ? '관리자' : $list[$i]['bd_writer_name'] ?></td>
                    <td><?= $list[$i]['bt_reg_date'] ?></td>
                    <?= ($flag_use_bgn) ? '<td>' . $list[$i]['bt_bd_bgn_date'] . (($flag_use_end) ? ' ~ ' . $list[$i]['bt_bd_end_date'] : '') . '</td>' : '' ?>
                    <?= ($flag_use_state) ? '<td><strong class="' . $list[$i]['state_class'] . '">' . $list[$i]['txt_bd_state'] . '</strong></td>' : '' ?>
                    <?php if ($bd_code != 'article') { ?>
                        <td><?= number_format($list[$i]['bd_hit']) ?></td>
                    <?php } ?>
                </tr>
                <?php
            }
            echo (!count($list)) ? Html::makeNoTd($colspan) : null;
        }
        ?>
        </tbody>
        </table>
        <!-- //list_table -->

        <!-- list_footer -->
        <div class="list_footer">
            <div class="left">
            </div>
            <div class="right">
                <a href="./write.html?page=<?= $page ?><?= $query_string ?>" class="sButton small primary" title="글쓰기">글쓰기</a>
            </div>
        </div>
        <!-- //list_footer -->

        </form>

        <!-- pagination -->
        <div class="pagination">
            <ul>
            <?= Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
        <!-- //pagination -->

    </div>
    <!-- //list -->
</div>
