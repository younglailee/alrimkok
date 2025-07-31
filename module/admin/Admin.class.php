<?php
/**
 * 운영자 모듈 클래스
 * @file    Admin.class.php
 * @author  Alpha-Edu
 * @package admin
 */
namespace sFramework;

class Admin extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_admin';
    public static $pk = 'mb_id';

    /**
     * 모듈 환경설정
     */
    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'admin');
        $this->set('module_name', '운영자');

        // 검색
        $this->set('search_columns', 'mb_level,mb_no_login');
        $this->set('search_like_arr', array(
            'mb_id'     => '운영자ID',
            'mb_name'   => '이름',
            'mb_email'  => '이메일',
            'mb_hp'     => '휴대폰'
        ));

        $this->set('search_date_arr', array(
            'reg_time'      => '등록일',
            'mb_login_time'    => '최근로그인'
        ));

        // 정렬
        $order_arr = $this->get('order_arr');
        $order_arr['login_time'] = '최근로그인';
        $this->set('order_arr', $order_arr);

        // 에디터
        $this->set('flag_use_editor', true);
        $this->set('editor_columns', 'mb_memo');

        $cnt_rows_arr = $this->get('cnt_rows_arr');
        $cnt_rows_arr[1] = '1개씩';
        $this->set('cnt_rows_arr', $cnt_rows_arr);

        // 파일
        $this->set('max_file', 3);

        // 썸네일
        $this->set('flag_use_thumb', true);
        $this->set('thumb_width', 160);
        $this->set('thumb_height', 90);

        // code
        $this->set('mb_level_arr', array(
            '6' => '파트너',
            '7' => '등록관리자',
            '8' => '부서관리자',
            '9' => '최고관리자',
            '10' => '개발관리자'
        ));

        $this->set('mb_no_login_arr', array(
            'N' => '로그인 허용',
            'Y' => '로그인 금지'
        ));

        $this->set('mb_auth_code_arr', array(
            'members'   => '관리자계정',
            'manage'  => '운영관리',
            'user'  => '통합회원관리',
            'stats' => '통계관리',
            'contents'    => '콘텐츠관리',
            'board'    => '게시판관리',
            'setting' => '환경설정'
        ));

        // code to text
        $this->set('code_columns', 'mb_level,mb_no_login_arr');
    }

    protected function initInsert()
    {
        parent::initInsert();

        $this->set('insert_columns', 'mb_id,mb_pw,mb_level,mb_name,mb_email,mb_hp,mb_memo,mb_no_login,mb_auth_ips');
        $this->set('required_arr', array(
            'mb_id' => '아이디',
            'mb_pw' => '패스워드',
            'mb_name'   => '이름'
        ));
    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);

        // 패스워드
        $arr['mb_pw'] = Format::encryptString($arr['mb_pw']);
        $arr['mb_pw_time'] = _NOW_DATETIME_;

        // 권한
        $mb_auth_code_arr = $this->getRequestParameter('mb_auth_code');
        $mb_auth_codes = '';
        if (is_array($mb_auth_code_arr)) {
            $mb_auth_codes = implode('|', $mb_auth_code_arr);
        }
        $arr['mb_auth_codes'] = $mb_auth_codes;

        return $arr;
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $this->set('update_columns', 'mb_id,mb_level,mb_name,mb_email,mb_hp,mb_memo,mb_no_login,mb_auth_ips');
        $this->set('required_arr', array(
            'mb_id' => '아이디',
            'mb_name'   => '이름'
        ));
    }

    protected function convertUpdate($arr)
    {
        $arr = parent::convertUpdate($arr);

        // 패스워드
        $mb_pw = $_POST['mb_pw'];
        if ($mb_pw) {
            $arr['mb_pw'] = Format::encryptString($mb_pw);
            $arr['mb_pw_time'] = _NOW_DATETIME_;
        }

        // 권한
        $mb_auth_code_arr = $this->getRequestParameter('mb_auth_code');
        $mb_auth_codes = '';
        if (is_array($mb_auth_code_arr)) {
            $mb_auth_codes = implode('|', $mb_auth_code_arr);
        }
        $arr['mb_auth_codes'] = $mb_auth_codes;

        return $arr;
    }

    protected function convertDetail($data)
    {
        $data = parent::convertDetail($data);

        // 첨부파일 처리
        $max_file = $this->get('max_file');
        $file_list = $data['file_list'];
        $data['profile_img'] = null;
        $profile_seq = null;
        if (is_array($file_list)) {
            for ($i = 0; $i < count($file_list); $i++) {
                $file_type = $file_list[$i]['fi_type'];
                if ($file_type == 'profile') {
                    $data['profile_img'] = $file_list[$i];
                    $profile_seq = $i;
                    break;
                }
            }
        }
        if ($profile_seq > -1) {
            unset($file_list[$profile_seq]);
            if (count($file_list) > 0) {
                $file_list = array_values(array_filter($file_list));
                $data['file_list'] = $file_list;
            }
        }

        return $data;
    }

    protected function validateValues($arr)
    {
        $result = parent::validateValues($arr);
        if ($result['code'] != 'success') {
            return $result;
        }

        // 아이디 검사
        global $mode;
        if ($mode == 'insert') {
            $chk_arr = $this->validateMemberId($arr['mb_id']);
            if (!$chk_arr['flag']) {
                $result = array(
                    'flag' => 'failure',
                    'msg' => $chk_arr['msg']
                );
                return $result;
            }
        }

        // 패스워드 검사
        if ($_POST['mb_pw']) {
            $chk_arr = $this->validateMemberPassword($_POST['mb_pw']);
            if (!$chk_arr['flag']) {
                $result = array(
                    'code' => 'failure',
                    'msg' => $chk_arr['msg']
                );
                return $result;
            }

            if ($_POST['mb_pw'] != $_POST['mb_pw2']) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '패스워드가 정확하지 않습니다.'
                );
                return $result;
            }
        }

        return $result;
    }

    public function validateMemberId($mb_id)
    {
        $result = array(
            'flag'  => 1,
            'msg'   => '사용할 수 있는 아이디입니다.'
        );

        if (!$mb_id) {
            $result['flag'] = 0;
            $result['msg'] = '아이디를 입력하세요.';
        } elseif (preg_match("/[^0-9a-zA-Z_-]+/i", $mb_id)) {
            $result['flag'] = 0;
            $result['msg'] = '아이디는 영문자, 숫자, _, -만 입력하세요.';
        } elseif (strlen($mb_id) < 4) {
            $result['flag'] = 0;
            $result['msg'] = '아이디는 최소 4글자 이상 입력하세요.';
        } elseif (strlen($mb_id) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '아이디는 최대 20글자 이하 입력하세요.';
        } elseif (strpos('admin,root,sysop,manager', strtolower($mb_id)) > -1) {
            $result['flag'] = 0;
            $result['msg'] = '예약된 단어로 아이디로 사용할 수 없습니다.';
        } else {
            $id_chk = Db::selectCount($this->get('data_table'), "where mb_id = '$mb_id'");
            if ($id_chk > 0) {
                $result['flag'] = 0;
                $result['msg'] = '이미 사용중인 아이디입니다.';
            }
        }

        return $result;
    }

    public function validateMemberPassword($mb_pw)
    {
        $result = array(
            'flag'  => 1,
            'msg'   => '사용할 수 있는 패스워드입니다.'
        );

        if (!$mb_pw) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드를 입력하세요.';
        } elseif (!preg_match('/[a-zA-Z0-9]/', $mb_pw)) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 영대/소문자, 숫자, 특수문자 조합이어야 합니다.';
        } elseif (strlen($mb_pw) < 4) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최소 4글자 이상 입력하세요.';
        } elseif (strlen($mb_pw) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최대 20글자 이하 입력하세요.';
        }

        return $result;
    }
}
