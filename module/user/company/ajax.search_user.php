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

$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];
$type = $_GET['type'];

/* check auth */
if (!$oUser->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* list */
$list = $oUser->selectUserList($sch_like, $sch_keyword);
$cnt_total = $oUser->get('cnt_total');
//print_r($list);
//0print_r($_GET);
$colspan = 5;
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
function chooseUser(mb_name, mb_id, type) {
    $("#permit_admin_id").val(mb_id);
    $("#permit_admin").val(mb_name);
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
<col style="width:120px"/>
<col style="width:80px"/>
</colgroup>
<thead>
<tr>
    <th>No</th>
    <th>기업명</th>
    <th>수강생명</th>
    <th>전화번호</th>
    <th>선택</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < count($list); $i++) {
    $mb_name = $list[$i]['mb_name'];
    $mb_tel = $list[$i]['mb_tel'];
    $cp_name = $list[$i]['cp_name'];
    $mb_id = $list[$i]['mb_id'];
    ?>
    <tr class="list_tr_<?= $list[$i]['odd'] ?>">
        <td><?= $list[$i]['no'] ?></td>
        <td><?= $list[$i]['cp_name'] ?></td>
        <td><?= $list[$i]['mb_name'] ?></td>
        <td><?= $list[$i]['mb_tel'] ?></td>
        <td>
            <button type="button"
                    onclick="chooseUser('<?= $mb_name ?>', '<?= $mb_id ?>', '<?= $type ?>')"
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