<?php
/**
 * URL 요청을 받아서 적절한 로직을 실행
 * @file    controller.php
 * @author  Alpha-Edu
 */
/*
//error_reporting(E_ALL);
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', '1');
*/
use sFramework\Html;
use sFramework\Log;
use sFramework\Menu;
use sFramework\Session;

define('_ALPHA_', true);

require './common.php';

// 전역 변수 선언 yllee 220512
global $this_uri;
global $result;
global $head_file;
global $header_file;
global $footer_file;
global $menu;
global $page_no_arr;
global $doc_title;
global $group_title;
global $html_title;
global $title_path;
global $title_path;
global $title_path;
global $title_path;
global $body_class;

// Flag use layout file
$flag_use_head = true;
$flag_use_header = true;
$flag_use_footer = true;

// Layout, Module, Extenstion, Service, $mode
$layout = $_GET['la'];
$module = $_GET['md'];
$expansion = $_GET['ep'];
$service = $_GET['sv'];
$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$tomorrow = ($_POST['tomorrow']) ? $_POST['tomorrow'] : $_GET['tomorrow'];

// 경로 변수
$base_uri = _BASE_URI_;
$layout_uri = $base_uri . '/web' . $layout;
if (defined('_JS_URI_')) {
    $js_uri = _JS_URI_;
}
if (defined('_CSS_URI_')) {
    $css_uri = _CSS_URI_;
}
if (defined('_IMG_URI_')) {
    $img_uri = _IMG_URI_;
}
// User info
$mb_class_name = 'sFramework\Member' . ucfirst($layout);
//$lambda_member = create_function('', "return new {$mb_class_name}();");
$lambda_member = function() use ($mb_class_name) {
    return new $mb_class_name();
};
$oMember = $lambda_member();
$oMember->init();
$member = $oMember->getLoginMember();

// 최고 관리자 권한
$is_root = false;
if ($member['mb_level'] == 10 || $member['mb_level'] == 9) {
    $is_root = true;
}
// 비회원 권한
$is_member = false;
$is_guest = true;
//$is_mobile = true;
if ($member['mb_level'] > 2) {
    $is_member = true;
    $is_guest = false;
    $is_mobile = true;
}
// 레이아웃 권한 검사
$auth_layout = false;
if ($module == 'member' && defined('_AUTH_EXCEPTION_SERVICES_') && strpos(_AUTH_EXCEPTION_SERVICES_, $service) > -1) {
    $auth_layout = true;
} elseif ($module == 'member' && defined('_AUTH_EXCEPTION_MODES_') && strpos(_AUTH_EXCEPTION_MODES_, $mode) > -1) {
    $auth_layout = true;
} elseif (eval('return defined("_AUTH_' . strtoupper($layout) . '_MIN_LEVEL_");') && $member['mb_level'] >= eval('return _AUTH_' . strtoupper($layout) . '_MIN_LEVEL_;')) {
    $auth_layout = true;
}
if ($is_mobile && $layout == 'mobile') {
    $auth_layout = true;
}
// 권한 검사(인트라넷) yllee 191120
if ($layout == 'tutor') {
    if ($member['mb_level'] == 5) {
        $auth_layout = true;
    }
} elseif ($layout == 'company') {
    if ($member['mb_level'] == 4) {
        $auth_layout = true;
    }
} elseif ($layout == 'partner') {
    if ($member['mb_level'] == 6) {
        $auth_layout = true;
    }
} elseif ($layout == 'cyber') {
    // 인터넷연수원: 수강생, 기업관리자 모두 레이아웃 권한 허용
    //if ($member['mb_level'] == 1) {
    $auth_layout = true;
    //}
} elseif ($layout == 'damc') {
    // 인터넷연수원: 수강생, 기업관리자 모두 레이아웃 권한 허용
    //if ($member['mb_level'] == 1) {
    $auth_layout = true;
    //}
} elseif ($layout == 'grand' || $layout == 'misian' || $layout == 'kepid' || $layout == 'samkwang' || $layout == 'ssws') {
    // 한전산업개발주식회사 추가 yllee 221117
    // 더큰병원 모든 레이아웃 권한 허용 yllee 220126
    // 미시안안과 추가 yllee 220530
    $auth_layout = true;
} elseif ($layout == 'admin') {
    // 관리자 > 과정 콘텐츠 미리보기 접속 허용 yllee 220510
    if ($service == 'popup.course_preview') {
        $auth_layout = true;
    }

    if($service == 'api.login'){
        $auth_layout = true;
    }
}

