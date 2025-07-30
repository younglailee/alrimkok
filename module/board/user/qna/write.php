<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\QnaUser;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
global $member;
global $layout_uri;
global $layout;
global $bd_code;
$page = $_GET['page'];

/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/write.html';
$doc_title = '1:1문의하기';

// 로그인 창 이동 후 로그인 시 해당 페이지로 리턴
if (!$member['mb_id']) {
    $move_url = $layout_uri . '/member/login.html?return_uri=' . $this_uri;
    Html::alert('로그인 후 이용 가능합니다.', $move_url);
}

/* init Class */
$oBoard = new QnaUser();
$oBoard->init();
$pk = $oBoard->get('pk');

/* data */
global $member;
$uid = $oBoard->get('uid');
if ($uid) {
    $data = $oBoard->selectDetail($uid);
}

// 한국인터넷진흥원 웹 취약점 조치사항: 웹 접근 제어 yllee 220722
if ($data['code'] == 'failure') {
    Html::alert($data['msg']);
}
/* search condition */
$query_string = $oBoard->get('query_string');

/* code */
$bd_use_sms_arr = $oBoard->get('bd_use_sms_arr');
$bd_use_email_arr = $oBoard->get('bd_use_email_arr');
//$recruit_list_arr = $oBoard->recruitList();

/* file */
$max_file = $oBoard->get('max_file');
$file_list = $data['file_list'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $txt_mode = '등록';

    $data = array(
        'bd_writer_name' => Session::getSession('ss_bd_writer_name'),
        'bd_writer_tel' => Session::getSession('ss_bd_writer_tel'),
        'bd_reg_date' => _NOW_DATE_
    );
    if ($_GET['type']) {
        $data['bd_etc1'] = $_GET['type'];
    }
} else {
    $mode = 'update';
    $txt_mode = '수정';
}
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    $("#atch_file1").on('change', function() {
        var fileName = $(this).val().split("\\").pop();
        if (fileName) {
            $("#upload-file1").val(fileName);
        } else {
            $("#upload-file1").val("첨부파일");
        }
    });

    $("#atch_file2").on('change', function() {
        var fileName = $(this).val().split("\\").pop();
        if (fileName) {
            $("#upload-file2").val(fileName);
        } else {
            $("#upload-file2").val("첨부파일");
        }
    });
});
//]]>
</script>


<section id="board-write" class="contents">
    <div class="container">
        <h2 class="sec-title">1:1 문의</h2>
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <input type="hidden" name="bd_reg_date" value="<?= $data['bd_reg_date'] ?>"/>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>
        <input type="hidden" name="<?= $pk ?>" value="<?= $data[$pk] ?>"/>
        <input type="hidden" name="bd_writer_name" value="<?= $member['mb_name'] ?>"/>
        <input type="hidden" name="bd_writer_tel" value="<?= $member['mb_tel'] ?>"/>
        <input type="hidden" name="bd_category" value="homepage"/>

        <div class="writeForm">
            <p class="help">필수정보</p>
            <fieldset>
            <legend>1:1 문의 작성</legend>
            <div class="board-input required-input">
                <div class="left">
                    <label class="board-label" for="inquiry_title">제목</label>
                </div>
                <input type="text" id="inquiry_title" name="bd_subject" value="<?= $data['bd_subject'] ?>"
                       placeholder="제목명을 입력해주세요">
            </div>
            <div class="board-input required-input">
                <div class="left">
                    <label class="board-label" for="inquiry_content">상담내용</label>
                </div>
                <textarea id="inquiry_content" name="bd_content"
                          placeholder="문의할 내용을 작성해주세요"><?= $data['bd_content'] ?></textarea>
            </div>
            <div class="board-input file-input">
                <div class="left">
                    <p class="board-label">첨부파일</p>
                </div>
                <div class="upload-box">
                    <div class="inquiry_file-box">
                        <span class="file-name clamp c1"></span>
                        <span class="file-remove" onclick="removeFile()"><img src="/common/img/user/icon/close-84.svg"
                                                                              class="of-ct" alt="닫기"></span>
                    </div>
                    <input id="inquiry_file" type="file" name="atch_file[]" onchange="updateFileName(this)">
                    <label for="inquiry_file" class="btn-upload">파일찾기</label>
                </div>
            </div>
            </fieldset>
        </div>
        <div class="btn-wrap">
            <a href="./list.html" class="cancel-btn">취소</a>
            <button class="submit-btn" type="submit">등록</button>
        </div>
        </form>
    </div>
</section>
