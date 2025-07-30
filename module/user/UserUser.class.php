<?php
/**
 * 가용자 > 페이지 모듈 클래스
 * @file    UserUser.class.php
 * @author  Alpha-Edu
 * @package user
 */


namespace sFramework;

use function count;

class UserUser extends User
{
    public function deleteData()
    {
        $this->initDelete();
        $this->set('return_uri', './sub_list.html');
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

    public function checkUpdateAuth($uid)
    {
        global $member;
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $mb_id = $member['mb_id'];
        $db_where = "WHERE $pk='$uid' and reg_id='$mb_id'";
        //echo $data_table .' '. $db_where;exit;
        if (Db::selectCount($data_table, $db_where)) {
            return true;
        }
        return false;
    }
}
