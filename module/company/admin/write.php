<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Html;
use sFramework\UserAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout, $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';
if ($_GET['book'] == 'book') {
    $this_uri = '/web' . $layout . '/book/company_list.html';
}

/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();

$oUser = new UserAdmin();
$oUser->init();

$pk = $oCompany->get('pk');

/* check auth */
if (!$oCompany->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
/* data */
$uid = $oCompany->get('uid');
$data = $oCompany->selectDetail($uid);

$cp_id = $uid;
$mb_data = $oUser->selectDetailUser($cp_id);
//print_r($mb_data);

/* search condition */
$query_string = $oCompany->get('query_string');

/* code */
$mb_level_arr = $oCompany->get('mb_level_arr');
$flag_tomocard_arr = $oCompany->get('flag_tomocard_arr');
$flag_use_arr = $oCompany->get('flag_use_arr');
$flag_auth_arr = $oCompany->get('flag_auth_arr');

$cp_count_arr = $oCompany->get('cp_count_arr');
$cp_size_arr = $oCompany->get('cp_size_arr');
$cp_revenue_arr = $oCompany->get('cp_revenue_arr');
$flag_venture_arr = $oCompany->get('flag_venture_arr');
$flag_research_arr = $oCompany->get('flag_research_arr');
$flag_product_arr = $oCompany->get('flag_product_arr');

$partner_arr = $oUser->selectPartnerList();

/* file */
$max_file = $oCompany->get('max_file');
$file_list = $data['file_list'];

$sch_partner = $_GET['sch_partner'];
$page = $_GET['page'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'flag_use' => 'Y'
    );
} else {
    $mode = 'update';
}
// 테스트 계정 확인 yllee 211014
global $member;
$mb_flag_test = $member['flag_test'];
if ($sch_partner) {
    $query_string .= "&sch_partner=" . $sch_partner;
}
// 북러닝 기본 설정 yllee 240827
if ($_GET['book'] == 'book') {
    $data['flag_book'] = 'Y';
}
// 다음 우편번호 서비스 yllee 190306
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
            $("input[name='cp_cp_address2']").focus();
        }
    }).open();
}

var editor_min_width = 800;
var oEditors = [];

// 사업자등록번호 정규식 변환
const koreanEID = e => {
    return e.toString().slice(0, 10).replace(/^(\d{3})(\d{2})(\d{5})$/, '$1-$2-$3');
}

/* 사업자등록번호 자동 하이픈 입력 */
document.addEventListener('DOMContentLoaded', () => {
    const inputBox = document.getElementById('cp_number');
    inputBox.addEventListener('input', () => {
        let inputValue = inputBox.value.replace(/[^0-9]/g, '');
        console.log(inputValue.length);
        if (inputValue.length > 10) {
            inputValue = inputValue.slice(0, 10);
        }
        inputBox.value = inputValue
        .replace(/^(\d{3})(\d{0,2})(\d{0,5})$/, (match, p1, p2, p3) => {
            if (p3) {
                return `${p1}-${p2}-${p3}`;
            } else if (p2) {
                return `${p1}-${p2}`;
            } else {
                return p1;
            }
        });
    });
});

