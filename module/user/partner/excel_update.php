<?php
/**
 * @file    process.php
 * @author  Alpha-Edu
 */

use sFramework\Api;
use sFramework\CompanyAdmin;
use sFramework\DB;
use sFramework\Format;
use sFramework\Html;
use sFramework\UserAdmin;

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/
if (!defined('_ALPHA_')) {
    exit;
}
date_default_timezone_set("Asia/Seoul");

/* init Class */
$oUser = new UserAdmin();
$oUser->init();

$oCompany = new CompanyAdmin();
$oCompany->init();

$mb_level_arr = $oUser->get('mb_level_arr');
$flag_use_arr = $oUser->get('flag_use_arr');

global $member;

$error_arr = array();
$error_mb_name_arr = array();
$error_mb_id_arr = array();
$error_msg_arr = array();

$is_check = true;

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$flag_json = ($_POST['flag_json']) ? $_POST['flag_json'] : $_GET['flag_json'];

if ($mode == 'excel_update') {
    // 엑셀 파일 등록
    //Log::debug($_POST);
    //Log::debug($_FILES);

    include_once _EXCEL_PATH_ . '/Classes/PHPExcel/IOFactory.php';
    $input_file = $_FILES['user_excel']['tmp_name'];
    $objReader = new PHPExcel_Reader_Excel2007();
    $objPHPExcel = $objReader->load($input_file);
    $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

    $result = array();

    if ($sheetData[1]['A'] != '이름' || !$sheetData[2]['B']) {
        $is_check = false;

        $result = array(
            'msg' => '엑셀파일을 확인해 주세요.',
            'uri' => './list.html'
        );
        return $result;

    }
    if ($is_check) {
        // API 회원 정보 배열 적용
        $api_arr = array();
        $j = 0;

        for ($i = 2; $i <= count($sheetData); $i++) {
            $is_check = true;
            $mb_name = trim($sheetData[$i]['A']);
            $mb_id = trim($sheetData[$i]['B']);
            $mb_id = preg_replace('/\s+/', '', $mb_id);
            $vResultId = $oUser->validateMemberId($mb_id);
            $mb_tel = Html::beautifyTel(trim($sheetData[$i]['E']));
            $mb_level = preg_replace('/\s+/', '', $sheetData[$i]['F']);
            $mb_level = array_search($mb_level, $mb_level_arr);
            $flag_use = preg_replace('/\s+/', '', $sheetData[$i]['G']);
            $flag_use = array_search($flag_use, $flag_use_arr);
            $mb_email = preg_replace('/\s+/', '', $sheetData[$i]['H']);
            $mb_direct_line = preg_replace('/\s+/', '', $sheetData[$i]['I']);
            $cp_id = trim($sheetData[$i]['J']);
            $cp_id = preg_replace('/\s+/', '', $cp_id);
            $mb_position = trim($sheetData[$i]['K']);
            $mb_zip = trim($sheetData[$i]['L']);
            $mb_addr = trim($sheetData[$i]['M']);
            $mb_addr2 = trim($sheetData[$i]['N']);
            $flag_book = $sheetData[$i]['O'];
            $flag_live = $sheetData[$i]['P'];
            $flag_sms = $sheetData[$i]['S'];
            $mb_cost_business_num = trim($sheetData[$i]['V']);

            $sw_depart_arr = array();

            if ($cp_id == '1628649339') {
                $sw_depart_arr = $oUser->get('sw_depart_arr');
            } elseif ($cp_id == '1628649560') {
                $sw_depart_arr = $oUser->get('sh_depart_arr');
            } elseif ($cp_id == '1645667084') {
                // 엑스퍼트 부서 추가 yllee 220830
                $sw_depart_arr = $oUser->get('expt_depart_arr');
            } elseif ($cp_id == '1680487190') {
                // (주) 중원이엔아이 부서 추가 minju 230705
                $sw_depart_arr = $oUser->get('jw_depart_arr');
            } elseif ($cp_id == '1706751639') {
                // 한양이엔지 (주) yllee 240226
                $sw_depart_arr = $oUser->get('hye_depart_arr');
            } elseif ($cp_id == '1502260229') {
                // 알파에듀 부서 추가 yllee 240311
                $sw_depart_arr = $oUser->get('alpha_depart_arr');
            } elseif ($cp_id == '1713925936') {
                // 재영텍 yllee 240830
                $sw_depart_arr = $oUser->get('jaeyoungtech_depart_arr');
            }
            $mb_data = Db::selectOnce('tbl_user', '*', "WHERE mb_id = '$mb_id'", '');
            $sw_depart = preg_replace('/\s+/', '', $sheetData[$i]['W']);
            $sw_depart = array_search($sw_depart, $sw_depart_arr);
            if (!$flag_book && !$flag_live) {
                if ($mb_data) {
                    $is_check = false;

                    $result = array(
                        'msg' => '이미 존재하는 아이디입니다.(' . $i . '열)',
                        'uri' => './list.html'
                    );
                    array_push($error_arr, $i);
                    array_push($error_msg_arr, '이미 존재하는 아이디입니다.');
                    array_push($error_mb_id_arr, $mb_id);
                    array_push($error_mb_name_arr, $mb_name);
                }
                if (!$cp_id && $cp_id != '') {
                    $sResultCode = $oCompany->searchCompanyCode($cp_id);

                    if (!$sResultCode || !$cp_id) {
                        $is_check = false;

                        array_push($error_arr, $i);
                        array_push($error_msg_arr, '기업이 존재하지않습니다.');
                        array_push($error_mb_id_arr, $mb_id);
                        array_push($error_mb_name_arr, $mb_name);
                    }
                }
                if ($sheetData[$i]['W'] && !array_search($sheetData[$i]['W'], $sw_depart_arr)) {
                    $is_check = false;

                    array_push($error_arr, $i);
                    array_push($error_msg_arr, '존재하지 않는 부서입니다.');
                    array_push($error_mb_id_arr, $mb_id);
                    array_push($error_mb_name_arr, $mb_name);
                }
            }
            if ($is_check) {
                $sResultCode = $oCompany->searchCompanyCode($cp_id);

                $flag_tomocard = $sheetData[$i]['R'];

                $flag_book = $sheetData[$i]['O'];
                $flag_live = $sheetData[$i]['P'];

                if (!$flag_tomocard) {
                    $flag_tomocard = 'N';
                }
                $flag_cyber = 'N';

                // 서원유통, 서원홀딩스, the큰병원, 미시안안과의원 인터넷연수원 사용 자동
                // 인생한반병원, 메디바이저, (주)삼광, (주)삼광윈테크, 경진기업, 삼성웰스토리 (주) 추가 minju 231121
                if ($cp_id == '1628649339' || $cp_id == '1628649560' || $cp_id == '1569223802' || $cp_id == '1590644638'
                    || $cp_id == '1685433610' || $cp_id == '1620917974' || $cp_id == '1504660510' || $cp_id == '1504660247' || $cp_id == '1504659952' || $cp_id == '1700532542') {
                    $flag_cyber = 'Y';
                }
                if ($mb_id) {
                    if (($flag_live == 'Y' || $flag_book == 'Y') && $mb_data) {

                        if (!$flag_live) {
                            $flag_live = $mb_data['flag_live'];
                        }
                        if (!$flag_book) {
                            $flag_book = $mb_data['flag_book'];
                        }
                        $arr = array(
                            'flag_book' => $flag_book,
                            'flag_live' => $flag_live
                        );
                        if (!Db::updateByArray('tbl_user', $arr, "WHERE mb_id='$mb_id'")) {
                            array_push($error_arr, $i);
                            array_push($error_msg_arr, '수정 과정에 오류가 발생했습니다.');
                            array_push($error_mb_id_arr, $mb_id);
                            array_push($error_mb_name_arr, $mb_name);
                        }
                    } else {
                        // 주민등록번호 트림 적용 yllee 210323
                        $mb_jumin = trim($sheetData[$i]['D']);
                        $mb_jumin_arr = explode('-', $mb_jumin);
                        // 주민등록번호 하이픈 없을 시 처리 minju 221212
                        // 조건 잘못 작성하여 수정 !$mb_jumin[1] -> !$mb_jumin_arr[1], offset 6 -> -7 변경 minju 230214
                        /*
                         * substr($mb_jumin,-7) 코드로 인해 생년월일만 입력된 엑셀 파일 업로드 시 주민등록번호 뒷자리에 생년월일도 적용되는 문제 발생하여 주석처리 yllee 230724
                        if (!$mb_jumin_arr[1]) {
                            $mb_jumin_arr[0] = substr($mb_jumin,0,6);
                            $mb_jumin_arr[1] = substr($mb_jumin,-7);
                        }
                        */
                        $mb_id = $mb_id;
                        $mb_pw = trim($sheetData[$i]['C']);
                        $mb_pw = preg_replace('/\s+/', '', $mb_pw);

                        if (!$mb_pw) {
                            $mb_pw = '1234';
                        }
                        $mb_pw = Format::encryptString($mb_pw);

                        $emon_res_no = $mb_jumin_arr[1];
                        $mb_resident_num = Format::encrypt($emon_res_no);

                        $mb_stu_type = $sheetData[$i]['T'];
                        $mb_irregular_type = $sheetData[$i]['U'];
                        $stu_type_count = strlen($mb_stu_type);
                        $irregular_type_count = strlen($mb_irregular_type);

                        if ($stu_type_count == 1) {
                            $mb_stu_type = '00' . $mb_stu_type;
                        } elseif ($stu_type_count == 2) {
                            $mb_stu_type = '0' . $mb_stu_type;
                        }
                        if ($irregular_type_count == 1) {
                            $mb_irregular_type = '00' . $mb_irregular_type;
                        } elseif ($irregular_type_count == 2) {
                            $mb_irregular_type = '0' . $mb_irregular_type;
                        }
                        $flag_auth = $sheetData[$i]['Q'];

                        if (!$flag_auth) {
                            $flag_auth = 'N';
                        }
                        // 이름 앞/뒤 공백 제거
//                        $mb_name = trim($mb_name);
                        // 이름에 유니코드 NBSP(U+00A0, no-break space) 공백 제거 yllee 240122
                        $mb_name = mb_ereg_replace("\x{00A0}", "", $mb_name);

                        $arr = array(
                            'mb_id' => $mb_id,
                            'mb_pw' => $mb_pw,
                            'mb_level' => $mb_level,
                            'mb_name' => $mb_name,
                            'mb_birthday' => $mb_jumin_arr[0],
                            'mb_resident_num' => $mb_resident_num,
                            'emon_res_no' => $emon_res_no,
                            'mb_email' => $mb_email,
                            'mb_tel' => $mb_tel,
                            'mb_direct_line' => $mb_direct_line,
                            'cp_id' => $cp_id,
                            'cp_name' => $sResultCode['cp_name'],
                            'mb_position' => $mb_position,
                            'mb_zip' => $mb_zip,
                            'mb_addr' => $mb_addr,
                            'mb_addr2' => $mb_addr2,
                            'mb_stu_type' => trim($mb_stu_type),
                            'mb_irregular_type' => trim($mb_irregular_type),
                            'mb_cost_business_num' => $mb_cost_business_num,
                            'flag_tomocard' => $flag_tomocard,
                            'flag_use' => $flag_use,
                            'flag_auth' => $flag_auth,
                            'flag_cyber' => $flag_cyber,
                            'flag_personal' => 'N',
                            'flag_sms' => $flag_sms,
                            'flag_book' => $flag_book,
                            'flag_live' => $flag_live,
                            'sw_depart' => $sw_depart,
                            'reg_id' => $member['mb_id'],
                            'reg_time' => _NOW_DATETIME_
                        );
                        if (!Db::insertByArray('tbl_user', $arr)) {
                            array_push($error_arr, $i);
                            array_push($error_msg_arr, '등록 과정에서 요류가 발생했습니다.');
                            array_push($error_mb_id_arr, $mb_id);
                            array_push($error_mb_name_arr, $mb_name);
                        } else {
                            Db::update('tbl_user', "emon_res_no = ''", "WHERE mb_id = '$mb_id'");
                            if ($sheetData[$i]['F'] == '기업관리자') {
                                Db::update('tbl_company', "staff_name='" . $mb_name . "', staff_position='" . $sheetData[$i]['K'] . "', staff_email='" . $sheetData[$i]['H'] . "'", "WHERE cp_id = '" . $cp_id . "'");
                            }
                            // API 전송 로직 위치 변경 yllee 220714
                            // API 기반 모니터링: 회원정보(기록) yllee 220610
                            if ($arr['mb_id']) {
                                //$api_arr[$j] = array(
                                $api_arr = array(
                                    'mb_id' => $arr['mb_id'],
                                    'mb_name' => $arr['mb_name'],
                                    'mb_birthday' => $arr['mb_birthday'],
                                    'emon_res_no' => $arr['emon_res_no'],
                                    'cp_name' => $arr['cp_name'],
                                    'mb_email' => $arr['mb_email'],
                                    'mb_tel' => $arr['mb_tel'],
                                    'reg_time' => $arr['reg_time'],
                                    'mb_cost_business_num' => $arr['mb_cost_business_num'],
                                    'mb_stu_type' => $arr['mb_stu_type'],
                                    'mb_irregular_type' => $arr['mb_irregular_type']
                                );
                                Api::userHist($api_arr, 'C');
                                $j++;
                            }
                        }
                    }
                }
            }
        }
    }
}
if (count($error_mb_id_arr) > 0) {
    echo count($error_mb_id_arr) . '건의 실패가 있습니다.';
    echo '</br>';
    echo '<table>';
    echo '<tr>';
    echo '<td>행</td>';
    echo '<td>실패메세지</td>';
    echo '<td>아이디</td>';
    echo '<td>이름</td>';
    echo '</tr>';
    for ($i = 0; $i < count($error_mb_id_arr); $i++) {
        echo '<tr>';
        echo '<td>' . $error_arr[$i] . '</td>';
        echo '<td>' . $error_msg_arr[$i] . '</td>';
        echo '<td>' . $error_mb_id_arr[$i] . '</td>';
        echo '<td>' . $error_mb_name_arr[$i] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    $result = array(
        'msg' => '정상적으로 등록되었습니다.',
        'uri' => "./list.html"
    );
    $flag_json = '';
    Html::postprocessFromResult($result, $flag_json);
}
