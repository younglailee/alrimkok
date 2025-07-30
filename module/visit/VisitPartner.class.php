<?php
/**
 * 파트너 > 방문기록 모듈 클래스
 * @file    VisitPartner.class.php
 * @author  Alpha-Edu
 * @package visit
 */

namespace sFramework;

use function implode;

class VisitPartner extends Visit
{
    public function checkViewAuth($data)
    {
        return true;
    }

    public function checkWriteAuth()
    {
        return true;
    }

    public function checkUpdateAuth($uid)
    {
        return true;
    }

    /**
     * 조회 조건 생성 (오버라이드)
     * @return string
     */
    protected function makeDbWhere()
    {
        global $member;
        $mb_id = $member['mb_id'];
        $cp_list = Db::select("tbl_company", "cp_id", "WHERE partner_id = '$mb_id'", "", "");
        $cp_id_arr = array();
        for ($i = 0; $i < count($cp_list); $i++) {
            $cp_id_arr[] = $cp_list[$i]['cp_id'];
        }
        $cp_id_set = implode(',', $cp_id_arr);
        //echo $cp_id_set;
        $db_where = parent::makeDbWhere();
        $db_where .= " AND reg_id != '' AND cp_id IN ($cp_id_set) ";
        $this->set('db_where', $db_where);

        return $db_where;
    }

    public function searchVisitMemberId($mb_id)
    {
        $data_table = $this->get('data_table');
        $db_where = "WHERE reg_id = '$mb_id'";
        $db_order = "ORDER BY reg_id DESC";
        $list = Db::selectOnce($data_table, '*', $db_where, $db_order);

        return $list;
    }
}
