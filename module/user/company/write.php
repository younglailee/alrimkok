<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\Format;
use sFramework\Html;
use sFramework\UserCompany;

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
$oUser = new UserCompany();
$oUser->init();
$pk = $oUser->get('pk');

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

/* search condition */
$query_string = $oUser->get('query_string');
$page = $_GET['page'];

/* code */
$mb_level_arr = $oUser->get('mb_level_arr');
$flag_tomocard_arr = $oUser->get('flag_tomocard_arr');
$flag_use_arr = $oUser->get('flag_use_arr');
$flag_auth_arr = $oUser->get('flag_auth_arr');
$mb_stu_type_arr = $oUser->get('mb_stu_type_arr');
$mb_irregular_type_arr = $oUser->get('mb_irregular_type_arr');
$flag_test_arr = $oUser->get('flag_test_arr');

$sw_depart_arr = array();
if ($data['cp_id'] == '1628649339') {
    $sw_depart_arr = $oUser->get('sw_depart_arr');
} elseif ($data['cp_id'] == '1628649560') {
    $sw_depart_arr = $oUser->get('sh_depart_arr');
} elseif ($data['cp_id'] == '1645667084') {
    // 엑스퍼트(지점) 부서 추가 yllee 220830
    $sw_depart_arr = $oUser->get('expt_depart_arr');
}
/* file */
$max_file = $oUser->get('max_file');
$file_list = $data['file_list'];
$profile_img = $data['profile_img'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'flag_use' => 'work',
        'flag_tomocard' => 'N',
        'flag_auth' => 'N',
        'flag_test' => 'N',
        'flag_live' => 'N'
    );
} else {
    $mode = 'update';
}
// 테스트 계정 확인 yllee 211014
global $member;
$mb_flag_test = $member['flag_test'];
?>
<script type="text/javascript" src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
//<![CDATA[
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            var roadAddr = data.roadAddress;
            $("input[name='mb_zip']").val(data.zonecode);
            $("input[name='mb_addr']").val(roadAddr);
            $("input[name='mb_addr2']").focus();
        }
    }).open();
}
$(function() {
    /* 아이디 검사 */
    $("#mb_id").on("blur", function() {
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

var editor_min_width = 800;
var oEditors = [];
//]]>
</script>
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
            <th><label for="mb_name">이름</label></th>
            <td>
                <?= $data['mb_name'] ?>
            </td>
            <th<?= ($mode == 'insert') ? '' : '' ?>>
            <?= ($mode == 'insert') ? '<label for="mb_id">아이디</label>' : '아이디' ?>
            </th>
            <td>
                <?php if ($mode == 'insert') { ?>
                    <?= Html::makeInputText('mb_id', '아이디', $data['mb_id'], 'required', 20, 20) ?>
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
            <th><label for="mb_tel">핸드폰번호</label></th>
            <td>
                <?= $data['mb_tel'] ?>
            </td>
            <th><label for="mb_birthday">생년월일</label></th>
            <td>
                <?= $data['mb_birthday'] ?>
            </td>
        </tr>
        <tr>
            <th><label for="mb_direct_line">직통번호</label></th>
            <td>
                <?= $data['mb_direct_line'] ?>
            </td>
            <th><label for="mb_email">이메일</label></th>
            <td colspan="">
                <input type="hidden" name="mb_email_old" value="<?= $data['mb_email'] ?>"/>
                <?= $data['mb_email'] ?>
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
            <th><label for="cp_name">기업명</label></th>
            <td id="cp_name_td">
                <?php
                $cp_id_arr = explode('|', $data['cp_id']);
                $cp_name_arr = explode('|', $data['cp_name']);
                ?>
                <div class="cp_data">
                    <input type="hidden" name="cp_id_arr[]" class="cp_id"
                           value="<?= $cp_id_arr[0] ?>"/>
                    <?= $cp_name_arr[0] ?>
                </div>
                <?php
                if ($cp_id_arr[1]) {
                    for ($i = 1; $i < count($cp_id_arr); $i++) {
                        echo $cp_name_arr[$i];
                    }
                }
                ?>
            </td>
            <th><label for="mb_position">직책</label></th>
            <td>
                <?= $data['mb_position'] ?>
            </td>
        </tr>
        <tr>
            <th><label for="mb_addr2">주소</label></th>
            <td colspan="3">
                <p>
                    <?= $data['mb_zip'] ?>
                </p>
                <?= $data['mb_addr'] ?>
                <?= $data['mb_addr2'] ?>
            </td>
        </tr>
        <tr>
            <th><label for="mb_level">권한구분</label></th>
            <td>
                <?= $mb_level_arr[$data['mb_level']] ?>
            </td>
            <th><label for="flag_tomocard">내일배움<br/>가입여부</label></th>
            <td>
                <?= Html::makeRadio('flag_tomocard', $flag_tomocard_arr, $data['flag_tomocard'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="flag_use">사용여부</label></th>
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
                <?= $mb_stu_type_arr[$data['mb_stu_type']] ?>
            </td>
            <th><label for="mb_irregular_type">비정규직구분</label></th>
            <td>
                <?= $mb_irregular_type_arr[$data['mb_irregular_type']] ?>
            </td>
        </tr>
        <tr>
            <th><label for="mb_cost_business_num">비용수급<br/>사업장번호</label></th>
            <td>
                <?= $data['mb_cost_business_num'] ?>
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
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <?php
            if (!$flag_blank) {
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
