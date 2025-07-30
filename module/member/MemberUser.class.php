<?php
/**
 * 사용자모드 > 회원 모듈 클래스
 * @file    MemberUser.class.php
 * @author  Alpha-Edu
 * @package member
 */

namespace sFramework;

use function array_filter;
use function array_key_exists;
use function array_map;
use function array_values;
use function count;
use function explode;
use function implode;
use function is_array;
use function preg_replace;
use function print_r;
use function str_replace;
use function strpos;
use function strtolower;
use function time;
use function trim;
use const _NOW_DATETIME_;

class MemberUser extends Member
{
    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('data_table', User::$data_table);
        $this->set('pk', User::$pk);

        $instance = new User();
        $instance->init();
        $this->set('instance', $instance);

        $this->set('max_file', 1);
    }

    /* update password */
    public function updatePassword()
    {
        global $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $uid = $member[$pk];

        $mb_pass = $_POST['mb_pass'];
        $new_pass = $_POST['new_pass'];
        $new_pass2 = $_POST['new_pass2'];

        if (!$mb_pass || !$new_pass || !$new_pass2) {
            $this->result['msg'] = '비정상적인 접근입니다.';
            return $this->result;
        }
        if ($new_pass != $new_pass2) {
            $this->result['msg'] = '변경할 비밀번호가 서로 다릅니다.';
            return $this->result;
        }
        $mb_pass = Format::encryptString($mb_pass);
        $new_pass = Format::encryptString($new_pass);

        //Log::debug($mb_pass);
        $chk = Db::selectCount($data_table, "where $pk = '$uid' and mb_pw = '$mb_pass'");
        if (!$chk) {
            $this->result['msg'] = '현재 비밀번호가 정확하지 않습니다.';
            //$this->result['uri'] = './modify_info.html?mode=fail';
            return $this->result;
        }
        if ($mb_pass == $new_pass) {
            $this->result['msg'] = '신규 비밀번호는 현재 비밀번호와 다르게 입력해야 합니다.';
            //$this->result['uri'] = './modify_info.html?mode=fail';
            return $this->result;
        }
        $chk_arr = $this->validateMemberPassword($_POST['new_pass']);
        if (!$chk_arr['flag']) {
            $result = array(
                'code' => 'failure',
                'msg' => $chk_arr['msg']
            );
            return $result;
        }
        Db::update($data_table, "mb_pw = '$new_pass'", "where $pk = '$uid' and mb_pw = '$mb_pass'");

        $this->result['code'] = 'update_ok';
        $this->result['uri'] = './modify_info.html';
        $this->result['msg'] = '비밀번호 변경에 성공하였습니다.';

        return $this->result;
    }

    public function updateMemberData(): array
    {
        global $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $uid = $member[$pk];

        parent::initUpdate();
        $update_columns = 'cp_name,,mb_zip,mb_email,mb_hp,mb_depart,mb_name,flag_notice,upt_id,upt_time';

        $arr = $this->getParameters($update_columns, 'post');
        $arr['upt_id'] = $uid;
        $arr['upt_time'] = _NOW_DATETIME_;

        if (Db::updateByArray($data_table, $arr, "WHERE $pk = '$uid'")) {
            $result = array(
                'code' => 'success',
                'uri' => './modify_info.html',
                'msg' => $this->get('success_msg')
            );
            // 부계정은 기업 정보 저장하지 않음 yllee 250723
            if ($member['mb_level'] != '2') {
                $cp_count = ($_POST['cp_count']) ?: 0;
                $cp_revenue = ($_POST['cp_revenue']) ?: 0;
                $cp_date = ($_POST['cp_date']) ?: '0000-00-00';
                if ($cp_date == '00-1-11-30') {
                    $cp_date = '0000-00-00';
                }
                $cp_arr = array(
                    'cp_name' => $_POST['cp_name'],
                    'cp_count' => $cp_count,
                    'cp_date' => $cp_date,
                    'cp_size' => $_POST['cp_size'],
                    'cp_revenue' => $cp_revenue,
                    'flag_venture' => $_POST['flag_venture'],
                    'flag_research' => $_POST['flag_research'],
                    'flag_product' => $_POST['flag_product'],
                    'cp_zip' => $_POST['cp_zip'],
                    'cp_address' => $_POST['cp_address'],
                    'cp_address2' => $_POST['cp_address2'],
                    'upt_id' => $uid,
                    'upt_time' => _NOW_DATETIME_
                );
                $cp_id = $_POST['cp_id'];
                Db::updateByArray("tbl_company", $cp_arr, "WHERE cp_id='$cp_id'");
            }
            //print_r($_POST);exit;
            $it_arr = array(
                'upt_id' => $uid,
                'upt_time' => _NOW_DATETIME_
            );
            $it_arr['it_area'] = (is_array($_POST['it_area'])) ? implode('|', $_POST['it_area']) : '';
            $it_arr['it_type'] = (is_array($_POST['it_type'])) ? implode('|', $_POST['it_type']) : '';
            $it_arr['it_info'] = (is_array($_POST['it_info'])) ? implode('|', $_POST['it_info']) : '';

            Db::updateByArray("tbl_interest", $it_arr, "WHERE mb_id='$uid'");
            // 첨부파일
            if ($this->get('max_file')) {
                // 기업소개서는 기업 모듈로 업로드 yllee 250718
                $this->uploadFiles($cp_id, 'company');
            }
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '수정 과정에서 장애가 발생하였습니다.'
            );
        }
        return $result;

    }

    public function selectApplicationList()
    {
        global $member;

        $mb_id = $member['mb_id'];

        $list = Db::select('tbl_application', '*', "WHERE us_id = '$mb_id' AND ap_state != 'C' AND payment_state != 'E'", '', '');

        for ($i = 0; $i < count($list); $i++) {
            $cs_id = $list[$i]['cs_numbers'];
            $list[$i]['cs_data'] = Db::selectOnce('tbl_course', '*', "WHERE cs_id ='$cs_id'", '');
            $list[$i]['no'] = $i + 1;
            $list[$i]['reg_date'] = substr($list[$i]['reg_time'], 0, 10);

            if ($list[$i]['ap_state'] == 'E') {
                $bt_data = Db::selectOnce('tbl_batch_user a JOIN tbl_batch b ON a.bu_bt_code = b.bt_code', '*', "WHERE a.bu_mb_id = '$mb_id' AND b.bt_course = '$cs_id'", '');
                if ($bt_data) {
                    $list[$i]['bt_data'] = $bt_data;
                }
            }
        }

        return $list;
    }

    public function cancelApplication()
    {
        $ap_id = $_GET['ap_id'];

        $result['code'] = 'success';

        if (Db::update('tbl_application', "ap_state = 'C'", "WHERE ap_id = $ap_id")) {
            $result['msg'] = '취소하였습니다.';
        } else {
            $result['code'] = 'failure';
            $result['msg'] = '취소하는 과정에 오류가 발생하였습니다. 관리자에게 문의바랍니다.';
        }

        return $result;
    }

    public function checkId()
    {
        $mb_id = $_GET['mb_id'];
        $result['code'] = 'success';

        // 웹 취약점 조치: 필드를 * -> mb_id 변경 yllee 220708
        $db_where = "WHERE mb_id = '$mb_id'";
        $mb_data = Db::selectOnce('tbl_user', 'mb_id', $db_where, '');
        $result['data'] = $mb_data['mb_id'];

        return $result;
    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);

        $stu_type = $arr['mb_stu_type'];
        $irregular_type = $arr['mb_irregular_type'];

        $cp_id_arr = $_POST['cp_id_arr'];
        $cp_name_arr = $_POST['cp_name_arr'];


        $cp_id = $cp_id_arr;
        $cp_name = $cp_name_arr;

        if (is_array($cp_id_arr)) {
            $cp_id = implode('|', $cp_id_arr);
        }

        if (is_array($cp_name_arr)) {
            $cp_name = implode('|', $cp_name_arr);
        }

        $arr['cp_id'] = $cp_id;
        $arr['cp_name'] = $cp_name;

        $stu_type_count = strlen($stu_type);
        $irregular_type_count = strlen($irregular_type);

        if ($stu_type_count == 1) {
            $arr['mb_stu_type'] = '00' . $stu_type;
        } elseif ($stu_type_count == 2) {
            $arr['mb_stu_type'] = '0' . $stu_type;
        }

        if ($irregular_type_count == 1) {
            $arr['mb_irregular_type'] = '00' . $irregular_type;
        } elseif ($irregular_type_count == 2) {
            $arr['mb_irregular_type'] = '0' . $irregular_type;
        }

        // 패스워드
        $arr['mb_pw'] = Format::encryptString($arr['mb_pw']);
        $arr['mb_pw_time'] = _NOW_DATETIME_;

        // 주민번호
        $arr['mb_resident_num'] = Format::encrypt($arr['mb_resident_num']);

        return $arr;
    }

    public function join()
    {
        $mb_pw = Format::encryptString($_POST['mb_pw']);

        // 비밀번호 검증 yllee 220811
        $chk_arr = $this->validateMemberPassword($_POST['mb_pw']);
        if (!$chk_arr['flag']) {
            return array(
                'code' => 'failure',
                'msg' => $chk_arr['msg']
            );
        }
        $mb_hp = preg_replace("/[^0-9]/", "", $_POST['mb_hp']);;
        $mb_id = strtolower(trim($_POST['mb_id']));
        $information_terms = $_POST['information_terms'];

        // 이메일 주소
        $mb_email = $_POST['mb_email'];
        $mb_email2 = $_POST['mb_email2'];
        if ($mb_email2 == 'manual') {
            $mb_email .= '@' . $_POST['managerEmailCustomDomain'];
        } else {
            $mb_email .= '@' . $mb_email2;
        }
        $cp_id = time();
        $cp_name = $_POST['cp_name'];

        $arr = array(
            'mb_id' => $mb_id,
            'mb_pw' => $mb_pw,
            'mb_level' => '1',
            'mb_name' => trim($_POST['mb_name']),
            'mb_depart' => trim($_POST['mb_depart']),
            'mb_hp' => $mb_hp,
            'mb_email' => $mb_email,
            'cp_id' => $cp_id,
            'cp_name' => $cp_name,
            'flag_use' => 'use',
            'flag_notice' => $information_terms,
            'flag_personal' => 'Y',
            'flag_selection' => 'Y',
            'policy_time' => _NOW_DATETIME_,
            'privacy_time' => _NOW_DATETIME_,
            'selection_time' => _NOW_DATETIME_,
            'reg_id' => $mb_id,
            'reg_time' => _NOW_DATETIME_
        );
        if (Db::insertByArray('tbl_user', $arr)) {
            // 기업 정보 기록
            $cp_arr = array(
                'cp_id' => $cp_id,
                'cp_name' => $cp_name,
                'cp_number' => $mb_id,
                'reg_id' => $mb_id,
                'reg_time' => _NOW_DATETIME_
            );
            Db::insertByArray('tbl_company', $cp_arr);
            $it_arr = array(
                'mb_id' => $mb_id,
                'reg_id' => $mb_id,
                'reg_time' => _NOW_DATETIME_
            );
            Db::insertByArray('tbl_interest', $it_arr);

            $result = array(
                'code' => 'success',
                'uri' => "./join.html?step=3",
                'msg' => '회원가입을 완료하였습니다.'
            );
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
        }
        return $result;
    }

    // 부계정 등록/수정 yllee 250723
    public function joinSub()
    {
        $mb_pw = $_POST['mb_pw'];
        if ($mb_pw) {
            $mb_pw = Format::encryptString($_POST['mb_pw']);
            $chk_arr = $this->validateMemberPassword($_POST['mb_pw']);
            if (!$chk_arr['flag']) {
                return array(
                    'code' => 'failure',
                    'msg' => $chk_arr['msg']
                );
            }
        }
        $mb_hp = preg_replace("/[^0-9]/", "", $_POST['mb_hp']);;
        $mb_id = strtolower(trim($_POST['mb_id']));

        $result = array(
            'code' => 'failure',
            'msg' => '등록 과정에서 장애가 발생하였습니다.'
        );
        $mode = $_POST['mode'];
        if ($mode == 'sub_insert') {
            // 이메일 주소
            $mb_email = $_POST['mb_email'];
            $mb_email2 = $_POST['mb_email2'];
            if ($mb_email2 == 'manual') {
                $mb_email .= '@' . $_POST['managerEmailCustomDomain'];
            } else {
                $mb_email .= '@' . $mb_email2;
            }
            // 기업 정보는 member 배열에서 가져오기
            global $member;
            $cp_id = $member['cp_id'];
            $cp_name = $member['cp_name'];

            $arr = array(
                'mb_id' => $mb_id,
                'mb_pw' => $mb_pw,
                'mb_level' => '2',
                'mb_name' => trim($_POST['mb_name']),
                'mb_depart' => trim($_POST['mb_depart']),
                'mb_hp' => $mb_hp,
                'mb_email' => $mb_email,
                'cp_id' => $cp_id,
                'cp_name' => $cp_name,
                'flag_use' => 'use',
                'flag_personal' => 'Y',
                'flag_selection' => 'Y',
                'flag_notice' => 'Y',
                'policy_time' => _NOW_DATETIME_,
                'privacy_time' => _NOW_DATETIME_,
                'selection_time' => _NOW_DATETIME_,
                'reg_id' => $member['mb_id'],
                'reg_time' => _NOW_DATETIME_
            );
            //print_r($arr);exit;
            if (Db::insertByArray('tbl_user', $arr)) {
                $query_string = $_POST['query_string'];
                $result = array(
                    'code' => 'success',
                    'uri' => "./sub_list.html?" . $query_string,
                    'msg' => '부계정 등록이 완료하였습니다.'
                );
                $it_arr = array(
                    'mb_id' => $mb_id,
                    'reg_id' => $member['mb_id'],
                    'reg_time' => _NOW_DATETIME_
                );
                Db::insertByArray('tbl_interest', $it_arr);
            }
        } elseif ($mode == 'sub_update') {
            $mb_email = $_POST['mb_email'];
            $arr = array(
                'upt_id' => $mb_id,
                'upt_time' => _NOW_DATETIME_
            );
            if ($mb_pw) {
                $arr['mb_pw'] = $mb_pw;
            }
            if ($_POST['mb_name']) {
                $arr['mb_name'] = trim($_POST['mb_name']);
            }
            if ($_POST['mb_depart']) {
                $arr['mb_depart'] = trim($_POST['mb_depart']);
            }
            if ($mb_hp) {
                $arr['mb_hp'] = $mb_hp;
            }
            if ($mb_email) {
                $arr['mb_email'] = $mb_email;
            }
            $db_where = "WHERE mb_id='$mb_id'";
            //print_r($_POST);
            //print_r($arr);echo $db_where;exit;
            if (Db::updateByArray('tbl_user', $arr, $db_where)) {
                $query_string = $_POST['query_string'];
                $result = array(
                    'code' => 'success',
                    'uri' => "./sub_list.html?" . $query_string,
                    'msg' => '해당 부계정을 수정하였습니다.'
                );
            }
        }
        return $result;
    }

    public function paymentApplicationList($ap_ids)
    {
        $ap_id_arr = explode(',', $ap_ids);

        $ap_id_arr = array_values(array_filter($ap_id_arr));

        $list = array();

        for ($i = 0; $i < count($ap_id_arr); $i++) {
            $ap_id = $ap_id_arr[$i];
            array_push($list, Db::selectOnce('tbl_application', '*', "WHERE ap_id = '$ap_id'", ''));
        }

        return $list;
    }

    public function updateMethod($ap_ids)
    {
        $ap_id_arr = explode(',', $ap_ids);

        $ap_id_arr = array_values(array_filter($ap_id_arr));

        $result['code'] = 'success';

        for ($i = 0; $i < count($ap_id_arr); $i++) {
            $ap_id = $ap_id_arr[$i];

            $arr = array(
                'payment_methods' => 'D',
                'payment_state' => 'W'
            );

            Db::updateByArray('tbl_application', $arr, "WHERE ap_id = '$ap_id'");
        }

        return $result;
    }

    public function updatePayment($ap_id)
    {
        $arr = array(
            'payment_methods' => 'C',
            'payment_state' => 'E',
            'payment_time' => _NOW_DATETIME_
        );

        Db::updateByArray('tbl_application', $arr, "WHERE ap_id = '$ap_id'");
    }

    public function certification()
    {
        global $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $uid = $member[$pk];

        $mb_pass = $_POST['mb_pass'];
        $new_pass = $_POST['new_pass'];
        $new_pass2 = $_POST['new_pass2'];

        //Log::debug('1 : '.$mb_pass);
        //Log::debug('2 : '.$new_pass);
        //Log::debug('3 : '.$new_pass2);

        //if (!$mb_pass || !$new_pass || !$new_pass2 || $new_pass != $new_pass2) {
        if (!$mb_pass || !$new_pass || !$new_pass2) {
            $this->result['msg'] = '비밀번호를 확인하세요.';
            return $this->result;
        }
        // 신규 비밀번호 비교 yllee 230725
        if ($new_pass != $new_pass2) {
            Log::debug('비밀번호 확인');
            Log::debug($new_pass);
            Log::debug($new_pass2);
            $this->result['msg'] = '신규 비밀번호가 서로 다릅니다.';
            return $this->result;
        }
        $mb_pass = Format::encryptString($mb_pass);
        $new_pass = Format::encryptString($new_pass);

        //Log::debug($mb_pass);
        $chk = Db::selectCount($data_table, "where $pk = '$uid' and mb_pw = '$mb_pass'");
        if (!$chk) {
            $this->result['msg'] = '현재 비밀번호가 정확하지 않습니다.';
            return $this->result;
        }

        if ($mb_pass == $new_pass) {
            $this->result['msg'] = '신규 비밀번호는 현재 비밀번호와 다르게 입력해야 합니다.';
            return $this->result;
        }

        $chk_arr = $this->validateMemberPassword($_POST['new_pass']);
        if (!$chk_arr['flag']) {
            $result = array(
                'code' => 'failure',
                'msg' => $chk_arr['msg']
            );
            return $result;
        }

        $selection_time = '';

        $flag_selection = 'N';

        if ($_POST['selection_agree'] == 'on') {
            $selection_time = _NOW_DATETIME_;
            $flag_selection = 'Y';
        }

        $arr = array(
            'mb_pw' => $new_pass,
            'flag_auth' => 'Y',
            'flag_personal' => 'Y',
            'flag_selection' => $flag_selection,
            'policy_time' => $_POST['agree_time'],
            'privacy_time' => $_POST['agree_time'],
            'selection_time' => $selection_time
        );

        Db::updateByArray($data_table, $arr, "where $pk = '$uid' and mb_pw = '$mb_pass'");

        $this->result['code'] = 'update_ok';
        $this->result['uri'] = './process.html?mode=logout';
        $this->result['msg'] = '변경한 비밀번호로 다시 로그인해주세요.';

        return $this->result;
    }

    // 이몬 데이터 전송용 로그인 기록 yllee 200911
    public function insertLoginData($member, $result_visit)
    {
        //Log::debug($member);
        $arr = array(
            'lg_mode' => 'login',
            'lg_ip' => $member['mb_login_ip'],
            'lg_device' => $result_visit['vs_device'],
            'lg_browser' => $result_visit['vs_browser'],
            'mb_level' => $member['mb_level'],
            'reg_name' => $member['mb_name'],
            'reg_id' => $member['mb_id'],
            'reg_time' => _NOW_DATETIME_
        );
        if ($member['mb_id'] && $member['mb_login_ip']) {
            Db::insertByArray('tbl_login', $arr);
            // 이몬 API 기반 데이터 수집 시스템: 회원 로그인 데이터 전송 yllee 220426
            $result = Api::userLoginHist($arr);
            //Log::debug($result);
        }
    }

    // 마지막 로그인 기록 확인 yllee 210316
    public function checkTodayLastLoginIp($mb_id)
    {
        $date = _NOW_DATE_;
        $data_table = 'tbl_login';
        $db_where = "WHERE reg_id = '$mb_id' AND reg_time LIKE '{$date}%'";
        $db_order = "ORDER By lg_id DESC";
        $data = Db::selectOnce($data_table, 'lg_ip', $db_where, $db_order);
        $login_ip = $data['lg_ip'];
        //Log::debug($db_where);
        //Log::debug($login_ip);

        return $login_ip;
    }

    public function selectCompanyList()
    {
        return Db::select('tbl_company', '*', "WHERE cp_id != '98765'", "ORDER BY cp_name", '');
    }

    // 휴대폰 번호 파라미터 추가 minju 221012
    public function checkJoin($mb_name, $mb_birth, $mb_tel)
    {

        $mb_data = Db::selectOnce('tbl_user', 'mb_id,cp_id,flag_tomocard', "WHERE mb_name = '$mb_name' AND mb_birthday = '$mb_birth' AND mb_tel = '$mb_tel'", '');

        if ($mb_data['cp_id'] != '12345' && $mb_data['flag_tomocard'] == 'Y') {
            $mb_data = '';
        }

        return $mb_data;
    }

    public function selectPaymentList()
    {
        global $member;
        $mb_id = $member['mb_id'];

        $list = Db::select('tbl_application', '*', "WHERE us_id = '$mb_id' AND ap_state = 'E' AND payment_state = 'E'", '', '');

        for ($i = 0; $i < count($list); $i++) {
            $cs_id = $list[$i]['cs_numbers'];
            $list[$i]['cs_data'] = Db::selectOnce('tbl_course', '*', "WHERE cs_id ='$cs_id'", '');
            $list[$i]['no'] = $i + 1;
            $list[$i]['reg_date'] = substr($list[$i]['reg_time'], 0, 10);

            if ($list[$i]['ap_state'] == 'E') {
                $bt_data = Db::selectOnce('tbl_batch_user a JOIN tbl_batch b ON a.bu_bt_code = b.bt_code', '*', "WHERE a.bu_mb_id = '$mb_id' AND b.bt_course = '$cs_id'", '');
                if ($bt_data) {
                    $list[$i]['bt_data'] = $bt_data;
                }
            }
        }

        return $list;
    }

    // 부계정 리스트
    public function selectSubMember($cp_id)
    {
        // count
        $cnt_total = $this->countTotalSubMember($cp_id);
        $this->set('cnt_total', $cnt_total);

        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');
        $db_where = "WHERE cp_id='$cp_id' AND mb_level=2";
        $this->makeDbWhereSubMember();
        $db_where .= $this->get('db_where');
        //echo $query_string;exit;

        $db_having = $this->get('db_having');
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;

        $list_mode = $this->get('list_mode');
        if ($list_mode == 'excel') {
            $db_limit = '';
        }
        // 정렬 기본값: cp_id 숫자형으로 정렬 yllee 210324
        $sch_order = $_POST['sch_order'] ?: $_GET['sch_order'];
        if ($sch_order) {
            $this->set('order_column', $sch_order);
            if ($sch_order == 'cp_name' || $sch_order == 'partner_name') {
                $this->set('order_direct', 'ASC');
            }
        } else {
            $this->set('order_column', 'reg_time');
        }
        $db_order = $this->makeDbOrder();
        //echo $db_where;exit;
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);

        return $this->convertList($list);
    }

    public function makeDbWhereSubMember()
    {
        $db_where = $this->get('db_where');
        $sch_like = $_GET['sch_like'];
        $sch_text = $_GET['sch_text'];

        if ($sch_like == 'all') {
            $db_where .= " AND (mb_name LIKE '%{$sch_text}%' OR mb_id LIKE '%{$sch_text}%'";
            $db_where .= " OR mb_hp LIKE '%{$sch_text}%' OR mb_depart LIKE '%{$sch_text}%')";
        } else {
            if ($sch_text) {
                $db_where .= " AND $sch_like LIKE '%{$sch_text}%'";
            }
        }
        $this->set('db_where', $db_where);
    }

    public function countTotalSubMember($cp_id)
    {
        $this->initSelect();
        $select_table = $this->get('select_table');
        //$db_where = $this->makeDbWhere();
        $db_where = "WHERE cp_id='$cp_id' AND mb_level=2";
        $this->makeDbWhereSubMember();
        $db_where .= $this->get('db_where');
        $group_column = $this->get('group_column');
        $db_having = $this->makeDbHaving();
        //echo $select_table;
        //echo $db_where;exit;
        $cnt_total = Db::selectCount($select_table, $db_where, $group_column, $db_having);

        return $cnt_total;
    }
}
