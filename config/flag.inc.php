<?php
/**
 * 각종 환경설정의 플래그 정보를 설정
 * @file    flag.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}

unset($flag);

// 쿼리 로그 파일 기록 다시 활성화 yllee 200902
// 쿼리 로그 파일 기록 비활성화 yllee 200901
$flag['LOG_QUERY'] = true;
//$flag['LOG_QUERY'] = false;
//$flag['LOG_PAYMENT'] = true;

$flag['DISPLAY_ERROR'] = true;
