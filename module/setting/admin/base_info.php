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
global $module, $page;

// 다음 우편번호 서비스 yllee 190306
?>
<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script type="text/javascript">
//<![CDATA[
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            var roadAddr = data.roadAddress;
            $("input[name='cp_zip']").val(data.zonecode);
            $("input[name='cp_address']").val(roadAddr);
            $("input[name='cp_cp_address2']").focus();
        }
    }).open();
}
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
        <input type="hidden" name="uid" value="<?= $uid ?>"/>
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
            <th class="required"><label for="name_eng">영문명</label></th>
            <td>
                <?= Html::makeInputText('name_eng', '이름', $data['name_eng'], 'required', 20, 15) ?>
            </td>
            <th class="required"><label for="name_corporate">법인명</label></th>
            <td>
                <?= Html::makeInputText('name_corporate', '이름', $data['name_corporate'], 'required', 40, 30) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="cp_number">사업자등록번호</label></th>
            <td>
                <?= Html::makeInputText('cp_number', '사업자등록번호', $data['cp_number'], 'required', 20, 10) ?>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
            <th class="required"><label for="cp_ceo">대표자명</label></th>
            <td>
                <?= Html::makeInputText('cp_ceo', '대표자명', $data['cp_ceo'], 'required', 20, 10) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="cp_tel">대표번호</label></th>
            <td>
                <?= Html::makeInputText('cp_tel', '대표번호', $data['cp_tel'], 'tel required', 20, 15) ?>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
            <th class="required"><label for="cp_fax">팩스번호</label></th>
            <td>
                <?= Html::makeInputText('cp_fax', '팩스번호', $data['cp_fax'], 'tel required', 20, 15) ?>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="cp_email">이메일</label></th>
            <td>
                <?= Html::makeInputText('cp_email', '이메일', $data['cp_email'], 'email', 40, 50) ?>
            </td>
            <th class="required"><label for="establishment">설립연도</label></th>
            <td>
                <?= Html::makeInputText('establishment', '설립연도', $data['establishment'], 'tel required', 20, 15) ?>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
        </tr>
        <tr>
            <th><label for="mb_addr2">주소</label></th>
            <td colspan="3">
                <p>
                    <input type="text" name="cp_zip" value="<?= $data['cp_zip'] ?>"
                           class="text readonly" size="8" maxlength="5" title="우편번호"/>
                    <button type="button" class="sButton small" onclick="execDaumPostcode();">우편번호찾기</button>
                </p>
                <input type="text" name="cp_address" value="<?= $data['cp_address'] ?>"
                       class="text  readonly" size="50" maxlength="40" title="주소"/>
                <input type="text" name="cp_address2" value="<?= $data['cp_address2'] ?>"
                       class="text " size="40" maxlength="30" title="상세주소"/>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="personal_manager">개인정보<br/>책임자</label></th>
            <td>
                <?= Html::makeInputText('personal_manager', '개인정보책임자', $data['personal_manager'], 'required', 20, 15) ?>
            </td>
            <th class="required"><label for="name_corporate">통신판매업<br/>신고번호</label></th>
            <td>
                <?= Html::makeInputText('mail_order_no', '통신판매업신고번호', $data['mail_order_no'], 'required', 40, 30) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <fieldset class="etc">
        <legend>홈페이지 정보</legend>
        <h4>홈페이지 정보</h4>
        <table class="write_table">
        <caption>홈페이지 정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <?php
            $logo_arr = array('상단', '하단', '모바일');
            for ($i = 0; $i < 2; $i++) {
                ?>
                <th class="required"><label for="setting_<?= $i + 1 ?>">로고(<?= $logo_arr[$i] ?>)</label></th>
                <td>
                    <input type="hidden" name="fi_type[]" value="logo_<?= $i + 1 ?>"/>
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
                               target="_blank" title="새창 다운로드">
                                <strong><?= $file_list[$i]['fi_name'] ?></strong>
                                <span>(<?= $file_list[$i]['bt_fi_size'] ?>)</span>
                            </a>
                        </p>
                        <?php
                    }
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            for ($i = 2; $i < 3; $i++) {
                ?>
                <th class="required"><label for="setting_<?= $i + 1 ?>">로고(<?= $logo_arr[$i] ?>)</label></th>
                <td>
                    <input type="hidden" name="fi_type[]" value="logo_<?= $i + 1 ?>"/>
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
                               target="_blank" title="새창 다운로드">
                                <strong><?= $file_list[$i]['fi_name'] ?></strong>
                                <span>(<?= $file_list[$i]['bt_fi_size'] ?>)</span>
                            </a>
                        </p>
                        <?php
                    }
                    ?>
                </td>
                <?php
            }
            for ($i = 0; $i < 1; $i++) {
                ?>
                <th class="required"><label for="certificate_stamp_<?= $i + 1 ?>">수료증확인</label></th>
                <td>
                    <input type="hidden" name="fi_type[]" value="certificate_stamp"/>
                    <input type="file" name="atch_file[]" id="certificate_stamp_<?= $i + 1 ?>" class="file" size="100"
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
                               target="_blank" title="새창 다운로드">
                                <strong><?= $file_list[$i]['fi_name'] ?></strong>
                                <span>(<?= $file_list[$i]['bt_fi_size'] ?>)</span>
                            </a>
                        </p>
                        <?php
                    }
                    ?>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <th><label for="select_login">선택로그인</label></th>
            <td>
                <?= Html::makeCheckbox('select_login', $use_arr, $data['select_login'], 1) ?>
            </td>
            <th><label for="send_sms">SMS발송</label></th>
            <td>
                <?= Html::makeCheckbox('send_sms', $use_arr, $data['send_sms'], 1) ?>
            </td>
        </tr>
        <tr>
            <th><label for="test_contents">콘텐츠테스트</label></th>
            <td>
                <?= Html::makeCheckbox('test_contents', $use_arr, $data['test_contents'], 1) ?>
            </td>
            <th><label for="send_sms">도메인</label></th>
            <td>
                <?= Html::makeInputText('domain', '도메인', $data['domain'], 'required', 40, 30) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <fieldset class="etc">
        <legend>본인인증 정보</legend>
        <h4>본인인증 정보</h4>
        <table class="write_table">
        <caption>본인인증 정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="auth_hp_code">휴대폰인증<br/>코드</label></th>
            <td>
                <?= Html::makeInputText('auth_hp_code', '휴대폰인증코드', $data['auth_hp_code'], '', 40, 30) ?>
            </td>
            <th><label for="auth_hp_pw">휴대폰인증<br/>비밀번호</label></th>
            <td>
                <?= Html::makeInputText('auth_hp_pw', '휴대폰인증비밀번호', $data['auth_hp_pw'], '', 40, 30) ?>
            </td>
        </tr>
        <tr>
            <th><label for="ipin_code">아이핀<br/>코드</label></th>
            <td>
                <?= Html::makeInputText('ipin_code', '아이핀코드', $data['ipin_code'], '', 40, 30) ?>
            </td>
            <th><label for="ipin_pw">아이핀<br/>비밀번호</label></th>
            <td>
                <?= Html::makeInputText('ipin_pw', '아이핀비밀번호', $data['ipin_pw'], '', 40, 30) ?>
            </td>
        </tr>
        <?php
        // OTP 대신 본인인증 적용 기능 추가 yllee 220204
        ?>
        <tr>
            <th><label for="otp_auth_chk">OTP 대신<br/>본인인증 적용</label></th>
            <td colspan="3">
                <?= Html::makeCheckbox('otp_auth_chk', $use_arr, $data['otp_auth_chk'], 1) ?>
                <input type="text" name="otp_auth_date" id="otp_auth_date" value="<?= $data['otp_auth_date'] ?>"
                       class="text date" size="10" maxlength="10" title="적용일자"/>
                <input type="text" name="otp_auth_s_time" id="otp_auth_s_time" value="<?= $data['otp_auth_s_time'] ?>"
                       class="text" size="5" maxlength="5" title="시작시간"/>
                ~
                <input type="text" name="otp_auth_e_time" id="otp_auth_e_time" value="<?= $data['otp_auth_e_time'] ?>"
                       class="text" size="5" maxlength="5" title="종료시간"/>
                예) 2022-02-04 18:00 ~ 22:00
            </td>
        </tr>
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
