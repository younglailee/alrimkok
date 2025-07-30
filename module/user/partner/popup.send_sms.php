<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\UserAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserAdmin();
$oUser->init();

$mb_name = $_GET['mb_name'];
$mb_hp = $_GET['mb_hp'];
$mb_id = $_GET['mb_id'];
$mb_ids = $_GET['mb_ids'];

if ($mb_ids) {
    $list = $oUser->selectListIds($mb_ids);
    $mb_name_arr = array();
    $mb_hp_arr = array();

    for ($i = 0; $i < count($list); $i++) {
        array_push($mb_name_arr, $list[$i]['mb_name']);
        array_push($mb_hp_arr, $list[$i]['mb_hp']);
    }
    $mb_hp = implode(',', $mb_hp_arr);
    $mb_name = implode(',', $mb_name_arr);
    $mb_id = $mb_ids;
}
?>
<style>
.sms_example {
    font-size:16px;
    width:80px;
    height:50px;
    margin-bottom:5px;
}
</style>

<script type="text/javascript">
//<![CDATA[
$(function() {
    $("#sendMsgBtn").click(function() {
        var remote_phone = $("#remote_phone").val();
        var remote_name = $("#remote_name").val();
        var remote_msg = $("#remote_msg").val();
        var mb_id = $("#mb_id").val();
        /*var send_type = */
        if (!remote_phone) {
            alert('수신번호를 입력하세요.');
        } else {
            $.ajax({
                url: "process.html",
                type: "GET",
                dataType: "json",
                data: {
                    flag_json: '1',
                    mode: 'send_sms',
                    remote_name: remote_name,
                    remote_phone: remote_phone,
                    remote_msg: remote_msg,
                    mb_id: mb_id
                },
                success: function(result) {
                    alert('발송완료하였습니다.');
                    location.reload();
                    closeLayerPopup();
                }
            })
        }
    });
});

function fnChkByte(obj, maxByte) {
    var str = obj.value;
    var str_len = str.length;
    var rbyte = 0;
    var rlen = 0;
    var one_char = "";
    var f = document.SMSFORM;

    for (var i = 0; i < str_len; i++) {
        one_char = str.charAt(i);
        if (escape(one_char).length > 4) {
            rbyte += 2;
        } else {
            rbyte++;
        }
        if (rbyte <= maxByte) {
            rlen = i + 1;
        }
    }
    $("#byte_span").text(rbyte + "/90 bytes");
}
//]]>
</script>
<div class="search" style="text-align: center">
    <h2>문자의 신 URL 연동 서비스</h2>
    <div>
        수신번호<br/>
        <input type="text" name="remote_phone" id="remote_phone" value="<?= $mb_hp ?>" size="<?= $mb_ids ? 50 : 25 ?>">
        <!-- 수신번호 다수일때는 쉼표','로 구분 -->
    </div>
    <div>
        수신자명<br/>
        <input type="text" name="remote_name" id="remote_name" value="<?= $mb_name ?>" size="<?= $mb_ids ? 50 : 25 ?>">
        <!-- 수신번호 다수일때는 이름을 쉼표','로 구분 -->
    </div>
    <input type="hidden" name="remote_subject" value=""><!--장문(lms)전송시에만 입력 -->
    <input type="hidden" name="mb_id" id="mb_id" value="<?= $mb_id ?>"/>
    <div>
        <label for="remote_msg">메시지</label><br/>
        <textarea id="remote_msg" name="remote_msg" style="width:167px;height:200px"
                  oninput="fnChkByte(this,90)"></textarea>
    </div>
    <div style="margin-top: 10px">
        <input type="button" id="sendMsgBtn" name="submit1" value="전송">
        <span id='byte_span'></span>
    </div>
</div>
