<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\VisitAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
global $module;

/* init Class */
$oVisit = new VisitAdmin();
$oVisit->init();
$pk = $oVisit->get('pk');

/* check auth */
if (!$oVisit->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$cp_id = $_GET['cp_id'];
$vs_ip = $_GET['vs_ip'];
$mb_id = $_GET['reg_id'];
$list = $oVisit->selectDuplicateIp($cp_id, $vs_ip);
$cnt_total = $oVisit->get('cnt_total');

/* pagination */
$page = $oVisit->get('page');
$page_arr = $oVisit->getPageArray();
$query_string = $oVisit->get('query_string');

$layout_size = 'large';
$doc_title = '접속IP현황(수강생)';
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>
<div id="<?= $module ?>">
    <div class="list">
        <div class="list_header">
            <div class="left">
                <i class="xi-file-text-o"></i> Total : <strong><?= number_format($cnt_total) ?></strong> 건, 현재 :
                <strong><?= number_format($page) ?></strong> 페이지
            </div>
            <div class="right">
            </div>
        </div>

        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="delete"/>

        <fieldset>
        <legend>자료목록</legend>
        <table class="list_table border odd" border="1">
        <colgroup>
        <col width="50"/>
        <col width="100"/>
        <col width="100"/>
        <col width="250"/>
        <col width="120"/>
        <col width="100"/>
        <col width="90"/>
        <col width="110"/>
        <col width="140"/>
        <col/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>아이디</th>
            <th>이름</th>
            <th>기업명</th>
            <th>아이피</th>
            <th>디바이스</th>
            <th>운영체제</th>
            <th>브라우저</th>
            <th>접속일시</th>
            <th>접속경로</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($list); $i++) {
            $uid = substr($list[$i][$pk], 2, 1);
            if (!$list[$i]['cp_name']) {
                if ($list[$i]['mb_level'] && $list[$i]['mb_level'] <= 2) {
                    $list[$i]['cp_name'] = '내일배움카드';
                } else {
                    $list[$i]['cp_name'] = '(주)알파에듀';
                }
            }
            $vs_device = $list[$i]['vs_device'];
            if ($vs_device == 'Desktop') {
                $vs_device = 'PC';
            }
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $list[$i]['reg_id'] ?></td>
                <td>
                    <a href="/webadmin/user/crm.html?mb_id=<?= $list[$i]['reg_id'] ?>" target="_blank">
                        <?= $list[$i]['reg_name'] ?>
                    </a>
                </td>
                <td title="<?= $list[$i]['cp_id'] ?>"><?= $list[$i]['cp_name'] ?></td>
                <td><?= $list[$i]['vs_ip'] ?></td>
                <td><?= $vs_device ?></td>
                <td><?= $list[$i]['vs_os'] ?></td>
                <td><?= $list[$i]['vs_browser'] ?></td>
                <td><?= $list[$i]['reg_time'] ?></td>
                <td><?= Html::cutString($list[$i]['vs_referer'], 50) ?></td>
            </tr>
        <?php } ?>
        <?= !count($list) ? Html::makeNoTd(10) : null ?>
        </tbody>
        </table>
        </form>
    </div>
</div>
