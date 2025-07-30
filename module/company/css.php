<?php
/**
 * 모듈의 CSS 파일
 * @file    css.php
 * @author  Alpha-Edu
 */
header('Content-Type: text/css; charset=utf-8');

$layout = $_GET['la'];
$module = $_GET['md'];

$css_file = './' . $layout . '/module.css';
if (file_exists($css_file)) {
    require_once $css_file;
}
