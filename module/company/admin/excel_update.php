<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\UserAdmin;
use sFramework\CompanyAdmin;
use sFramework\Html;
use sFramework\Db;

if (!defined('_ALPHA_')) {
    exit;
}

date_default_timezone_set("Asia/Seoul");

/* init Class */
$oUser = new UserAdmin();
$oUser->init();

$oCompany = new CompanyAdmin();
$oCompany->init();

$cp_type_arr = $oCompany->get('cp_type_arr');

global $member;
$mode = ($_POST['mode']) ?: $_GET['mode'];
$flag_json = ($_POST['flag_json']) ?: $_GET['flag_json'];

if ($mode == 'excel_update') {
    // 엑셀 파일 등록
    //Log::debug($_POST);
    //Log::debug($_FILES);

    include_once _EXCEL_PATH_ . '/Classes/PHPExcel/IOFactory.php';
    $input_file = $_FILES['user_excel']['tmp_name'];
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($input_file);
    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


    $is_check = true;

    $result = array();

    if ($sheetData[1]['A'] != '기업구분' || !$sheetData[2]['A']) {
        $is_check = false;

        $result = array(
            'msg' => '엑셀파일을 확인해 주세요.',
            'uri' => './list.html'
        );
    }
    if ($is_check) {
        for ($i = 2; $i <= count($sheetData); $i++) {
            if ($sheetData[$i]['D']) {
                $check_cs_code = $oCompany->searchCompanyNum($sheetData[$i]['D']);
                $partner_data = $oUser->searchPartnerId($sheetData[$i]['N']);

                $flag_book = $sheetData[$i]['F'];
                $flag_live = $sheetData[$i]['G'];

                if(!$flag_book && !$flag_live){
                    if ($check_cs_code['data']) {
                        $is_check = false;

                        $result = array(
                            'msg' => $i . '열의 사업자등록번호가 중복됩니다.',
                            'uri' => './list.html'
                        );
                        break;
                    }
                }
            }
        }
    }

    if ($is_check) {
        for ($i = 2; $i <= count($sheetData); $i++) {

            $cp_id = time()+$i;
            $partner_data = $oUser->searchPartnerId($sheetData[$i]['P']);
            $partner_name = $partner_data['mb_name'];

            $flag_book = $sheetData[$i]['F'];
            $flag_live = $sheetData[$i]['G'];
            $cp_number = $sheetData[$i]['D'];

            $check_cs_code = $oCompany->searchCompanyNum($cp_number);

            if(($flag_book || $flag_live) && $check_cs_code['data']){
                $arr = array(
                    'flag_book' => $flag_book,
                    'flag_live' => $flag_live,
                    'upt_id' => $member['mb_id'],
                    'upt_time' => _NOW_DATETIME_
                );

                Db::updateByArray('tbl_company',$arr,"WHERE cp_number = '$cp_number'");
            }else{
                if ($sheetData[$i]['E']) {
                    // 주소 '(홀따옴표) -> ’(아포스트로피) 변환 yllee 240906
                    $sheetData[$i]['J'] = str_replace("'", "’", $sheetData[$i]['J']);
                    $sheetData[$i]['K'] = str_replace("'", "’", $sheetData[$i]['K']);
                    $_POST = array(
                        'cp_id' => $cp_id,
                        'cp_type' => array_search($sheetData[$i]['A'], $cp_type_arr),
                        'cp_name' => $sheetData[$i]['B'],
                        'cp_ceo' => $sheetData[$i]['C'],
                        'cp_number' => $sheetData[$i]['D'],
                        'cp_tel' => $sheetData[$i]['E'],
                        'flag_book' => $sheetData[$i]['F'],
                        'flag_live' => $sheetData[$i]['G'],
                        'cp_fax' => $sheetData[$i]['H'],
                        'cp_zip' => $sheetData[$i]['I'],
                        'cp_address' => $sheetData[$i]['J'],
                        'cp_address2' => $sheetData[$i]['K'],
                        'staff_name' => $sheetData[$i]['L'],
                        'staff_position' => $sheetData[$i]['M'],
                        'staff_email' => $sheetData[$i]['N'],
                        'staff_tax_bill' => $sheetData[$i]['O'],
                        'partner_id' => $sheetData[$i]['P'],
                        'partner_name' => $partner_name
                    );
                    $result = $oCompany->insertData();
                }
            }
        }
    }
}

// 결과 처리
Html::postprocessFromResult($result, $flag_json);
