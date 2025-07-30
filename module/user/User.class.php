<?php
/**
 * 사용자 모듈 클래스
 * @file    User.class.php
 * @author  Alpha-Edu
 * @package user
 */

namespace sFramework;

use function array_merge;
use function count;
use function implode;
use function is_array;
use function preg_match;
use function print_r;
use function str_replace;
use function strlen;
use function strpos;
use function strtolower;
use const _NOW_DATETIME_;

class User extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_user';
    public static $pk = 'mb_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'member');
        $this->set('module_name', '멤버십');

        $this->set('order_column', 'reg_time DESC, user_id');
        // 검색
        $this->set('search_columns', 'mb_level,flag_use,flag_notice');
        $this->set('search_like_arr', array(
            'all' => '통합검색',
            'mb_name' => '이름',
            'mb_id' => '아이디',
            'mb_hp' => '연락처',
            'cp_name' => '기업명',
        ));
        $this->set('search_date_arr', array(
            'reg_time' => '등록일',
            'mb_login_time' => '최근로그인'
        ));
        $this->set('order_arr', array(
            'reg_time' => '가입일',
            'mb_login_time' => '최근로그인'
        ));
        // code
        $this->set('mb_level_arr', array(
            '1' => '주계정',
            '2' => '부계정',
            '6' => '파트너',
            '3' => '기타',
        ));
        $this->set('flag_use_arr', array(
            'use' => '사용',
            'stop' => '정지',
            'pause' => '일시정지',
            'out' => '탈퇴'
        ));
        $this->set('sm_state_arr', array(
            '0000' => '전송성공',
            '0001' => '접속에러',
            '0002' => '인증에러',
            '0003' => '잔여콜수 없음',
            '0004' => '메시지 형식에러',
            '0005' => '콜백번호 에러',
            '0006' => '수신번호 개수 에러',
            '0007' => '예약시간 에러',
            '0008' => '잔여콜수 부족',
            '0009' => '전송실패',
            '0010' => '이미지없음',
            '0011' => '이미지전송오류',
            '0012' => '메시지 길이오류',
            '0030' => '발신번호 사전등록 미등록',
            '0033' => '발신번호 형식에러',
            '0080' => '발송제한',
            '6666' => '일시차단',
            '9999' => '요금미납',
        ));
        $this->set('flag_test_arr', array(
            'Y' => '사용',
            'N' => '미사용'
        ));
        $this->set('flag_yn_arr', array(
            'Y' => '유',
            'N' => '무'
        ));
        $this->set('flag_auth_arr', array(
            'Y' => '인증',
            'N' => '미인증',
            'A' => '수기인증'
        ));
        $this->set('flag_book_arr', array(
            'Y' => '사용',
            'N' => '미사용',
        ));
        $this->set('flag_notice_arr', array(
            'Y' => '동의',
            'N' => '미동의',
        ));
        $stu_page = $this->getRequestParameter('stu_page');
        if (!$stu_page) {
            $stu_page = 1;
        }
        $this->set('stu_page', $stu_page);
    }

    protected function initInsert()
    {
        parent::initInsert();
        $insert_columns = 'mb_id,mb_pw,mb_level,mb_name,mb_email,mb_hp';
        $insert_columns .= ',cp_id,cp_name,flag_use,flag_sms';
        $insert_columns .= ',mb_memo,flag_notice,mb_depart';
        $this->set('insert_columns', $insert_columns);
        $this->set('required_arr', array(
            'mb_name' => '이름',
            'mb_id' => '아이디',
            'mb_pw' => '비밀번호',
            'mb_hp' => '휴대폰번호',
            'mb_level' => '권한구분',
            'flag_use' => '사용여부'
        ));
        //'cp_id' => '구분'

        if ($_POST['book'] == 'book') {
            $this->set('return_uri', '/webadmin/book/user_list.html');
        }
        if ($_POST['live'] == 'book') {
            $this->set('return_uri', '/webadmin/live/user_list.html');
        }
    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);

        if ($_POST['mode'] == 'excel') {
            if (!$arr['mb_pw']) {
                $arr['mb_pw'] = 1234;
            }
        }
        // 패스워드
        $arr['mb_pw'] = Format::encryptString($arr['mb_pw']);
        $arr['mb_pw_time'] = _NOW_DATETIME_;
        // 전화번호 공백, 하이픈 제거
        $arr['mb_hp'] = str_replace('-', '.', trim($arr['mb_hp']));
        // 이름 공백 제거
        $arr['mb_name'] = trim($arr['mb_name']);

        return $arr;
    }

    protected function postInsert($arr)
    {
        // uid 구하기
        global $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $data = Db::selectOnce($data_table, $pk, "WHERE reg_id = '" . $member['mb_id'] . "'", "ORDER BY reg_time DESC");
        $uid = $data[$pk];
        $arr[$pk] = $uid;

        $mb_level = $arr['mb_level'];

        if ($mb_level == 4) {
            $staff_name = $arr['mb_name'];
            $staff_position = $arr['mb_position'];
            $staff_mail = $arr['mb_email'];
            $cp_id = $arr['cp_id'];

            $updateResult = Db::update('tbl_company', "staff_name='" . $staff_name . "', staff_position='" . $staff_position . "', staff_email='" . $staff_mail . "'", "WHERE cp_id = '" . $cp_id . "'");
            if (!$updateResult) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '등록 과정에서 장애가 발생하였습니다.'
                );
                return $result;
            }
        }
        if (!$uid) {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
            return $result;
        }
        // 에디터
        if ($this->get('flag_use_editor')) {
            $this->moveEditorImages($arr);
        }
        $result = array(
            'code' => 'success',
            'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid,
            'msg' => $this->get('success_msg'),
            $pk => $uid
        );
        return $result;
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $update_columns = 'mb_id,mb_level,mb_name,mb_birthday,mb_resident_num,mb_email,mb_hp,mb_direct_line';
        $update_columns .= ',cp_id,cp_name,mb_zip,mb_addr,mb_addr2,mb_stu_type,mb_position,mb_irregular_type';
        $update_columns .= ',mb_cost_business_num,flag_tomocard,flag_use,flag_auth,flag_test,flag_sms';
        $update_columns .= ',mb_hp,mb_depart';
        $this->set('update_columns', $update_columns);
        $this->set('required_arr', array(
            'mb_name' => '이름',
            'mb_id' => '아이디',
            'mb_hp' => '휴대폰번호',
            'mb_level' => '권한구분',
            'flag_use' => '사용여부'
        ));
    }

    protected function convertUpdate($arr)
    {
        $arr = parent::convertUpdate($arr);

        $mb_level = $arr['mb_level'];

        $cp_id_arr = $_POST['cp_id_arr'];
        $cp_name_arr = $_POST['cp_name_arr'];

        $cp_id = '';
        $cp_name = '';

        if (is_array($cp_id_arr)) {
            $cp_id = implode('|', $cp_id_arr);
        }
        if (is_array($cp_name_arr)) {
            $cp_name = implode('|', $cp_name_arr);
        }
        $arr['cp_id'] = $cp_id;
        $arr['cp_name'] = $cp_name;

        if ($mb_level == 4) {
            $staff_name = $arr['mb_name'];
            $staff_position = $arr['mb_position'];
            $staff_mail = $arr['mb_email'];

            for ($i = 0; $i < count($cp_id_arr); $i++) {
                $updateResult = Db::update('tbl_company', "staff_name='" . $staff_name . "', staff_position='" . $staff_position . "', staff_email='" . $staff_mail . "'", "WHERE cp_id = '" . $cp_id_arr[$i] . "'");
                if (!$updateResult) {
                    $result = array(
                        'code' => 'failure',
                        'msg' => '등록 과정에서 장애가 발생하였습니다.'
                    );
                    return $result;
                }
            }
        }
        // 패스워드
        $mb_pw = $_POST['mb_pw'];
        if ($mb_pw) {
            $arr['mb_pw'] = Format::encryptString($mb_pw);
            $arr['mb_pw_time'] = _NOW_DATETIME_;
        }
        $resident_num = $arr['mb_resident_num'];

        $arr['mb_resident_num'] = Format::encrypt($resident_num);
        $arr['emon_res_no'] = $resident_num;

        $arr['mb_tel'] = Html::beautifyTel($arr['mb_tel']);

        // 이름 공백 제거
        $arr['mb_name'] = trim($arr['mb_name']);

        //Log::debug($arr);
        return $arr;
    }

    public function validateMemberId($mb_id)
    {
        $result = array(
            'flag' => 1,
            'msg' => '사용할 수 있는 아이디입니다.'
        );
        if (!$mb_id) {
            $result['flag'] = 0;
            $result['msg'] = '아이디를 입력하세요.';
        } elseif (preg_match("/[^0-9a-zA-Z_-]+/i", $mb_id)) {
            $result['flag'] = 0;
            $result['msg'] = '영문자, 숫자, _, - 만 입력하세요.';
        } elseif (strlen($mb_id) < 4) {
            $result['flag'] = 0;
            $result['msg'] = '최소 4글자 이상 입력하세요.';
        } elseif (strlen($mb_id) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '아이디는 최대 20글자 이하 입력하세요.';
        } elseif (strpos('admin,root,sysop,manager', strtolower($mb_id)) > -1) {
            $result['flag'] = 0;
            $result['msg'] = '사용 불가능한 아이디입니다.';
        } else {
            $id_chk = Db::selectCount($this->get('data_table'), "where mb_id = '$mb_id'");
            if ($id_chk > 0) {
                $result['flag'] = 0;
                $result['msg'] = '이미 사용중인 아이디입니다.';
            }
        }
        return $result;
    }

    public function insertData()
    {
        // 권한 체크
        if (!$this->checkWriteAuth()) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }
        $this->initInsert();
        $arr = $this->getParameters($this->get('insert_columns'), 'post');
        $arr = $this->convertInsert($arr);

        if ($_POST['mode'] != 'excel') {
            $result = $this->validateValues($arr);
            if ($result['code'] != 'success') {
                return $result;
            }
        }
        $mb_id = $arr['mb_id'];

        if (Db::selectOnce('tbl_user', '*', "WHERE mb_id = '$mb_id'", '')) {
            $result = array(
                'code' => 'failure',
                'msg' => '중복된 아이디입니다.'
            );
            return $result;
        }
        $mb_level = $arr['mb_level'];
        $cp_id = $arr['cp_id'];

        if ($mb_level == 4) {
            if (!$cp_id) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '기업관리자의 경우 기업을 선택해야합니다.'
                );
                return $result;
            }
            $mb_check = Db::selectOnce("tbl_user", "*", "WHERE mb_level = '$mb_level' AND cp_id = '$cp_id'", "");
            if ($mb_check) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '해당 기업의 기업관리자가 이미 존재합니다.'
                );
                return $result;
            }
        }
        $data_table = $this->get('data_table');
        //print_r($_POST);print_r($arr);exit;
        if (Db::insertByArray($data_table, $arr)) {
            $result = $this->postInsert($arr);
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
        }
        return $result;
    }

    public function updateData()
    {
        $uid = $this->get('uid');
        // 권한 체크
        if (!$uid || !$this->checkUpdateAuth($uid)) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }
        $this->initUpdate();
        $arr = $this->getParameters($this->get('update_columns'), 'post');
        //Log::debug($this->get('update_columns'));
        //Log::debug($arr);
        //print_r($_POST);
        //print_r($arr);
        $mb_level = $arr['mb_level'];
        $cp_id = $arr['cp_id'];

        if ($mb_level == 4) {
            // 같은 기업의 아이디가 다른 기업 담당자 있는지 검사 yllee 230601
            $db_where = "WHERE mb_level = '$mb_level' AND cp_id = '$cp_id' AND mb_id = '$uid'";
            $mb_check = Db::selectOnce("tbl_user", "*", $db_where, "");
            if ($mb_check) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '해당 기업의 기업담당자가 이미 존재합니다.'
                );
                return $result;
            }
            $mb_email = $arr['mb_email'];
            if ($_POST['mb_email_old'] != $mb_email) {
                Db::update('tbl_company', "staff_email = '$mb_email'", "WHERE cp_id='$cp_id'");
            }
            // 담당자 이름 변경 시 기업리스트 교육담당자 이름 자동 변경 기능 minju 230626
            $mb_name = $arr['mb_name'];
            if ($_POST['mb_name_old'] != $mb_name) {
                Db::update('tbl_company', "staff_name = '$mb_name'", "WHERE cp_id='$cp_id'");
            }
        }
        $arr = $this->convertUpdate($arr);

        $result = $this->validateValues($arr);
        if ($result['code'] != 'success') {
            return $result;
        }
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        unset($arr[$pk]);
        //print_r($arr);
        //exit;
        if (Db::updateByArray($data_table, $arr, "WHERE $pk = '$uid'")) {
            $result = $this->postUpdate($arr);
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '수정 과정에서 장애가 발생하였습니다.'
            );
        }

        return $result;
    }

    public function updateUserData(): array
    {
        global $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $uid = $member[$pk];
        $mb_id = $_POST[$pk];

        parent::initUpdate();
        $update_columns = 'cp_name,,mb_zip,mb_email,mb_hp,mb_depart,mb_name,mb_level,flag_notice,upt_id,upt_time';

        $arr = $this->getParameters($update_columns, 'post');
        $arr['upt_id'] = $uid;
        $arr['upt_time'] = _NOW_DATETIME_;
        //echo $data_table;print_r($arr);echo $pk;echo $mb_id;exit;
        if (Db::updateByArray($data_table, $arr, "WHERE $pk = '$mb_id'")) {
            $result = array(
                'code' => 'success',
                'msg' => $this->get('success_msg')
            );
            //print_r($arr);exit;
            $mb_level = $arr['mb_level'];
            if ($mb_level == '1') {
                $cp_arr = array(
                    'cp_name' => $_POST['cp_name'],
                    'cp_count' => $_POST['cp_count'],
                    'cp_date' => $_POST['cp_date'],
                    'cp_size' => $_POST['cp_size'],
                    'cp_revenue' => $_POST['cp_revenue'],
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
                //print_r($cp_arr);echo $cp_id;exit;
                Db::updateByArray("tbl_company", $cp_arr, "WHERE cp_id='$cp_id'");
            }
            $it_area = $_POST['it_area'];
            $it_type = $_POST['it_type'];
            $it_info = $_POST['it_info'];
            $it_area_txt = '';
            $it_type_txt = '';
            $it_info_txt = '';
            if (is_array($it_area)) {
                $it_area_txt = implode('|', $_POST['it_area']);
            }
            if (is_array($it_type)) {
                $it_type_txt = implode('|', $_POST['it_type']);
            }
            if (is_array($it_info)) {
                $it_info_txt = implode('|', $_POST['it_info']);
            }
            $it_arr = array(
                'it_area' => $it_area_txt,
                'it_type' => $it_type_txt,
                'it_info' => $it_info_txt,
                'upt_id' => $uid,
                'upt_time' => _NOW_DATETIME_
            );
            //print_r($it_arr);echo $mb_id;exit;
            Db::updateByArray("tbl_interest", $it_arr, "WHERE mb_id='$mb_id'");
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

    protected function postUpdate($arr)
    {
        $pk = $this->get('pk');
        $uid = $arr[$pk];
        if (!$uid) {
            $uid = $this->get('uid');
            $arr[$pk] = $uid;
        }
        // 기존 파일 삭제
        $del_file_arr = $_POST['del_file'];
        if (is_array($del_file_arr)) {
            for ($i = 0; $i < count($del_file_arr); $i++) {
                $this->deleteFile($del_file_arr[$i]);
            }
        }
        // 첨부파일
        if ($this->get('max_file')) {
            $this->uploadFiles($uid);
        }
        // 에디터
        if ($this->get('flag_use_editor')) {
            $this->moveEditorImages($arr);
        }
        $page = $this->get('page');
        $query_string = $this->get('query_string');

        if ($query_string) {
            $query_string = '&' . $query_string;
        }
        $result = array(
            'code' => 'success',
            'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&page=' . $page . $query_string,
            'msg' => $this->get('success_msg')
        );
        if ($_POST['book'] == 'book') {
            $result = array(
                'code' => 'success',
                'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&page=' . $page . $query_string . '&book=book',
                'msg' => $this->get('success_msg')
            );
        }
        if ($_POST['live'] == 'live') {
            $result = array(
                'code' => 'success',
                'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&page=' . $page . $query_string . '&live=live',
                'msg' => $this->get('success_msg')
            );
        }
        $mb_id = $arr['mb_id'];
        Db::update('tbl_user', "emon_res_no = ''", "WHERE mb_id = '$mb_id'");
        $mb_name = $arr['mb_name'];

        // 기업정보 수정 yllee 250709
        $cp_id = $_POST['cp_id'];
        $cp_count = $_POST['cp_count'];
        $cp_date = $_POST['cp_date'];
        $cp_size = $_POST['cp_size'];
        $cp_revenue = $_POST['cp_revenue'];
        $flag_venture = $_POST['flag_venture'];
        $flag_research = $_POST['flag_research'];
        $flag_product = $_POST['flag_research'];
        $cp_zip = $_POST['cp_zip'];
        $cp_address = $_POST['cp_address'];
        $cp_address2 = $_POST['cp_address2'];

        $cp_arr = array();
        if ($cp_count) {
            Db::updateByArray('tbl_company', $cp_arr, "WHERE cp_id = '$cp_id'");
        }
        return $result;
    }

    public function deleteData()
    {
        $this->initDelete();
        $list_uid_arr = $_POST['list_uid'];

        $uid = $this->get('uid');
        if (!$list_uid_arr && $uid) {
            $list_uid_arr = array(
                '0' => $uid
            );
        }
        for ($i = 0; $i < count($list_uid_arr); $i++) {
            $uid = $list_uid_arr[$i];
            // 권한 체크
            if ($uid && !$this->checkUpdateAuth($uid)) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '권한이 없습니다.'
                );
                return $result;
            }
            $result = $this->deleteRows($uid);
            if ($result['code'] != 'success') {
                return $result;
            }
        }
        return $this->postDelete();
    }

    protected function deleteRows($uid)
    {
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        if (!Db::delete($data_table, "WHERE $pk = '$uid'")) {
            $result = array(
                'code' => 'failure',
                'msg' => '삭제 과정에 문제가 발생하였습니다.'
            );
            return $result;
        }
        $result = array(
            'code' => 'success'
        );
        return $result;
    }

    protected function postDelete()
    {
        $page = $this->get('page');
        $query_string = $this->get('query_string');
        if ($query_string) {
            $query_string = '&' . $query_string;
        }

        $result = array(
            'code' => 'success',
            'msg' => $this->get('success_msg'),
            'uri' => $this->get('return_uri') . '?page=' . $page . $query_string
        );

        return $result;
    }

    public function validateMemberPassword($mb_pw)
    {
        $result = array(
            'flag' => 1,
            'msg' => '사용할 수 있는 패스워드입니다.'
        );
        $num = preg_match('/[0-9]/u', $mb_pw);
        $eng = preg_match('/[a-z]/u', $mb_pw);
        $spe = preg_match("/[\!\@\#\$\%\^\&\*]/u", $mb_pw);
        $pw_start_length = 8;

        if (!$mb_pw) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드를 입력하세요.';
        } elseif ($num == 0 || $eng == 0 || $spe == 0) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 영대/소문자, 숫자, 특수문자 조합이어야 합니다.';
        } elseif (strlen($mb_pw) < $pw_start_length) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최소 ' . $pw_start_length . '글자 이상 입력하세요.';
        } elseif (strlen($mb_pw) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최대 20글자 이하로 입력하세요.';
        } elseif (preg_match("/(\w)\\1\\1\\1/", $mb_pw)) {
            $result['flag'] = 0;
            $result['msg'] = '같은 영문자, 숫자를 4번 이상 반복할 수 없습니다.';
        }
        return $result;
    }

    public function selectPartnerList()
    {
        $db_where = "WHERE mb_level = '6'";

        return Db::select('tbl_user', '*', $db_where, 'ORDER BY mb_name ASC', '');
    }

    public function selectPartnerListAjax()
    {
        $db_where = "WHERE mb_level = '6'";
        $sch_text = $_GET['sch_text'];
        $result['code'] = 'success';

        if ($sch_text) {
            $db_where .= " AND mb_name LIKE '%$sch_text%'";
        }

        $result['data'] = Db::select('tbl_user', '*', $db_where, '', '');

        return $result;
    }

    public function selectTutorList()
    {
        $db_where = "WHERE mb_level = '5'";

        return Db::select('tbl_user', '*', $db_where, 'ORDER BY mb_name', '');
    }

    public function selectTutorListAjax()
    {
        $db_where = "WHERE mb_level = '5'";

        $sch_text = $_GET['sch_text'];

        $result['code'] = 'success';

        if ($sch_text) {
            $db_where .= " AND mb_name LIKE '%$sch_text%'";
        }

        $result['data'] = Db::select('tbl_user', '*', $db_where, '', '');

        return $result;
    }

    public function selectCompanyList()
    {
        $db_where = "WHERE mb_level = '4'";

        return Db::select('tbl_user', '*', $db_where, '', '');
    }

    public function selectCompanyListAjax()
    {
        $db_where = "WHERE mb_level = '4'";

        $sch_text = $_GET['sch_text'];

        $result['code'] = 'success';

        if ($sch_text) {
            $db_where .= " AND mb_name LIKE '%$sch_text%'";
        }

        $result['data'] = Db::select('tbl_user', '*', $db_where, '', '');

        return $result;
    }

    public function searchPartnerId($mb_id)
    {
        return Db::selectOnce('tbl_user', '*', "WHERE mb_id = '" . $mb_id . "' AND mb_level = '6'", '');
    }

    public function searchMemberId($mb_id)
    {
        return Db::selectOnce('tbl_user', '*', "WHERE mb_id = '" . $mb_id . "'", '');
    }

    public function selectListMbId($mb_id)
    {
        //$select_table = 'tbl_progress';
        $select_table = 'tbl_batch_user a JOIN tbl_batch b ON a.bu_bt_id = b.bt_id AND a.bu_cs_code = b.cs_code';
        $select_columns = '*';
        //$db_where = "WHERE us_id = '$mb_id'";
        // CRM > 수강이력에서 북러닝 제외 yllee 240829
        $db_where = "WHERE a.bu_mb_id='$mb_id' AND b.bt_type!='book'";
        $db_having = $this->get('db_having');
        //$db_order = 'ORDER BY a.reg_time DESC';
        $db_order = 'ORDER BY b.bt_s_date DESC';
        /*
        global $member;
        if($member['mb_id'] == 'silva') {
            $select_table = "(SELECT bt_s_date,bt_e_date,rate_progress,cs_id,cs_code,bt_code,bt_type,bt_id,rep_score,flag_complete from tbl_progress WHERE us_id='$mb_id' AND bt_e_date >= '$today' UNION SELECT bt_s_date,bt_e_date,rate_progress,cs_id,cs_code,bt_code,bt_type,bt_id,rep_score,flag_complete from tbl_book_progress WHERE us_id='$mb_id' AND bt_e_date >= '$today') A";
            $db_order = "ORDER BY A.bt_s_date DESC";
            $db_where = "";
        }
        */
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, '');
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $list = Db::select($select_table, "*", $db_where, $db_having . ' ' . $db_order, $db_limit);

        for ($i = 0; $i < count($list); $i++) {
            $bt_code = $list[$i]['bt_code'];
            $cs_code = $list[$i]['cs_code'];
            $exam_year = '';
            /*
             * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
            $bt_end_date = $list[$i]['bt_e_date'];
            $bt_end_date_arr = explode('-', $bt_end_date);
            $bt_end_year = $bt_end_date_arr[0];
            $now_year = date('Y');
            if ($now_year > $bt_end_year) {
                $exam_year = '_' . $bt_end_year;
            }
            */
            $exam_mid_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_midterm' AND er_cs_code='$cs_code' AND flag_delete IS NULL";
            $exam_fin_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_final' AND er_cs_code='$cs_code' AND flag_delete IS NULL";
            $report_where = "WHERE rr_mb_id = '$mb_id' AND rr_bt_code='$bt_code' AND rr_cs_code='$cs_code' AND rr_submit_time IS NOT NULL";
            $exam_mid_data = Db::selectOnce('tbl_exam_result' . $exam_year, '*', $exam_mid_where, '');
            // 마지막 시험 데이터 기준으로 정렬 yllee 240415
            $exam_fin_order = "ORDER BY er_id DESC";
            $exam_fin_data = Db::selectOnce('tbl_exam_result' . $exam_year, '*', $exam_fin_where, $exam_fin_order);
            $report_data = Db::selectOnce('tbl_report_result', '*', $report_where, '');
            $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_code = '$cs_code'", '');

            //Log::debug("WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_final' AND er_cs_code='$cs_code' AND flag_delete IS NULL");
            //Log::debug($exam_fin_data);

            $list[$i]['mid_data'] = $exam_mid_data;
            $list[$i]['fin_data'] = $exam_fin_data;
            $list[$i]['report_data'] = $report_data;
            $list[$i]['cs_data'] = $cs_data;
            // 진도율
            $pr_where = "WHERE bt_code = '$bt_code' AND cs_code = '$cs_code' AND us_id = '$mb_id'";
            $pr_data = Db::selectOnce('tbl_progress', '*', $pr_where, '');
            if (!$pr_data) {
                $pr_data = Db::selectOnceLxn('tbl_progress', '*', $pr_where, '');
            }
            $list[$i]['rate_progress'] = $pr_data['rate_progress'];
            $list[$i]['pr_id'] = $pr_data['pr_id'];
        }
        return $this->convertList($list);
    }

    public function getMemberList($mb_level)
    {
        $this->initSelect();
        $select_table = "tbl_user";
        $select_columns = $this->get('select_columns');

        $db_where = "where mb_level = '" . $mb_level . "'";

        $result['code'] = 'success';

        $data = Db::select($select_table, $select_columns, $db_where, '', '');

        $result['data'] = $data;

        // 권한 체크
        if (!$this->checkViewAuth($data)) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
        }

        return $result;
    }

    public static function makePagination($arr, $query_string = '', $a_arr_class = '')
    {
        $result = '';
        for ($i = 0; $i < count($arr); $i++) {
            $result .= '<li';
            if ($arr[$i]['class']) {
                $result .= ' class="' . $arr[$i]['class'] . '"';
            }
            $result .= '><a href="?page=' . $arr[$i]['page'] . $query_string;
            $result .= '" class="' . $a_arr_class . '" title="' . $arr[$i]['title'] . ' 페이지">' . $arr[$i]['title'];
            $result .= '</a></li>' . "\n";
        }
        return $result;
    }

    protected function makeDbWhere()
    {
        $db_where = $this->getDefaultWhere();

        $search_cp_name = $_GET['search_cp_name'];

        if ($search_cp_name) {
            $db_where .= " AND cp_name LIKE '%$search_cp_name%'";
        }

        $this->set('db_where', $db_where);

        return $db_where;
    }

    public function resetPW()
    {
        $mb_id_arr = $_GET['mb_id_arr'];

        $result['code'] = 'success';

        for ($i = 0; $i < count($mb_id_arr); $i++) {
            $arr = array(
                'mb_pw' => Format::encryptString(1234)
            );

            Db::updateByArray('tbl_user', $arr, "WHERE mb_id = '$mb_id_arr[$i]'");
        }

        return $result;
    }

    public function updateAuth()
    {
        $mb_id_arr = $_GET['mb_id_arr'];
        $auth = $_GET['auth'];

        $result['code'] = 'success';

        for ($i = 0; $i < count($mb_id_arr); $i++) {
            $arr = array(
                'flag_auth' => $auth
            );

            Db::updateByArray('tbl_user', $arr, "WHERE mb_id = '$mb_id_arr[$i]'");
        }

        return $result;
    }

    public function progressOc()
    {
        $result['code'] = 'success';

        $mb_id = $_GET['mb_id'];
        $cs_id = $_GET['cs_id'];
        $bt_code = $_GET['bt_code'];

        $cs_data = Db::selectOnce('tbl_course', 'cs_code', "WHERE cs_id = '$cs_id'", '');
        $oc_data = Db::select('tbl_occasion', '*', "WHERE oc_uid = '$cs_id'", 'ORDER BY oc_num', '');
        //$bt_data = Db::selectOnce('tbl_batch', 'bt_e_date', "WHERE bt_code = '$bt_code'", '');

        $cs_code = $cs_data['cs_code'];
        $target_table = 'tbl_time';
        /*
         * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
        $bt_e_date = $bt_data['bt_e_date'];
        $bt_e_year = substr($bt_e_date, 0, 4);
        //$timestamp = strtotime("-14 days");
        //$end_date = date('Y-m-d', $timestamp);
        // 2021년 데이터까지 tbl_time_2021 형식의 테이블로 이동 시킴 yllee 220504
        $end_date = '2021-12-31';
        //Log::debug("$bt_e_date <= $end_date");
        if ($bt_e_date <= $end_date) {
            $target_table .= '_' . $bt_e_year;
        }
        */
        for ($i = 0; $i < count($oc_data); $i++) {
            $oc_num = $oc_data[$i]['oc_num'];
            $where_progress = "WHERE tm_bt_code = '$bt_code' AND tm_cs_code = '$cs_code' AND tm_mb_id = '$mb_id' AND tm_oc_num='$oc_num'";
            $tm_data = Db::selectOnce($target_table, '*', $where_progress, '');
            if (!$tm_data) {
                $tm_data = Db::selectOnceLxn($target_table, '*', $where_progress, '');
            }
            $pg_list = Db::select('tbl_page', 'SUM(pg_time) as sum', "WHERE cs_id='$cs_id' AND oc_num='$oc_num'", '', '');
            $oc_data[$i]['oc_time'] = $pg_list[0]['sum'];
            $page_info = unserialize($tm_data['tm_record']);

            $studied_page_total = 0;
            $page_cnt = 1;
            $first_time = '';
            $last_time = '';

            if (!empty($page_info)) {
                foreach ($page_info as $page) {
                    if ($page_cnt == 1) {
                        $first_time = $page['date'];
                    }
                    $page_cnt++;
                    if ($page['check'] == '1') {
                        $studied_page_total++;
                        $last_time = $page['date'];
                    }
                }
            }
            $tm_data['first_time'] = $first_time;
            $tm_data['last_time'] = $last_time;

            if ($tm_data['tm_pg_num'] != $studied_page_total) {
                $tm_data['tm_pg_num'] = $studied_page_total;
            }
            $oc_data[$i]['tm_data'] = $tm_data;
        }
        // 진도율 추가 예정
        $result['data'] = $oc_data;

        return $result;
    }

    public function selectTime()
    {
        $mb_id = $this->get('mb_id');
        $oc_num = $this->get('oc_num');
        $bt_code = $this->get('bt_code');
        $cs_id = $this->get('cs_id');

        //$bt_data = Db::selectOnce('tbl_batch', 'bt_e_date', "WHERE bt_code = '$bt_code'", '');
        $cs_data = Db::selectOnce('tbl_course', 'cs_code', "WHERE cs_id = '$cs_id'", '');

        $cs_code = $cs_data['cs_code'];
        $target_table = 'tbl_time';
        /*
         * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
        $bt_e_date = $bt_data['bt_e_date'];
        $bt_e_year = substr($bt_e_date, 0, 4);
        //$timestamp = strtotime("-14 days");
        //$end_date = date('Y-m-d', $timestamp);
        // 2021년 데이터까지 tbl_time_2021 형식의 테이블로 이동 시킴 yllee 220504
        $end_date = '2021-12-31';

        if ($bt_e_date <= $end_date) {
            $target_table .= '_' . $bt_e_year;
        }
        */
        $db_where = "WHERE tm_cs_code = '$cs_code' AND tm_mb_id = '$mb_id' AND tm_oc_num = '$oc_num' AND tm_bt_code = '$bt_code'";
        $list = Db::selectOnce($target_table, '*', $db_where, "");
        // 데이터가 없을 경우 엘엑스 DB 호출 yllee 220503
        if (!$list) {
            $list = Db::selectOnceLxn($target_table, '*', $db_where, "");
        }
        $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_code = '$cs_code'", '');

        $list['cs_name'] = $cs_data['cs_name'];
        $list['pg_data'] = Db::select('tbl_page', '*', "WHERE cs_id = '$cs_id' AND oc_num = '$oc_num'", 'ORDER BY pg_num', '');

        return $list;
    }

    public function selectPage()
    {
        $cs_id = $this->get('cs_id');
        $oc_num = $this->get('oc_num');

        $pg_list = Db::select('tbl_page', '*', "WHERE cs_id = '$cs_id' AND oc_num = '$oc_num'", 'ORDER BY pg_num', '');

        $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_id = '$cs_id'", '');

        $list['cs_name'] = $cs_data['cs_name'];

        $list['pg_list'] = $pg_list;

        return $list;
    }

    public function selectListSms($us_id)
    {
        // 220916 분기로 문자 발송 내역 DB 테이블 분리함 yllee 220916
        $db_where = "WHERE us_id = '$us_id'";
        $sms_list_base = Db::select('tbl_sms', '*', $db_where, 'ORDER BY sm_id DESC', '');
        $sms_list_2022 = Db::select('tbl_sms_2022', '*', $db_where, 'ORDER BY sm_id DESC', '');

        $sms_list = array();
        if ($sms_list_base && $sms_list_2022) {
            $sms_list = array_merge($sms_list_base, $sms_list_2022);
        } elseif (!$sms_list_base) {
            $sms_list = $sms_list_2022;
        } elseif (!$sms_list_2022) {
            $sms_list = $sms_list_base;
        }
        return $sms_list;
    }

    public function sendSms()
    {
        $remote_phone = $_GET['remote_phone'];
        $remote_name = $_GET['remote_name'];
        $remote_msg = $_GET['remote_msg'];
        $mb_tel = Html::beautifyTel($remote_phone);

        $host = "www.munjasin.co.kr";
        $id = "bestonealpha";
        $pass = "bestone6508";

        $param = "remote_id=" . $id;
        $param .= "&remote_pass=" . $pass;
        $param .= "&remote_phone=" . $mb_tel;
        $param .= "&remote_name=" . $remote_name;
        $param .= "&remote_callback=0552556364";
        $param .= "&remote_msg=" . $remote_msg;
        //$param .= "&remote_subject=[알파에듀] 개강안내";
        $path = "/Remote/RemoteSms.html";

        if (mb_strwidth($remote_msg, 'UTF-8') > 90) {
            $path = "/Remote/RemoteMms.html";
        }
        $fp = @fsockopen($host, 80, $errno, $errstr, 30);
        $return = "";

        if (!$fp) {
            echo $errstr . "(" . $errno . ")";
        } else {
            fputs($fp, "POST " . $path . " HTTP/1.1\r\n");
            fputs($fp, "Host: " . $host . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($param) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $param . "\r\n\r\n");

            while (!feof($fp)) $return .= fgets($fp, 4096);
        }
        fclose($fp);

        $_temp_array = explode("\r\n\r\n", $return);
        $_temp_array2 = explode("\r\n", $_temp_array[1]);

        if (sizeof($_temp_array2) > 1) {
            $return_string = $_temp_array2[1];

        } else {
            $return_string = $_temp_array2[0];
        }
        $result['code'] = 'success';
        $result['data'] = $return_string;

        $return_string_arr = explode('|', $return_string);
        $mb_id_arr = explode('|', $_GET['mb_id']);

        for ($i = 0; $i < count($mb_id_arr); $i++) {
            $mb_id = $mb_id_arr[$i];
            global $member;

            if ($mb_id) {
                $insert_arr = array(
                    'us_id' => $mb_id,
                    'sm_content' => $remote_msg,
                    'sm_state' => $return_string_arr[0],
                    'reg_id' => $member['mb_id'],
                    'reg_time' => _NOW_DATETIME_
                );
                Db::insertByArray('tbl_sms', $insert_arr);
            }
        }
        return $result;
    }

    public function selectListIds($mb_ids)
    {
        $mb_id_arr = explode('|', $mb_ids);

        $db_mb_id = implode("','", $mb_id_arr);

        $db_where = "WHERE mb_id IN('$db_mb_id')";

        return Db::select('tbl_user', '*', $db_where, '', '');
    }
}
