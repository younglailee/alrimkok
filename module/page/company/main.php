<?php
/**
 * @file    main.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\NoticeCompany;
use sFramework\QnaAdmin;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', '1');

//Html::movePage('../visit/statistics.html');
$doc_title = '메인';
//$layout_size = 'normal';

// 공지사항
$oNotice = new NoticeCompany();
$oNotice->init();
$oNotice->set('bd_code', 'notice');
$notice_pk = $oNotice->get('pk');
$nt_list = $oNotice->selectLatestList(3);

// 1:1 문의
$oQna = new QnaCompany();
$oQna->init();
$oQna->set('bd_code', 'qna');
$qna_pk = $oQna->get('pk');
$qna_list = $oQna->selectLatestList(3);
?>
<style type="text/css">
/* stats */
div.list_header { height:35px; }
div.list_header > div.left { padding-left:0; }
.success { color:#7ab770 !important; }
.failed { color:#e95a72 !important; }
.info { color:#538fd4 !important; }
.disabled { color:#d2d2d2 !important; }
.point { color:#e95a72 !important; }
.stand { color:#e95a72 !important; }
.wait { color:#6e6e6e !important; }

#main { min-height:600px; }
#main div.main_box { margin-bottom:30px; }
#main div.main_box.banner {padding:20px 30px; background-color:#f5f5f5; border:1px solid #538fd4 !; }
#main div.main_box.banner table {background-color:#fff;}

#main div.main_top { }
#main div.main_article { }
#main div.main_wrap { position:relative; height:205px; margin-bottom:30px; *zoom:1; }
#main div.main_wrap:after { clear:both; display:block; content:""; }
#main div.main_wrap div.main_box { float:left; width:46%; margin-bottom:0; }
#main div.main_wrap > div.main_box:first-child { margin-right:4%; }
</style>
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
<div id="main">
    <div class="main_box">
        <div class="main_top">
            <h4>공지사항</h4>
            <div></div>
        </div>
        <div class="main_article">
            <table class="list_table border odd">
            <colgroup>
            <col/>
            <col style="width:20%"/>
            <col style="width:20%"/>
            </colgroup>
            <thead>
            <tr>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (is_array($nt_list)) {
                for ($i = 0; $i < sizeof($nt_list); $i++) {
                    $nt_url = "../notice/view.html?$notice_pk=" . $nt_list[$i][$notice_pk];
                    ?>
                    <tr>
                        <td class=""><a
                                href="<?= $nt_url ?>"><?= $nt_list[$i]['bd_subject'] ?></a>
                        </td>
                        <td><?= $nt_list[$i]['bd_writer_name'] ?></td>
                        <td><?= $nt_list[$i]['reg_date'] ?></td>
                    </tr>
                    <?php
                }
                echo (!count($nt_list)) ? Html::makeNoTd(3) : '';
            }
            ?>
            </tbody>
            </table>
        </div>
    </div>
    <div class="main_box">
        <div class="main_top">
            <h4>1:1 문의</h4>
            <div></div>
        </div>
        <div class="main_article">
            <table class="list_table border odd">
            <colgroup>
            <col/>
            <col style="width:20%"/>
            <col style="width:20%"/>
            </colgroup>
            <thead>
            <tr>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (is_array($qna_list)) {
                for ($i = 0; $i < sizeof($qna_list); $i++) {
                    $qna_url = "../qna/view.html?$qna_pk=" . $qna_list[$i][$qna_pk];
                    ?>
                    <tr>
                        <td class=""><a
                                href="<?= $qna_url ?>"><?= $qna_list[$i]['bd_subject'] ?></a>
                        </td>
                        <td><?= $qna_list[$i]['bd_writer_name'] ?></td>
                        <td><?= $qna_list[$i]['reg_date'] ?></td>
                    </tr>
                    <?php
                }
                echo (!count($qna_list)) ? Html::makeNoTd(3) : '';
            }
            ?>
            </tbody>
            </table>
        </div>
    </div>
    <div class="main_box">
        <div class="main_top">
            <h4>출퇴근기록</h4>
            <div></div>
        </div>
        <div class="main_article">
            <?php
            global $member;
            $us_id = $member['mb_id'];
            $us_name = $member['mb_name'];
            ?>
            <button type="button" onclick="attendOffice('<?= $us_id ?>', '<?= $us_name ?>')" class="sButton large info"
                    title="출근">출근
            </button>
            <button type="button" onclick="getOffWork('<?= $us_id ?>', '<?= $us_name ?>')" class="sButton large"
                    title="퇴근">퇴근
            </button>
            <button type="button" onclick="workOvertime()" class="sButton large" title="연장근로신청">연장근로신청</button>
        </div>
    </div>
</div>
<form name="commute_form" method="post" action="../commute/process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="us_id" value=""/>
<input type="hidden" name="us_name" value=""/>
</form>
