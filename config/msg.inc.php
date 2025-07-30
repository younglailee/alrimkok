<?php
/**
 * 메시지 정보를 설정
 * @file    msg.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}

//unset($msg);
$msg = array();

$msg['SENDER_NO'] = '0522597928';
//$msg['SENDER_NO'] = '01077283681';
$msg['SENDER_EMAIL'] = '';

//$msg['SMS_URI'] = 'http://sms.direct.co.kr/link/send.php';
//$msg['SMS_URI'] .= '?guest_no=080525&guest_key=6a62a72e97b9d6b64cdc19f08c8e38b1';
//$msg['SMS_URI'] .= '&stran_phone={sm_receiver_no}&stran_callback={sm_sender_no}';
//$msg['SMS_URI'] .= '&stran_msg={sm_content}';

$msg['SMS_URI'] = 'http://www.smscore.co.kr/webapi/message/send.html';
//$msg['SMS_URI'] = 'http://test.smscore.co.kr/webapi/message/send.html';
$msg['SMS_ID'] = 'usarte';
$msg['SMS_KEY'] = 'TL10K3BC6F01FJ7QY3IC';

