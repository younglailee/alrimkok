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

$sch_like = $_GET['sch_like'];
$sch_keyword = $_GET['sch_keyword'];

/* check auth */
if (!$oCourse->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* code */
$cs_refund_type_arr = $oCourse->get('cs_refund_type_arr');

/* list */
$list = $oCourse->selectCourseList($sch_like, $sch_keyword);
$cnt_total = $oCourse->get('cnt_total');
//print_r($list);
//0print_r($_GET);
$colspan = 4;
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
function chooseCourse(cp_id, cs_code, cp_name) {
    $("#cs_code").val(cs_code);
    $("#cs_name").val(cp_name);
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
    <th>과정명</th>
    <th>식별자코드</th>
    <th>선택</th>
</tr>
</thead>
<tbody>
<?php
for ($i = 0; $i < count($list); $i++) {
    $cs_id = $list[$i]['cs_id'];
    $cs_code = $list[$i]['cs_code'];
    $cs_name = $list[$i]['cs_name'];
    ?>
    <tr class="list_tr_<?= $list[$i]['odd'] ?>">
        <td><?= $list[$i]['no'] ?></td>
        <td><?= $list[$i]['cs_name'] ?></td>
        <td><?= $list[$i]['cs_code'] ?></td>
        <td>
            <button type="button" onclick="chooseCourse('<?= $cs_id ?>', '<?= $cs_code ?>', '<?= $cs_name ?>')"
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