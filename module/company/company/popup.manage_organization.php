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
    <fieldset>
    <legend><i class="xi-search"></i> 조직도</legend>
    
    </fieldset>
    </form>
</div>
<div id="search_list">
</div>