<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\AdminAdmin;
use sFramework\Format;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
global $layout, $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oAdmin = new AdminAdmin();
$oAdmin->init();
$pk = $oAdmin->get('pk');

/* check auth */
if (!$oAdmin->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
/* data */
$uid = $oAdmin->get('uid');
$data = $oAdmin->selectDetail($uid);
$flag_able_update = $oAdmin->checkUpdateAuth($uid);

/* search condition */
$query_string = $oAdmin->get('query_string');

/* code */
$mb_level_arr = $oAdmin->get('mb_level_arr');
$mb_no_login_arr = $oAdmin->get('mb_no_login_arr');
$mb_auth_code_arr = $oAdmin->get('mb_auth_code_arr');

/* file */
$max_file = $oAdmin->get('max_file');
$file_list = $data['file_list'];
$profile_img = $data['profile_img'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'mb_level' => 9,
        'mb_no_login' => 'N'
    );
} else {
    $mode = 'update';
}
?>
<script type="text/javascript" src="<?= _EDITOR_URI_ ?>/js/HuskyEZCreator.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    /* 아이디 검사 */
    /*
    $("#mb_id").on("blur", function() {
        //validateMemberId(false);
        validateMemberId(true);
    });
    */
    /* 비밀번호 검사 */
    $("#mb_pw").on("blur", function() {
        validateMemberPassword(false);
    });
    // 에디터 초기화
    nhn.husky.EZCreator.createInIFrame({
        oAppRef: oEditors,
        elPlaceHolder: "mb_memo",
        sSkinURI: "<?=_EDITOR_URI_?>/SmartEditor2Skin.html",
        htParams: {
            bUseToolbar: true,
            bUseVerticalResizer: false,
            bUseModeChanger: true,
            nMinWidth: editor_min_width
        },
        fCreator: "createSEditor2"
    });
});
var editor_min_width = 800;
var oEditors = [];
//]]>
</script>

<div id="<?= $module ?>">
    <div class="manual">
        <h4>도움말</h4>
        <p>
            운영자 관리는 모듈 개발을 위해 실제 운영자 관리와 무관한 기능도 구현되어 있습니다.<br/>
            모듈 개발을 할 때는, 운영자 모듈을 참고하세요.
        </p>
    </div>
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        </fieldset>

        <fieldset>
        <legend>기본정보</legend>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>

        <h4>기본사항</h4>
        <table class="write_table" border="1">
        <caption>기본정보 입력 테이블</caption>
        <colgroup>
        <col width="140"/>
        <col width="*"/>
        <col width="140"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th<?= ($mode == 'insert') ? ' class="required"' : '' ?>>
            <?= ($mode == 'insert') ? '<label for="mb_id">아이디</label>' : '아이디' ?>
            </th>
            <td>
                <?php
                if ($mode == 'insert') {
                    ?>
                    <?= Html::makeInputText('mb_id', '아이디', $data['mb_id'], 'required', 20, 20) ?>
                    <input type="hidden" name="flag_mb_id" value="0"/>
                    <span id="state_mb_id" class="state_msg"></span>
                    <p class="comment">
                        아이디는 20자 이하의 영대/소문자, 숫자, _만 입력 가능합니다.
                    </p>
                    <?php
                } else {
                    ?>
                    <strong><?= $data['mb_id'] ?></strong>
                    <input type="hidden" name="mb_id" value="<?= $data['mb_id'] ?>"/>
                    <?php
                }
                ?>
            </td>
            <th>최근로그인</th>
            <td>
                <?= Format::getWithoutNull($data['mb_login_time']) ?>
            </td>
        </tr>
        <tr>
            <th<?= ($mode == 'insert') ? ' class="required"' : '' ?>><label for="mb_pw">패스워드</label></th>
            <td>
                <input type="password" name="mb_pw" id="mb_pw" class="text<?= ($mode == 'insert') ? ' required' : '' ?>"
                       value="" size="20" maxlength="20" title="패스워드"/>
                <input type="hidden" name="flag_mb_pw" value="0"/>
                <span id="state_mb_pw" class="state_msg"></span>
            </td>
            <th<?= ($mode == 'insert') ? ' class="required"' : '' ?>><label for="mb_pw2">패스워드 확인</label>
            <td>
                <input type="password" name="mb_pw2" id="mb_pw2" title="패스워드 확인"
                       class="text<?= ($mode == 'insert') ? ' required' : '' ?>" value="" size="20" maxlength="20"/>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="mb_name">이름</label></th>
            <td>
                <?= Html::makeInputText('mb_name', '이름', $data['mb_name'], 'required', 20, 10) ?>
            </td>
            <th class="required"><label for="mb_level">권한</label></th>
            <td>
                <select name="mb_level" id="mb_level" class="select required" title="권한">
                <?= Html::makeSelectOptions($mb_level_arr, $data['mb_level'], 1) ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="mb_email">이메일</label></th>
            <td>
                <?= Html::makeInputText('mb_email', '이메일', $data['mb_email'], 'email', 40, 50) ?>
            </td>
            <th><label for="mb_hp">휴대폰</label></th>
            <td>
                <?= Html::makeInputText('mb_hp', '휴대폰', $data['mb_hp'], 'tel', 20, 15) ?>
            </td>
        </tr>
        <?= Html::makeTextareaInTable('메모', 'mb_memo', $data['mb_memo'], '', 3, 80, 3) ?>
        </tbody>
        </table>
        </fieldset>

        <fieldset class="etc">
        <legend>권한 정보</legend>
        <h4>권한 정보</h4>
        <!--<p class="guide">
            상품이미지는 되도록이면 영문, 숫자으로만 등록해주세요. 한글로 등록시 정상적으로 출력되지 않을 수 있습니다.
        </p>-->
        <table class="write_table" border="1">
        <caption>권한 정보 입력 테이블</caption>
        <colgroup>
        <col width="140"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th>권한부여</th>
            <td>
                <?= Html::makeCheckbox('mb_auth_code', $mb_auth_code_arr, $data['mb_auth_codes'], 1) ?>
            </td>
        </tr>
        <tr>
            <th>로그인<br/>허용 여부</th>
            <td>
                <?= Html::makeRadio('mb_no_login', $mb_no_login_arr, $data['mb_no_login'], 1) ?>
                <span class="comment">로그인 금지를 설정할 경우, 관리자모드에 로그인이 되지 않습니다.</span>
            </td>
        </tr>
        <tr>
            <th>로그인<br/>허용 IP</th>
            <td>
                <?= Html::makeTextarea('mb_auth_ips', '로그인 허용 IP', $data['mb_auth_ips'], '', 3, 80) ?>
                <p class="comment">
                    로그인 허용 IP를 2개 이상 작성할 경우, Enter로 구분해주세요.<br/>
                    로그인 허용 IP를 비워둘 경우, 모든 IP에서 로그인이 허용됩니다.
                </p>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
            <?php
            if ($mode == 'update') {
                ?>
                <a href="./process.html?mode=delete&<?= $pk ?>=<?= $uid ?>&page=<?= $page ?><?= $query_string ?>"
                   class="sButton warning btn_delete" title="삭제">삭제</a>
                <?php
            }
            ?>
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