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
$colspan = 4;
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
    #new_cmm_sub {line-height: 1.6; letter-spacing: -0.2px;}
    .set {width: 1440px; text-align: center; margin: 0 auto;}
    .title {font-size: 40px; text-align: left; margin-bottom: 33px;}
    .title img {width: 35px; height: 35px; margin-right: 14px;}

    .select {display: flex; align-items: center; margin-bottom: 45px;}
    .select a {float: left; width: 480px; height: 60px; font-size: 16px; text-align : center; line-height : 60px; background: #F6F6F6; border: 1px solid #DDDDDD;}
    .select .selected { color: #2342B5; background: #FFFFFF; border: 1px solid #2342B5; }

    .notice_search_box {  position: relative; background: #F6F6F6; height: 125px; margin-bottom: 45px; display: flex; justify-content: center; }
    .notice_search { line-height : 125px; display: flex; align-items: center;}
    .notice_search_text { font-size: 24px; margin-right: 23px; }
    .searchbox_submit {position: absolute;right: 494px;top: 54px;}
    #sch_text { width: 350px; height: 40px; border: 1px solid #C4C4C4; border-radius: 20px; padding-left: 20px; }

    .count { display: flex; font-size: 18px; margin-bottom: 16px; }
    .count span { font-weight: bold; color: #2342B5; }

    table.notice_table {width:100%; margin:0; table-layout:fixed; border-top:1px solid #000000;}
    table.notice_table tr th, table.notice_table tr td {padding:15px 0;text-align:center;vertical-align:middle;}
    table.notice_table tr th {border-bottom:1px solid #C4C4C4;font-weight:400;font-size:16px;background:#F6F6F6;}
    table.notice_table tr td { border-bottom:1px solid #C4C4C4; font-size:16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    /*   제목   */
    table.notice_table tr .sub { text-align: left; padding-left: 45px; }
    /*    th 구분선   */
    .th_border { position: absolute; width: 1440px; height: 24px; }
    .th_120 {width: 119px; border-right: 1px solid #C4C4C4; height: 20px; float: left; margin-top: 18px;}
    .th_120:last-child { border-right: none; }
    .th_960 {width: 960px; border-right: 1px solid #C4C4C4; height: 20px; float: left; margin-top: 18px;}
    /*    공지 div   */
    .no .notice { display: inline-block; width: 50px; height: 26px; color: #1A2BA6; border: 1px solid #1A2BA6; border-radius: 13px;}


    div.pagination {margin-bottom: 110px;}

</style>
<div id="new_cmm_sub" class="brd_notice">
    <div class="set">
        <div class="title"><img src="<?= $layout_uri ?>/img/new_faq/new_community.png"><span>커뮤니티</span></div>
        <!--    커뮤니티 카테고리    -->
        <div class="select">
            <a href="/webuser/notice/new_list.html" class="selected">공지사항</a>
            <a href="/webuser/faq/new_list.html">FAQ</a>
            <a href="/webuser/qna/new_write.html">1:1 문의하기</a>
            <a href="/webuser/review/new_list.html">수강후기</a>
            <a href="/webuser/event/new_list.html">이벤트</a>
        </div>
        <!--    검색 박스    -->
        <div class="notice_search_box">
            <div class="notice_search">
                <div class="notice_search_text">공지사항</div>
                <form name="search_form" action="./new_list.html" method="get" onsubmit="return submitSearchForm(this)">
                    <input type="hidden" name="sch_like" value="bd_subject"/>
                    <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="searchbox"
                           size="24"
                           maxlength="30" title="검색어"/>
                    <input type="image" src="<?= $layout_uri ?>/img/new_faq/faq_search.png" alt="검색" title="검색"
                           class="searchbox_submit"/>
                </form>
            </div>
        </div>
        <!--   건수   -->
        <div class="count">
            <p>총 <span><?= number_format($cnt_total) ?></span>건</p>
        </div>
        <!--    리스트    -->
        <div class="list">
            <!-- list_table -->
            <div class="th_border">
                <div class="th_120"></div>
                <div class="th_960"></div>
                <div class="th_120"></div>
                <div class="th_120"></div>
                <div class="th_120"></div>
            </div>
            <table class="notice_table" border="1">
                <colgroup>
                    <col width="120"/>
                    <col width="*" />
                    <col width="120" />
                    <col width="120" />
                    <col width="120" />
                </colgroup>
                <thead>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>첨부파일</th>
                    <th>등록일</th>
                    <th>조회수</th>
                </tr>
                </thead>
                <tbody>
                <?php for ($i = 0; $i < count($list); $i++) { ?>
                    <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                        <?php
                        if ($list[$i]['bd_is_notice'] == 'Y') {
                            ?>
                            <td class="no"><div class="notice">공지</div></td>
                            <?php
                        } else { ?>
                            <td class="no" ><?= $list[$i]['no'] ?></td>
                            <?php
                        }
                        ?>
                        <td class="sub">
                            <a href="./new_view.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>" <?= ($list[$i]['bd_is_notice'] == 'Y') ? 'style="color:#2342B5"' : '' ?>>
                                <?= $list[$i]['bd_subject'] ?></a>
                        </td>
                        <td>
                            <?php
                            $file_list = $list[$i]['file_list'];
                            if ($file_list) {
                                for ($j = 0; $j < count($file_list); $j++) {
                                    ?>
                                    <a href="./download.html?fi_id=<?= $file_list[$j]['fi_id'] ?>" class="btn_download"
                                       target="_blank" title="새창 다운로드">
                                        <img src="<?= $layout_uri ?>/img/new_ico_file.png" alt="첨부파일">
                                    </a>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                        <td><?= $list[$i]['bt_reg_date'] ?></td>
                        <td><?= number_format($list[$i]['bd_hit']) ?></td>
                    </tr>
                <?php } ?>
                <?= (!count($list)) ? Html::makeNoTd($colspan) : '' ?>
                </tbody>
            </table>
        </div>
        <!-- pagination -->
        <div class="pagination">
            <ul>
                <?= Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
    </div>
</div>
