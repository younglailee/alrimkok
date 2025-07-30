<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\SettingAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oSetting = new SettingAdmin();
$oSetting->init();
$pk = $oSetting->get('pk');

/* check auth */
if (!$oSetting->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
/* data */
$uid = $oSetting->get('uid');
$data = $oSetting->selectDetail($uid);

/* search condition */
$query_string = $oSetting->get('query_string');

/* code */
$cs_type_arr = $oSetting->get('cs_type_arr');
$cs_refund_type_arr = $oSetting->get('cs_refund_type_arr');
$cs_category_arr = $oSetting->get('cs_category_arr');
$cs_state_arr = $oSetting->get('cs_state_arr');
$bl_auth_type_arr = $oSetting->get('bl_auth_type_arr');

/* file */
$max_file = $oSetting->get('max_file');
$file_list = $data['file_list'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
} else {
    $mode = 'update';
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>
<div id="<?= $module ?>">
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>
        <fieldset>
        <legend>기본 정보</legend>
        <h4>기본 정보</h4>
        <table class="write_table">
        <caption>기본 정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class="required"><label for="bl_auth_type">권한구분</label></th>
            <td>
                <select name="bl_auth_type" id="bl_auth_type" class="select" title="권한구분">
                <?= Html::makeSelectOptions($bl_auth_type_arr, $data['bl_auth_type'], 1) ?>
                </select>
            </td>
            <th><label for="bl_name">이름</label></th>
            <td>
                <?= Html::makeInputText('bl_name', '이름', $data['bl_name'], 'required', 20, 10) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="bl_subject">제목</label></th>
            <td colspan="3">
                <?= Html::makeInputText('bl_subject', '제목', $data['bl_subject'], 'required', 100, 90) ?>
            </td>
        </tr>
        <tr>
            <th class="required">금액</th>
            <td>
                <?= Html::makeInputText('cs_money', '교육비', $data['cs_money'], 'number', 20, 10) ?> 원
            </td>
            <th class="required">지급일</th>
            <td>
                <input type="text" name="payemnt_date" id="payemnt_date" value="<?= $data['payemnt_date'] ?>"
                       class="text date" size="10" maxlength="10" title="지급일"/>
            </td>
        </tr><tr>
            <th class="required"><label for="bl_subject">제목</label></th>
            <td colspan="3">
                <?= Html::makeInputText('bl_subject', '제목', $data['bl_subject'], 'required', 100, 90) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="bl_account">입금계좌</label></th>
            <td colspan="3">
                <select name="bl_bank" class="select" title="은행">
                <option value="">은횅</option>
                <option value="">IBK기업은행</option>
                <option value="">KDB산업은행</option>
                <option value="">KEB하나은행</option>
                <option value="">NH농협은행</option>
                <option value="">SC제일은행</option>
                <option value="">Sh수협은행</option>
                <option value="">경남은행</option>
                <option value="">광주은행</option>
                <option value="">국민은행</option>
                <option value="">대구은행</option>
                <option value="">대구은행</option>
                <option value="">부산은행</option>
                <option value="">신한은행</option>
                <option value="">우리은행</option>
                <option value="">우체국</option>
                <option value="">전북은행</option>
                <option value="">제주은행</option>
                <option value="">카카오뱅크</option>
                <option value="">케이뱅크</option>
                <option value="">한국씨티은행</option>
                </select>
                <?= Html::makeInputText('bl_account', '입금계좌', $data['bl_account'], 'required', 40, 30) ?>
            </td>
        </tr>
        <?= Html::makeTextareaInTable('비고', 'bl_memo', $data['bl_memo'], '', 2, 100) ?>
        <?php
        for ($i = 0; $i < 3; $i++) {
            ?>
            <tr>
                <th class="required"><label for="cp_ceo">첨부서류 #<?= $i + 1 ?></label></th>
                <td colspan="3">
                    <input type="hidden" name="fi_type[]" value="setting"/>
                    <input type="file" name="atch_file[]" id="setting_<?= $i + 1 ?>" class="file" size="100"
                           title="첨부서류"/>
                    <?php
                    if ($file_list[$i]['fi_id']) {
                        ?>
                        <p>
                            <input type="checkbox" name="del_file[]" id="del_file_<?= $i + 1 ?>"
                                   value="<?= $file_list[$i]['fi_id'] ?>" class="checkbox" title="기존파일 삭제"/>
                            <label for="del_file_<?= $i + 1 ?>">기존파일삭제</label>
                            <span>|</span>
                            <a href="./download.html?fi_id=<?= $file_list[$i]['fi_id'] ?>" class="btn_download"
                               target="_blank"
                               title="새창 다운로드">
                                <strong><?= $file_list[$i]['fi_name'] ?></strong>
                                <span>(<?= $file_list[$i]['bt_fi_size'] ?>)</span>
                            </a>
                        </p>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
            <?php if ($mode == 'update') { ?>
                <a href="./process.html?mode=delete&<?= $pk ?>=<?= $uid ?>&page=<?= $page ?><?= $query_string ?>"
                   class="sButton warning btn_delete" title="삭제">삭제</a>
            <?php } ?>
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
