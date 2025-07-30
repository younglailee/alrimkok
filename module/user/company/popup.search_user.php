<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\UserCompany;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserCompany();
$oUser->init();

$mode = $_GET['mode'];
$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];
$type = $_GET['type'];

if ($mode == 'search_user') {
    /* check auth */
    if (!$oUser->checkListAuth()) {
        Html::alert('권한이 없습니다.');
    }
    /* list */
    $list = $oUser->selectList();
    $cnt_total = $oUser->get('cnt_total');
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {

    var sch_mb_name = '<?=$sch_keyword?>';

    if (sch_mb_name !== '') {
        $('#submit_btn').click();
    }
});
function submitSearchAjaxForm(f) {
    if (!validateForm(f)) {
        return false;
    }
    if (f.sch_keyword.value.length < 2) {
        alert("검색어는 2글자 이상 입력해주세요.");
        f.sch_keyword.focus();
        return false;
    }
    submitByAjax(f, function(result) {
    });
    return false;
}
//]]>
</script>
<div class="search">
    <form name="search_form" action="../user/process_search.html" target="search_list" method="get"
          onsubmit="return submitSearchAjaxForm(this)">
    <input type="hidden" name="mode" value="search_user"/>
    <input type="hidden" name="flag_json" value="1"/>
    <input type="hidden" name="type" value="<?= $type ?>"/>
    <fieldset>
    <legend><i class="xi-search"></i> 검색조건</legend>
    <table class="search_table">
    <caption>검색조건</caption>
    <colgroup>
    <col style="width:80px"/>
    <col/>
    </colgroup>
    <tbody>
    <tr>
        <th><label for="sch_text">검색어</label></th>
        <td>
            <select name="sch_like" class="select" title="검색컬럼">
            <option value="all">통합검색</option>
            <option value="mb_name">수강생명</option>
            <option value="mb_tel">전화번호</option>
            </select>
            <input type="text" name="sch_keyword" id="sch_keyword" value="<?= $sch_keyword ?>" class="text" size="30"
                   maxlength="30" title="검색어"/>
            <button type="submit" id="submit_btn" class="sButton info small" title="검색">검색</button>
        </td>
    </tr>
    </tbody>
    </table>
    </fieldset>
    </form>
</div>
<div id="search_list">
</div>