<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();

$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];

/* check auth */
if (!$oCompany->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* list */
$list = $oCompany->selectCompanyList($sch_like, $sch_keyword);
$cnt_total = $oCompany->get('cnt_total');
//print_r($list);
//0print_r($_GET);
$colspan = 4;
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
function chooseCompany(cp_id, cp_name) {
    $("#cp_id").val(cp_id);
    $("#cp_name").val(cp_name);
    closeLayerPopup();
}
//]]>
</script>
<div class="list_header">
    <div class="left">
        <i class="xi-file-text-o"></i> Total : <strong><?= number_format($cnt_total) ?></strong> 건
    </div>
    <div class="right">
    </div>
</div>
<fieldset>
<legend>자료목록</legend>
<table class="list_table border odd">
<colgroup>
<col style="width:50px"/>
<col/>
<col style="width:140px"/>
<col style="width:80px"/>
</colgroup>
<thead>
<tr>
    <th>No</th>
    <th>기업명</th>
    <th>대표자</th>
    <th>선택</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < count($list); $i++) {
    $cp_id = $list[$i]['cp_id'];
    $cp_name = $list[$i]['cp_name'];
    ?>
    <tr class="list_tr_<?= $list[$i]['odd'] ?>">
        <td><?= $list[$i]['no'] ?></td>
        <td><?= $list[$i]['cp_name'] ?></td>
        <td><?= $list[$i]['cp_ceo'] ?></td>
        <td>
            <button type="button" onclick="chooseCompany('<?= $cp_id ?>', '<?= $cp_name ?>')"
                    class="sButton tiny">선택
            </button>
        </td>
    </tr>
    <?php
}
echo (!count($list)) ? Html::makeNoTd($colspan) : null;
?>
</tbody>
</table>