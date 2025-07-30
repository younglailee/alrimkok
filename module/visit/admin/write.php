<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\VisitAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
global $layout;
global $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oVisit = new VisitAdmin();
$oVisit->init();
$pk = $oVisit->get('pk');

/* check auth */
if (!$oVisit->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}
/* data */
$uid = $oVisit->get('uid');
$data = $oVisit->selectDetail($uid);

/* search condition */
$query_string = $oVisit->get('query_string');

if ($data['vs_device'] == 'Desktop') {
    $data['vs_device'] = 'PC';
}
?>
<script type="text/javascript">
//<![CDATA[
//]]>
</script>
<div id="<?= $module ?>">
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>
        <input type="hidden" name="<?= $pk ?>" value="<?= $uid ?>"/>
        <input type="hidden" name="bt_code" value="<?= $data['bt_code'] ?>"/>

        <fieldset>
        <legend>기수 정보</legend>
        <table class="write_table">
        <caption>기수 정보 입력 테이블</caption>
        <colgroup>
        <col style="width:140px"/>
        <col/>
        <col style="width:140px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th class=""><label for="us_name">이름</label></th>
            <td>
                <span id="reg_name"><?= $data['reg_name'] ?></span>
            </td>
            <th class=""><label for="us_name">아이디</label></th>
            <td>
                <span id="reg_id"><?= $data['reg_id'] ?></span>
            </td>
        </tr>
        <tr>
            <th class=""><label for="bt_company_name">기업명</label></th>
            <td colspan="3">
                <span id="cp_name"><?= $data['cp_name'] ?></span>
            </td>
        </tr>
        <tr>
            <th class=""><label for="vs_device">단말기 정보</label></th>
            <td colspan="3">
                <span id="vs_device"><?= $data['vs_os'] . ' ' . $data['vs_browser'] ?></span>
            </td>
        </tr>
        <tr>
            <th class=""><label for="vs_referer">접속경로</label></th>
            <td colspan="3">
                <span id="vs_referer"><?= $data['vs_referer'] ?></span>
            </td>
        </tr>
        <tr>
            <th class=""><label for="vs_ip">접속IP</label></th>
            <td>
                <span id="vs_ip"><?= $data['vs_ip'] ?></span>
            </td>
            <th>경로</th>
            <td>
                <span><?= $data['vs_device'] ?></span>
            </td>
        </tr>
        <tr>
            <th class=""><label for="reg_time">접속일시</label></th>
            <td colspan="3">
                <span id="reg_time"><?= $data['reg_time'] ?></span>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
        </div>
        </form>
    </div>
</div>
<form name="check_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="mb_id" value=""/>
<input type="hidden" name="mb_pw" value=""/>
</form>
