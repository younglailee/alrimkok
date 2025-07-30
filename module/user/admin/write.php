<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\Format;
use sFramework\Html;
use sFramework\UserAdmin;
use sFramework\CompanyAdmin;
use sFramework\InterestAdmin;

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
$oUser = new UserAdmin();
$pk = 'mb_id';
$oUser->init();
$oCompany = new CompanyAdmin();
$oCompany->init();
$oInterest = new InterestAdmin();
$oInterest->init();

/* check auth */
if (!$oUser->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
$div_style = '';
$flag_blank = false;
if ($_GET['type'] == 'blank') {
    $flag_use_header = false;
    $flag_use_footer = false;
    $div_style = ' style="padding:20px"';
    $flag_blank = true;
}

/* data */
$uid = $oUser->get('uid');
$data = $oUser->selectDetail($uid);
$mb_id = $data['mb_id'];

// 기업정보 포함 출력 yllee 250709
$cp_id = $data['cp_id'];
$cp_data = $oCompany->selectDetail($cp_id);
$cp_count_arr = $oCompany->get('cp_count_arr');
$cp_size_arr = $oCompany->get('cp_size_arr');
$cp_revenue_arr = $oCompany->get('cp_revenue_arr');
$flag_venture_arr = $oCompany->get('flag_venture_arr');
$flag_research_arr = $oCompany->get('flag_research_arr');
$flag_product_arr = $oCompany->get('flag_product_arr');
//print_r($cp_data);

// 관심 분야
$it_data = $oInterest->selectInterest($mb_id);
$it_area_arr = $oInterest->get('it_area_arr');
$it_type_arr = $oInterest->get('it_type_arr');
$it_info_arr = $oInterest->get('it_info_arr');

/* search condition */
$query_string = $oUser->get('query_string');
$page = $_GET['page'];

/* code */
$mb_level_arr = $oUser->get('mb_level_arr');
$flag_tomocard_arr = $oUser->get('flag_tomocard_arr');
$flag_use_arr = $oUser->get('flag_use_arr');
$flag_yn_arr = $oUser->get('flag_yn_arr');
$flag_auth_arr = $oUser->get('flag_auth_arr');
$mb_stu_type_arr = $oUser->get('mb_stu_type_arr');
$mb_irregular_type_arr = $oUser->get('mb_irregular_type_arr');
$flag_test_arr = $oUser->get('flag_test_arr');

/* file */
$max_file = $oUser->get('max_file');
$file_list = $cp_data['file_list'];
$profile_img = $data['profile_img'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'flag_use' => 'use',
        'flag_notice' => 'Y'
    );
    $data['cp_id'] = time();
} else {
    $mode = 'update';
}
// 테스트 계정 확인 yllee 211014
global $member;
// 경남TP 시험성적서 발급을 위해 주석처리 yllee 230718
//$mb_flag_test = $member['flag_test'];
?>
<script type="text/javascript" src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
//<![CDATA[
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            var roadAddr = data.roadAddress;
            $("input[name='cp_zip']").val(data.zonecode);
            $("input[name='cp_address']").val(roadAddr);
            $("input[name='cp_address2']").focus();
        }
    }).open();
}
$(function() {
    /* 아이디 검사 */
    $("#mb_id").on("blur", function() {
        validateMemberId(false);
    });
    $("#chance").on("blur", function() {
        validateMemberId(false);
    });
    /* 비밀번호 검사 */
    $("#mb_pw").on("blur", function() {
        validateMemberPassword(false);
    });
    // 테스트 환경 알림
    $('.ready').click(function() {
        alert('테스트 환경에서는 이용할 수 없습니다.');
        return false;
    });
});

function add_cp_name() {
    var add_num = $(".cp_data").length;

    var html = "";
    html += "<div class='cp_date'>";
    html += "<input type='hidden' name='cp_id_arr[]' class='cp_id'>";
    html += "<input type='text' name='cp_name_arr[]' id='cp_name_arr[]' class='text readonly required cp_name' size='30' maxlength='20' title='기업명' readonly>";
    html += "<a  style='margin-right: 4px; margin-left: 4px' href='./popup.search_company.html?num=" + add_num + "' class='btn_ajax sButton small' target='layer_popup' title='기업 검색'>";
    html += "<span class='sButton-container'>";
    html += "<span class='sButton-bg'>";
    html += "<span class='text'>검색</span>"
    html += "</span>";
    html += "</span>";
    html += "</a>";
    html += "<button type='button' class='del_cp_name sButton small' onclick='deleteCpName(this);'>";
    html += "<span class='sButton-container'>";
    html += "<span class='sButton-bg'>";
    html += "<span class='text'>삭제</span>"
    html += "</span>";
    html += "</span>";
    html += "</button>";
    html += "</div>";

    $("#cp_name_td").append(html);
}

