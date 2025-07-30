<?php
/**
 * 관리자 > 페이지 모듈 클래스
 * @file    UserAdmin.class.php
 * @author  Alpha-Edu
 * @package page
 */

namespace sFramework;

use DateTime;
use function count;
use const _NOW_DATETIME_;

class BizUser extends Biz
{
    public function selectUserList($sch_like, $sch_keyword)
    {
        $data_table = $this->get('data_table');
        $order_column = 'mb_name';
        $db_where = 'WHERE 1 = 1';

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

    public function selectStudentList($sch_like = '', $sch_keyword = '')
    {
        $data_table = $this->get('data_table');
        $order_column = 'mb_name';
        $db_where = 'WHERE mb_level = 1';

        if ($sch_like == 'all') {
            $db_where .= " AND (mb_name LIKE '%{$sch_keyword}%' OR mb_id LIKE '%{$sch_keyword}%' OR mb_tel LIKE '%{$sch_keyword}%')";
        } else {
            if ($sch_keyword) {
                $db_where .= " AND $sch_like LIKE '%{$sch_keyword}%'";
            }
        }
        $list = Db::select($data_table, "*", $db_where, "ORDER BY $order_column ASC", "");
        $cnt_total = count($list);
        $this->set('stu_cnt_total', $cnt_total);
        $stu_page = $this->get('stu_page');
        $cnt_rows = 20;
        $db_limit = 'LIMIT ' . ($stu_page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $list = Db::select($data_table, "*", $db_where, "ORDER BY $order_column ASC", $db_limit);

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

    protected function makeDbWhere()
    {
        $sch_bz_category = $_GET['sch_bz_category'] ?? [];
        $sch_bz_field = $_GET['sch_bz_field'] ?? [];
        $sch_bz_region = $_GET['sch_bz_region'] ?? [];
        $sch_bz_institution = $_GET['sch_bz_institution'] ?? '';
        $sch_s_date = $_GET['sch_s_date'] ?? '';
        $sch_e_date = $_GET['sch_e_date'] ?? '';
        global $member;
        $mb_id = $member['mb_id'];

        $target = $this->get('target');

        $db_where = $this->getDefaultWhere();

        if ($target == 'recommend' || $target == 'recent' || $target == 'like') {
            $db_where .= " AND DATE(b.bz_apply_e_datetime) >= CURDATE() AND b.bz_title IS NOT NULL AND b.bz_title != '' AND a.mb_id = '$mb_id'";

            if ($sch_bz_category) {
                $in_bz = implode("','", $sch_bz_category);
                $db_where .= " AND b.bz_category IN ('$in_bz')";
            }

            if ($sch_bz_field) {
                $in_bz = implode("','", $sch_bz_field);
                $db_where .= " AND b.bz_field IN ('$in_bz')";
            }

            if ($sch_bz_region) {
                $in_bz = implode("','", $sch_bz_region);
                $db_where .= " AND b.bz_region IN ('$in_bz')";
            }

            if ($sch_bz_institution) {
                $db_where .= " AND b.bz_institution LIKE ('%$sch_bz_institution%')";
            }

            if ($sch_s_date) {
                $db_where .= " AND b.bz_apply_e_datetime >= '$sch_s_date 00:00:00'";
            }

            if ($sch_e_date) {
                $db_where .= " AND b.bz_apply_e_datetime <= '$sch_e_date 23:59:59'";
            }
        } else {
            $db_where .= " AND DATE(bz_apply_e_datetime) >= CURDATE() AND bz_title IS NOT NULL AND bz_title != ''";

            if ($sch_bz_category) {
                $in_bz = implode("','", $sch_bz_category);
                $db_where .= " AND bz_category IN ('$in_bz')";
            }

            if ($sch_bz_field) {
                $in_bz = implode("','", $sch_bz_field);
                $db_where .= " AND bz_field IN ('$in_bz')";
            }

            if ($sch_bz_region) {
                $in_bz = implode("','", $sch_bz_region);
                $db_where .= " AND bz_region IN ('$in_bz')";
            }

            if ($sch_bz_institution) {
                $db_where .= " AND bz_institution LIKE ('%$sch_bz_institution%')";
            }

            if ($sch_s_date) {
                $db_where .= " AND bz_apply_e_datetime >= '$sch_s_date 00:00:00'";
            }

            if ($sch_e_date) {
                $db_where .= " AND bz_apply_e_datetime <= '$sch_e_date 23:59:59'";
            }
        }

        $this->set('db_where', $db_where);

        return $db_where;
    }

    protected function initSelect()
    {
        $this->set('select_table', $this->get('data_table'));
        $this->set('select_columns', '*');

        $target = $this->get('target');
        if ($target == 'recent') {
            $this->set('select_table', 'tbl_biz_recent a JOIN tbl_biz_notice b ON a.bz_id = b.bz_id');
        } elseif ($target == 'like') {
            $this->set('select_table', 'tbl_biz_like a JOIN tbl_biz_notice b ON a.bz_id = b.bz_id');
        } elseif ($target == 'recommend') {
            $this->set('select_table', 'tbl_biz_recommend a JOIN tbl_biz_notice b ON a.bz_id = b.bz_id');
        }
    }

    public function selectList()
    {
        global $member;
        $test_id = 'alphatest';
        if ($member['mb_id'] == $test_id) {
            $db_where_add = " AND reg_id = '$test_id'";
        } else {
            $db_where_add = "";
        }
        //$cnt_total = $this->countTotal();
        $this->initSelect();
        $select_table = $this->get('select_table');
        $db_where = $this->makeDbWhere();
        $db_where .= $db_where_add;
        $group_column = $this->get('group_column');
        $db_having = $this->makeDbHaving();
        $cnt_total = Db::selectCount($select_table, $db_where, $group_column, $db_having);
        $this->set('cnt_total', $cnt_total);

        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');
        $db_where = $this->get('db_where');
        $db_where .= $db_where_add;
        $db_having = $this->get('db_having');
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $list_mode = $this->get('list_mode');
        $target = $this->get('target');

        if ($list_mode == 'excel') {
            $db_limit = '';
        }
        if ($target == 'recommend' || $target == 'like' || $target == 'recent') {
            $db_order = " ORDER BY b.reg_time DESC";
        } else {
            $db_order = $this->makeDbOrder();
        }
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
            $pr_id = $list[$i]['pr_id'];
            $bt_code = $list[$i]['bt_code'];
            $cs_code = $list[$i]['cs_code'];

            $bt_data = Db::selectOnce('tbl_book_batch', 'bt_exam_rate,bt_report_rate', "WHERE bt_code='$bt_code'", '');
            $cs_data = Db::selectOnce('tbl_book_course', 'cs_rate_exam,cs_rate_report', "WHERE cs_code='$cs_code'", '');
            $er_data = Db::selectOnce('tbl_book_exam_result', '*', "WHERE pr_id='$pr_id' AND flag_delete IS NULL", '');
            $rr_data = Db::selectOnce('tbl_book_report_result', '*', "WHERE pr_id='$pr_id' AND flag_delete IS NULL", '');

            $list[$i]['er_data'] = $er_data;
            $list[$i]['rr_data'] = $rr_data;

            $list[$i]['exam_rate'] = $bt_data['bt_exam_rate'];
            $list[$i]['report_rate'] = $bt_data['bt_report_rate'];

            if (!$bt_data['bt_exam_rate'] && !$bt_data['bt_report_rate']) {
                $list[$i]['exam_rate'] = $cs_data['cs_rate_exam'];
                $list[$i]['report_rate'] = $cs_data['cs_rate_report'];
            }
        }
        return $this->convertList($list);
    }

    /*
     * 북러닝(환급) CRM 리스트 호출 yllee 240618
     */
    public function selectListBookRefund($mb_id): array
    {
        $select_table = 'tbl_progress';
        $select_columns = '*';
        $db_where = "WHERE us_id = '$mb_id' AND bt_type='book'";
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
            $pr_id = $list[$i]['pr_id'];
            $bt_code = $list[$i]['bt_code'];
            $cs_code = $list[$i]['cs_code'];

            $bt_data = Db::selectOnce('tbl_book_batch', 'bt_exam_rate,bt_report_rate', "WHERE bt_code='$bt_code'", '');
            $cs_data = Db::selectOnce('tbl_book_course', 'cs_rate_exam,cs_rate_report', "WHERE cs_code='$cs_code'", '');
            $er_column = 'er_id, er_score, er_mb_id, er_bt_code, er_cs_code, er_type, er_week, er_scoring_time';
            $er_where = "WHERE er_bt_code='$bt_code' AND er_cs_code='$cs_code' AND er_mb_id='$mb_id' AND er_submit_time IS NOT NULL";
            $er_list = Db::select('tbl_book_exam_result', $er_column, $er_where, '', '');
            $rr_list = Db::select('tbl_book_report_result', '*', "WHERE pr_id='$pr_id' AND flag_delete IS NULL", '', '');
            //Log::debug($rr_list);
            //Log::debug("WHERE pr_id='$pr_id' AND flag_delete IS NULL");
            $list[$i]['er_data'] = $er_list;
            $list[$i]['rr_data'] = $rr_list;

            $list[$i]['exam_rate'] = $bt_data['bt_exam_rate'];
            $list[$i]['report_rate'] = $bt_data['bt_report_rate'];

            if (!$bt_data['bt_exam_rate'] && !$bt_data['bt_report_rate']) {
                $list[$i]['exam_rate'] = $cs_data['cs_rate_exam'];
                $list[$i]['report_rate'] = $cs_data['cs_rate_report'];
            }
        }
        if (!$list) {
            $list = array();
        }
        return $this->convertList($list);
    }

    public function selectListBookQuiz($mb_id, $bt_code): array
    {

        $select_table = "(SELECT p.bt_e_date, er.er_id, er.er_week, er.er_score, rr.rr_month, rr.rr_score 
                  FROM tbl_progress p 
                  LEFT OUTER JOIN tbl_book_exam_result er ON p.bt_code = er.er_bt_code 
                  LEFT OUTER JOIN tbl_book_report_result rr ON p.bt_code = rr.rr_bt_code 
                  WHERE p.us_id = '$mb_id' AND p.bt_code = '$bt_code') as a";

        $db_columns = "
            MAX(CASE WHEN a.er_week = 1 THEN a.er_score END) AS '1week',
            MAX(CASE WHEN a.er_week = 1 THEN a.er_id END) AS '1week_er_id',
            MAX(CASE WHEN a.er_week = 2 THEN a.er_score END) AS '2week',
            MAX(CASE WHEN a.er_week = 2 THEN a.er_id END) AS '2week_er_id',
            MAX(CASE WHEN a.er_week = 3 THEN a.er_score END) AS '3week',
            MAX(CASE WHEN a.er_week = 3 THEN a.er_id END) AS '3week_er_id',
            MAX(CASE WHEN a.er_week = 4 THEN a.er_score END) AS '4week',
            MAX(CASE WHEN a.er_week = 4 THEN a.er_id END) AS '4week_er_id',
            MAX(CASE WHEN a.er_week = 5 THEN a.er_score END) AS '5week',
            MAX(CASE WHEN a.er_week = 5 THEN a.er_id END) AS '5week_er_id',
            MAX(CASE WHEN a.er_week = 6 THEN a.er_score END) AS '6week',
            MAX(CASE WHEN a.er_week = 6 THEN a.er_id END) AS '6week_er_id',
            MAX(CASE WHEN a.er_week = 7 THEN a.er_score END) AS '7week',
            MAX(CASE WHEN a.er_week = 7 THEN a.er_id END) AS '7week_er_id',
            MAX(CASE WHEN a.er_week = 8 THEN a.er_score END) AS '8week',
            MAX(CASE WHEN a.er_week = 8 THEN a.er_id END) AS '8week_er_id',
            MAX(CASE WHEN a.rr_month = 1 THEN a.rr_score END) AS '1month',
            MAX(CASE WHEN a.rr_month = 2 THEN a.rr_score END) AS '2month'
        ";

        $db_where = ""; // 서브쿼리 안에서 이미 조건이 설정되어 있으므로 여기는 빈 문자열로 둠.

        $list = Db::select($select_table, $db_columns, $db_where, '', '');

        if (!$list) {
            $list = array();
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

        // 이몬 API 수강 데이터 삭제 yllee 220830
        // 기업직업훈련카드 추가 minju 230119
        // 진도(카페24, 엘엑스) 데이터 삭제 전 이몬 전송용 D(삭제) 데이터 기록 yllee 240322
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
        $result['code'] = 'success';

        return $result;
    }

    public function deleteProgressCrmBook($bt_code, $cs_code, $mb_id)
    {
        $db_where = "WHERE bt_code = '$bt_code' AND us_id = '$mb_id'";
        $pr_data = Db::selectOnce('tbl_book_progress', '*', $db_where, '');
        $pr_id = $pr_data['pr_id'];

        if (Db::delete('tbl_book_progress', "WHERE pr_id='$pr_id'")) {
            Db::delete('tbl_book_exam_result', "WHERE pr_id='$pr_id'");
            Db::delete('tbl_book_report_result', "WHERE pr_id='$pr_id'");
            $pr_list = Db::select('tbl_book_progress', 'flag_complete', "WHERE bt_code='$bt_code'", '', '');
            $bt_upt_count = 0;
            $bt_complete_count = 0;
            for ($i = 0; $i < count($pr_list); $i++) {
                $bt_upt_count++;
                if ($pr_list[$i]['flag_complete'] == 'Y') {
                    $bt_complete_count++;
                }
            }
            $db_column = "bt_count = $bt_upt_count, bt_completion_member_c = '$bt_complete_count'";
            Db::update('tbl_book_batch', $db_column, "WHERE bt_code='$bt_code'");
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

    function formatApplyDateRange($start, $end)
    {
        // 시작일 포맷
        $startDate = new DateTime($start);
        $startStr = $startDate->format('Y.m.d') . '(' . $this->getKoreanWeekday($startDate->format('w')) . ')';

        // 종료일 포맷
        $endDate = new DateTime($end);
        $hasTime = $endDate->format('H:i') !== '00:00';
        $endStr = $endDate->format('Y.m.d') . '(' . $this->getKoreanWeekday($endDate->format('w')) . ')';
        if ($hasTime) {
            $endStr .= ' ' . $endDate->format('H:i');
        } else {
            $endStr .= ' 23:59';
        }

        return $startStr . ' ~ ' . $endStr . ' 까지';
    }

    function getKoreanWeekday($w)
    {
        $weekdays = ['일', '월', '화', '수', '목', '금', '토'];
        return $weekdays[$w];
    }

    function formatNumberedListWithBr($text)
    {
        // 문장을 쉼표(,) 기준으로 나눔
        $parts = preg_split('/,\s+/', $text);

        // 결과 문자열 초기화
        $result = '';

        // 각 요소에 번호와 <br/> 추가
        foreach ($parts as $index => $part) {
            $number = $index + 1;
            $trimmed = trim($part);

            // 마지막 항목에서 끝에 마침표(.) 제거
            if ($index === count($parts) - 1) {
                $trimmed = rtrim($trimmed, '.');
            }

            $result .= "{$number}) {$trimmed}<br/>";
        }

        return $result;
    }

    function selectListRegion($bz_region = '')
    {
        $db_where = "WHERE DATE(bz_apply_e_datetime) >= CURDATE() AND bz_title IS NOT NULL AND bz_title != ''";
        if ($bz_region != '') {
            $db_where .= " AND bz_region = '{$bz_region}'";
        }
        return Db::select('tbl_biz_notice', '*', $db_where, 'ORDER BY bz_id DESC', '');
    }

    public function convertDetail($data)
    {
        global $member;
        $mb_id = $member['mb_id'];
        $bz_id = $data['bz_id'];

        // 찜하기 데이터 추출
        $bl_data = Db::selectOnce('tbl_biz_like', 'bl_id', "WHERE bz_id='$bz_id' AND mb_id='$mb_id'", '');
        if ($bl_data['bl_id']) {
            $data['is_like'] = 1;
        }

        // 메모 데이터 추출
        $bm_data = Db::selectOnce('tbl_biz_memo', 'bm_id, bm_content', "WHERE bz_id='$bz_id' AND mb_id='$mb_id'", '');

        $data['bm_content'] = $bm_data['bm_content'];
        $data['bm_id'] = $bm_data['bm_id'];

        return parent::convertDetail($data);
    }

    public function updateRecent($bz_id)
    {
        // mb_id 있을 시 최근 본 공고 추가
        global $member;
        $mb_id = $member['mb_id'];
        if ($mb_id) {
            Db::delete('tbl_biz_recent', "WHERE mb_id='$mb_id' AND bz_id='$bz_id'");
            $insert_arr = array(
                'mb_id' => $mb_id,
                'bz_id' => $bz_id,
                'reg_id' => $mb_id,
                'reg_time' => _NOW_DATETIME_
            );
            Db::insertByArray('tbl_biz_recent', $insert_arr);
        }
    }

    function updateHits($bz_id)
    {
        $sessionKey = 'viewed_' . $bz_id;
        $now = time();

        if (!isset($_SESSION[$sessionKey]) || $now - $_SESSION[$sessionKey] > 1800) {
            // 조회수 증가
            Db::update('tbl_biz_notice', 'bz_hits = bz_hits + 1', "WHERE bz_id = '" . addslashes($bz_id) . "'");

            // 조회 시간 기록
            $_SESSION[$sessionKey] = $now;
        }
    }

    function selectListHits()
    {
        global $member;
        $mb_id = $member['mb_id'];

        $db_where = "WHERE DATE(bz_apply_e_datetime) >= CURDATE() AND bz_title IS NOT NULL AND bz_title != '' AND  bz_hits > 0";
        $list = Db::select('tbl_biz_notice', '*', $db_where, 'ORDER BY bz_hits DESC', 'LIMIT 0, 8');

        for ($i = 0; $i < count($list); $i++) {
            $bz_id = $list[$i]['bz_id'];
            $is_like = 0;

            if ($mb_id) {
                $bl_data = Db::selectOnce('tbl_biz_like', 'bl_id', "WHERE bz_id='$bz_id' AND mb_id='$mb_id'", '');
                $bl_id = $bl_data['bl_id'];
                if ($bl_id) {
                    $is_like = 1;
                }
            }

            $cnt_like = Db::selectCount('tbl_biz_like', "WHERE bz_id='$bz_id'");

            $list[$i]['is_like'] = $is_like;
            $list[$i]['cnt_like'] = $cnt_like;
        }

        return $list;
    }

    function bizLike($bz_id, $state)
    {
        global $member;
        $mb_id = $member['mb_id'];

        Log::debug($state);
        $result = array(
            'code' => 'failure',
            'msg' => "좋아요 실패"
        );

        if ($state == 'like') {
            if (Db::delete('tbl_biz_like', "WHERE bz_id = '$bz_id' AND mb_id='$mb_id'")) {
                $result = array(
                    'code' => 'success',
                    'msg' => "좋아요 성공"
                );
            }
        } else {
            $insert_arr = array(
                'mb_id' => $mb_id,
                'bz_id' => $bz_id,
                'reg_id' => $mb_id,
                'reg_time' => _NOW_DATETIME_
            );
            Db::delete('tbl_biz_like', "WHERE bz_id = '$bz_id' AND mb_id='$mb_id'");
            if (Db::insertByArray('tbl_biz_like', $insert_arr)) {
                $result = array(
                    'code' => 'success',
                    'msg' => "좋아요 성공"
                );
            }
        }

        return $result;
    }

    function saveMemo($bz_id, $memoText)
    {
        global $member;
        $mb_id = $member['mb_id'];

        $result = array(
            'code' => 'success',
            'msg' => "메모 저장 성공"
        );

        $bm_data = Db::selectOnce('tbl_biz_memo', 'bm_id', "WHERE mb_id = '$mb_id' AND bz_id='$bz_id'", '');
        $bm_id = $bm_data['bm_id'];

        $arr = array(
            'bm_content' => $memoText,
        );

        if ($memoText == '' || !$memoText) {
            if ($bm_id) {
                if (!Db::delete('tbl_biz_memo', "WHERE bm_id = '$bm_id'")) {
                    $result = array(
                        'code' => 'failure',
                        'msg' => "메모 저장 실패"
                    );
                } else {
                    $bl_data = Db::selectOnce('tbl_biz_like', "bl_id", "WHERE mb_id='$mb_id' AND bz_id='$bz_id'", '');
                    $bl_id = $bl_data['bl_id'];
                    if($bl_id){
                        Db::delete('tbl_biz_like',"WHERE bl_id='$bl_id'");
                    }
                }
            }
        } else {
            if ($bm_id) {
                $arr['upt_id'] = $mb_id;
                $arr['upt_time'] = _NOW_DATETIME_;
                if (!Db::updateByArray('tbl_biz_memo', $arr, "WHERE bm_id='$bm_id'")) {
                    $result = array(
                        'code' => 'failure',
                        'msg' => "메모 저장 실패"
                    );
                }
            } else {
                $arr['mb_id'] = $mb_id;
                $arr['bz_id'] = $bz_id;
                $arr['reg_id'] = $mb_id;
                $arr['reg_time'] = _NOW_DATETIME_;

                if (!Db::insertByArray('tbl_biz_memo', $arr)) {
                    $result = array(
                        'code' => 'failure',
                        'msg' => "메모 저장 실패"
                    );
                } else {
                    $bl_data = Db::selectOnce('tbl_biz_like', "bl_id", "WHERE mb_id='$mb_id' AND bz_id='$bz_id'", '');
                    if (!$bl_data['bl_id']) {
                        $insert_arr = array(
                            'mb_id' => $mb_id,
                            'bz_id' => $bz_id,
                            'reg_id' => $mb_id,
                            'reg_time' => _NOW_DATETIME_
                        );
                        Db::insertByArray('tbl_biz_like', $insert_arr);
                    }
                }
            }
        }

        return $result;
    }

    public function getCompanyIntroduction()
    {
        global $member;
        $cp_id = $member['cp_id'];

        return Db::selectOnce('tbl_file', '*', "WHERE fi_uid='$cp_id' AND fi_module = 'company'", '');
    }
}
