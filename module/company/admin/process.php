<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Db;
use sFramework\Format;
use sFramework\Html;
use sFramework\Board;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();
$oBoard = new Board();
$oBoard->init();

// Parameter
$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$flag_json = ($_POST['flag_json']) ? $_POST['flag_json'] : $_GET['flag_json'];

// 금액 천담위 콤바 제거 yllee 240905
if ($_POST['cp_edu_money']) {
    $_POST['cp_edu_money'] = filter_var($_POST['cp_edu_money'], FILTER_SANITIZE_NUMBER_INT);
}
if ($mode == 'search_company') {
    $result['code'] = 'success';
    $sch_like = $_GET['sch_like'];
    $sch_keyword = $_GET['sch_keyword'];
    ob_start();
    include_once _MODULE_PATH_ . '/company/admin/ajax.search_company.php';
    $content = ob_get_contents();
    ob_end_clean();
    $result['content'] = $content;
} elseif ($mode == 'insert') {
    $result = $oCompany->insertData();
} elseif ($mode == 'search_cp_num') {
    $cp_number = $_GET['cp_number'];
    $result = $oCompany->searchCompanyNum($cp_number);
} elseif ($mode == 'update') {
    $result = $oCompany->updateData();

    // 기업명이 변경되면 수강생의 기업명 데이터도 함께 변경(이몬 전송용 주민번호 뒷자리 생성/삭제) yllee 211001
    //Log::debug('cp_name_old: ' . $_POST['cp_name_old']);
    //Log::debug('cp_name: ' . $_POST['cp_name']);
    $cp_name = $_POST['cp_name'];
    if ($_POST['cp_name_old'] != $cp_name) {
        $cp_id = $_POST['cp_id'];
        $data_table = 'tbl_user';
        $db_column = "mb_id, mb_resident_num, mb_birthday";
        $db_where = "WHERE cp_id = '$cp_id'";
        $db_order = "ORDER BY mb_id ASC";
        $list = Db::select($data_table, $db_column, $db_where, $db_order, '');
        //Log::debug($db_where);
        //Log::debug($list);
        $now_time = date('Y-m-d H:i:s');
        for ($i = 0; $i < count($list); $i++) {
            $mb_id = $list[$i]['mb_id'];
            $mb_resident_num = $list[$i]['mb_resident_num'];
            $mb_birthday = $list[$i]['mb_birthday'];
            // 생년월일유무 분기문 geosan 240705
            if ($mb_birthday) {
                $emon_res_no = Format::decrypt($mb_resident_num);
                $update_value = "cp_name = '$cp_name', ";
                $update_value .= "emon_res_no = '$emon_res_no', ";
                $update_value .= "upt_id = 'root', upt_time = '$now_time'";
                $db_where = "WHERE mb_id ='$mb_id'";
                if (Db::update($data_table, $update_value, $db_where)) {
                    // 이몬 전송 후 주민등록번호 뒷자리 제거 yllee 211001
                    Db::update($data_table, "emon_res_no = ''", "WHERE mb_id = '$mb_id'");
                }
            } else {
                $update_value = "cp_name = '$cp_name', ";
                $update_value .= "upt_id = 'root', upt_time = '$now_time'";
                $db_where = "WHERE mb_id ='$mb_id'";
                Db::update($data_table, $update_value, $db_where);
            }
        }
    }
    // 담당자 이메일 변경 시 기업관리자(교육담당자) 이메일 주소 자동 변경 기능 yllee 230601
    $staff_email = $_POST['staff_email'];
    if ($_POST['staff_email_old'] != $staff_email) {
        $cp_id = $_POST['cp_id'];
        $data_table = 'tbl_user';
        $now_time = date('Y-m-d H:i:s');
        $update_value = "mb_email = '$staff_email', ";
        $update_value .= "upt_id = 'root', upt_time = '$now_time'";
        $db_where = "WHERE cp_id ='$cp_id' AND mb_level = '4'";
        Db::update($data_table, $update_value, $db_where);
    }
    // 담당자 이름 변경 시 기업관리자(교육담당자) 이름 자동 변경 기능 minju 230626
    $staff_name = $_POST['staff_name'];
    if ($_POST['staff_name_old'] != $staff_name) {
        $cp_id = $_POST['cp_id'];
        $data_table = 'tbl_user';
        $now_time = date('Y-m-d H:i:s');
        $update_value = "mb_name = '$staff_name', ";
        $update_value .= "upt_id = 'root', upt_time = '$now_time'";
        $db_where = "WHERE cp_id ='$cp_id' AND mb_level = '4'";
        Db::update($data_table, $update_value, $db_where);
    }

} elseif ($mode == 'delete') {
    $result = $oCompany->deleteData();
} elseif ($_GET['mode'] == 'reset_pw') {
    $result = $oCompany->resetPass();
} elseif ($mode == 'send_mail') {
    //Log::debug($_POST);
    $result = $oBoard->sendCompanyMail();
}
Html::postprocessFromResult($result, $flag_json);
