<?php
/**
 * @file    main.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\NoticeAdmin;
use sFramework\QnaAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

//Html::movePage('../visit/statistics.html');
$doc_title = '메인';
//$layout_size = 'normal';

// 공지사항
$oNotice = new NoticeAdmin();
$oNotice->init();
$oNotice->set('bd_code', 'admin_notice');
$notice_pk = $oNotice->get('pk');
$nt_list = $oNotice->selectLatestList(4);
$bd_code_arr = $oNotice->get('bd_code_arr');

// 1:1 문의
$oQna = new QnaAdmin();
$oQna->init();
$oQna->set('bd_code', 'qna');
$qna_pk = $oQna->get('pk');
$qna_list = $oQna->selectLatestList(4);
$bd_category_arr = $oQna->get('bd_category_arr');
//global $member;
//print_r($member);
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
#main div.main_box.banner {padding:20px 30px; background-color:#f5f5f5; border:1px solid #538fd4; }
#main div.main_box.banner table {background-color:#fff;}

#main div.main_top { }
#main div.main_article { }
#main div.main_batch_user span { font-size:30px;}
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
    <?php
    // 센터장님은 빈 페이지가 나오도록 처리
    global $member;
    if ($member['mb_id'] != 'kjhok' && $member['mb_id'] != 'green' && $member['mb_id'] != 'green2') {
        ?>
        <div class="main_box">
            <div class="main_top">
                <h4 style="float:left">알림</h4>
            </div>
            <div class="main_article">
                <table class="list_table border odd">
                <colgroup>
                <col style="width:20%"/>
                <col/>
                <col style="width:20%"/>
                <col style="width:20%"/>
                </colgroup>
                <thead>
                <tr>
                    <th>카테고리</th>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>작성일시</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (is_array($nt_list)) {
                    $nt_count = count($nt_list);
                    for ($i = 0; $i < $nt_count; $i++) {
                        $nt_url = "../news/view.html?$notice_pk=" . $nt_list[$i][$notice_pk];
                        $ow_url = "../owner/write.html?$notice_pk=" . $nt_list[$i][$notice_pk];
                        ?>
                        <tr>
                            <td><?= $bd_code_arr[$nt_list[$i]['bd_code']] ?></td>
                            <td class="">
                                <?php
                                if ($nt_list[$i]['bd_code'] == 'owner') {
                                    ?>
                                    <a href="<?= $ow_url ?>"><?= $nt_list[$i]['bd_etc5'] ?></a>
                                    <?php
                                } else {
                                    ?>
                                    <a href="<?= $nt_url ?>"><?= $nt_list[$i]['bd_subject'] ?></a>
                                    <?php
                                } ?>
                            </td>
                            <td>
                                <?php
                                if ($nt_list[$i]['bd_code'] == 'owner') {
                                    ?>
                                    <?= $nt_list[$i]['bd_etc1'] ?>
                                    <?php
                                } else {
                                    ?>
                                    <?= $nt_list[$i]['bd_writer_name'] ?>
                                    <?php
                                } ?></td>
                            <td><?= $nt_list[$i]['reg_time'] ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    $nt_count = 0;
                }
                echo (!$nt_count) ? Html::makeNoTd(4) : '';
                ?>
                </tbody>
                </table>
            </div>
        </div>
        <div class="main_box">
            <div class="main_top">
                <h4 style="float:left">문의</h4>
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
                    <th>작성일시</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (is_array($qna_list)) {
                    $qna_count = count($qna_list);
                    for ($i = 0; $i < $qna_count; $i++) {
                        $qna_url = "../qna/view.html?$qna_pk=" . $qna_list[$i][$qna_pk];
                        ?>
                        <tr>
                            <td class=""><a
                                    href="<?= $qna_url ?>"><?= $qna_list[$i]['bd_subject'] ?></a>
                            </td>
                            <td><?= $qna_list[$i]['bd_writer_name'] ?>(<?= $qna_list[$i]['reg_id'] ?>)</td>
                            <td><?= $qna_list[$i]['reg_time'] ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    $qna_count = 0;
                }
                echo (!$qna_count) ? Html::makeNoTd(3) : '';
                ?>
                </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<form name="commute_form" method="post" action="../commute/process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="us_id" value=""/>
<input type="hidden" name="us_name" value=""/>
</form>
