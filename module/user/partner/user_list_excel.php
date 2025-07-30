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
$mb_flag_test = $member['flag_test'];
if ($mb_flag_test == 'Y') {
    $list = array(
        '0' => array(
            'no' => '1',
            'mb_level' => '1',
            'mb_id' => 'alpha',
            'mb_name' => '홍길동',
            'mb_birthday' => '900000',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_sms' => 'Y',
            'mb_email' => 'alpha@alpha-edu.co.kr',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '1' => array(
            'no' => '2',
            'mb_level' => '1',
            'mb_id' => 'alpha_test2',
            'mb_name' => '임꺽정',
            'mb_birthday' => '900000',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_sms' => '',
            'mb_email' => 'alpha@alpha-edu.co.kr',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '2' => array(
            'no' => '3',
            'mb_level' => '1',
            'mb_id' => 'alpha_test3',
            'mb_name' => '이순신',
            'mb_birthday' => '900000',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_sms' => '',
            'mb_email' => 'alpha@alpha-edu.co.kr',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '3' => array(
            'no' => '4',
            'mb_level' => '1',
            'mb_id' => 'alpha_test4',
            'mb_name' => '정약용',
            'mb_birthday' => '900000',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_sms' => 'Y',
            'mb_email' => 'alpha@alpha-edu.co.kr',
            'bt_reg_date' => '2020-10-14 19:02:23'
        ),
        '4' => array(
            'no' => '5',
            'mb_level' => '1',
            'mb_id' => 'alpha_test5',
            'mb_name' => '유관순',
            'mb_birthday' => '900000',
            'cp_name' => '(주)알파에듀',
            'mb_tel' => '000-000-0000',
            'flag_auth' => 'Y',
            'flag_use' => 'work',
            'flag_sms' => 'Y',
            'mb_email' => 'alpha@alpha-edu.co.kr',
            'bt_reg_date' => '2020-10-14 19:02:23'
        )
    );
} else {
    $list = $oUser->selectList();
}
$flag_auth_arr = $oUser->get('flag_auth_arr');
$mb_level_arr = $oUser->get('mb_level_arr');
$flag_use_arr = $oUser->get('flag_use_arr');

// 파일 저장용 현재 시간
$now_time_file = date("Ymd_His");

// PHPExcel
include_once _EXCEL_PATH_ . '/Classes/PHPExcel.php';

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
    $flag_sms = $list[$i]['flag_sms'];
    $txt_flag_sms = '수신';
    if ($flag_sms == 'Y') {
        $txt_flag_sms = '비수신';
    }
    $k = $i + 3;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValueExplicit("A$k", $list[$i]['no'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("B$k", $mb_level_arr[$list[$i]['mb_level']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("C$k", $list[$i]['mb_id'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("D$k", $list[$i]['mb_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("E$k", $list[$i]['mb_birthday'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("F$k", $list[$i]['mb_tel'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("G$k", $list[$i]['cp_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("H$k", $list[$i]['mb_email'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("I$k", $flag_use_arr[$list[$i]['flag_use']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("J$k", $flag_auth_arr[$list[$i]['flag_auth']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("K$k", $txt_flag_sms, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("L$k", $list[$i]['reg_date'], PHPExcel_Cell_DataType::TYPE_STRING);

}
//$objPHPExcel->getActiveSheet()->getStyle("A6:L$k")->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle("A3:L$k")->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    )
);
$objPHPExcel->getActiveSheet()->getStyle("A3:L$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


// 커서 초기화
$objPHPExcel->setActiveSheetIndex(0)->getStyle("A3");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file_name");
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
