<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\UserAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserAdmin();
$oUser->init();

$mode = $_GET['mode'];
$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];

if ($mode == 'search_company') {
    /* check auth */
    if (!$oUser->checkListAuth()) {
        Html::alert('권한이 없습니다.');
    }
    /* list */
    $list = $oUser->selectList();
    $cnt_total = $oUser->get('cnt_total');
}
// 테스트 계정 확인 yllee 211014
global $member;
$mb_flag_test = $member['flag_test'];
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    // 테스트 환경 알림
    $('.ready').click(function(e) {
        alert('테스트 환경에서는 이용할 수 없습니다.');
        e.preventDefault();
        return false;
    });
});
function submitFileUpload(f) {
    console.log(f.user_excel.value);
    if (!f.user_excel.value) {
        alert("엑셀 파일이 선택되지 않았습니다.");
        return false;
    }
    return true;
}
//]]>
</script>
<form name="file_form" action="./excel_update.html" enctype="multipart/form-data"
      method="post" onsubmit="return submitFileUpload(this)">
<input type="hidden" name="mode" value="excel_update"/>
<div class="search">
    <fieldset>
    <legend><i class="xi-save"></i> 파일등록</legend>
    <table class="search_table">
    <caption>파일등록</caption>
    <colgroup>
    <col style="width:80px"/>
    <col/>
    </colgroup>
    <tbody>
    <tr>
        <th><label for="user_excel">엑셀 파일</label></th>
        <td>
            <?php
            for ($i = 0; $i < 1; $i++) {
                ?>
                <input type="hidden" name="fi_type[]" value="user_excel"/>
                <input type="file" name="user_excel" id="user_excel" class="file" size="100"
                       title="엑셀 파일"/>
                <?php
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>샘플</th>
        <td>
            <a href="/data/excel/company_sample.xlsx" target="_blank" class="sButton small">샘플 다운로드</a>
        </td>
    </tr>
    </tbody>
    </table>
    </fieldset>
</div>
<div>
    <p class="comment">필수 항목: 기업구분, 기업명, 대표자명, 사업자등록번호, 대표번호, 팩스번호</p>
</div>
<div style="text-align:center;margin-top:20px;">
    <button type="submit" class="sButton small primary<?= ($mb_flag_test == 'Y') ? ' ready' : '' ?>" title="등록">등록
    </button>
</div>
</form>
