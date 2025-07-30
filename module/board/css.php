<?php
/**
 * 모듈의 CSS 파일
 * @file    css.php
 * @author  Alpha-Edu
 */
header('Content-Type: text/css; charset=utf-8');

$layout = $_GET['la'];
$module = $_GET['md'];
$expansion = $_GET['ep'];

$css_file = './' . $layout . '/' . $expansion . '/expansion.css';
if (file_exists($css_file)) {
    require_once $css_file;
}
