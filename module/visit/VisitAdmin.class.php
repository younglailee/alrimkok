<?php
/**
 * 관리자 > 방문기록 모듈 클래스
 * @file    VisitAdmin.class.php
 * @author  Alpha-Edu
 * @package visit
 */

namespace sFramework;

class VisitAdmin extends Visit
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
        $db_where = parent::makeDbWhere();
        $db_where .= " AND reg_id != '' ";
        $this->set('db_where', $db_where);

        return $db_where;
    }

    /**
     * 목록 데이터 반환(오버라이드)
     * @return array
     */
    public function selectList()
    {
        //$cnt_total = $this->countTotal();
        //$this->set('cnt_total', $cnt_total);

        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');
        $db_where = $this->get('db_where');
        $db_having = $this->get('db_having');

        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $db_order = $this->makeDbOrder();

        // 2021년 방문 데이터 검색 시 DB 부하 발생으로 주석처리 yllee 221207
        //$list = Db::selectUnion($select_table, 'tbl_visit_2021', $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);
        $list = array(
            $select_table,
            $select_columns,
            $db_where,
            $db_having,
            $db_order,
            $db_limit
        );
        //$list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);
        //return $this->convertList($list);
        return $list;
    }

    /**
     * 목록 집계(오버라이드)
     * @return int
     */
    public function countTotal()
    {
        $this->initSelect();
        $select_table = $this->get('select_table');
        $db_where = $this->makeDbWhere();

        // 2021년 방문 데이터 검색 시 DB 부하 발생으로 주석처리 yllee 221207
        //$list = Db::selectUnion($select_table, 'tbl_visit_2021', "vs_id", $db_where, "", "");
        $list = Db::select($select_table, "vs_id", $db_where, "", "");
        $cnt_total = count($list);

        return $cnt_total;
    }
}
