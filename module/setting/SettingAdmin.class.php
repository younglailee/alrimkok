<?php
/**
 * 관리자 모드 > 환경설정 모듈 클래스
 * @file    SettingAdmin.class.php
 * @author  Alpha-Edu
 * @package setting
 */

namespace sFramework;

class SettingAdmin extends Setting
{
    public function checkWriteAuth()
    {
        return true;
    }
    
    public function selectSettingList($sch_like, $sch_keyword)
    {
        $data_table = $this->get('data_table');
        $order_column = 'cp_name';
        $db_where = "WHERE cp_name LIKE '%{$sch_keyword}%'";
        $list = Db::select($data_table, "*", $db_where, "ORDER BY $order_column ASC", "");
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }
}
