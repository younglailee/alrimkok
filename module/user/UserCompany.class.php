<?php
/**
 * 관리자 > 페이지 모듈 클래스
 * @file    UserCompany.class.php
 * @author  Alpha-Edu
 * @package page
 */

namespace sFramework;

use function count;
use function print_r;
use const _NOW_DATETIME_;

class UserCompany extends User
{
    public function checkWriteAuth()
    {
        global $member;
        //print_r($member);
        $uid = $this->get('uid');
        //echo $uid;
        $us_data = $this->searchMemberId($uid);
        //print_r($us_data);

        //if ($member['mb_id']) {
        // 해당 기업 수강생 정보만 열람 가능 yllee 240617
        if ($member['cp_id'] == $us_data['cp_id']) {
            return true;
        }
        return false;
    }

    public function selectUserList($sch_like, $sch_keyword)
    {
        $data_table = $this->get('data_table');
        $order_column = 'mb_name';
        $db_where = 'WHERE 1 = 1';

        // 해당 기업의 사원만 출력 yllee 230901
        global $member;
        $cp_id = $member['cp_id'];
        $db_where .= " AND cp_id = '$cp_id'";
        $db_where .= " AND mb_level = '1'";
        //$db_where .= " AND flag_test != 'Y'";

        if ($sch_like == 'all') {
            $db_where .= " AND (mb_name LIKE '%{$sch_keyword}%' OR mb_tel LIKE '%{$sch_keyword}%')";
        } else {
            if ($sch_keyword) {
                $db_where .= " AND $sch_like LIKE '%{$sch_keyword}%'";
            }
        }
        $db_order = "ORDER BY $order_column ASC, reg_time DESC";
        $list = Db::select($data_table, "*", $db_where, $db_order, "");
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $update_columns = 'mb_id,mb_level,mb_name,mb_birthday,mb_resident_num,mb_email,mb_tel,mb_direct_line';
        $update_columns .= ',cp_id,cp_name,mb_zip,mb_addr,mb_addr2,mb_stu_type,mb_position,mb_irregular_type';
        $update_columns .= ',mb_cost_business_num,flag_tomocard,flag_use,flag_auth,flag_test,flag_sms,flag_cyber';
        $update_columns .= ',mb_hp,mb_memo,flag_book,sw_depart,flag_live';
        $this->set('update_columns', $update_columns);
        $this->set('required_arr', array(
            'mb_id' => '아이디',
            'mb_name' => '이름'
        ));
    }

    protected function convertUpdate($arr)
    {
        $arr = parent::convertUpdate($arr);
        if ($_POST['flag_auth_admin']) {
            $arr['auth_time_admin'] = _NOW_DATETIME_;
        }
        return $arr;
    }

    public function selectListMbId($mb_id)
    {
        //$select_table = 'tbl_progress';
        $select_table = 'tbl_batch_user a JOIN tbl_batch b ON a.bu_bt_id = b.bt_id AND a.bu_cs_code = b.cs_code';
        $select_columns = '*';
        //$db_where = "WHERE us_id = '$mb_id'";
        $db_where = "WHERE a.bu_mb_id = '$mb_id'";
        $db_having = $this->get('db_having');
        //$db_order = 'ORDER BY a.reg_time DESC';
        $db_order = 'ORDER BY b.bt_s_date DESC';
        /*
        global $member;
        if($member['mb_id'] == 'silva') {
            $select_table = "(SELECT bt_s_date,bt_e_date,rate_progress,cs_id,cs_code,bt_code,bt_type,bt_id,rep_score,flag_complete from tbl_progress WHERE us_id='$mb_id' AND bt_e_date >= '$today' UNION SELECT bt_s_date,bt_e_date,rate_progress,cs_id,cs_code,bt_code,bt_type,bt_id,rep_score,flag_complete from tbl_book_progress WHERE us_id='$mb_id' AND bt_e_date >= '$today') A";
            $db_order = "ORDER BY A.bt_s_date DESC";
            $db_where = "";
        }
        */
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, '');
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $list = Db::select($select_table, "*", $db_where, "$db_order", $db_limit);

