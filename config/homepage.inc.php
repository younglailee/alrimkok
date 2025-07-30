<?php
/**
 * 홈페이지 정보를 설정
 * @file    homepage.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}

//unset($homepage);
$homepage = array();

$homepage['TITLE'] = '알림콕';
//$homepage['TITLE'] = '알파에듀 | 내일배움카드, 안전보건교육, 위험성평가, 법정필수';
$homepage['ENG_TITLE'] = 'Alpha-Edu Online Education Center';
$homepage['ADDR'] = '경상남도 창원시 의창구 창원대로363번길 22-33(대찬빌딩) 503호';
$homepage['TEL'] = '055-255-6364';
$homepage['FAX'] = '055-255-6369';

$homepage['DOMAIN'] = $_SERVER['HTTP_HOST'];
$homepage['EMAIL'] = 'alpha@alpha-edu.co.kr';
