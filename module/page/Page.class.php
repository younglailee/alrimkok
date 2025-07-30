<?php
/**
 * 페이지 모듈 클래스
 * @file    Page.class.php
 * @author  Alpha-Edu
 * @package page
 */
namespace sFramework;

class Page extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_page';
    public static $pk = 'pg_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'member');
        $this->set('module_name', '멤버십');
    }
}