function deleteCpName(obj) {
    $(obj).parent().remove();
}

// 비밀번호 일치여부 검사
function checkPasswordsMatch() {
    const password = document.getElementById('mb_pw').value;
    const confirmPassword = document.getElementById('mb_pw_confirm').value;
    const messageElement = document.getElementById('passwordMatchMessage');

    if (password === confirmPassword && password !== '') {
        messageElement.textContent = '비밀번호가 일치합니다.';
        messageElement.style.color = 'blue';
    } else if (password === '' || confirmPassword === '') {
        messageElement.textContent = '';
    } else {
        messageElement.textContent = '비밀번호가 일치하지 않습니다.';
        messageElement.style.color = '#de234f';
    }
}

// 이메일 포맷 검사
function emailFormatCheck(obj) {
    var value = obj.val();
    var fmt = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if (value && !fmt.test(value)) {
        validateAlert(obj, "정확하게 입력해주세요.");
        return false;
    }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('mb_hp').oninput = function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    };
    <?php
    if ($mode == 'insert') {
    ?>
    document.getElementById('chance').oninput = function() {
        document.getElementById('mb_id').value = this.value;
    };
    <?php
    }
    ?>
});

$(document).on('change', '#mb_level', function() {
    //console.log(this);
    let selectedOption = $(this).find('option:selected');
    //console.log(selectedOption.attr('value'));
    let fieldset_company = $('#fieldset_company');
    if (selectedOption.attr('value') === '1') {
        fieldset_company.show();
    } else {
        fieldset_company.hide();
    }
});

