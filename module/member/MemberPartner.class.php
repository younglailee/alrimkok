<?php
/**
 * 관리자 > 회원 모듈 클래스
 * @file    MemberPartner.class.php
 * @author  Alpha-Edu
 * @package member
 */

namespace sFramework;

class MemberPartner extends Member
{
    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('data_table', Admin::$data_table);
        $this->set('pk', Admin::$pk);

        $instance = new Admin();
        $instance->init();
        $this->set('instance', $instance);
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

        if (!$mb_pass || !$new_pass || !$new_pass2 || $new_pass != $new_pass2) {
            $this->result['msg'] = '비정상적인 접근입니다.';
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
        Db::update($data_table, "mb_pw = '$new_pass'", "where $pk = '$uid' and mb_pw = '$mb_pass'");

        $this->result['code'] = 'update_ok';
        $this->result['uri'] = './modify_password.html';

        return $this->result;
    }
}
