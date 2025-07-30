<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Db;
use sFramework\Format;
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

$oCompany = new CompanyAdmin();
$oCompany->init();
$cp_type_arr = $oCompany->get('cp_type_arr');

/* check auth */
if (!$oVisit->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$cp_id = $_GET['cp_id'];
$vs_ip = $_GET['vs_ip'];
$mb_id = $_GET['reg_id'];
//$list = $oVisit->selectDuplicateIp($cp_id, $vs_ip, $mb_id);
$list_mode = $_GET['list_mode'];
$list = $oVisit->selectListIpMonth($list_mode);
$cnt_total = $oVisit->get('cnt_total');

/* pagination */
$page = $oVisit->get('page');
$page_arr = $oVisit->getPageArray();
$query_string = $oVisit->get('query_string');

$layout_size = 'large';

$doc_title = 'IP/단말기정보수집';
if ($list_mode == 'company') {
    $doc_title = '접속 기업 현황(' . $sch_s_date . '~' . $sch_e_date . ')';
}
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
        <col style="width:100px"/>
        <col style="width:100px"/>
        <col style="width:300px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:100px"/>
        <col style="width:110px"/>
        <col style="width:110px"/>
        <col style="width:110px"/>
        <col style="width:110px"/>
        <col/>
        </colgroup>
        <thead>
        <tr>
            <th>No</th>
            <th>기업코드</th>
            <th>기업구분</th>
            <th>기업명</th>
            <th>아이피</th>
            <th>수강생</th>
            <th>파트너</th>
            <th>사업자등록번호</th>
            <th>대표번호</th>
            <th>담당자명</th>
            <th>직급</th>
            <th>최종접속일시</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $db_where = "WHERE reg_id != ''";
        if ($sch_e_date && $sch_s_date) {
            $db_where .= " AND reg_time >= '$sch_s_date 00:00:00' AND reg_time <= '$sch_e_date 23:59:59'";
        }
        //$db_where .= " GROUP BY vs_ip";
        $db_order = "ORDER BY reg_time DESC";
        $vs_list = Db::select('tbl_visit', "*", $db_where, $db_order, "");

        $arr_cp = array();
        $arr_us = array();
        foreach ($vs_list as $key => $val) {
            $vs_cp_id = $val['cp_id'];
            $vs_cp_ip = $val['vs_id'];
            if ($vs_cp_id && $vs_cp_ip) {
                $arr_cp[$vs_cp_id][$vs_cp_ip] = '';
            }
            $vs_user = $val['reg_id'];
            if ($vs_user) {
                $arr_us[$vs_cp_id][$vs_user] = '';
            }
        }
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
            $cp_id = $list[$i]['cp_id'];
            //$db_where = "WHERE cp_id = '$cp_id' AND reg_id != ''";
            //$ip_count = count($vs_list);
            $ip_count = count($arr_cp[$cp_id]);
            $us_count = count($arr_us[$cp_id]);

            // 파트너 출력
            $cp_where = "WHERE cp_id = '$cp_id'";
            $cp_data = Db::selectOnce('tbl_company', "*", $cp_where, "");
            $partner_name = $cp_data['partner_name'];
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $cp_data['cp_id'] ?></td>
                <td><?= $cp_type_arr[$cp_data['cp_type']] ?></td>
                <td title="<?= $list[$i]['cp_id'] ?>"><?= $list[$i]['cp_name'] ?></td>
                <td><?= $ip_count ?></td>
                <td><?= $us_count ?></td>
                <td><?= Format::getWithoutNull($partner_name) ?></td>
                <td><?= $cp_data['cp_number'] ?></td>
                <td><?= Html::beautifyTel($cp_data['cp_tel']) ?></td>
                <td><?= Format::getWithoutNull($cp_data['staff_name']) ?></td>
                <td><?= Format::getWithoutNull($cp_data['staff_position']) ?></td>
                <td><?= $list[$i]['reg_time'] ?></td>
            </tr>
        <?php } ?>
        <?= !count($list) ? Html::makeNoTd(10) : null ?>
        </tbody>
        </table>
        </form>
        <div class="pagination">
            <ul>
            <?= Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
    </div>
</div>
