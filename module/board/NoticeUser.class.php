<?php
/**
 * 사용자모드 > 공지사항 모듈 클래스
 * @file    NoticeUser.class.php
 * @author  Alpha-Edu
 * @package board
 */

namespace sFramework;

class NoticeUser extends Notice
{
    protected function setModuleConfig()
    {
        parent::setModuleConfig();
    }

    public function checkViewAuth($data)
    {
        return true;
    }

    public function checkWriteAuth()
    {
        return false;
    }

    public function checkUpdateAuth($uid)
    {
        return false;
    }

    public function checkDownloadSession()
    {
        $ss_bd_writer_name = Session::getSession('ss_bd_writer_name');
        $ss_bd_writer_tel = Session::getSession('ss_bd_writer_tel');

        if ($ss_bd_writer_name && $ss_bd_writer_tel) {
            return true;
        } else {
            return false;
        }
    }

    public function insetDownload($data)
    {
        //Log::debug($data);
        $data_table = 'tbl_download';
        $arr = array(
            'dw_name' => $data['us_name'],
            'dw_tel' => $data['us_tel'],
            'fi_id' => $data['fi_id'],
            'fi_name' => $data['fi_name'],
            'bd_category' => $data['bd_category']
        );
        global $member;
        $arr['reg_id'] = $member['mb_id'];
        $arr['reg_time'] = _NOW_DATETIME_;
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

    protected function makeDbWhere()
    {
        $db_where = parent::makeDbWhere();

        // 공지사항 게시판만 출력 여부 적용
        $bd_code = $this->get('bd_code');
        // 과정자료실 추가 yllee 230321
        if ($bd_code == 'notice' || $bd_code == 'reference') {
            $db_where .= " AND (bd_is_display = 'Y' OR bd_is_display = 'A') AND (bd_etc10 IS NULL || bd_etc10 != 'Y')";
        }
        $this->set('db_where', $db_where);

        return $db_where;
    }
}
