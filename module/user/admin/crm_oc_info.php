<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/

use sFramework\Html;
use sFramework\UserAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout;
global $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oUser = new UserAdmin();
$oUser->init();
$pk = $oUser->get('pk');
$mb_id = $_GET['mb_id'];
$oc_num = $_GET['oc_num'];
$bt_code = $_GET['bt_code'];
$cs_id = $_GET['cs_id'];

/* check auth */
if (!$oUser->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
//$flag_use_head = false;
$flag_use_header = false;
$flag_use_footer = false;

/* data */
$uid = $oUser->get('uid');

$oUser->set('mb_id', $mb_id);
$oUser->set('oc_num', $oc_num);
$oUser->set('bt_code', $bt_code);
$oUser->set('cs_id', $cs_id);

$list = $oUser->selectTime();
/*print_r($list);
print_r(unserialize($list['tm_record']));
print_r(unserialize($list['tm_ip_record']));*/
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>
<div id="<?= $module ?>" style="padding:20px;">
    <div class="write" style="float:left;width:880px">
        <fieldset>
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 10px"><?= $list['cs_name'] ?></h3>
        <h4 style="font-size: 18px; font-weight: bold; margin-bottom: 15px"><?= $oc_num ?>차시</h4>
        <table class="write_table">
        <caption>페이지 정보</caption>
        <colgroup>
        <col style="width:90px"/>
        <col style="width:120px"/>
        <col style="width:120px"/>
        <col style=""/>
        <col style="width:120px"/>
        <!--<col style="width:90px"/>-->
        <col style="width:120px"/>
        </colgroup>
        <thead>
        <tr>
            <th>페이지</th>
            <th>과정시간</th>
            <th>학습시간</th>
            <th>학습일시</th>
            <th>학습상태</th>
            <!--<th>학습IP</th>-->
            <th>학습기록횟수</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $pg_list = unserialize($list['tm_record']);
        $ip_list = unserialize($list['tm_ip_record']);
        $pg_data = $list['pg_data'];

        for ($i = 0; $i < count($pg_data); $i++) {
            $j = $i + 1;
            $pg_time_S = $pg_list[$j]['rtime'] % 60;
            $pg_time_M = ($pg_list[$j]['rtime'] - $pg_time_S) / 60;
            $stime_S = $pg_list[$j]['stime'] % 60;
            $stime_M = ($pg_list[$j]['stime'] - $stime_S) / 60;

            if ($pg_time_S < 10) {
                $pg_time_S = "0" . $pg_time_S;
            }
            if ($stime_S < 10) {
                $stime_S = "0" . $stime_S;
            }
            $pg_list[$j]['page_time'] = $pg_time_M . ":" . $pg_time_S;
            $stime = $stime_M . ":" . $stime_S;
            $count = count($pg_list[$j]['data']);
            $progress_pg = 'X';

            if ($pg_list[$j]['check'] == 1) {
                $progress_pg = 'O';
            }
            ?>
            <tr>
                <td><?= $j ?></td>
                <td><?= $pg_list[$j]['page_time'] ?></td>
                <td><?= $stime ?></td>
                <td><?= $pg_list[$j]['date'] ?></td>
                <td><?= $progress_pg ?></td>
                <!--<td><?= $ip_list[$j] ?></td>-->
                <td><?= $count ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        </table>
        </fieldset>
    </div>
