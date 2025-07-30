<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CourseAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oCourse = new CourseAdmin();
$oCourse->init();

$mode = $_GET['mode'];
$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];

if ($mode == 'search_course') {
    /* check auth */
    if (!$oCourse->checkListAuth()) {
        Html::alert('권한이 없습니다.');
    }
    /* list */
    $list = $oCourse->selectList();
    $cnt_total = $oCourse->get('cnt_total');
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
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
    <form name="search_form" action="./process.html" target="search_list" method="get"
          onsubmit="return submitSearchAjaxForm(this)">
    <input type="hidden" name="mode" value="search_course"/>
    <input type="hidden" name="flag_json" value="1"/>
    <input type="hidden" name="sch_like" value="cs_name"/>
    <input type="hidden" name="cs_no" value="<?= $_GET['cs_no'] ?>"/>
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
            <input type="text" name="sch_keyword" id="sch_keyword" value="<?= $sch_keyword ?>" class="text" size="30"
                   maxlength="30" title="검색어"/>
            <button type="submit" class="sButton info small" title="검색">검색</button>
        </td>
    </tr>
    </tbody>
    </table>
    </fieldset>
    </form>
</div>
<div id="search_list">
</div>
