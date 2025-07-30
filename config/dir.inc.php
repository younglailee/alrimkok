<?php
/**
 * 디렉토리의 경로를 설정
 * @file    dir.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}

unset($dir);

$dir['COMMON']  = 'common';
    $dir['CSS']     = $dir['COMMON'] . '/css';
    $dir['JS']      = $dir['COMMON'] . '/js';
    $dir['IMG']     = $dir['COMMON'] . '/img';

$dir['DATA']    = 'data';
    $dir['CACHE']  = $dir['DATA'] . '/cache';
    $dir['SESSION'] = $dir['DATA'] . '/session';
    $dir['UPLOAD']  = $dir['DATA'] . '/upload';
    $dir['DATA_EXCEL']  = $dir['DATA'] . '/excel';

$dir['LAYOUT']  = 'layout';

$dir['LOG']     = 'log';
    $dir['DEBUG']   = $dir['LOG'] . '/debug';
    $dir['PAYMENT'] = $dir['LOG'] . '/payment';
    $dir['QUERY']   = $dir['LOG'] . '/query';

$dir['MODULE']  = 'module';

$dir['PLUGIN']  = 'plugin';
    $dir['EDITOR']  = $dir['PLUGIN'] . '/smarteditor2';
    $dir['FILTER']  = $dir['PLUGIN'] . '/htmlpurifier';
    $dir['UA']      = $dir['PLUGIN'] . '/browscap';
    $dir['EXCEL']   = $dir['PLUGIN'] . '/PHPExcel-1.8';

$dir['UTIL']     = 'util';
