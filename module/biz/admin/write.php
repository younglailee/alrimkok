<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\BizAdmin;
use sFramework\Format;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout, $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';
if ($_GET['book'] == 'book') {
    $this_uri = '/web' . $layout . '/book/user_list.html';
}
if ($_GET['live'] == 'live') {
    $this_uri = '/web' . $layout . '/live/user_list.html';
}
/* init Class */
$oBiz = new BizAdmin();
$oBiz->init();
$pk = $oBiz->get('pk');

/* check auth */
if (!$oBiz->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}

/* data */
$uid = $oBiz->get('uid');
$data = $oBiz->selectDetail($uid);

/* search condition */
$query_string = $oBiz->get('query_string');
$page = $_GET['page'];

/* file */
$max_file = $oBiz->get('max_file');
$file_list = $data['file_list'];
$profile_img = $data['profile_img'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
} else {
    $mode = 'update';
}
print_r($data)
?>
<script type="text/javascript" src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
});


var editor_min_width = 800;
var oEditors = [];
//]]>
</script>
<style>
    table.write_table tr td {
        width: 325px;
    }
</style>
<div id="<?= $module ?>"<?= $div_style ?>>
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>
        <input type="hidden" name="book" value="<?= $_GET['book'] ?>"/>
        <fieldset>
        <legend>기본 정보</legend>
        <h4>기본 사항</h4>
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
            <th class="required"><label for="mb_name">이름</label></th>
            <td>
                <input type="hidden" name="mb_name_old" value="<?= $data['mb_name'] ?>"/>
                <?= Html::makeInputText('mb_name', '이름', $data['mb_name'], 'required', 20, 50) ?>
            </td>
            <th<?= ($mode == 'insert') ? ' class="required"' : '' ?>>
            <?= ($mode == 'insert') ? '<label for="mb_id">아이디</label>' : '아이디' ?>
            </th>
            <td>
                <?php if ($mode == 'insert') { ?>
                    <?= Html::makeInputText('chance', '', $data['mb_id'], 'required', 20, 20) ?>
                    <input type="hidden" name="mb_id" id="mb_id" value="<?= $data['mb_id'] ?>"/>
                    <input type="hidden" name="flag_mb_id" value="0"/>
                    <span id="state_mb_id" class="state_msg"></span>
                    <p class="comment">
                        20자 이하의 영대/소문자, 숫자, _만 입력 가능
                    </p>
                <?php } else { ?>
                    <strong><?= $data['mb_id'] ?></strong>
                    <input type="hidden" name="mb_id" value="<?= $data['mb_id'] ?>"/>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th <?= ($mode == 'insert') ? ' class="required"' : '' ?>><label for="mb_pw">비밀번호</label></th>
            <td>
                <input type="password" name="mb_pw" id="mb_pw" class="text<?= ($mode == 'insert') ? ' required' : '' ?>"
                       value="" size="20" maxlength="20" title="비밀번호" onkeyup="checkPasswordsMatch()"/>
                <input type="hidden" name="flag_mb_pw" value="0"/>
                <span id="state_mb_pw" class="state_msg"></span>
                <p class="comment">
                    20자 이하의 영대/소문자, 숫자, 특수문자 조합
                </p>
            </td>
            <th class="required"><label for="mb_birthday">주민등록번호</label></th>
            <td>
                <?= Html::makeInputText('mb_birthday', '주민등록번호(앞자리)', $data['mb_birthday'], 'number required', 8, 6) ?>
                -
                <input type="password" name="mb_resident_num" id="mb_resident_num"
                       value="<?= Format::decrypt($data['mb_resident_num']) ?>" class="text number"
                       size="10" maxlength="7" title="주민등록번호(뒷자리)"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </td>
            <!--<th<? /*= ($mode == 'insert') ? ' class="required"' : '' */ ?>><label for="mb_pw2">비밀번호 확인</label>
            <td>
                <input type="password" name="mb_pw2" id="mb_pw2"
                       class="text<? /*= ($mode == 'insert') ? ' required' : '' */ ?>"
                       value="" size="20" maxlength="20" title="비밀번호 확인"/>
            </td>-->
        </tr>
        <tr>
            <th <?= ($mode == 'insert') ? ' class="required"' : '' ?>><label for="mb_pw_confirm">비밀번호 확인</label></th>
            <td>
                <input type="password" name="mb_pw_confirm" id="mb_pw_confirm" class="text<?= ($mode == 'insert') ? ' required' : '' ?>"
                       value="" size="20" maxlength="20" title="비밀번호 확인" onkeyup="checkPasswordsMatch()"/>
                <span id="state_mb_pw_confirm" class="state_msg"></span>
                <span id="passwordMatchMessage"></span>
            </td>
            <th class="required"><label for="mb_tel">핸드폰번호</label></th>
            <td>
                <?= Html::makeInputText('mb_tel', '핸드폰번호', $data['mb_tel'], 'tel required', 20, 15) ?>
                <p class="comment">
                    숫자만 입력해주세요.
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="mb_email">이메일</label></th>
            <td colspan="1">
                <input type="hidden" name="mb_email_old" value="<?= $data['mb_email'] ?>" onkeyup="emailFormatCheck()"/>
                <?= Html::makeInputText('e_address', '이메일', $data['mb_email'], 'email', 40, 50) ?>
                <input type="hidden" name="mb_email" id="mb_email" value="<?= $data['mb_email'] ?>"/>
            </td>
            <th><label for="mb_direct_line">직통번호</label></th>
            <td>
                <?= Html::makeInputText('mb_direct_line', '직통번호', $data['mb_direct_line'], 'tel', 20, 15) ?>
                <p class="comment">
                    숫자만 입력해주세요.
                </p>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <fieldset class="etc">
        <legend>세부 사항</legend>
        <h4>세부 사항</h4>
        <table class="write_table">
        <caption>세부 사항 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class="required"><label for="cp_name" class="required">기업명</label></th>
            <td id="cp_name_td">
                <?php
                $cp_id_arr = explode('|', $data['cp_id']);
                $cp_name_arr = explode('|', $data['cp_name']);
                ?>
                <div class="cp_data">
                    <input type="hidden" name="cp_id_arr[]" class="cp_id"
                           value="<?= $cp_id_arr[0] ?>"/>
                    <?= Html::makeInputText('cp_name_arr[]', '기업명', $cp_name_arr[0], 'readonly required cp_name', 30, 20) ?>
                    <a href="./popup.search_company.html?num=0" target="layer_popup"
                       class="btn_ajax sButton small<?= ($mb_flag_test == 'Y') ? ' ready' : '' ?>"
                       title="기업 검색">검색</a>
                    <button type="button" class="sButton small" onclick="add_cp_name()">추가</button>
                </div>
                <?php
                if ($cp_id_arr[1]) {
                    for ($i = 1; $i < count($cp_id_arr); $i++) {
                        ?>
                        <div class="cp_data">
                            <input type="hidden" name="cp_id_arr[]" class="cp_id"
                                   value="<?= $cp_id_arr[$i] ?>"/>
                            <?= Html::makeInputText('cp_name_arr[]', '기업명', $cp_name_arr[$i], 'readonly required cp_name', 30, 20) ?>
                            <a href="./popup.search_company.html?num=<?= $i ?>"
                               class="btn_ajax sButton small"
                               target="layer_popup"
                               title="기업 검색">검색</a>
                            <button type="button" class="del_cp_name sButton small"
                                    onclick="deleteCpName(this);">삭제
                            </button>
                        </div>
                        <?php
                    }
                }
                ?>
            </td>
            <th><label for="mb_position">직책</label></th>
            <td>
                <?= Html::makeInputText('mb_position', '직책', $data['mb_position'], '', 20, 15) ?>
            </td>
        </tr>
        <?php
        // 서원유통(지점), 서원홀딩스(부서)
        // 엑스퍼트(지점) yllee 220830
        // (주) 중원이엔아이 minju 230705
        //if ($data['cp_id'] == '1628649339' || $data['cp_id'] == '1628649560' || $data['cp_id'] == '1645667084' || $data['cp_id'] == '1680487190') {
        // 부서가 있는 기업: 한양이엔지, 한국전기연구원 기술창업센터 yllee 240510
        // 추가: 창원대학교 산학협력단 창업보육센터 yllee 240724
        //$depart_cp_id_arr = array('1628649339', '1628649560', '1645667084', '1680487190', '1706751639', '1715236672', '1561421213');
        //if (in_array($data['cp_id'], $depart_cp_id_arr)) {

        // 부서 코드 배열가 있으면 출력 방식으로 변경 yllee 240829
        if ($sw_depart_arr) {
            ?>
            <tr>
                <th><label for="sw_depart">부서</label></th>
                <td colspan="3">
                    <select name="sw_depart" id="sw_depart" class="select" title="부서">
                    <option value="">========================</option>
                    <?= Html::makeSelectOptions($sw_depart_arr, $data['sw_depart'], 1) ?>
                    </select>
                </td>
            </tr>
            <?php
        }
        // 북러닝 기본 설정 yllee 240827
        if ($_GET['book'] == 'book') {
            $data['flag_book'] = 'Y';
        }
        ?>
        <tr>
            <th><label for="mb_addr2">주소</label></th>
            <td colspan="3">
                <p>
                    <input type="text" name="mb_zip" value="<?= $data['mb_zip'] ?>"
                           class="text readonly" size="8" maxlength="5" title="우편번호"/>
                    <button type="button" class="sButton small" onclick="execDaumPostcode();">우편번호찾기</button>
                </p>
                <input type="text" name="mb_addr" value="<?= $data['mb_addr'] ?>"
                       class="text  readonly" size="40" maxlength="30" title="주소"/>
                <input type="text" name="mb_addr2" value="<?= $data['mb_addr2'] ?>"
                       class="text " size="40" maxlength="30" title="상세주소"/>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="mb_level">권한구분</label></th>
            <td>
                <select name="mb_level" id="mb_level" class="select required" title="권한구분">
                <?= Html::makeSelectOptions($mb_level_arr, $data['mb_level'], 1) ?>
                </select>
                <?= Html::makeCheckbox('flag_book', array('Y' => '북러닝'), $data['flag_book'], 1) ?>
                <?= Html::makeCheckbox('flag_live', array('Y' => '화상교육'), $data['flag_live'], 1) ?>
            </td>
            <th><label for="flag_tomocard">내일배움<br/>가입여부</label></th>
            <td>
                <?= Html::makeRadio('flag_tomocard', $flag_tomocard_arr, $data['flag_tomocard'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="flag_use">사용여부</label></th>
            <td>
                <?= Html::makeRadio('flag_use', $flag_use_arr, $data['flag_use'], 1, '') ?>
            </td>
            <th><label for="flag_auth">본인인증여부</label></th>
            <td>
                <?= Html::makeRadio('flag_auth', $flag_auth_arr, $data['flag_auth'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="flag_sms">문자 비수신</label></th>
            <td>
                <?= Html::makeCheckbox('flag_sms', array('Y' => '비수신'), $data['flag_sms'], 1) ?>
            </td>
            <th><label for="flag_test">테스트용</label></th>
            <td>
                <?= Html::makeRadio('flag_test', $flag_test_arr, $data['flag_test'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="mb_stu_type">훈련생구분</label></th>
            <td>
                <select name="mb_stu_type" id="mb_stu_type" class="select" title="훈련생구분">
                <option value="">::훈련생구분선택::</option>
                <?= Html::makeSelectOptions($mb_stu_type_arr, $data['mb_stu_type'], 1) ?>
                </select>
            </td>
            <th><label for="mb_irregular_type">비정규직구분</label></th>
            <td>
                <select name="mb_irregular_type" id="mb_irregular_type" class="select" title="비정규직구분">
                <option value="">::비정규직구분선택::</option>
                <?= Html::makeSelectOptions($mb_irregular_type_arr, $data['mb_irregular_type'], 1) ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="mb_cost_business_num">비용수급<br/>사업장번호</label></th>
            <td>
                <?= Html::makeInputText('mb_cost_business_num', '비용수급사업장번호', $data['mb_cost_business_num'], 'num', 50, 30) ?>
            </td>
            <th><label for="flag_test">인터넷연수원</label></th>
            <td>
                <?= Html::makeRadio('flag_cyber', $flag_test_arr, $data['flag_cyber'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th>회원가입일시</th>
            <td><?= Format::getWithoutNull($data['reg_time']) ?></td>
            <th>최근로그인</th>
            <td>
                <?php
                // 기존 필드 백업: bf_last_login
                echo Format::getWithoutNull($data['mb_login_time']);
                ?>
            </td>
        </tr>
        <tr>
            <th>개인정보동의</th>
            <td><?= Format::getWithoutNull($data['privacy_time']) ?></td>
            <th>선택정보동의</th>
            <td><?= Format::getWithoutNull($data['selection_time']) ?></td>
        </tr>
        <?= Html::makeTextareaInTable('비고', 'mb_memo', $data['mb_memo'], '', 3, 130, 3) ?>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <?php
            if (!$flag_blank) {
                if ($mb_flag_test == 'Y') {
                    echo '<button type="button" class="sButton primary ready">확인</button>';
                } else {
                    ?>
                    <button type="submit" class="sButton primary">확인</button>
                    <?php
                }
                if ($_GET['book'] == 'book') {
                    ?>
                    <a href="/webadmin/book/user_list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active"
                       title="목록">목록</a>
                    <?php
                } else {
                    ?>
                    <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
                    <?php
                }
                if ($mode == 'update') {
                    ?>
                    <a href="./process.html?mode=delete&<?= $pk ?>=<?= $uid ?>&page=<?= $page ?><?= $query_string ?>"
                       class="sButton warning btn_delete" title="삭제">삭제</a>
                    <?php
                }
            } else {
                echo '<button type="submit" class="sButton primary">수정</button>';
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
