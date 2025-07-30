<?php
/**
 * @file    view.php
 * @author  Alpha-Edu
 */
use sFramework\NoticeAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';

/* init Class */
$oBoard = new NoticeAdmin();
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
<div id="<?=$module?>">
    <!-- view -->
    <div class="view">
        <h4 class="subject">
            <?= ($flag_use_category) ? '[' . $data['bd_category'] . ']': '' ?>
            <?= $data['bd_subject'] ?>
        </h4>

        <div class="info division_80x20">
            <div class="left">
                <dl>
                <dt>작성자</dt>
                <dd><?= $bd_code == 'news' && $data['bd_writer_mode'] ? '관리자' : $list[$i]['bd_writer_name']?> (<?= $data['bd_writer_ip'] ?>)</dd>
                <dt>작성일시</dt>
                <dd><?= $data['bt_reg_datetime'] ?></dd>
                <dt>조회수</dt>
                <dd><?= number_format($data['bd_hit']) ?></dd>
                </dl>
            </div>

            <div class="right">
                <?php if ($flag_use_state) { ?>
                <dl>
                <dt>상태</dt>
                <dd><strong class="<?=$data['state_class']?>"><?= $data['txt_bd_state'] ?></strong></dd>
                </dl>
                <?php } ?>
            </div>
        </div>

        <?php if ($flag_use_bgn) { ?>
        <div class="info">
            <div class="left">
                <dl>
                <dt>진행기간</dt>
                <dd><?= $data['bt_bd_bgn_date'] ?><?= ($flag_use_end) ? ' ~ ' . $data['bt_bd_end_date'] : '' ?></dd>
                </dl>
            </div>

            <div class="right">

            </div>
        </div>
        <?php } ?>

        <div class="content">
            <?= $data['bd_content'] ?>
        </div>

        <?php if ($data['cnt_file'] > 0) { ?>
        <div class="file">
            <p>현재 게시물에는 총 <strong><?= $data['cnt_file']?>개</strong>의 파일이 첨부되어 있습니다.</p>
            <ul>
            <?php for ($i = 0; $i < count($file_list); $i++) { ?>
            <li>
                <a href="./download.html?fi_id=<?=$file_list[$i]['fi_id']?>" class="btn_download" target="_blank" title="새창 다운로드">
                    <strong><?=$file_list[$i]['fi_name']?></strong>
                    <span>(<?=$file_list[$i]['bt_fi_size']?>b)</span>
                </a>
            </li>
            <?php } ?>
            </ul>
        </div>
        <?php } ?>

        <!-- view_footer -->
        <div class="view_footer">
            <div class="left">
            </div>

            <div class="right">
                <a href="./list.html?page=<?=$page?><?=$query_string?>" class="sButton active" title="목록">목록</a>
            </div>
        </div>
        <!-- //view_footer -->

        <!-- around -->
        <div class="around">
            <ul>
            <li class="prev">
                <?php if ($prev_data[$pk]) { ?>
                <a href="./view.html?<?=$pk?>=<?=$prev_data[$pk]?>&page=<?=$page?><?=$query_string?>">
                    <em>이전글</em>
                    <?= $prev_data['bd_subject'] ?>
                </a>
                <?php } else { ?>
                <span>
                    <em>이전글</em>
                    게시물이 존재하지 않습니다.
                </span>
                <?php } ?>
            </li>
            <li class="next">
                <?php if ($next_data[$pk]) { ?>
                <a href="./view.html?<?=$pk?>=<?=$next_data[$pk]?>&page=<?=$page?><?=$query_string?>">
                    <em>다음글</em>
                    <?= $next_data['bd_subject'] ?>
                </a>
                <?php } else { ?>
                <span>
                    <em>다음글</em>
                    게시물이 존재하지 않습니다.
                </span>
                <?php } ?>
            </li>
            </ul>
        </div>
        <!-- //around -->

    </div>
    <!-- //view -->

</div>
