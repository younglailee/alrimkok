<?php
/**
 * 프로그램을 실행하기 위한 공통 설정
 * @file    common.php
 * @author  Alpha-Edu
 */

use sFramework\File;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
// 메모리 설정
ini_set('memory_limit','512M');

// 에러 출력 레벨
error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING);

// GET, POST 변수를 해제
$remove_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
    'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
    'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$cnt_remove = count($remove_arr);
for ($i = 0; $i < $cnt_remove; $i++) {
    if (isset($_GET[$remove_arr[$i]])) {
        unset($_GET[$remove_arr[$i]]);
    }
    if (isset($_POST[$remove_arr[$i]])) {
        unset($_POST[$remove_arr[$i]]);
    }
}
unset($remove_arr);
unset($cnt_remove);

/**
 * 배열을 순회하면서 addslashes를 실행
 * @param $arr
 * @return array
 */
function addSlashesWithArray($arr)
{
    if (!is_array($arr)) {
        return $arr;
    }

    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $val = addSlashesWithArray($val);
        } else {
            $val = addslashes($val);
        }

        $arr[$key] = $val;
    }

    return $arr;
}

// Magic quotes
/*
if (!get_magic_quotes_gpc()) {
    $_GET       = addSlashesWithArray($_GET);
    $_POST      = addSlashesWithArray($_POST);
    $_COOKIE    = addSlashesWithArray($_COOKIE);
    $_REQUEST   = addSlashesWithArray($_REQUEST);
}
*/
function get_magic_quotes_gpc()
{
    $_GET       = addSlashesWithArray($_GET);
    $_POST      = addSlashesWithArray($_POST);
    $_COOKIE    = addSlashesWithArray($_COOKIE);
    $_REQUEST   = addSlashesWithArray($_REQUEST);
}
// Register globals
@extract($_GET);
@extract($_POST);

// Timezone
if (PHP_VERSION >= '5.3.0') {
    date_default_timezone_set('Asia/Seoul');
}
// 기준 경로 설정
$root_path = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);
define('_ROOT_PATH_', $root_path);
define('_CONFIG_PATH_', $root_path . '/config');
unset($root_path);

// 기준 URI 설정
//$base_uri = 'http://' . $_SERVER['HTTP_HOST'];
$base_uri = '//' . $_SERVER['HTTP_HOST'];
define('_BASE_URI_', $base_uri);
unset($base_uri);

// 경로 설정
//unset($dir);
require _CONFIG_PATH_ . '/dir.inc.php';
foreach ($dir as $key => $val) {
    define('_' . $key . '_PATH_', _ROOT_PATH_ . '/' . $val);
    define('_' . $key . '_URI_', _BASE_URI_ . '/' . $val);
}
unset($dir);

// DB 접속 정보
require _CONFIG_PATH_ . '/db.inc.php';
foreach ($db as $key => $val) {
    define('_DB_' . $key . '_', $val);
}
unset($db);

// FLAG 정보
require _CONFIG_PATH_ . '/flag.inc.php';
foreach ($flag as $key => $val) {
    define('_FLAG_' . $key . '_', $val);
}
unset($flag);

// Auth 정보
require _CONFIG_PATH_ . '/auth.inc.php';
foreach ($auth as $key => $val) {
    define('_AUTH_' . $key . '_', $val);
}
unset($auth);

// Homepage 정보
require _CONFIG_PATH_ . '/homepage.inc.php';
foreach ($homepage as $key => $val) {
    define('_HOMEPAGE_' . $key . '_', $val);
}
unset($homepage);

// Expansion 정보
require _CONFIG_PATH_ . '/exp.inc.php';
foreach ($exp as $key => $val) {
    define('_EXP_' . $key . '_', $val);
}
unset($exp);

// Message 정보
require _CONFIG_PATH_ . '/msg.inc.php';
foreach ($msg as $key => $val) {
    define('_MSG_' . $key . '_', $val);
}
unset($msg);

if (!defined('_MODULE_PATH_')) {
    define('_UTIL_PATH_', _ROOT_PATH_ . '/util');
    define('_MODULE_PATH_', _ROOT_PATH_ . '/module');
    define('_SESSION_PATH_', _ROOT_PATH_ . '/session');
}

/**
 * 오토로딩
 * @param $class_name
 */
function autoloadClassFile($class_name)
{
    $class_name_arr = explode('\\', $class_name);
    if ($class_name_arr[0] == 'sFramework') {
        $class_name = $class_name_arr[1];
    }
    if (file_exists(_UTIL_PATH_ . '/' . $class_name . '.class.php')) {
        // util
        $class_file = _UTIL_PATH_ . '/' . $class_name . '.class.php';
    } elseif (file_exists(_MODULE_PATH_ . '/core/' . $class_name . '.class.php')) {
        // core
        $class_file = _MODULE_PATH_ . '/core/' . $class_name . '.class.php';
    } else {
        // module
        $module_name = '';
        for ($i = 0; $i < strlen($class_name); $i++) {
            $str = $class_name[$i];
            if ($i > 0 && ctype_upper($str)) {
                break;
            }
            $module_name .= $str;
        }
        $module_name = strtolower($module_name);
        // expansion
        if (defined('_EXP_' . strtoupper($module_name) . '_')) {
            $module_name = eval('return _EXP_' . strtoupper($module_name) . '_;');
            $module_name = strtolower($module_name);
        }
        $class_file = _MODULE_PATH_ . '/' . $module_name . '/' . $class_name . '.class.php';
    }
    if (!$class_file || !file_exists($class_file)) {
        Html::printError($class_file);
        Html::printError("Class '" . $class_name . "' does not exist.");
    }
    require_once $class_file;
}
// Autoloading
spl_autoload_register('autoloadClassFile');

