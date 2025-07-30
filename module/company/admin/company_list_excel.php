<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();

$flag_use_header = false;
$flag_use_footer = false;

$oCompany->set('list_mode', 'excel');

// 테스트 계정 확인
global $member;
$mb_flag_test = $member['flag_test'];
$unsubscribe = $_GET['unsubscribe'];
if ($mb_flag_test == 'Y') {
    // 테스트 배열
    $list = array(
        '0' => array(
            'no' => '3',
            'cp_id' => '1502260229',
            'cp_type' => 'priority_support',
            'cp_name' => '(주)알파에듀',
            'cp_ceo' => '장재선',
            'cp_number' => '4928100339',
            'cp_edu_money' => '0',
            'cp_tel' => '0552556364',
            'cp_fax' => '0552556369',
            'cp_zip' => '51395',
            'cp_address' => '경남 창원시 의창구 창원대로18번길 46 (팔용동, 경남창원과학기술진흥원)',
            'cp_address2' => '1213호',
            'staff_name' => '서혜미',
            'staff_position' => '대리',
            'staff_email' => 'alpha@alpha-edu.co.kr',
            'partner_name' => '장재선'
        ),
        '1' => array(
            'no' => '2',
            'cp_id' => '1602908890',
            'cp_type' => 'priority_support',
            'cp_name' => '혜미컴퍼니',
            'cp_ceo' => '장재선',
            'cp_number' => '1234567890',
            'cp_edu_money' => '16587',
            'cp_tel' => '055-111-1112',
            'cp_fax' => '055-222-2223',
            'cp_zip' => '51347',
            'cp_address' => '경남 창원시 마산회원구 봉암북7길 21',
            'cp_address2' => '5동 305호',
            'staff_name' => '혜미기업관리자',
            'staff_position' => '',
            'staff_email' => 'admin@alpha-edu.co.kr',
            'partner_name' => '장재선'
        ),
        '2' => array(
            'no' => '1',
            'cp_id' => '1482940026',
            'cp_type' => '',
            'cp_name' => '직업능력심사평가원',
            'cp_ceo' => '이문수',
            'cp_number' => '1234567890',
            'cp_edu_money' => '57620',
            'cp_tel' => '1644-5113',
            'cp_fax' => '02-6943-4025',
            'cp_zip' => '04637',
            'cp_address' => '서울특별시 중구 퇴계로 10 메트로타워',
            'cp_address2' => '12층 직업능력심사평가원 종합민원센터',
            'staff_name' => '김민경',
            'staff_position' => '담당',
            'staff_email' => 'ksqa@koreatech.ac.kr',
            'partner_name' => ''
        )
    );
} else {
    $list = $oCompany->selectList();
}
$cp_type_arr = $oCompany->get('cp_type_arr');

// 파일 저장용 현재 시간
$now_time_file = date("Ymd_His");

// PHPExcel
include_once _EXCEL_PATH_ . '/Classes/PHPExcel.php';

//템플릿 엑셀 파일
$file = _MODULE_PATH_ . '/company/admin/company_list_excel.xlsx';
$objReader = PHPExcel_IOFactory::createReaderForFile($file);
$objPHPExcel = new PHPExcel();
$objPHPExcel = $objReader->load($file);

//엑셀 파일명
$file_name = '기업리스트' . $now_time_file . '.xls';
$file_name = iconv('UTF-8', 'EUC-KR', $file_name);

//셀 입력 for문
for ($i = 0; $i < count($list); $i++) {
    $txt_edu_money = '-';
    if ($list[$i]['cp_edu_money']) {
        $txt_edu_money = number_format($list[$i]['cp_edu_money']);
    }
    // 인원 추가: 윤지현 요청 yllee 230427
    $cp_id = $list[$i]['cp_id'];
    $count_user = $oCompany->countUser($cp_id);
    $k = $i + 3;
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValueExplicit("A$k", $list[$i]['no'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("B$k", $list[$i]['cp_id'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("C$k", $cp_type_arr[$list[$i]['cp_type']], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("D$k", $list[$i]['cp_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("E$k", $list[$i]['cp_ceo'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("F$k", $list[$i]['cp_number'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("G$k", $txt_edu_money, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("H$k", Html::beautifyTel($list[$i]['cp_tel']), PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("I$k", Html::beautifyTel($list[$i]['cp_fax']), PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("J$k", '(' . $list[$i]['cp_zip'] . ')' . $list[$i]['cp_address'] . ' ' . $list[$i]['cp_address2'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("K$k", $list[$i]['staff_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("L$k", $list[$i]['staff_position'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("M$k", $list[$i]['staff_email'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("N$k", $list[$i]['partner_name'], PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("O$k", $count_user, PHPExcel_Cell_DataType::TYPE_STRING)
        ->setCellValueExplicit("P$k", $list[$i]['staff_email_unsubscribe'], PHPExcel_Cell_DataType::TYPE_STRING);
}
//$objPHPExcel->getActiveSheet()->getStyle("A6:L$k")->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle("A3:P$k")->applyFromArray(
    array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
            )
        )
    )
);
$objPHPExcel->getActiveSheet()->getStyle("A3:P$k")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// 커서 초기화
$objPHPExcel->setActiveSheetIndex(0)->getStyle("A3");
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file_name");
header("Cache-Control: max-age=0");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