if (!$auth_layout) {
    Session::setSession('ss_' . $layout . '_' . 'mb_id', '');
    $login_uri = $layout_uri . '/member/login.html?return_uri=' . urlencode($_SERVER['REQUEST_URI']);
    if ($module == 'page' && $service == 'main') {
        // 비상 서비스 장애 접속 허용 로직 yllee 230125
        $emergency = $_SESSION['emergency'] ?: $_GET['emergency'];
        if ($emergency == 'incident' && $layout == 'admin') {
            if (!$_SESSION['emergency']) {
                $_SESSION['emergency'] = $emergency;
            }
        }
        Html::movePage($login_uri);
    } else {
        //Html::alert('권한이 없습니다', $login_uri);
        Html::movePage($login_uri);
    }
}
// 현재 URI
if (!$this_uri) {
    $this_uri = $_SERVER['REQUEST_URI'];
    $this_uri = substr($this_uri, 0, strpos($this_uri, '?') - 1);
}
if (!strpos($this_uri, '.html') > -1) {
    $this_uri .= '/page/main.html';
    if ($member['mb_level'] == 5) {
        $this_uri .= '/contents/list.html';
    } elseif ($member['mb_level'] == 4) {
        //$this_uri .= '/progress/list.html';
    }
    str_replace('//', '/', $this_uri);
}
// 모듈 클래스 검사
$module_class_file = _MODULE_PATH_ . '/' . $module . '/';
if ($expansion) {
    $module_class_file .= ucfirst($expansion);
} else {
    $module_class_file .= ucfirst($module);
}
$module_class_file .= ucfirst($layout) . '.class.php';
if (!file_exists($module_class_file)) {
    $content = '<strong>Module Error</strong>';
    $content .= '<br />';
    $content .= 'module_class_file : ' . $module_class_file;
    Html::printError($content);
}
// 서비스 파일 검사
$service_file = _MODULE_PATH_ . '/' . $module . '/' . $layout . '/';
if ($expansion) {
    $service_file .= $expansion . '/';
}
$service_file .= $service . '.php';

