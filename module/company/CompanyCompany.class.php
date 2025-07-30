<?php
/**
 * 관리자 > 기업 모듈 클래스
 * @file    CompanyCompany.class.php
 * @author  Alpha-Edu
 * @package company
 */

namespace sFramework;

use function str_replace;

class CompanyCompany extends Company
{
    public function checkWriteAuth()
    {
        return true;
    }

    public function selectCompanyList($sch_like, $sch_keyword)
    {
        $data_table = $this->get('data_table');
        $order_column = 'cp_name';

        $db_where = "WHERE cp_name LIKE '%{$sch_keyword}%'";

        $book = $this->get('book');
        if ($book == 'Y') {
            $db_where .= " AND flag_book = 'Y'";
        }

        $live = $this->get('live');
        if ($live == 'Y') {
            $db_where .= " AND flag_live = 'Y'";
        }

        $list = Db::select($data_table, "*", $db_where, "ORDER BY $order_column ASC", "");
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    protected function makeDbWhere()
    {
        $db_where = $this->getDefaultWhere();

        $book_list = $this->get('book_list');
        $live_list = $this->get('live_list');

        if ($book_list == 'Y') {
            $db_where .= " AND flag_book = 'Y'";
        }

        if ($live_list == 'Y') {
            $db_where .= " AND flag_live = 'Y'";
        }

        $sch_partner = $_GET['sch_partner'];

        if ($sch_partner) {
            if ($sch_partner == '-') {
                $db_where .= " AND (partner_name IS NULL OR partner_name = '')";
            } else {
                $db_where .= " AND partner_name LIKE '%$sch_partner%'";
            }
        }

        $sch_flag_book = $_GET['sch_flag_book'];
        $sch_flag_live = $_GET['sch_flag_live'];

        if ($sch_flag_book == 'Y') {
            $db_where .= " AND flag_book = 'Y'";
        } elseif ($sch_flag_book == 'N') {
            $db_where .= " AND (flag_book = '' OR flag_book IS NULL)";
        }

        if ($sch_flag_live == 'Y') {
            $db_where .= " AND flag_live = 'Y'";
        } elseif ($sch_flag_live == 'N') {
            $db_where .= " AND (flag_live = '' OR flag_live IS NULL)";
        }
        // 대표번호 -(하이픈) 제거 검색 yllee 230417
        $sch_like = $_GET['sch_like'];
        $sch_text = $_GET['sch_text'];
        if ($sch_like == 'cp_tel') {
            $sch_text = str_replace('-', '', $sch_text);
            $db_where .= " OR REPLACE(cp_tel, '-', '') LIKE '%$sch_text%'";
            //echo $db_where;
        }
        $this->set('db_where', $db_where);

        return $db_where;
    }

    public function selectList()
    {
        // count
        $cnt_total = $this->countTotal();
        $this->set('cnt_total', $cnt_total);

        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');
        $db_where = $this->get('db_where');
        $db_having = $this->get('db_having');

        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;

        $list_mode = $this->get('list_mode');
        if ($list_mode == 'excel') {
            $db_limit = '';
        }
        // 정렬 기본값: cp_id 숫자형으로 정렬 yllee 210324
        $sch_order = $_POST['sch_order'] ? $_POST['sch_order'] : $_GET['sch_order'];
        if ($sch_order) {
            $this->set('order_column', $sch_order);
            if ($sch_order == 'cp_name' || $sch_order == 'partner_name') {
                $this->set('order_direct', 'ASC');
            }
        } else {
            $this->set('order_column', 'cp_id*1');
        }
        $db_order = $this->makeDbOrder();
        //Log::debug('company admin list');
        //Log::debug($db_order);

        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);

        return $this->convertList($list);
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
        for ($i = 0; $i < count($del_file_arr); $i++) {
            $this->deleteFile($del_file_arr[$i]);
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

        $cp_id = $arr['cp_id'];
        $cp_cost_num = $arr['cp_cost_num'];

        $mb_list = Db::select('tbl_user', '*', "WHERE cp_id='$cp_id' AND mb_level='1'", '', '');

        if ($cp_cost_num) {
            for ($i = 0; $i < count($mb_list); $i++) {
                $mb_resident_num = $mb_list[$i]['mb_resident_num'];

                $emon_res_no = Format::decrypt($mb_resident_num);

                $mb_id = $mb_list[$i]['mb_id'];

                $arr = array(
                    'emon_res_no' => $emon_res_no,
                    'mb_cost_business_num' => $cp_cost_num,
                );

                if (Db::updateByArray('tbl_user', $arr, "WHERE mb_id='$mb_id'")) {
                    Db::update('tbl_user', "emon_res_no = ''", "WHERE mb_id = '$mb_id'");
                }

                $api_arr = array(
                    'mb_id' => $mb_id,
                    'mb_name' => $mb_list[$i]['mb_name'],
                    'mb_birthday' => $mb_list[$i]['mb_birthday'],
                    'emon_res_no' => $emon_res_no,
                    'cp_name' => $mb_list[$i]['cp_name'],
                    'mb_email' => $mb_list[$i]['mb_email'],
                    'mb_tel' => $mb_list[$i]['mb_tel'],
                    'reg_time' => $mb_list[$i]['reg_time'],
                    'mb_cost_business_num' => $cp_cost_num,
                    'mb_stu_type' => $mb_list[$i]['mb_stu_type'],
                    'mb_irregular_type' => $mb_list[$i]['mb_irregular_type']
                );

                Api::userHist($api_arr, 'U');
            }
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

        return $result;
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

        if (!$uid) {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
            return $result;
        }

        // 첨부파일
        if ($this->get('max_file')) {
            $this->uploadFiles($uid);
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

        if ($_POST['book'] == 'book') {
            $result = array(
                'code' => 'success',
                'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&book=book',
                'msg' => $this->get('success_msg')
            );
        }

        return $result;
    }

    // 해당 기업 수강생 인원 산출 yllee 220511
    public function countUser($cp_id)
    {
        $count = Db::selectCount("tbl_user", "WHERE cp_id = '$cp_id'");

        return $count;
    }
}
