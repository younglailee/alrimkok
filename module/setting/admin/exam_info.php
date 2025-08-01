<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\SettingAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oSetting = new SettingAdmin();
$oSetting->init();
$pk = $oSetting->get('pk');

/* check auth */
if (!$oSetting->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
/* data */
$uid = 1;
$data = $oSetting->selectDetail($uid);

/* search condition */
$query_string = $oSetting->get('query_string');

/* code */
$cs_type_arr = $oSetting->get('cs_type_arr');
$cs_refund_type_arr = $oSetting->get('cs_refund_type_arr');
$cs_category_arr = $oSetting->get('cs_category_arr');
$cs_state_arr = $oSetting->get('cs_state_arr');
$use_arr = $oSetting->get('use_arr');

/* file */
$max_file = $oSetting->get('max_file');
$file_list = $data['file_list'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
} else {
    $mode = 'update';
}
// 다음 우편번호 서비스 yllee 190306
?>
<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script type="text/javascript">
    //<![CDATA[
    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function (data) {
                var roadAddr = data.roadAddress;
                $("input[name='cp_zip']").val(data.zonecode);
                $("input[name='cp_address']").val(roadAddr);
                $("input[name='cp_cp_address2']").focus();
            }
        }).open();
    }

    $(function () {
    });
    //]]>
</script>
<div id="<?= $module ?>">
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
            <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
            <input type="hidden" name="page" value="<?= $page ?>"/>
            <input type="hidden" name="uid" value="<?= $uid ?>"/>
            <input type="hidden" name="mode" value="<?= $mode ?>"/>
            <input type="hidden" name="target" value="exam"/>
            <input type="hidden" name="return_uri" value="./exam_info.html"/>
            <fieldset>
                <legend>기본 정보</legend>
                <h4>기본 정보</h4>
                <table class="write_table">
                    <caption>기본 정보 입력 테이블</caption>
                    <colgroup>
                        <col style="width:150px"/>
                        <col/>
                    </colgroup>
                    <tbody>
                    <?= Html::makeTextareaInTable('중간평가유의사항', 'midterm_notice', $data['midterm_notice'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('최종시험유의사항', 'exam_notice', $data['exam_notice'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('레포트유의사항', 'report_notice', $data['report_notice'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('레포트 모사답안 처리기준', 'report_standard', $data['report_standard'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('북러닝(환급)<br/>최종시험유의사항', 'book_exam_caution', $data['book_exam_caution'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('북러닝(환급)<br/>레포트유의사항', 'book_report_caution', $data['book_report_caution'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('북러닝(비환급)<br/>최종시험유의사항', 'book_exam_notice', $data['book_exam_notice'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('북러닝(비환급)<br/>레포트유의사항', 'book_report_notice', $data['book_report_notice'], 'required', 9, 130, 3) ?>
                    </tbody>
                </table>
            </fieldset>

            <div class="button">
                <button type="submit" class="sButton primary">확인</button>
            </div>
        </form>
    </div>
</div>
<form name="check_form" method="post" action="./process.html">
    <input type="hidden" name="flag_json" value="1"/>
    <input type="hidden" name="mode" value=""/>
    <input type="hidden" name="mb_id" value=""/>
    <input type="hidden" name="mb_pw" value=""/>
</form>
