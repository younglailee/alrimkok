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
$page = $oSetting->get('page');

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
global $module;
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
            <input type="hidden" name="target" value="terms"/>
            <input type="hidden" name="return_uri" value="./terms_info.html"/>
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
                    <?= Html::makeTextareaInTable('필수약관', 'necessary', $data['necessary'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('사이트이용약관', 'terms', $data['terms'], 'required', 9, 130, 3) ?>
                    <?= Html::makeTextareaInTable('개인정보취급방침', 'privacy', $data['privacy'], 'required', 9, 130, 3) ?>
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
