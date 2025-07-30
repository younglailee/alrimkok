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
$data = $oSetting->selectPayment($uid);

/* search condition */
$query_string = $oSetting->get('query_string');

/* code */
$cs_type_arr = $oSetting->get('cs_type_arr');
$cs_refund_type_arr = $oSetting->get('cs_refund_type_arr');
$cs_category_arr = $oSetting->get('cs_category_arr');
$cs_state_arr = $oSetting->get('cs_state_arr');
$use_arr = $oSetting->get('use_arr');
$payment_mode_arr = $oSetting->get('payment_mode_arr');
$tax_type_arr = $oSetting->get('tax_type_arr');
$product_type_arr = $oSetting->get('product_type_arr');
$payment_type_arr = $oSetting->get('payment_type_arr');

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
        <input type="hidden" name="mode" value="update_payment"/>
            <input type="hidden" name="uid" value="<?= $uid ?>"/>
            <input type="hidden" name="target" value="payment"/>
            <input type="hidden" name="return_uri" value="./payment.html"/>
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
            <th class="required"><label for="payment_mode">결제모드</label></th>
            <td>
                <?= Html::makeRadio('payment_mode', $payment_mode_arr, $data['payment_mode'], 1) ?>
            </td>
            <th class="required"><label for="tax_type">과세여부</label></th>
            <td>
                <?= Html::makeRadio('tax_type', $tax_type_arr, $data['tax_type'], 1) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="product_type">상품구분</label></th>
            <td>
                <?= Html::makeRadio('product_type', $product_type_arr, $data['product_type'], 1) ?>
            </td>
            <th class="required"><label for="payment_type">결제방식</label></th>
            <td>
                <?= Html::makeCheckbox('payment_type', $payment_type_arr, $data['payment_type'], 1) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="shop_id">상점ID</label></th>
            <td>
                <?= Html::makeInputText('shop_id', '상점ID', $data['shop_id'], 'required', 20, 10) ?>
            </td>
            <th class="required"><label for="test_id">테스트ID</label></th>
            <td>
                <?= Html::makeInputText('test_id', '테스트ID', $data['test_id'], 'required', 20, 10) ?>
            </td>
        </tr>
        <tr>
            <th class=""><label for="shop_id2">상점ID</label></th>
            <td>
                <?= Html::makeInputText('shop_id2', '상점ID', $data['shop_id2'], '', 20, 10) ?>
                평생교육바우처(농협 채움카드)
            </td>
            <th class=""><label for="test_id2">테스트ID</label></th>
            <td>
                <?= Html::makeInputText('test_id2', '테스트ID', $data['test_id2'], '', 20, 10) ?>
            </td>
        </tr>
        <tr>
            <th><label for="pay_course">수강신청결제</label></th>
            <td>
                <?= Html::makeCheckbox('pay_course', $use_arr, $data['pay_course'], 1) ?>
            </td>
            <th><label for="bank_account">은행계좌번호</label></th>
            <td>
                <?= Html::makeInputText('bank_account', '은행계좌번호', $data['bank_account'], '', 40, 50) ?>
            </td>
        </tr>
        <?= Html::makeTextareaInTable('유의사항<br/>(내일배움)', 'notice', $data['notice'], '', 11, 130, 3) ?>
        <?= Html::makeTextareaInTable('유의사항<br/>(바우처)', 'notice_v', $data['notice_v'], '', 11, 130, 3) ?>
        <?= Html::makeTextareaInTable('환불규정', 'refund_rule', $data['refund_rule'], '', 11, 130, 3) ?>
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
