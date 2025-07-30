<?php
/**
 * 모듈의 JS 파일
 * @file    js.php
 * @author  Alpha-Edu
 */
header('Content-Type: text/javascript; charset=utf-8');

$layout = $_GET['la'];
$module = $_GET['md'];

$js_file = './' . $layout . '/module.js';
if (file_exists($js_file)) {
    require_once $js_file;
}
