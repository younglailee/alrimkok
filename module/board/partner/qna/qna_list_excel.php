<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\QnaAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

$doc_title = 'Q&A ';

$body_class = 'intranet_list sub';
$doc_title = ''; //페이지 타이틀, menu와 연동
$layout_size = '';

/* init Class */
$oBoard = new QnaAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');
/* check auth */
if (!$oBoard->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* list */
$oBoard->set('list_mode','excel');
$list = $oBoard->selectList();
$cnt_total = $oBoard->get('cnt_total');
/* search condition */
$search_like_arr = $oBoard->get('search_like_arr');
$search_date_arr = $oBoard->get('search_date_arr');
$query_string = $oBoard->get('query_string');
/* pagination */
$page = $oBoard->get('page');
$page_arr = $oBoard->getPageArray();
/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_state = $oBoard->get('flag_use_state');
/* code */
if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}
/* colspan */
$colspan = 4;
if ($flag_use_category) {
    $colspan++;
}
if ($flag_use_state) {
    $colspan++;
}
/* notice */
$notice_list = $oBoard->selectNoticeList();

$sch_bd_category = $_GET['sch_bd_category'];

// 파일 저장용 현재 시간
$now_time_file = date("Ymd_His");

// PHPExcel
include_once _EXCEL_PATH_ . '/Classes/PHPExcel.php';

//템플릿 엑셀 파일
$file = _MODULE_PATH_.'/board/admin/qna/qna_list_excel.xlsx';
$objReader = PHPExcel_IOFactory::createReaderForFile($file);
$objPHPExcel = new PHPExcel();
$objPHPExcel = $objReader->load($file);

//엑셀 파일명
$file_name = '1:1문의_리스트' . $now_time_file . '.xls';

$file_name = iconv('UTF-8','EUC-KR',$file_name);

//셀 입력 for문
for($i = 0; $i < count($list); $i++) {
    $k = $i + 3;
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValueExplicit("A$k", $list[$i]['no'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("B$k", $bd_category_arr[$list[$i]['bd_category']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("C$k", $list[$i]['bd_subject'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("D$k", $list[$i]['bd_content'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("E$k", $list[$i]['bd_answer_content'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("F$k", $list[$i]['reg_id'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("G$k", $list[$i]['bd_writer_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("H$k", $list[$i]['bd_reg_date'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("I$k", $list[$i]['txt_bd_state'], PHPExcel_Cell_DataType::TYPE_STRING);

}
// 커서 초기화
$objPHPExcel->setActiveSheetIndex(0)->getStyle("A3");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file_name");
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


