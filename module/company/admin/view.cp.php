<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Db;
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
$list = $oVisit->selectIpCompanyGrop($vs_ip);
$cnt_total = $oVisit->get('cnt_total');

/* pagination */
$page = $oVisit->get('page');
$page_arr = $oVisit->getPageArray();
$query_string = $oVisit->get('query_string');

$layout_size = 'large';
$doc_title = '접속IP현황(기업)';
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
        <col style="width:50px"/>
        <col style="width:270px"/>
        <col style="width:120px"/>
        <col style="width:100px"/>
        <col style="width:90px"/>
        <col style="width:110px"/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
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
            // 동일 기업 중복 IP 카운트 yllee 220705
            $cp_id = $list[$i]['cp_id'];
            $vs_ip = $list[$i]['vs_ip'];
            $mb_id = $list[$i]['reg_id'];
            // AND cp_id = '$cp_id' AND reg_id != '' AND reg_id != '$mb_id'
            $db_where = "WHERE vs_ip = '$vs_ip' AND cp_id != ''";
            $db_where .= " GROUP BY reg_id";
            $db_order = "ORDER BY reg_time DESC";
            $vs_list = Db::select('tbl_visit', "*", $db_where, $db_order, "");
            //$count_ip = count($vs_list);
            $count_ip = 0;
            //print_r($vs_list);
            $view_url = './view.user.html?cp_id=' . $cp_id . '&vs_ip=' . $vs_ip . '&mb_id=' . $mb_id;
            $view_url_ip = './view.company.html?vs_ip=' . $vs_ip . '&mb_id=' . $mb_id;
            // 타기업 포함 여부 yllee 220705
            $count_ip_company = 0;
            foreach ($vs_list as $key => $val) {
                //echo $val['cp_id'];
                if ($val['cp_id'] != $cp_id) {
                    $count_ip_company++;
                } else {
                    $count_ip++;
                }
            }
            $count_color = '';
            if ($count_ip > 1) {
                $count_color = ' style="color:red"';
            }
            $count_company_color = '';
            if ($count_ip_company > 0) {
                $count_company_color = ' style="color:red"';
            }
            /*
            if ($i == 4) {
                print_r($vs_list);
            }
            */
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td title="<?= $list[$i]['cp_id'] ?>"><?= $list[$i]['cp_name'] ?></td>
                <td><?= $list[$i]['vs_ip'] ?></td>
                <td><?= $vs_device ?></td>
                <td><?= $list[$i]['vs_os'] ?></td>
                <td><?= $list[$i]['vs_browser'] ?></td>
                <td><?= $list[$i]['reg_time'] ?></td>
                <td><?= Html::cutString($list[$i]['vs_referer'], 70) ?></td>
            </tr>
        <?php } ?>
        <?= !count($list) ? Html::makeNoTd(10) : null ?>
        </tbody>
        </table>
        </form>
    </div>
</div>
