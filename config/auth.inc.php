<?php
/**
 * 각 레이아웃별 권한 정보
 * @file    auth.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}
unset($auth);

// 권한 설정에 해당되지 않는 서비스와 모드
$auth['EXCEPTION_SERVICES'] = 'login,ajax_find_password,ajax_find_password_result';
$auth['EXCEPTION_MODES'] = 'login,logout,check_member_id,check_member_password,check_member_email';

// 각 레이아웃 모드의
$auth['ADMIN_MIN_LEVEL'] = 7;
$auth['MANAGAER_MIN_LEVEL'] = 7;
$auth['USER_MIN_LEVEL'] = 1;