$(function() {
    $("#partner_id").change(function() {
        var partner_name = $("#partner_id option:selected").text();

        $("#partner_name").val(partner_name);
    });
    // 테스트 환경 알림
    $('.ready').click(function(e) {
        alert('테스트 환경에서는 이용할 수 없습니다.');
        e.preventDefault();
        return false;
    });
    // 교육비 한도
    moneyFormat(document.getElementById('cp_edu_money'));
    // 사업자등록번호 형식 변환
    const cp_number = $("#cp_number");
    cp_number.val(koreanEID(cp_number.val()));
})
//]]>
</script>
<div id="<?= $module ?>">
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" id="mode" value="<?= $mode ?>"/>
        <input type="hidden" name="book" value="<?= $_GET['book'] ?>"/>
        <fieldset>
        <legend>기본정보</legend>
        <h4>기본정보</h4>
        <table class="write_table">
        <caption>기본정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:160px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class="required"><label for="cp_size">기업규모</label></th>
            <td>
                <select name="cp_size" id="cp_size" class="select" title="기업구분" style="margin-right:15px;">
                <?= Html::makeSelectOptions($cp_size_arr, $data['cp_size'], 1) ?>
                </select>
            </td>
            <th><label for="mb_id">기업코드</label></th>
            <td>
                <?php
                $cp_id = '';
                if (!$data['cp_id']) {
                    $cp_id = time();
                } else {
                    $cp_id = $data['cp_id'];
                }
                // 해당 기업 수강생 인원 산출 yllee 220511
                $count_user = $oCompany->countUser($cp_id);
                ?>
                <strong><?= $cp_id ?></strong>
                <input type="hidden" name="cp_id" value="<?= $cp_id ?>"/>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="cp_name">사업장명</label></th>
            <td>
                <input type="hidden" name="cp_name_old" value="<?= $data['cp_name'] ?>"/>
                <?= Html::makeInputText('cp_name', '기업명', $data['cp_name'], 'required', 30, 50) ?>
            </td>
            <th class="required"><label for="cp_number">사업자등록번호</label></th>
            <td>
                <?= Html::makeInputText('cp_number', '사업자등록번호', $data['cp_number'], 'required', 20, 12) ?>
                <button type="button" class="sButton small" onclick="check_cp_num()">중복확인</button>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="mb_name">담당자명</label></th>
            <td>
                <input type="hidden" name="mb_name_old" value="<?= $mb_data['mb_name'] ?>"/>
                <?= Html::makeInputText('mb_name', '담당자명', $mb_data['mb_name'], 'required', 20, 50) ?>
            </td>
            <th class="required"><label for="mb_hp">담당자 휴대폰번호</label></th>
            <td>
                <?= Html::makeInputText('mb_hp', '휴대폰번호', $mb_data['mb_hp'], 'tel required', 20, 15) ?>
                <p class="comment">
                    숫자만 입력해주세요.
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="mb_email">담당자 이메일</label></th>
            <td colspan="1">
                <?= Html::makeInputText('mb_email', '이메일', $mb_data['mb_email'], 'email', 40, 50) ?>
            </td>
            <th><label for="mb_depart">담당자 부서</label></th>
            <td>
                <?= Html::makeInputText('mb_depart', '담당자 부서', $mb_data['mb_depart'], '', 20, 15) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <fieldset class="etc">
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
                $cp_count = $data['cp_count'];
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
                <input type="text" name="cp_date" id="buildDate" value="<?= $data['cp_date'] ?>"
                       class="text date required" size="10" maxlength="10" title="시작일" onchange="select_tutor()"
                       autocomplete="off"/>
            </td>
        </tr>
        <tr>
            <th><label for="companySize">기업규모</label></th>
            <td>
                <select name="cp_size" id="companySize" class="select">
                <option value="" disabled="" selected="" hidden="">기업규모를 선택해주세요</option>
                <?php
                $cp_size = $data['cp_size'];
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
                $cp_revenue = $data['cp_revenue'];
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
                <?= Html::makeRadio('flag_venture', $flag_venture_arr, $data['flag_venture'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="research">연구소 보유</label></th>
            <td>
                <select name="flag_research" id="research" class="select">
                <option value="">연구소 유/무를 선택해주세요</option>
                <?php
                $flag_research = $data['flag_research'];
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
                <?= Html::makeRadio('flag_product', $flag_product_arr, $data['flag_product'], 1, '') ?>
            </td>
        </tr>
        <tr>
            <th><label for="cp_address2">업장소재지</label></th>
            <td colspan="3">
                <p>
                    <input type="text" name="cp_zip" value="<?= $data['cp_zip'] ?>"
                           class="text readonly" size="8" maxlength="5" title="우편번호"/>
                    <button type="button" class="sButton small" onclick="execDaumPostcode();">우편번호찾기</button>
                </p>
                <input type="text" name="cp_address" value="<?= $data['cp_address'] ?>"
                       class="text  readonly" size="40" maxlength="30" title="주소"/>
                <input type="text" name="cp_address2" value="<?= $data['cp_address2'] ?>"
                       class="text " size="40" maxlength="30" title="상세주소"/>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <?php
            if ($mb_flag_test == 'Y') {
                echo '<button type="button" class="sButton primary ready">확인</button>';
            } else {
                ?>
                <button type="submit" class="sButton primary">확인</button>
                <?php
            }
            if ($_GET['book'] == 'book') {
                ?>
                <a href="/webadmin/book/company_list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active"
                   title="목록">목록</a>
                <?php
            } else {
                ?>
                <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
                <?php
            }
            if ($mode == 'update') {
                if ($mb_flag_test == 'Y') {
                    echo '<button type="button" class="sButton ready">삭제</button>';
                } else {
                    ?>
                    <a href="./process.html?mode=delete&<?= $pk ?>=<?= $uid ?>&page=<?= $page ?><?= $query_string ?>"
                       class="sButton warning btn_delete" title="삭제">삭제</a>
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