if (file_exists($service_file)) {
    ob_start();
    require_once $service_file;
    $content = ob_get_contents();
    ob_end_clean();
} else {
    echo "File not found: $service_file";
}
// ajax-json
$flag_json = ($_GET['flag_json']) ? $_GET['flag_json'] : $_POST['flag_json'];
if ($flag_json) {
    if (!$result['code']) {
        $result['code'] = 'success';
    }
    $result['content'] = $content;
    //Log::debug("result");
    //Log::debug($result);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}
// 레이아웃 파일 검사
if (!$head_file) {
    $head_file = 'head.inc.php';
}
if (!$header_file) {
    // 인트라넷 로그인일 경우만 헤더파일을 바꿔줍니다. jepark 20191113
    $exception_php_url_p1 = explode('/', $_SERVER['REQUEST_URI']);
    $exception_php_url_p2 = explode('.', $exception_php_url_p1[3]);

    $header_file = 'header.inc.php';

    // 리뉴얼 레이아웃 파일
    $service_arr = explode('_', $service);
    if ($service_arr[0] == 'new') {
        $head_file = 'new_head.inc.php';
        $header_file = 'new_header.inc.php';
        $footer_file = 'new_footer.inc.php';
    }
}
if (!$footer_file) {
    // 인트라넷 로그인일 경우만 헤더파일을 바꿔줍니다. jepark 20191113
    $exception_php_url_p1 = explode('/', $_SERVER['REQUEST_URI']);
    $exception_php_url_p2 = explode('.', $exception_php_url_p1[3]);
    if ($exception_php_url_p2[0] == 'intranet_login' || $exception_php_url_p2[0] == 'intranet_material_login') {
        $footer_file = 'login_footer.inc.php';
    } else {
        $footer_file = 'footer.inc.php';
    }
}
if ($service == 'process') {
    $flag_use_head = false;
    $flag_use_header = false;
    $flag_use_footer = false;
}
$layout_path = _LAYOUT_PATH_ . '/' . $layout;
if ($flag_use_head && !file_exists($layout_path . '/' . $head_file)) {
    $content = '<strong>Layout Error</strong>';
    $content .= '<br />';
    $content .= 'layout_file : ' . $layout_path . '/' . $head_file;
    Html::printError($content);
}
if ($flag_use_header && !file_exists($layout_path . '/' . $header_file)) {
    $content = '<strong>Layout Error</strong>';
    $content .= '<br />';
    $content .= 'layout_file : ' . $layout_path . '/' . $header_file;
    Html::printError($content);
}
if ($flag_use_footer && !file_exists($layout_path . '/' . $footer_file)) {
    $content = '<strong>Layout Error</strong>';
    $content .= '<br />';
    $content .= 'layout_file : ' . $layout_path . '/' . $footer_file;
    Html::printError($content);
}
// 메뉴 정보
if ($flag_use_header) {
    $menu_file = _LAYOUT_PATH_ . '/' . $layout . '/menu.inc.php';
    if (file_exists($menu_file)) {
        require_once $menu_file;
        $oMenu = new Menu($menu, $this_uri);

        if (!$page_no_arr) {
            $page_no_arr = $oMenu->getPageNoArr();
        }
        if (!$doc_title) {
            $doc_title = $oMenu->getDocumentTitle();
        }
        if (!$group_title) {
            $group_title = $oMenu->getGroupTitle();
        }
        if (!$html_title) {
            $html_title = $oMenu->makeHtmlTitle($doc_title);
        }
        if (!$title_path) {
            $title_path = $oMenu->makeTitlePath('<a href="' . $layout_uri . '/page/main.html">Home</a>');
            if ($member['mb_level'] == 5) {
                $title_path = $oMenu->makeTitlePath('<a href="' . $layout_uri . '/contents/list.html">Home</a>');
            }
        }
        if (!$body_class) {
            $body_class = $oMenu->getBodyClass();
        }
        $gnb = $oMenu->makeGnb();
        if ($layout == 'mobile') {
            $gnb = $oMenu->makeMobileGnb();
        } elseif ($layout == 'admin') {
            $gnb = $oMenu->makeAdminGnb();
        }
        $snb = $oMenu->makeSnb();
        $sitemap = $oMenu->makeSitemap();
        if ($layout == 'admin') {
            $sitemap = $oMenu->makeAdminSitemap();
        }
    }
}
// head 출력
header('Content-Type: text/html; charset=utf-8');
if ($flag_use_head) {
    ob_start();
    require_once $layout_path . '/' . $head_file;
    $head_buffer = ob_get_contents();
    ob_end_clean();

    // 모듈 CSS 파일
    if (file_exists(_MODULE_PATH_ . '/' . $module . '/css.php')) {
        // 삽입될 위치 결정
        preg_match('#<script(.*)</script>#i', $head_buffer, $first_js);
        $first_js = $first_js[0];

        // css link
        $css_link = '<link rel="stylesheet" type="text/css" href="' . $layout_uri . '/';
        if ($expansion) {
            $css_link .= $expansion . '/expansion.css';
        } else {
            $css_link .= $module . '/module.css';
        }
        $css_link .= '" />';
        $head_buffer = str_replace($first_js, $css_link . "\n" . $first_js, $head_buffer);
    }
    # 모듈 JS 파일
    $module_js = $module . '.js';
    if (file_exists(_MODULE_PATH_ . '/' . $module . '/js.php')) {
        $js_link = '<script type="text/javascript" src="' . $layout_uri . '/';
        if ($expansion) {
            $js_link .= $expansion . '/expansion.js';
        } else {
            $js_link .= $module . '/module.js';
        }
        $js_link .= '"></script>';
        $head_buffer .= $js_link . "\n";
    }
    echo $head_buffer;
}
// header 출력
if ($flag_use_header) {
    require_once $layout_path . '/' . $header_file;
}
// 본문 출력
//echo '<!-- ' . $module . ' -->';
echo $content;
//echo '<!-- //' . $module . ' -->';
// footer 출력
if ($flag_use_footer) {
    require_once $layout_path . '/' . $footer_file;
}

