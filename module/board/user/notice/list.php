<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Format;
use sFramework\Html;
use sFramework\NoticeUser;

if (!defined('_ALPHA_')) {
    exit;
}
global $layout_uri;
// XSS 필터링 적용 yllee 220725
$sch_text = Format::filterXss($_GET['sch_text']);

/* init Class */
$oBoard = new NoticeUser();
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
$flag_use_state = $oBoard->get('flag_use_state');
/* code */
if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}
/* colspan */
$colspan = 5;
if ($flag_use_category) {
    $colspan++;
}
if ($flag_use_state) {
    $colspan++;
}
/* notice */
$notice_list = $oBoard->selectNoticeList();

$uid = '';
if ($_GET['uid']) {
    $uid = $_GET['uid'];
}
$bd_code = $oBoard->get('bd_code');

// 안전보건자료실 노데이터 콜스팬 +1 적용
// 안전보건공지사항 추가 220630 박금삼
if ($bd_code == 'safe' || $bd_code == 'snotice') {
    $colspan++;
    //print_r($list);
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    var uid = "<?= $uid ?>";
    if (uid) {
        $('#notice_content_<?= $uid ?>').toggle();
    }
});

function selectDetail(uid, i) {
    $('#notice_content_' + uid).toggle();
    $.ajax({
        url: "process.html",
        type: "GET",
        dataType: "json",
        data: {
            flag_json: '1',
            mode: 'select_detail',
            uid: uid
        },
        success: function(rs) {
            console.log(rs)
        }
    });
}
//]]>
</script>
<style>
.no_data { width:100%; }
</style>


<section id="notice" class="contents">
    <div class="container">
        <h2 class="sec-title">공지사항</h2>

        <div class="search">
            <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
            <input type="hidden" name="sch_like" value="bd_subject"/>
            <input type="search" name="sch_text" value="<?= $sch_text ?>" placeholder="검색어를 입력하세요.">
            <button class="btn_search">검색</button>
            </form>
        </div>

        <p class="list-total">총 <span><?= $cnt_total ?></span>건</p>

        <table class="list-table">
        <thead>
        <tr>
            <th class="num" scope="col">번호</th>
            <th class="title" scope="col">제목</th>
            <th class="file" scope="col">첨부파일</th>
            <th class="date" scope="col">등록일</th>
            <th class="view" scope="col">조회수</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                ?>
                <tr>
                    <td class="num"><?= $list[$i]['no'] ?></td>
                    <td class="title">
                        <a class="clamp c1"
                           href="./view.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['bd_subject'] ?></a>
                    </td>
                    <td class="file">
                        <?php
                        $file_list = $list[$i]['file_list'];
                        if ($file_list) {
                            for ($j = 0; $j < count($file_list); $j++) {
                                ?>
                                <a href="./download.html?fi_id=<?= $file_list[$j]['fi_id'] ?>" download="">
                                    <img class="of-ct" src="/common/img/user/icon/file-down.svg" alt="첨부파일">
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </td>
                    <td class="date"><?= $list[$i]['bt_reg_date'] ?></td>
                    <td class="view"><?= number_format($list[$i]['bd_hit']) ?></td>
                </tr>
                <?php
            }
            echo (!count($list)) ? Html::makeNoTd($colspan) : '';
        } else {
            echo Html::makeNoTd($colspan);
        }
        ?>
        </tbody>
        </table>
        <ul class="pg-num">
        <?= Html::makePagination($page_arr, $query_string); ?>
        </ul>
    </div>
</section>
