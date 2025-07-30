<?php
/**
 * 방문기록 모듈 클래스
 * @file    Visit.class.php
 * @author  Alpha-Edu
 * @package visit
 */

namespace sFramework;

use function count;
use function date;
use function explode;
use function number_format;

class Visit extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_visit';
    public static $pk = 'vs_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'visit');
        $this->set('module_name', '방문기록');

        // 검색
        $this->set('search_columns', 'vs_os,vs_browser');
        $this->set('search_like_arr', array(
            'all' => '통합검색',
            'vs_ip' => '아이피',
            'reg_name' => '수강생명',
            'reg_id' => '아이디',
            'cp_name' => '기업명'
        ));
        $this->set('search_date_arr', array(
            'reg_time' => '접속일'
        ));
    }

    public function insertData()
    {
        if (!defined('_UA_PATH_') || !defined('_CACHE_PATH_')) {
            return false;
        }
        require _UA_PATH_ . '/Browscap.php';

        $oBrowscap = new \phpbrowscap\Browscap(_CACHE_PATH_);
        $oBrowscap->doAutoUpdate = false;
        $oBrowscap->cacheFilename = 'browscap_cache.php';
        $info = $oBrowscap->getBrowser($_SERVER['HTTP_USER_AGENT']);

        // 미래종합물류 조윤환 접속 정보 확인 yllee 220711
        global $member;
        /*
         * 조윤환(미래종합물류) 접속 정보 확인 주석처리 yllee 221205
        if ($member['mb_id'] == 'mir065') {
            Log::debug($info);
        }
        */
        $vs_browser = $info->Comment;
        $vs_os = $info->Platform;
        $vs_device = $info->Device_Type;

        $arr = array(
            'vs_ip' => _USER_IP_,
            'vs_device' => $vs_device,
            'vs_os' => $vs_os,
            'vs_browser' => $vs_browser,
            'vs_referer' => addslashes(strip_tags($_SERVER['HTTP_REFERER'])),
            'cp_id' => $member['cp_id'],
            'cp_name' => $member['cp_name'],
            'us_level' => $member['mb_level'],
            'reg_name' => $member['mb_name'],
            'reg_id' => $member['mb_id'],
            'reg_time' => _NOW_DATETIME_
        );
        $data_table = $this->get('data_table');
        if (Db::insertByArray($data_table, $arr)) {
            $result = $this->postInsert($arr);
            $result['vs_device'] = $vs_device;
            $result['vs_browser'] = $vs_browser;
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
        }
        Session::setCookie('ck_user_ip', _USER_IP_);

        return $result;
    }

    protected function initSelect()
    {
        parent::initSelect();

        $list_mode = $this->get('list_mode');
        if ($list_mode == 'year' || $list_mode == 'month' || $list_mode == 'day') {
            if ($list_mode == 'year') {
                $group_column = 'LEFT(reg_time, 4)';
            } elseif ($list_mode == 'month') {
                $group_column = 'LEFT(reg_time, 7)';
            } elseif ($list_mode == 'day') {
                $group_column = 'LEFT(reg_time, 10)';
            }
            $pk = $this->get('pk');
            $this->set('group_column', $group_column);
            $this->set('order_column', 'date');
            $this->set('cnt_rows', 99999999);
            $this->set('select_columns', "$group_column as date, COUNT($pk) as cnt");
        }
    }

    public function selectListByDate()
    {
        $list = $this->selectList();
        $sch_s_date = $this->getRequestParameter('sch_s_date');
        $sch_e_date = $this->getRequestParameter('sch_e_date');

        if (!$sch_s_date) {
            $sch_s_date = $list[count($list) - 1]['date'];
        }
        if (!$sch_e_date) {
            $sch_e_date = $list[0]['date'];
        }
        unset($arr);
        $key_time = $sch_s_date;
        $list_mode = $this->get('list_mode');
        while (true) {
            if ($list_mode == 'year') {
                $key = substr($key_time, 0, 4);
                $txt = $key . '년';

                $add_time = '1y';
            } elseif ($list_mode == 'month') {
                $key = substr($key_time, 0, 7);
                $txt = str_replace('-', '년 ', $key) . '월';
                $add_time = '1m';
            } elseif ($list_mode == 'day') {
                $key = substr($key_time, 0, 10);
                $tmp = explode('-', $key);
                $week_arr = explode(',', '일,월,화,수,목,금,토');
                $week = date('w', strtotime($key));
                $txt = $tmp[0] . '년 ' . number_format($tmp[1]) . '월 ' . number_format($tmp[2]) . '일';
                $txt .= '(' . $week_arr[$week] . ')';
                $add_time = '1d';
            }
            $arr[$key] = array(
                'txt' => $txt,
                'cnt' => 0,
                'per' => 0
            );
            $key_time = Time::getAroundDate($add_time, $key_time);
            if (strtotime($key_time) > strtotime($sch_e_date)) {
                break;
            }
        }

        $min = 0;
        $max = 0;
        $total = 0;
        for ($i = 0; $i < count($list); $i++) {
            $key = $list[$i]['date'];
            $cnt = $list[$i]['cnt'];
            $arr[$key]['cnt'] = $cnt;
            if ($min > $cnt) {
                $min = $cnt;
            }
            if ($max < $cnt) {
                $max = $cnt;
            }
            $total += $cnt;
        }

        if ($total > 0) {
            for ($i = 0; $i < count($list); $i++) {
                $key = $list[$i]['date'];
                $cnt = $list[$i]['cnt'];
                $arr[$key]['per'] = round(($cnt * 1000) / $total) / 1;
            }
        }

        $result = array(
            'code' => 'success',
            'list' => $arr,
            'min' => $min,
            'max' => $max,
            'total' => $total
        );

        return $result;
    }

    /**
     * 날짜 기준 접속자 수 집계
     * @param string $date
     * @return array
     */
    public function selectCountByDate($date = null)
    {
        $data_table = $this->get('data_table');
        $db_where = "WHERE 1 = 1 ";
        if ($date) {
            $db_where .= " AND LEFT(reg_time, 10) = '$date' ";
        }
        $cnt = Db::selectCount($data_table, $db_where);

        return $cnt;
    }

    public function selectVisit($us_id)
    {
        $data_table = $this->get('data_table');
        $db_where = "WHERE reg_id = '$us_id'";
        //$list = Db::select($data_table, "*", $db_where, "ORDER BY vs_id DESC", "");

        // 2021년 방문 기록 함께 조회 yllee 220725
        $db_having = $this->get('db_having');
        $db_order = "ORDER BY vs_id DESC";
        // 100개만 들고오도록 LIMIT minju 230713
        $db_limit = "LIMIT 0,100";
        // 2021년 방문 데이터 검색 시 DB 부하 발생으로 주석처리 yllee 221207
        //$list = Db::selectUnion($data_table, 'tbl_visit_2021', "*", $db_where, $db_having . ' ' . $db_order, $db_limit);
        $list = Db::select($data_table, "*", $db_where, $db_having . ' ' . $db_order, $db_limit);
        $this->set('cnt_total', count($list));

        return $this->convertList($list);
    }

    /**
     * IP 중복 리스트
     * @param $cp_id
     * @param $vs_ip
     * @param $mb_id
     * @return array
     */
    public function selectDuplicateIp($cp_id, $vs_ip, $mb_id = '')
    {
        $db_where = "WHERE cp_id = '$cp_id' AND vs_ip = '$vs_ip'";
        if ($mb_id) {
            $db_where .= " AND reg_id != '' AND reg_id != '$mb_id'";
        }
        $sch_s_date = $_GET['sch_s_date'];
        $sch_e_date = $_GET['sch_e_date'];
        if ($sch_e_date && $sch_s_date) {
            $db_where .= " AND reg_time >= '$sch_s_date 00:00:00' AND reg_time <= '$sch_e_date 23:59:59'";
        }
        // 아이디 검색
        $sch_reg_id = $_GET['sch_reg_id'];
        if ($sch_reg_id) {
            $db_where .= " AND reg_id = '$sch_reg_id'";
        }
        // 이름 검색
        $sch_reg_name = $_GET['sch_reg_name'];
        if ($sch_reg_name) {
            $db_where .= " AND reg_name = '$sch_reg_name'";
        }
        // 기업명 검색
        $sch_cp_name = $_GET['sch_cp_name'];
        if ($sch_cp_name) {
            $db_where .= " AND cp_name LIKE '%$sch_cp_name%'";
        }
        // 테스트 계정 제외 yllee 220721
        /*
        $test_cp_id_in = "'1482940026','1602908890','98765'";
        $db_where .= " AND cp_id NOT IN ($test_cp_id_in)";
        */
        $db_where .= " GROUP BY reg_id";
        $db_order = "ORDER BY reg_name ASC, reg_time DESC";
        $list = Db::select('tbl_visit', "*", $db_where, $db_order, "");

        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    /**
     * 상세보기 데이터 반환
     * @param $cp_id
     * @param $vs_ip
     * @param $mb_id
     * @return array
     */
    public function selectVisitIp($cp_id, $vs_ip, $mb_id)
    {
        $data_table = $this->get('data_table');
        $db_where = "WHERE cp_id = '$cp_id' AND vs_ip = '$vs_ip' AND reg_id != '' AND reg_id != '$mb_id'";
        $db_where .= " GROUP BY reg_id";
        $db_order = "ORDER BY reg_time DESC";
        $vs_list = Db::select($data_table, "*", $db_where, $db_order, "");

        return $vs_list;
    }

    /**
     * IP 중복 리스트(타 기업) yllee 220705
     * @param string $vs_ip
     * @return array
     */
    public function selectDuplicateIpCompany($vs_ip)
    {
        $db_where = "WHERE vs_ip = '$vs_ip' AND reg_id != ''";

        $sch_s_date = $_GET['sch_s_date'];
        $sch_e_date = $_GET['sch_e_date'];
        if ($sch_e_date && $sch_s_date) {
            $db_where .= " AND reg_time >= '$sch_s_date 00:00:00' AND reg_time <= '$sch_e_date 23:59:59'";
        }
        //$db_where .= " GROUP BY vs_ip";
        $db_order = "ORDER BY reg_time DESC";
        $vs_list = Db::select('tbl_visit', "*", $db_where, $db_order, "");

        $arr_cp = array();
        $arr_us = array();
        foreach ($vs_list as $key => $val) {
            $vs_cp_id = $val['cp_id'];
            $vs_cp_ip = $val['vs_id'];
            if ($vs_cp_id && $vs_cp_ip) {
                $arr_cp[$vs_cp_id][$vs_cp_ip] = '';
            }
            $vs_user = $val['reg_id'];
            if ($vs_user) {
                $arr_us[$vs_cp_id][$vs_user] = '';
            }
        }
        $db_where .= " GROUP BY reg_id";
        $db_order = "ORDER BY cp_name ASC, reg_time DESC";
        $list = Db::select('tbl_visit', "*", $db_where, $db_order, "");
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    /**
     * IP 중복 리스트(타 기업) yllee 220705
     * @param string $vs_ip
     * @param int $cp_id
     * @return array
     */
    public function selectDuplicateIpCompanyOnly($vs_ip, $cp_id)
    {
        $db_where = "WHERE vs_ip = '$vs_ip' AND reg_id != ''";
        $db_where .= " AND cp_id != '$cp_id'";
        $db_where .= " AND us_level < 4";

        $sch_s_date = $_GET['sch_s_date'];
        $sch_e_date = $_GET['sch_e_date'];
        if ($sch_e_date && $sch_s_date) {
            $db_where .= " AND reg_time >= '$sch_s_date 00:00:00' AND reg_time <= '$sch_e_date 23:59:59'";
        }
        // 테스트 계정 제외 yllee 220721
        $test_cp_id_in = "'1482940026','1602908890','98765'";
        $db_where .= " AND cp_id NOT IN ($test_cp_id_in)";

        //$db_where .= " GROUP BY vs_ip";
        $db_order = "ORDER BY reg_time DESC";
        $vs_list = Db::select('tbl_visit', "*", $db_where, $db_order, "");

        $arr_cp = array();
        $arr_us = array();
        foreach ($vs_list as $key => $val) {
            $vs_cp_id = $val['cp_id'];
            $vs_cp_ip = $val['vs_id'];
            if ($vs_cp_id && $vs_cp_ip) {
                $arr_cp[$vs_cp_id][$vs_cp_ip] = '';
            }
            $vs_user = $val['reg_id'];
            if ($vs_user) {
                $arr_us[$vs_cp_id][$vs_user] = '';
            }
        }
        $db_where .= " GROUP BY reg_id";
        $db_order = "ORDER BY cp_name ASC, reg_time DESC";
        $list = Db::select('tbl_visit', "*", $db_where, $db_order, "");
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    /**
     * IP 중복 리스트(기업 그룹핑) yllee 220705
     * @param string $vs_ip
     * @return array
     */
    public function selectIpCompanyGrop($vs_ip)
    {
        $db_where = "WHERE vs_ip = '$vs_ip' AND cp_id != ''";

        $sch_s_date = $_GET['sch_s_date'];
        $sch_e_date = $_GET['sch_e_date'];
        if ($sch_e_date && $sch_s_date) {
            $db_where .= " AND reg_time >= '$sch_s_date 00:00:00' AND reg_time <= '$sch_e_date 23:59:59'";
        }
        $db_where .= " GROUP BY cp_id";
        $db_order = "ORDER BY reg_time DESC";
        $list = Db::select('tbl_visit', "*", $db_where, $db_order, "");

        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    /**
     * IP 중복 리스트(기업별, IP) yllee 220705
     * @param $cp_id
     * @return array
     */
    public function selectIpCompany($cp_id)
    {
        $db_where = "WHERE cp_id = '$cp_id' AND reg_id != ''";
        $db_where .= " AND us_level < 4";

        $sch_s_date = $_GET['sch_s_date'];
        $sch_e_date = $_GET['sch_e_date'];
        if ($sch_e_date && $sch_s_date) {
            $db_where .= " AND reg_time >= '$sch_s_date 00:00:00' AND reg_time <= '$sch_e_date 23:59:59'";
        }
        $sch_vs_ip = $_GET['sch_vs_ip'];
        if ($sch_vs_ip) {
            $db_where .= " AND vs_ip = '$sch_vs_ip'";
        }
        $db_where .= " GROUP BY vs_ip";
        $db_order = "ORDER BY reg_time DESC";
        $list = Db::select('tbl_visit', "*", $db_where, $db_order, "");

        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);

        return $this->convertList($list);
    }

    /**
     * 월별 접속 IP 현황
     * @return array
     */
    public function selectListIpMonth($list_mode = '')
    {
        $cnt_total = $this->countTotalIpMonth($list_mode);
        $this->set('cnt_total', $cnt_total);
        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');
        $db_where = $this->get('db_where');

        if ($list_mode == 'ip_check') {
            if ($_GET['ip_arr']) {
                $ip_arr = $_GET['ip_arr'];
            } else {
                $ip_arr = Session::getSession('duplicate_us');
            }
            $ip_arr = explode('|', $ip_arr);
            $ip_in = '';
            foreach ($ip_arr as $key) {
                if ($ip_in) {
                    $ip_in .= ",'" . $key . "'";
                } else {
                    $ip_in .= "'" . $key . "'";
                }
            }
            //echo $ip_in;
            $db_where .= " AND vs_ip IN ($ip_in)";
        }
        $db_having = '';
        if ($list_mode == 'company') {
            $db_having = "GROUP BY cp_id";
        } elseif ($list_mode == 'ip' || $list_mode == 'ip_check') {
            $db_having = "GROUP BY vs_ip";
        }
        if ($list_mode == 'total') {
            $db_limit = '';
        } else {
            $page = $this->get('page');
            $cnt_rows = $this->get('cnt_rows');
            $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        }
        $db_order = $this->makeDbOrder();
        //echo 'list_mode: ' . $list_mode;
        //echo $select_columns . ' ' . $db_where . ' ' . $db_having . ' ' . $db_order;
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);

        return $this->convertList($list);
    }

    /**
     * 목록 집계
     * @return int
     */
    public function countTotalIpMonth($list_mode = '')
    {
        $this->initSelect();
        $select_table = $this->get('select_table');
        $db_where = $this->makeDbWhere();

        if ($list_mode == 'ip_check') {
            if ($_GET['ip_arr']) {
                $ip_arr = $_GET['ip_arr'];
            } else {
                $ip_arr = Session::getSession('duplicate_us');
            }
            $ip_arr = explode('|', $ip_arr);
            $ip_in = '';
            foreach ($ip_arr as $key) {
                if ($ip_in) {
                    $ip_in .= ",'" . $key . "'";
                } else {
                    $ip_in .= "'" . $key . "'";
                }
            }
            $db_where .= " AND vs_ip IN ($ip_in)";
        }
        $group_column = $this->get('group_column');
        $db_having = $this->makeDbHaving();
        if ($list_mode == 'company') {
            $group_column = 'cp_id';
            $db_having = "GROUP BY cp_id HAVING cp_id != ''";
        } elseif ($list_mode == 'ip' || $list_mode == 'ip_check') {
            $group_column = 'vs_ip';
            $db_having = "GROUP BY vs_ip HAVING vs_ip != ''";
        }
        $cnt_total = Db::selectCount($select_table, $db_where, $group_column, $db_having);

        return $cnt_total;
    }
}
