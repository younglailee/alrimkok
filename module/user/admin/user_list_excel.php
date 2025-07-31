<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\UserAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oUser = new UserAdmin();
$oUser->init();

$flag_use_header = false;
$flag_use_footer = false;

$oUser->set('list_mode', 'excel');

global $member;
$list = $oUser->selectList();
$flag_auth_arr = $oUser->get('flag_auth_arr');
$mb_level_arr = $oUser->get('mb_level_arr');
$flag_use_arr = $oUser->get('flag_use_arr');

// 파일 저장용 현재 시간
$now_time_file = date("Ymd_His");

// _EXCEL_PATH_ 상수 체크
define('_EXCEL_PATH_', '/home/best1alpha/www/plugin/PHPExcel-1.8/');

// PHPExcel
include_once _EXCEL_PATH_ . '/Classes/PHPExcel.php';
//error_reporting(E_ALL & ~E_WARNING);ini_set('display_errors', '1');
//템플릿 엑셀 파일
$file = _MODULE_PATH_ . '/user/admin/user_list_excel.xlsx';
$objReader = PHPExcel_IOFactory::createReaderForFile($file);
$objPHPExcel = new PHPExcel();
$objPHPExcel = $objReader->load($file);

//엑셀 파일명
$file_name = '회원_리스트' . $now_time_file . '.xls';

$file_name = iconv('UTF-8', 'EUC-KR', $file_name);

//셀 입력 for문
for ($i = 0; $i < count($list); $i++) {
    $flag_notice = $list[$i]['flag_notice'];
    $txt_flag_notice = '미동의';
    if ($flag_notice == 'Y') {
        $txt_flag_notice = '동의';
    }
    $k = $i + 3;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValueExplicit("A$k", $list[$i]['no'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("B$k", $mb_level_arr[$list[$i]['mb_level']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("C$k", $list[$i]['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("D$k", $list[$i]['mb_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("E$k", $list[$i]['mb_depart'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("F$k", $list[$i]['mb_hp'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("G$k", $list[$i]['cp_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("H$k", $list[$i]['mb_email'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("I$k", $flag_use_arr[$list[$i]['flag_use']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("J$k", $txt_flag_notice, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("K$k", $list[$i]['reg_date'], PHPExcel_Cell_DataType::TYPE_STRING);
}
//$objPHPExcel->getActiveSheet()->getStyle("A6:L$k")->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle("A3:K$k")->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    )
);
$objPHPExcel->getActiveSheet()->getStyle("A3:K$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


// 커서 초기화
$objPHPExcel->setActiveSheetIndex(0)->getStyle("A3");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file_name");
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
