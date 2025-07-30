<?php
/**
 * @file    view.php
 * @author  Alpha-Edu
 */

use sFramework\NoticeUser;
use sFramework\Html;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';
/* init Class */
$oBoard = new NoticeUser();
$oBoard->init();
$pk = $oBoard->get('pk');
/* data */
$uid = $oBoard->get('uid');
$data = $oBoard->selectDetail($uid);
/* check auth */
if (!$uid || !$data[$pk]) {
    Html::alert('비정상적인 접근입니다.');
}
/* search condition */
$query_string = $oBoard->get('query_string');
/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_bgn = $oBoard->get('flag_use_bgn');
$flag_use_end = $oBoard->get('flag_use_end');
$flag_use_state = $oBoard->get('flag_use_state');
/* file */
$max_file = $oBoard->get('max_file');
$file_list = $data['file_list'];
$cnt_file = $data['cnt_file'];
/* around */
$prev_data = $oBoard->selectAroundData($uid, 'prev');
$next_data = $oBoard->selectAroundData($uid, 'next');
?>
<style>
    #new_cmm_sub {line-height: 1.6; letter-spacing: -0.2px;}
    .set {width: 1440px; text-align: center; margin: 0 auto;}
    .title {font-size: 40px; text-align: left; margin-bottom: 33px;}
    .title img {width: 35px; height: 35px; margin-right: 14px;}

    .select {display: flex; align-items: center; margin-bottom: 45px;}
    .select a {float: left; width: 480px; height: 60px; font-size: 16px; text-align : center; line-height : 60px; background: #F6F6F6; border: 1px solid #DDDDDD;}
    .select .selected { color: #2342B5; background: #FFFFFF; border: 1px solid #2342B5; }

    .subject { font-size: 30px; text-align: left; margin-bottom: 10px; }
    .reg_date { font-size: 16px; float: left; padding-right: 20px; }
    .content { font-size: 16px; text-align: left; padding: 30px 30px 150px 30px; }

    .left dl { border-bottom: 1px solid #C4C4C4; height: 45px; font-size: 15px; line-height : 45px;}
    .left dl:first-child { border-top: 1px solid #C4C4C4;}
    .left dt { height: 45px; width: 110px; background: #F6F6F6; float: left; text-align: center; }
    .left dt p { display: inline-block; width: 55px; text-align: left; }
    .left dd { height: 45px; text-align: left; padding-left: 125px; }
    .left dd a { color: #0061AE; text-decoration: underline; }

    .view_paging {  }

    .button { float: right; }
    .button a {display: inline-block; padding:20px 60px; text-align: center; font-size: 16px; color:#FFFFFF; border-radius: 4px; background: #000000;}
</style>
<div id="new_cmm_sub">
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
        <!-- board_view -->
        <div class="board_view">
            <div class="subject"><?= $data['bd_subject'] ?></div>
            <div class="reg_date"><?= $data['bt_reg_date'] ?></div>
            <div style="clear: both; padding-bottom: 20px; border-bottom: 1px solid #C4C4C4;"></div>
            <div class="left">
                <?php
                if ($file_list) {
                    for ($j = 0; $j < count($file_list); $j++) {
                        ?>
                        <dl>
                            <dt><p>첨부파일</p></dt>
                            <dd><a href="./download.html?fi_id=<?= $file_list[$j]['fi_id'] ?>" class="btn_download"
                                      target="_blank" title=""><?= $file_list[$j]['fi_name'] ?></a></dd>
                        </dl>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="content">
                <?= nl2br($data['bd_content']) ?>
            </div>
            <!-- view_paging -->
            <div class="view_paging">
                <dl class="prev">
                    <dt>이전글</dt>
                    <dd>
                        <?php if ($prev_data[$pk]) { ?>
                            <a href="./view.html?<?= $pk ?>=<?= $prev_data[$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $prev_data['bd_subject'] ?></a>
                        <?php } else { ?>게시물이 존재하지 않습니다.<?php } ?>
                    </dd>
                </dl>
                <dl class="next">
                    <dt>다음글</dt>
                    <dd>
                        <?php if ($next_data[$pk]) { ?>
                            <a href="./view.html?<?= $pk ?>=<?= $next_data[$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $next_data['bd_subject'] ?></a>
                        <?php } else { ?>게시물이 존재하지 않습니다.<?php } ?>
                    </dd>
                </dl>
            </div>
            <!-- //view_paging -->

            <!-- button -->
            <div class="button">
                <a href="./new_list.html?page=<?= $page ?><?= $query_string ?>">목록</a>
            </div>
            <!-- //button -->
        </div>
    </div>
</div>