<?php
/**
 * 관리자 > 운영자 모듈 클래스
 * @file    AdminAdmin.class.php
 * @author  Alpha-Edu
 * @package admin
 */
namespace sFramework;

class AdminAdmin extends Admin
{
    public function selectAdminList()
    {
        $db_where = "WHERE mb_level = '9' AND flag_use = 'work'";

        return Db::select('tbl_admin', '*', $db_where, 'ORDER BY mb_name ASC', '');
    }
}