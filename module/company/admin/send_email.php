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

/* search condition */
$query_string = $oCompany->get('query_string');

/* code */
$mb_level_arr = $oCompany->get('mb_level_arr');
$flag_tomocard_arr = $oCompany->get('flag_tomocard_arr');
$flag_use_arr = $oCompany->get('flag_use_arr');
$flag_auth_arr = $oCompany->get('flag_auth_arr');
$cp_type_arr = $oCompany->get('cp_type_arr');
$partner_arr = $oUser->selectPartnerList();

/* file */
$max_file = $oCompany->get('max_file');
$file_list = $data['file_list'];

$sch_partner = $_GET['sch_partner'];

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
        <legend>회사 정보</legend>
        <h4>회사 사항</h4>
        <table class="write_table">
        <caption>회사 정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class="required"><label for="cp_name">기업명</label></th>
            <td>
                <input type="hidden" name="cp_name_old" value="<?= $data['cp_name'] ?>"/>
                <?= Html::makeInputText('cp_name', '기업명', $data['cp_name'], 'required', 30, 50) ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="cp_number">사업자등록번호</label></th>
            <td>
                <?= Html::makeInputText('cp_number', '사업자등록번호', $data['cp_number'], 'required', 20, 10) ?>
                <button type="button" class="sButton small" onclick="check_cp_num()">중복확인</button>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
            <th class=""><label for="business_license">사업자등록증</label></th>
            <td>
                <?php
                for ($i = 0; $i < 1; $i++) {
                    ?>
                    <input type="file" name="atch_file[]" id="business_license" class="file" size="100"
                           title="사업자등록증"/>
                    <?php
                    if ($file_list[$i]['fi_id']) {
                        ?>
                        <p>
                            <input type="checkbox" name="del_file[]" id="del_file_<?= $i + 1 ?>"
                                   value="<?= $file_list[$i]['fi_id'] ?>" class="checkbox" title="기존파일 삭제"/>
                            <label for="del_file_<?= $i + 1 ?>">기존파일삭제</label>
                            <span>|</span>
                            <a href="./download.html?fi_id=<?= $file_list[$i]['fi_id'] ?>"
                               class="btn_download"
                               target="_blank"
                               title="새창 다운로드">
                                <strong><?= $file_list[$i]['fi_name'] ?></strong>
                                <span>(<?= $file_list[$i]['bt_fi_size'] ?>)</span>
                            </a>
                        </p>
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="cp_tel">대표번호</label></th>
            <td>
                <?= Html::makeInputText('cp_tel', '대표번호', $data['cp_tel'], 'tel required', 20, 15) ?>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
            <th class=""><label for="cp_fax">팩스번호</label></th>
            <td>
                <?= Html::makeInputText('cp_fax', '팩스번호', $data['cp_fax'], 'tel ', 20, 15) ?>
                <p class="comment">숫자만 입력해주세요.</p>
            </td>
        </tr>
        <button type="submit" class="sButton primary">확인</button>
        </form>
    </div>
</div>
<form name="check_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="mb_id" value=""/>
<input type="hidden" name="mb_pw" value=""/>
</form>