        for ($i = 0; $i < count($list); $i++) {
            $bt_code = $list[$i]['bt_code'];
            $cs_code = $list[$i]['cs_code'];
            $exam_year = '';
            /*
             * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
            $bt_end_date = $list[$i]['bt_e_date'];
            $bt_end_date_arr = explode('-', $bt_end_date);
            $bt_end_year = $bt_end_date_arr[0];
            $now_year = date('Y');
            if ($now_year > $bt_end_year) {
                $exam_year = '_' . $bt_end_year;
            }
            */
            $exam_mid_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_midterm' AND er_cs_code='$cs_code' AND flag_delete IS NULL";
            $exam_fin_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_final' AND er_cs_code='$cs_code' AND flag_delete IS NULL";
            $report_where = "WHERE rr_mb_id = '$mb_id' AND rr_bt_code='$bt_code' AND rr_cs_code='$cs_code' AND rr_submit_time IS NOT NULL";
            $exam_mid_data = Db::selectOnce('tbl_exam_result' . $exam_year, '*', $exam_mid_where, '');
            $exam_fin_data = Db::selectOnce('tbl_exam_result' . $exam_year, '*', $exam_fin_where, '');
            $report_data = Db::selectOnce('tbl_report_result', '*', $report_where, '');
            $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_code = '$cs_code'", '');

            //Log::debug("WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_final' AND er_cs_code='$cs_code' AND flag_delete IS NULL");
            //Log::debug($exam_fin_data);

            $list[$i]['mid_data'] = $exam_mid_data;
            $list[$i]['fin_data'] = $exam_fin_data;
            $list[$i]['report_data'] = $report_data;
            $list[$i]['cs_data'] = $cs_data;
            // 진도율
            $pr_where = "WHERE bt_code = '$bt_code' AND cs_code = '$cs_code' AND us_id = '$mb_id'";
            $pr_data = Db::selectOnce('tbl_progress', '*', $pr_where, '');
            if (!$pr_data) {
                $pr_data = Db::selectOnceLxn('tbl_progress', '*', $pr_where, '');
            }
            $list[$i]['rate_progress'] = $pr_data['rate_progress'];
            $list[$i]['pr_id'] = $pr_data['pr_id'];
        }
        return $this->convertList($list);
    }

    public function getPageArrayStu()
    {
        $cnt_total = $this->get('stu_cnt_total');
        $cnt_rows = 20;
        $cnt_page = $this->get('cnt_page');
        $page = $this->get('stu_page');

        $total_page = ceil($cnt_total / $cnt_rows);
        if (!$total_page) {
            $total_page = 1;
        }
        $total_group = ceil($total_page / $cnt_page);
        $now_group = ceil($page / $cnt_page);
        $this->set('total_group', $total_group);

        // 처음, 이전
        if ($now_group > 1) {
            $page_arr[] = array(
                'page' => 1,
                'title' => '처음',
                'class' => 'arrow begin'
            );
            $page_arr[] = array(
                'page' => ($now_group - 2) * $cnt_page + 1,
                'title' => '이전',
                'class' => 'arrow prev'
            );
        }
        // 반복
        $tmp_page = ($now_group - 1) * $cnt_page;
        for ($i = 0; $i < $cnt_page; $i++) {
            $tmp_page++;
            if ($tmp_page > $total_page) {
                break;
            }
            $page_arr[] = array(
                'page' => $tmp_page,
                'title' => number_format($tmp_page),
                'class' => ($tmp_page == $page) ? 'on' : ''
            );
        }
        // 다음&끝
        if ($now_group < $total_group) {
            $page_arr[] = array(
                'page' => $now_group * $cnt_page + 1,
                'title' => '다음',
                'class' => 'arrow next',
            );
            $page_arr[] = array(
                'page' => $total_page,
                'title' => '끝',
                'class' => 'arrow end'
            );
        }
        return $page_arr;
    }

    public static function makePaginationStu($arr, $query_string = '', $a_arr_class = '')
    {
        $result = '';
        for ($i = 0; $i < count($arr); $i++) {
            $result .= '<li';
            if ($arr[$i]['class']) {
                $result .= ' class="' . $arr[$i]['class'] . '"';
            }
            $result .= '><a href="?stu_page=' . $arr[$i]['page'] . $query_string;
            $result .= '" class="' . $a_arr_class . '" title="' . $arr[$i]['title'] . ' 페이지">' . $arr[$i]['title'];
            $result .= '</a></li>' . "\n";
        }
        return $result;
    }

    public static function makePagination($arr, $query_string = '', $a_arr_class = '')
    {
        $result = '';
        for ($i = 0; $i < count($arr); $i++) {
            $result .= '<li';
            if ($arr[$i]['class']) {
                $result .= ' class="' . $arr[$i]['class'] . '"';
            }
            $result .= '><a href="?page=' . $arr[$i]['page'] . $query_string;
            $result .= '" class="' . $a_arr_class . '" title="' . $arr[$i]['title'] . ' 페이지">' . $arr[$i]['title'];
            $result .= '</a></li>' . "\n";
        }
        return $result;
    }

    protected function makeDbWhere()
    {
        $db_where = $this->getDefaultWhere();
        $book_list = $this->get('book_list');
        $live_list = $this->get('live_list');

        // 해당 기업의 사원만 출력 yllee 230901
        global $member;
        $cp_id = $member['cp_id'];
        $db_where .= " AND cp_id = '$cp_id'";
        $db_where .= " AND mb_level = '1'";
        $db_where .= " AND (flag_test != 'Y' OR flag_test IS NULL)";

        if ($book_list == 'Y') {
            $db_where .= " AND flag_book = 'Y'";
        }
        if ($live_list == 'Y') {
            $db_where .= " AND flag_live = 'Y'";
        }
        $sch_flag_book = $_GET['sch_flag_book'];
        $sch_flag_live = $_GET['sch_flag_live'];
        $sch_company = $_GET['sch_company'];

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
        if ($sch_company) {
            $db_where .= " AND cp_name LIKE '%$sch_company%'";
        }
        $this->set('db_where', $db_where);
        //echo $db_where;

        return $db_where;
    }

    public function selectList()
    {
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
        //$book_list = $this->get('book_list');
        /*
        if ($book_list == 'Y') {
            $db_limit = '';
        }
        */
        if ($list_mode == 'application') {
            $db_order = " ORDER BY reg_time DESC";
        } else {
            $db_order = $this->makeDbOrder();
        }
        Log::debug('application_user');
        Log::debug($db_order);
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);

        return $this->convertList($list);
    }

    public function deleteDataBook()
    {
        $this->initDelete();
        $list_uid_arr = $_POST['list_uid'];

        $uid = $this->get('uid');
        if (!$list_uid_arr && $uid) {
            $list_uid_arr = array(
                '0' => $uid
            );
        }
        for ($i = 0; $i < count($list_uid_arr); $i++) {
            $uid = $list_uid_arr[$i];

            Db::update('tbl_user', "flag_book=''", "WHERE mb_id='$uid'");
        }
        $this->set('return_uri', './user_list.html');

        return $this->postDelete();
    }

    public function selectListBook($mb_id)
    {
        $select_table = 'tbl_book_progress';
        $select_columns = '*';
        $db_where = "WHERE us_id = '$mb_id'";
        $db_having = $this->get('db_having');
        $db_order = 'ORDER BY bt_s_date DESC';
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, '');

        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $list = Db::select($select_table, "*", $db_where, "$db_order", $db_limit);

        for ($i = 0; $i < count($list); $i++) {
            $bt_code = $list[$i]['bt_code'];
            $cs_code = $list[$i]['cs_code'];

            $er_data1 = Db::selectOnce('tbl_book_exam_result', '*', "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'quiz' AND er_week = 1 AND er_cs_code='$cs_code' AND flag_delete IS NULL", '');
            $er_data2 = Db::selectOnce('tbl_book_exam_result', '*', "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'quiz' AND er_week = 2 AND er_cs_code='$cs_code' AND flag_delete IS NULL", '');
            $er_data3 = Db::selectOnce('tbl_book_exam_result', '*', "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'quiz' AND er_week = 3 AND er_cs_code='$cs_code' AND flag_delete IS NULL", '');
            $er_data4 = Db::selectOnce('tbl_book_exam_result', '*', "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'quiz' AND er_week = 4 AND er_cs_code='$cs_code' AND flag_delete IS NULL", '');
            $report_data = Db::selectOnce('tbl_book_report_result', '*', "WHERE rr_mb_id = '$mb_id' AND rr_bt_code='$bt_code' AND rr_cs_code='$cs_code' AND rr_submit_time IS NOT NULL", '');
            $cs_data = Db::selectOnce('tbl_book_course', '*', "WHERE cs_code = '$cs_code'", '');

            $list[$i]['er_data1'] = $er_data1;
            $list[$i]['er_data2'] = $er_data2;
            $list[$i]['er_data3'] = $er_data3;
            $list[$i]['er_data4'] = $er_data4;
            $list[$i]['report_data'] = $report_data;
            $list[$i]['cs_data'] = $cs_data;
        }
        return $this->convertList($list);
    }

    /**
     * CRM 기수 진도 데이터 삭제: 파라미터 수정 pr_id -> bt_code, cs_code, mb_id yllee 220628
     * @param $bt_code
     * @param $cs_code
     * @param $mb_id
     * @return array
     */
    public function deleteProgressCrm($bt_code, $cs_code, $mb_id)
    {
        $db_where = "WHERE bt_code = '$bt_code' AND cs_code = '$cs_code' AND us_id = '$mb_id'";
        $pr_data = Db::selectOnce('tbl_progress', '*', $db_where, '');
        // 카페24 서버에 진도 데이터가 없을 경우 엘엑스 서버 호출 yllee 220614
        if (!$pr_data) {
            $pr_data = Db::selectOnceLxn('tbl_progress', '*', $db_where, '');
        }
        $bt_type = $pr_data['bt_type'];
        $bt_s_date = $pr_data['bt_s_date'];
        $cp_id = $pr_data['cp_id'];

        // 산업안전, 법정필수 기수이면서 2022년 6월 8일부터 교육 시작인 기수 엘엑스 서버에서 진도 데이터 호출
        if (($bt_type == 'safe' || $bt_type == 'court') && $bt_s_date >= '2022-06-08' && $cp_id != '1640577920') {
            Db::deleteLxn('tbl_progress', $db_where);
        } else {
            Db::delete('tbl_progress', $db_where);
        }
        // 삭제가 완료된 후 업데이트 하도록 조건문 적용 minju 221208
        if (Db::delete('tbl_batch_user', "WHERE bu_bt_code = '$bt_code' AND bu_mb_id = '$mb_id'")) {
            $this->updateBtCount($bt_code, $cs_code);
        }
        // 이몬 API 수강 데이터 삭제 yllee 220830
        // 기업직업훈련카드 추가 minju 230119
        if ($bt_type == 'refund' || $bt_type == 'tomocard' || $bt_type == 'job') {
            $class_agent_pk = $bt_code . ',' . $cs_code;
            $hs_where = "WHERE CLASS_AGENT_PK = '$class_agent_pk' AND USER_AGENT_PK = '$mb_id'";
            $hs_order = "ORDER BY SEQ DESC";
            $hs_data = Db::selectOnce('HIST_ATTEND', "*", $hs_where, $hs_order);
            $api_arr = array(
                'mb_id' => $mb_id,
                'cs_code' => $cs_code,
                'bt_code' => $bt_code,
                'bt_type' => $bt_type,
                'flag_complete' => '',
                'rate_progress' => $hs_data['PROGRESS_RATE'],
                'total_score' => $hs_data['TOTAL_SCORE'],
                'reg_date' => _NOW_DATETIME_
            );
            Api::attendHist($api_arr, 'D');
        }
        $result['code'] = 'success';

        return $result;
    }

    public function updateBtCount($bt_code, $cs_code)
    {
        // Db 두 번 접근하지 않도록 코드 수정 minju 221208
        $db_where = "WHERE bu_bt_code = '$bt_code' AND bu_cs_code = '$cs_code'";
        $bt_list = Db::select('tbl_batch_user', '*', $db_where, '', '');

        $bt_upt_count = 0;
        $bt_complete_count = 0;
        for ($i = 0; $i < count($bt_list); $i++) {
            $bt_upt_count++;
            if ($bt_list[$i]['flag_complete'] == 'Y') {
                $bt_complete_count++;
            }
        }
        $db_column = "bt_count = $bt_upt_count, bt_completion_member_c = '$bt_complete_count'";
        Db::update('tbl_batch', $db_column, "WHERE bt_code = '$bt_code' AND cs_code = '$cs_code'");
    }
}
