<?php
/**
 * @file    view.php
 * @author  Alpha-Edu
 */

use sFramework\QnaAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout, $bd_code, $module, $member;
$page = $_GET['page'];
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';

/* init Class */
$oBoard = new QnaAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');

/* data */
$uid = $oBoard->get('uid');
$data = $oBoard->selectDetail($uid);

if (!$data['bd_answer_name']) {
    $data['bd_answer_name'] = $member['mb_name'];
}
/* check auth */
if (!$uid || !$data[$pk]) {
    Html::alert('비정상적인 접근입니다.');
}
/* search condition */
$query_string = $oBoard->get('query_string');
?>
<div id="<?= $module ?>">
    <div class="view">
        <h4 class=""><?= $data['bd_subject'] ?></h4>
        <div class="info division_80x20">
            <div class="left">
                <dl>
                <dt>작성자</dt>
                <dd><?= $data['bd_writer_name'] ?> (<?= $data['bd_writer_ip'] ?>)</dd>
                <dt>작성일시</dt>
                <dd><?= $data['bt_reg_datetime'] ?></dd>
                <dt>조회수</dt>
                <dd><?= number_format($data['bd_hit']) ?></dd>
                </dl>
            </div>
            <div class="right">
                <dl>
                <dt>상태</dt>
                <dd><strong class="<?= $data['state_class'] ?>"><?= $data['txt_bd_state'] ?></strong></dd>
                </dl>
            </div>
        </div>
        <div class="info">
            <div class="left">
                <dl>
                <dt>연락처</dt>
                <dd><?= $data['bd_writer_tel'] ?> (<?= $data['txt_bd_use_sms'] ?>)</dd>
                <dt>이메일</dt>
                <dd><?= $data['bd_writer_email'] ?> (<?= $data['txt_bd_use_email'] ?>)</dd>
                </dl>
            </div>
            <div class="right">
            </div>
        </div>
        <div class="content">
            <?= $data['bd_content'] ?>
        </div>
    </div>
    <div class="reply">
        <form name="reply_form" method="post" action="./process.html" onsubmit="return submitReplyForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        </fieldset>

        <fieldset>
        <legend>기본정보</legend>
        <input type="hidden" name="mode" value="update_reply"/>
        <input type="hidden" name="<?= $pk ?>" value="<?= $data[$pk] ?>"/>
        <input type="hidden" name="reply_mode" value="qna"/>

        <h4>답변작성</h4>
        <table class="write_table" border="1">
        <caption>기본정보 입력 테이블</caption>
        <colgroup>
        <col width="140"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <?= Html::makeInputTextInTable('답변자', 'bd_answer_name', $data['bd_answer_name'], 'required readonly', 20, 10) ?>
        <?= Html::makeTextareaInTable('답변내용', 'bd_answer_content', $data['bd_answer_content'], 'required', 10, 90) ?>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="./view.html?<?= $pk ?>=<?= $data[$pk] ?>&page=<?= $page ?><?= $query_string ?>"
               class="sButton active" title="취소">취소</a>
        </div>
        </form>
    </div>
</div>
