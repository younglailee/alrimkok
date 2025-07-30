<?php
/**
 * @file    view.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\NoticeUser;

if (!defined('_ALPHA_')) {
    exit;
}
global $layout;
global $layout_uri;
$page = ($_GET['page']) ?: $_POST['page'];
$bd_code = ($_GET['bd_code']) ?: $_POST['bd_code'];

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
//print_r($data);
?>

<section id="board-view" class="contents">
    <div class="container">
        <h2 class="sec-title">공지사항</h2>
        <div class="top">
            <h3 class="title"><?= $data['bd_subject'] ?></h3>
            <div class="info">
                <p class="start-write">최초 작성일 <span><?= $data['bt_reg_date'] ?></span></p>
                <p class="end-write">최종 수정일 <span><?= $data['bt_upt_date'] ?></span></p>
                <p class="view">조회수 <span><?= $data['bd_hit'] ?></span></p>
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