// Session 설정
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');

File::makeDirectory(_SESSION_PATH_);
session_save_path(_SESSION_PATH_);

if (isset($SESSION_CACHE_LIMITER)) {
    @session_cache_limiter($SESSION_CACHE_LIMITER);
} else {
    @session_cache_limiter('no-cache, must-revalidate');
}
ini_set('session.cache_expire', 180);
ini_set('session.gc_maxlifetime', 10800);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

session_set_cookie_params(0, '/');
ini_set('session.cookie_domain', '');

session_start();

header('Expires: 0');                                           // rfc2616 - Section 14.21
header('Last-Modified: ' . date('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');   // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');  // HTTP/1.1
header('Pragma: no-cache');                                     // HTTP/1.0;

// Library
// PHPSESSID 다르면 로그아웃
if (isset($_REQUEST['PHPSESSID']) && $_REQUEST['PHPSESSID'] != session_id()) {
    exit;
}
// Mobile device
$is_mobile = preg_match('/phone|samsung|lgtel|mobile|skt|nokia|blackberry|android|sony/i', $_SERVER['HTTP_USER_AGENT']);

// Time
define('_NOW_TIME_', time());
define('_NOW_DATE_', date('Y-m-d', _NOW_TIME_));
define('_NOW_DATETIME_', date('Y-m-d H:i:s', _NOW_TIME_));
define('_NOW_YEAR_', substr(_NOW_DATE_, 0, 4));
define('_NOW_MONTH_', substr(_NOW_DATE_, 5, 2));
define('_NOW_DAY_', substr(_NOW_DATE_, 8, 2));
define('_USER_IP_', $_SERVER['REMOTE_ADDR']);

// 보안 취약점 점검 장비 IP 차단 yllee 230630
// 서버 부하 발생 좀비 PC 예상 IP 222.239.104.180 차단 yllee 240110
// 과정문의, 관리감독자교육, 위험성평가교육 스팸 글 등록 PC 203.160.72.246 차단 yllee 240304
// 과정문의, 관리감독자교육, 위험성평가교육 스팸 글 등록 PC 203.160.80.45 차단 yllee 240315
// 과정문의, 관리감독자교육, 위험성평가교육 스팸 글 등록 PC 203.160.80.45 차단 yllee 240315
$block_ip = array(
    '222.239.104.180',
    '203.160.72.246',
    '203.160.80.45',
    '203.160.80.2'
);
$request_ip = $_SERVER['REMOTE_ADDR'];
//if ($_SERVER['REMOTE_ADDR'] == '222.239.104.180') {
if (in_array($request_ip, $block_ip)) {
    echo '<img src="//alpha-edu.co.kr/data/upload/popup/1/111/4be2428b774f02f4b431a88e2448a372.png" alt="">';
    exit;
}
// Plugin > XSS
/*
 * 서버 부하 발생으로 주석 처리 yllee 230627
 * XSS 필터링 다시 적용 yllee 240304
 */
if (defined('_FILTER_PATH_')) {
    //$safe_domains = file_get_contents(_FILTER_PATH_ . '/safeiframe.txt');
    $safe_domains = "www.youtube(?:-nocookie)?.com/
serviceapi.rmcnmv.naver.com/
videofarm.daum.net/
player.vimeo.com/";
    $domain_arr = explode("\n", $safe_domains);
    $safe_arr = array('0' => $_SERVER['HTTP_HOST'] . '/');
    for ($i = 0; $i < count($domain_arr); $i++) {
        $domain_arr[$i] = trim($domain_arr[$i]);
        if ($domain_arr[$i]) {
            array_push($safe_arr, $domain_arr[$i]);
        }
    }
    $safe_domains = implode('|', $safe_arr);
    require _FILTER_PATH_ . '/HTMLPurifier.standalone.php';
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.SafeEmbed', false);
    $config->set('HTML.SafeObject', false);
    $config->set('Output.FlashCompat', false);
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(' . $safe_domains . ')%');
    $config->set('Attr.AllowedFrameTargets', array('_blank'));
    $config->set('HTML.MaxImgLength', null);
    $config->set('CSS.MaxImgLength', null);
    $oFilter = new HTMLPurifier($config);
}
// Plugin > User Agent
// DB 서버 CPU 서멀구리스 재도포 완료 후 방문 기록 다시 적용 yllee 220304
// DB 서버 부하로 주석 처리 yllee 220224
// 2022년 9월 1일 개강 기수 많아 DB 부하 발생 yllee 220901
/*
$ck_user_ip = Session::getCookie('ck_user_ip');
if ($ck_user_ip != _USER_IP_) {
    $oVisit = new Visit();
    $oVisit->init();
    $oVisit->insertData();
}
*/
