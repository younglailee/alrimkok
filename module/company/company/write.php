<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyCompany;
use sFramework\Html;
use sFramework\UserCompany;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout, $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';
if ($_GET['book'] == 'book') {
    $this_uri = '/web' . $layout . '/book/company_list.html';
}
$doc_title = '기본정보';

/* init Class */
$oCompany = new CompanyCompany();
$oCompany->init();

$oUser = new UserCompany();
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
            <th class="required"><label for="cp_type">기업구분</label></th>
            <td>
                <select name="cp_type" id="cp_type" class="select" title="기업구분">
                <?= Html::makeSelectOptions($cp_type_arr, $data['cp_type'], 1) ?>
                </select>
                <?= Html::makeCheckbox('flag_book', array('Y' => '북러닝'), $data['flag_book'], 1) ?>
                <?= Html::makeCheckbox('flag_live', array('Y' => '화상교육'), $data['flag_live'], 1) ?>
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
            <th class="required"><label for="cp_name">기업명</label></th>
            <td>
                <input type="hidden" name="cp_name_old" value="<?= $data['cp_name'] ?>"/>
                <?= Html::makeInputText('cp_name', '기업명', $data['cp_name'], 'required', 30, 50) ?>
            </td>
            <th class="required"><label for="cp_ceo">대표자명</label></th>
            <td>
                <?= Html::makeInputText('cp_ceo', '대표자명', $data['cp_ceo'], 'required', 20, 50) ?>
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
        <tr>
            <!-- 2.6 교육운영팀 요청 기업리스트 주소 필수값 지정 minju 230209-->
            <th class="required"><label for="mb_addr2">주소</label></th>
            <td colspan="3">
                <p>
                    <input type="text" name="cp_zip" value="<?= $data['cp_zip'] ?>"
                           class="text required" size="8" maxlength="5" title="우편번호"/>
                    <button type="button" class="sButton small" onclick="execDaumPostcode();">우편번호찾기
                    </button>
                </p>
                <!-- 박팀장님 요청 - 주소 수기 입력 가능 처리: 우편번호, 주소 readonly 제거 minju 230509 -->
                <input type="text" name="cp_address" value="<?= $data['cp_address'] ?>"
                       class="text " size="50" maxlength="40" title="주소"/>
                <input type="text" name="cp_address2" value="<?= $data['cp_address2'] ?>"
                       class="text " size="40" maxlength="50" title="상세주소"/>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <?php
        $data['staff_name'] = '';
        ?>
        <fieldset class="etc">
        <legend>세부 사항</legend>
        <h4>세부 사항</h4>
        <table class="write_table">
        <caption>세부 사항 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <!-- 담당자명, 이메일 class="required" 추가 (필수값 지정) minju 221212 -->
        <tr>
            <th class="required"><label for="staff_name">관리자</label></th>
            <td>
                <input type="hidden" name="permit_admin" id="permit_admin_id" value=""/>
                <?= Html::makeInputText('permit_admin', '관리자', $data['permit_admin'], 'required', 20, 10) ?>
                <a href="../user/popup.search_user.html?type=admin" class="btn_ajax sButton small"
                   target="layer_popup" title="검색">검색</a>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="staff_name">기본정보</label></th>
            <td>
                <input type="hidden" name="permit_basic_id" id="permit_basic_id" value=""/>
                <?= Html::makeInputText('permit_basic', '기본정보', $data['permit_basic'], 'required', 20, 10) ?>
                <a href="../user/popup.search_user.html?type=basic" class="btn_ajax sButton small"
                   target="layer_popup" title="검색">검색</a>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="staff_name">휴가설정</label></th>
            <td>
                <input type="hidden" name="permit_setting_id" id="permit_setting_id" value=""/>
                <?= Html::makeInputText('permit_setting', '휴가설정', $data['permit_setting'], 'required', 20, 10) ?>
                <a href="../user/popup.search_user.html?type=setting" class="btn_ajax sButton small"
                   target="layer_popup" title="검색">검색</a>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="staff_name">통계분석</label></th>
            <td>
                <input type="hidden" name="permit_state" value=""/>
                <?= Html::makeInputText('permit_state', '통계분석', $data['permit_state'], 'required', 20, 10) ?>
                <a href="../user/popup.search_user.html?type=state" class="btn_ajax sButton small"
                   target="layer_popup" title="검색">검색</a>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="staff_name">검토자</label></th>
            <td>
                <input type="hidden" name="permit_examine_id" id="permit_examine_id" value=""/>
                <?= Html::makeInputText('permit_examine', '검토자', $data['permit_examine'], 'required', 20, 10) ?>
                <a href="../user/popup.search_user.html?type=examine" class="btn_ajax sButton small"
                   target="layer_popup" title="검색">검색</a>
            </td>
        </tr>
        <tr>
            <th class="required"><label for="staff_name">결재자</label></th>
            <td>
                <input type="hidden" name="permit_approval_id" id="permit_approval_id" value=""/>
                <?= Html::makeInputText('permit_approval', '결재자', $data['permit_approval'], 'required', 20, 10) ?>
                <a href="../user/popup.search_user.html?type=approval" class="btn_ajax sButton small"
                   target="layer_popup" title="검색">검색</a>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <button type="submit" class="sButton primary">수정</button>
        </div>
        </form>

        <fieldset class="etc">
        <legend>조직도</legend>
        <h4>조직도</h4>
        <div class="list">
            <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
            <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
            <input type="hidden" name="page" value="<?= $page ?>"/>
            <input type="hidden" name="mode" value="delete_leave"/>
            <fieldset>
            <legend>자료목록</legend>
            <table class="list_table border odd" style="width:400px">
            <colgroup>
            <col style="width:30px"/>
            <col style="width:100px"/>
            <col/>
            <col style="width:110px"/>
            </colgroup>
            <thead>
            <tr>
                <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
                <th>부서명</th>
                <th>직원명</th>
                <th>관리</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $mb_level_arr = array();
            $list[0] = array(
                'department' => '경영기획팀',
                'employee' => '박민주'
            );
            $list[1] = array(
                'department' => '경영기획팀',
                'employee' => '오재현'
            );
            $list[2] = array(
                'department' => '경영기획팀',
                'employee' => '장혜진'
            );
            $list[3] = array(
                'department' => '경영기획팀',
                'employee' => '배가람'
            );
            $list[4] = array(
                'department' => '교육운영팀',
                'employee' => '고미화'
            );
            $list[5] = array(
                'department' => '교육운영팀',
                'employee' => '김보미'
            );
            for ($i = 0; $i < count($list); $i++) {
                ?>
                <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                    <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                                data-mb="<?= $list[$i]['lv_target_id'] ?>"
                                                class="list_checkbox" title="선택/해제"/></td>
                    <td><?= $list[$i]['department'] ?></td>
                    <td><?= $list[$i]['employee'] ?></td>
                    <td class="button">
                        <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"
                           class="sButton tiny" title="수정">수정</a>
                        <a href="./print.html?<?= $pk ?>=<?= $list[$i][$pk] ?>" target="_blank"
                           class="sButton tiny" title="출력">삭제</a>
                    </td>
                </tr>
                <?php
            }
            echo !count($list) ? Html::makeNoTd(4) : null;
            ?>
            </tbody>
            </table>
            <div class="list_footer" style="width:400px">
                <div class="left">
                    <button type="submit" class="sButton small">선택삭제</button>
                </div>
                <div class="right">
                    <a href="./popup.manage_organization.html" class="btn_ajax sButton small primary" target="layer_popup"
                       title="관리">관리</a>
                </div>
            </div>
            </form>
        </div>
        </fieldset>

        <fieldset class="etc">
        <legend>결재라인</legend>
        <h4>결재라인</h4>
        <div class="list">
            <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
            <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
            <input type="hidden" name="page" value="<?= $page ?>"/>
            <input type="hidden" name="mode" value="delete_leave"/>
            <fieldset>
            <legend>자료목록</legend>
            <table class="list_table border odd" style="width:500px">
            <colgroup>
            <col style="width:100px"/>
            <col/>
            <col style="width:110px"/>
            <col style="width:110px"/>
            </colgroup>
            <thead>
            <tr>
                <th>결재구분</th>
                <th>결재자명</th>
                <th>직위</th>
                <th>관리</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $mb_level_arr = array();
            $list = array();
            $list[0] = array(
                'approval_type' => '검토자',
                'approval_name' => '윤지현',
                'approval_part' => '부장'
            );
            $list[1] = array(
                'approval_type' => '결재자',
                'approval_name' => '장재선',
                'approval_part' => '대표'
            );
            for ($i = 0; $i < count($list); $i++) {
                ?>
                <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                    <td><?= $list[$i]['approval_type'] ?></td>
                    <td><?= $list[$i]['approval_name'] ?></td>
                    <td><?= $list[$i]['approval_part'] ?></td>
                    <td class="button">
                        <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"
                           class="sButton tiny" title="수정">수정</a>
                        <a href="./print.html?<?= $pk ?>=<?= $list[$i][$pk] ?>" target="_blank"
                           class="sButton tiny" title="출력">삭제</a>
                    </td>
                </tr>
                <?php
            }
            echo !count($list) ? Html::makeNoTd(3) : null;
            ?>
            </tbody>
            </table>
            <div class="list_footer" style="width:500px">
                <div class="left">
                    <button type="button" class="sButton small primary">등록</button>
                </div>
            </div>
            </form>
        </div>
        </fieldset>
    </div>
</div>
<form name="check_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="mb_id" value=""/>
<input type="hidden" name="mb_pw" value=""/>
</form>