var editor_min_width = 800;
var oEditors = [];
//]]>
</script>
<style>
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
        <legend>기본정보</legend>
        <h4>기본정보</h4>
        <table class="write_table">
        <caption>기본 정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:160px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class="required"><label for="cp_name" class="required">사업장명</label></th>
            <td id="cp_name_td">
                <?php
                //print_r($data);
                ?>
                <div class="cp_data">
                    <input type="hidden" name="cp_id" class="cp_id"
                           value="<?= $data['cp_id'] ?>"/>
                    <?= Html::makeInputText('cp_name', '사업장명', $data['cp_name'], 'required cp_name', 30, 20) ?>
                </div>
            </td>
            <th<?= ($mode == 'insert') ? ' class="required"' : '' ?>>
            <?= ($mode == 'insert') ? '<label for="mb_id">사업자등록번호<br/>(아이디)</label>' : '사업자등록번호' ?>
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
                    <p class="comment">
                        (로그인 ID로 사용)
                    </p>
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
            <th <?= ($mode == 'insert') ? ' class="required"' : '' ?>><label for="mb_pw_confirm">비밀번호 확인</label></th>
            <td>
                <input type="password" name="mb_pw_confirm" id="mb_pw_confirm"
                       class="text<?= ($mode == 'insert') ? ' required' : '' ?>"
                       value="" size="20" maxlength="20" title="비밀번호 확인" onkeyup="checkPasswordsMatch()"/>
                <span id="state_mb_pw_confirm" class="state_msg"></span>
                <span id="passwordMatchMessage"></span>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="mb_name">담당자명</label></th>
            <td>
                <input type="hidden" name="mb_name_old" value="<?= $data['mb_name'] ?>"/>
                <?= Html::makeInputText('mb_name', '담당자명', $data['mb_name'], 'required', 20, 50) ?>
            </td>
            <th class="required"><label for="mb_hp">담당자 휴대폰번호</label></th>
            <td>
                <?= Html::makeInputText('mb_hp', '휴대폰번호', $data['mb_hp'], 'tel required', 20, 15) ?>
                <p class="comment">
                    숫자만 입력해주세요.
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="mb_email">담당자 이메일</label></th>
            <td colspan="1">
                <?= Html::makeInputText('mb_email', '담당자 이메일', $data['mb_email'], 'email', 40, 50) ?>
            </td>
            <th><label for="mb_depart">담당자 부서</label></th>
            <td>
                <?= Html::makeInputText('mb_depart', '담당자 부서', $data['mb_depart'], '', 20, 15) ?>
            </td>
        </tr>
        <tr>
            <th class=""><label for="mb_level">권한구분</label></th>
            <td>
                <select name="mb_level" id="mb_level" class="select" title="권한구분">
                <?= Html::makeSelectOptions($mb_level_arr, $data['mb_level'], 1) ?>
                </select>
            </td>
            <th class=""><label for="flag_use">사용여부</label></th>
            <td>
                <?= Html::makeRadio('flag_use', $flag_use_arr, $data['flag_use'], 1, '') ?>
            </td>
        </tr>
        <?php
        $flag_notice = $data['flag_notice'];
        $checked_notice = ($flag_notice == 'Y') ? ' checked' : '';
        ?>
        <tr>
            <th><label for="alarm-check">알림수신 동의</label></th>
            <td colspan="3">
                <?= Html::makeCheckbox('flag_notice', array('Y' => '동의'), $data['flag_notice'], 1) ?>
            </td>
        </tr>
        <tr>
            <th>회원가입일시</th>
            <td><?= Format::getWithoutNull($data['reg_time']) ?></td>
            <th>최근로그인</th>
            <td>
                <?= Format::getWithoutNull($data['mb_login_time']) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <?php
        // 부계정만 숨김 yllee 250723
        $style_company = '';
        if ($data['mb_level'] != '1') {
            $style_company = ' style="display: none;"';
        }
        ?>
        <fieldset class="etc"<?= $style_company ?> id="fieldset_company">
        <legend>기업정보</legend>
        <h4>기업정보</h4>
        <table class="write_table">
        <caption>기업정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:160px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="mb_stu_type">종업원수</label></th>
            <td>
                <select name="cp_count" id="mb_stu_type" class="select">
                <option value="">종업원수를 선택해주세요</option>
                <?php
                $cp_count = $cp_data['cp_count'];
                foreach ($cp_count_arr as $key => $val) {
                    $selected = ($key == $cp_count) ? ' selected' : '';
                    ?>
                    <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                    <?php
                }
                ?>
                </select>
            </td>
            <th><label for="buildDate">설립일자</label></th>
            <td>
                <input type="text" name="cp_date" id="buildDate" value="<?= $cp_data['cp_date'] ?>"
                       class="text date" size="10" maxlength="10" title="설립일자"
                       autocomplete="off"/>
            </td>
        </tr>
        <tr>
            <th><label for="companySize">기업규모</label></th>
            <td>
                <select name="cp_size" id="companySize" class="select">
                <option value="" disabled="" selected="" hidden="">기업규모를 선택해주세요</option>
                <?php
                $cp_size = $cp_data['cp_size'];
                foreach ($cp_size_arr as $key => $val) {
                    $selected = ($key == $cp_size) ? ' selected' : '';
                    ?>
                    <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                    <?php
                }
                ?>
                </select>
            </td>
            <th><label for="revenue">매출액규모</label></th>
            <td>
                <select name="cp_revenue" id="revenue" class="select">
                <option value="">매출액규모를 선택해주세요</option>
                <?php
                $cp_revenue = $cp_data['cp_revenue'];
                foreach ($cp_revenue_arr as $key => $val) {
                    $selected = ($key == $cp_revenue) ? ' selected' : '';
                    ?>
                    <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                    <?php
                }
                ?>
                </select>
                </select>
            </td>
        </tr>
        <tr>
            <th class=""><label for="">기업소개서</label></th>
            <td>
                <input type="hidden" name="fi_type[]" value="company"/>
                <input id="companyFile" name="atch_file[]" type="file">
                <?php
                if ($file_list[0]['fi_id']) {
                    ?>
                    <p>
                        <a href="./download.html?fi_id=<?= $file_list[0]['fi_id'] ?>"
                           class="btn_download" target="_blank" title="새창 다운로드">
                            <strong><?= $file_list[0]['fi_name'] ?></strong>
                        </a>
                    </p>
                    <?php
                }
                ?>
            </td>
            <th class=""><label for="flag_venture">벤처기업 유무</label></th>
            <td>
                <?= Html::makeRadio('flag_venture', $flag_venture_arr, $cp_data['flag_venture'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="research">연구소 보유</label></th>
            <td>
                <select name="flag_research" id="research" class="select">
                <option value="">연구소 유/무를 선택해주세요</option>
                <?php
                $flag_research = $cp_data['flag_research'];
                foreach ($flag_research_arr as $key => $val) {
                    $selected = ($key == $flag_research) ? ' selected' : '';
                    ?>
                    <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                    <?php
                }
                ?>
                </select>
            </td>
            <th class=""><label for="flag_venture">완제품 생산 유무</label></th>
            <td>
                <?= Html::makeRadio('flag_product', $flag_product_arr, $cp_data['flag_product'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="cp_address2">업장소재지</label></th>
            <td colspan="3">
                <p>
                    <input type="text" name="cp_zip" value="<?= $cp_data['cp_zip'] ?>"
                           class="text readonly" size="8" maxlength="5" title="우편번호"/>
                    <button type="button" class="sButton small" onclick="execDaumPostcode();">우편번호찾기</button>
                </p>
                <input type="text" name="cp_address" value="<?= $cp_data['cp_address'] ?>"
                       class="text  readonly" size="40" maxlength="30" title="주소"/>
                <input type="text" name="cp_address2" value="<?= $cp_data['cp_address2'] ?>"
                       class="text " size="40" maxlength="30" title="상세주소"/>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <fieldset class="etc">
        <legend>관심분야</legend>
        <h4>관심분야</h4>
        <table class="write_table">
        <caption>관심분야 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:160px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th><label>주력분야 사업</label></th>
            <td colspan="3">
                <p><strong>제조: </strong>
                <?php
                //print_r($it_area_arr);
                $selected_area_str = $it_data['it_area'];
                $selected_area_arr = explode('|', $selected_area_str);
                foreach ($it_area_arr as $key => $val) {
                    if (str_starts_with($key, 'a')) {
                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                        ?>
                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                        <?php
                    }
                }
                ?>
                </p>
                <p style="margin-top:6px"><strong>서비스: </strong>
                <?php
                foreach ($it_area_arr as $key => $val) {
                    if (str_starts_with($key, 'b')) {
                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                        ?>
                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                        <?php
                    }
                }
                ?>
                </p>
                <p style="margin-top:6px"><strong>IT신산업: </strong>
                <?php
                foreach ($it_area_arr as $key => $val) {
                    if (str_starts_with($key, 'c')) {
                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                        ?>
                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                        <?php
                    }
                }
                ?>
                </p>
                <p style="margin-top:6px"><strong>건설: </strong>
                <?php
                foreach ($it_area_arr as $key => $val) {
                    if (str_starts_with($key, 'd')) {
                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                        ?>
                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                        <?php
                    }
                }
                ?>
                </p>
            </td>
        </tr>
        <tr>
            <th><label>유형</label></th>
            <td colspan="3">
                <?php
                //print_r($it_type_arr);
                $selected_type_str = $it_data['it_type'];
                $selected_type_arr = explode('|', $selected_type_str);
                foreach ($it_type_arr as $key => $val) {
                    $checked = in_array($key, $selected_type_arr) ? ' checked' : '';
                    ?>
                    <label class="ws-check"><input type="checkbox" name="it_type[]" <?= $checked ?>
                                                   value="<?= $key ?>"><span><?= $val ?></span></label>
                    <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <th><label>주요 관심정보</label></th>
            <td colspan="3">
                <?php
                //print_r($it_info_arr);
                $selected_info_str = $it_data['it_info'];
                $selected_info_arr = explode('|', $selected_info_str);
                foreach ($it_info_arr as $key => $val) {
                    $checked = in_array($key, $selected_info_arr) ? ' checked' : '';
                    ?>
                    <label class="ws-check"><input type="checkbox" name="it_info[]" <?= $checked ?>
                                                   value="<?= $key ?>"><span><?= $val ?></span></label>
                    <?php
                }
                ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <fieldset class="etc" style="display:none">
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
            $mb_flag_test = '';
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
