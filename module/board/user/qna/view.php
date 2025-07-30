<?php
/**
 * @file    view.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\QnaUser;

if (!defined('_ALPHA_')) {
    exit;
}
global $member;
global $layout_uri;
global $layout;
global $bd_code;
$page = $_GET['page'];

/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';
$doc_title = '1:1문의하기';

// 한국인터넷진흥원 웹 취약점 조치사항: 웹 접근 제어 yllee 220722
if (!$member['mb_id']) {
    $move_url = $layout_uri . '/member/login.html?return_uri=' . $this_uri;
    Html::alert('로그인 후 이용 가능합니다.', $move_url);
}
/* init Class */
$oBoard = new QnaUser();
$oBoard->init();
$pk = $oBoard->get('pk');

/* data */
$uid = $oBoard->get('uid');
$data = $oBoard->selectDetail($uid);

if ($data['code'] == 'failure') {
    Html::alert($data['msg']);
}
/* check auth */
$flag_update_auth = $oBoard->checkUpdateAuth($uid);

/* search condition */
$query_string = $oBoard->get('query_string');

/* file */
$max_file = $oBoard->get('max_file');
$file_list = $data['file_list'];
$cnt_file = $data['cnt_file'];

/* around */
$prev_data = $oBoard->selectAroundData($uid, 'prev');
$next_data = $oBoard->selectAroundData($uid, 'next');


$reg_date = explode(' ', $data['bd_answer_time'])[0];
?>


<section id="board-view" class="contents">
    <div class="container">
        <h2 class="sec-title">1:1 문의</h2>

        <div class="top">
            <h3 class="title"><?= $data['bd_subject'] ?></h3>
            <div class="info">
                <p class="inquiry-write">문의 날짜 <span><?= $data['bt_reg_date'] ?></span></p>
            </div>
        </div>
        <?php
        if ($file_list) {
            for ($j = 0; $j < count($file_list); $j++) {
                ?>
                <div class="file-box">
                    <p>첨부파일</p>
                    <a href="./download.html?fi_id=<?= $file_list[$j]['fi_id'] ?>" class="clamp c1 btn_download"
                       target="_blank" title=""><?= $file_list[$j]['fi_name'] ?></a>
                </div>
                <?php
            }
        } else {
            echo '<div style="height:40px"></div>';
        }
        ?>
        <div class="con">
            <?= nl2br($data['bd_content']) ?>
        </div>
        <?php
        if ($reg_date) {
            ?>
            <div class="answer">
                <div class="an-top">
                    <p class="title">답변 내용</p>
                    <p class="date">답변 날짜 <span><?= str_replace('-', '.', $reg_date) ?></span></p>
                </div>
                <div class="an-bottom">
                    <?= nl2br($data['bd_answer_content']) ?>
                </div>
            </div>
            <?php
        }
        ?>
        <ul class="pg-move">
        <li class="prev">
            <div>다음글</div>
            <?php if ($prev_data[$pk]) { ?>
                <a class="pg-title clamp c1"
                   href="./view.html?<?= $pk ?>=<?= $prev_data[$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $prev_data['bd_subject'] ?></a>
            <?php } else { ?><span class="pg-title clamp c1">게시물이 존재하지 않습니다.</span><?php } ?>
        </li>
        <li class="next">
            <div>이전글</div>
            <?php if ($next_data[$pk]) { ?>
                <a class="pg-title clamp c1"
                   href="./view.html?<?= $pk ?>=<?= $next_data[$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $next_data['bd_subject'] ?></a>
            <?php } else { ?><span class="pg-title clamp c1">게시물이 존재하지 않습니다.</span><?php } ?>
        </li>
        </ul>

        <a class="list-btn" href="./list.html?page=<?= $page ?><?= $query_string ?>"><p>목록</p></a>
    </div>
</section>
