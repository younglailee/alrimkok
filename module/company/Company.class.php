<?php
/**
 * 페이지 모듈 클래스
 * @file    Company.class.php
 * @author  Alpha-Edu
 * @package page
 */

namespace sFramework;

use function str_replace;

class Company extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_company';
    public static $pk = 'cp_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'company');
        $this->set('module_name', '기업');

        $this->set('order_column', 'cp_id');

        // 검색
        $this->set('search_columns', 'cp_name,cp_id,cp_tel,staff_name,cp_type');
        $this->set('search_like_arr', array(
            'all' => '통합검색',
            'cp_name' => '기업명',
            'cp_tel' => '대표번호',
            'staff_name' => '담당자명',
            'cp_id' => '기업코드',
            'cp_number' => '사업자등록번호'
        ));
        $this->set('search_date_arr', array(
            's_date' => '시작일',
            'e_date' => '종료일'
        ));
        $this->set('order_arr', array(
            'cp_id*1' => '기업코드',
            'cp_name' => '기업명',
            'reg_time' => '등록일',
            'partner_name' => '파트너명'
        ));
        $this->set('cp_type_arr', array(
            'priority_support' => '우선지원기업',
            'small_businesses' => '중견기업',
            'major' => '대기업'
        ));
        // 종업원수
        $this->set('cp_count_arr', array(
            '5' => '5인 미만',
            '30' => '30인 미만',
            '50' => '50인 미만',
            '300' => '300인 미만',
            '999' => '300인 이상'
        ));
        // 기업 규모
        $this->set('cp_size_arr', array(
            'L' => '대기업',
            'D' => '중견기업',
            'M' => '중기업',
            'S' => '소기업',
            'C' => '소상공인'
        ));
        // 매출액 규모
        $this->set('cp_revenue_arr', array(
            '100' => '50백만원-100백만원',
            '3000' => '1000백만원-3000백만원',
            '5000' => '5000백만원 이상',
            '9999' => '기타'
        ));
        $this->set('flag_venture_arr', array(
            'Y' => '유',
            'N' => '무'
        ));
        $this->set('flag_research_arr', array(
            'L' => '기업부설연구소',
            'R' => '연구개발전담부서',
            'N' => '없음'
        ));
        $this->set('flag_product_arr', array(
            'Y' => '유',
            'N' => '무'
        ));

        $this->set('max_file', 1);
    }

    protected function initInsert()
    {
        parent::initInsert();

        $this->set('insert_columns', 'cp_id,cp_name,cp_type,cp_ceo,cp_number,cp_tel,cp_fax,cp_zip,cp_address,cp_address2,staff_name,staff_position,staff_email,staff_tax_bill,staff_memo,partner_id,partner_name,cp_edu_money,flag_book,flag_live,cp_cost_num');

        $this->set('required_arr', array(
            'cp_type' => '기업구분',
            'cp_name' => '기업명',
            'cp_ceo' => '대표자명',
            'cp_number' => '사업자등록번호',
            'cp_tel' => '대표번호',
        ));

        if ($_POST['book'] == 'book') {
            $this->set('return_uri', '/webadmin/book/company_list.html');
        }
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $this->set('update_columns', 'cp_id,cp_name,cp_type,cp_ceo,cp_number,cp_tel,cp_fax,cp_zip,cp_address,cp_address2,staff_name,staff_position,staff_email,staff_tax_bill,staff_memo,partner_id,partner_name,cp_edu_money,flag_book,flag_live,cp_cost_num');

        $this->set('required_arr', array(
            'cp_type' => '기업구분',
            'cp_name' => '기업명',
            'cp_ceo' => '대표자명',
            'cp_number' => '사업자등록번호',
            'cp_tel' => '대표번호',
        ));
    }

    public function searchCompanyCode($cp_code)
    {
        $table = $this->get('data_table');

        $where = 'WHERE cp_id=' . $cp_code;

        $data = Db::selectOnce($table, '*', $where, '');

        return $data;
    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);
        //Log::debug($arr);
        $arr['cp_name'] = trim($arr['cp_name']);
        $mb_id = $arr['partner_id'];
        $data = Db::selectOnce('tbl_user', 'mb_name', "WHERE mb_id ='$mb_id' AND mb_level = '6'", '');
        $arr['partner_name'] = $data['mb_name'];

        // 사업자등록번호 숫자만 추출 yllee 240905
        $arr['cp_number'] = str_replace("-", "", $arr['cp_number']);

        return $arr;
    }

    public function searchCompanyNum($cp_number)
    {

        $this->initSelect();
        $select_table = "tbl_company";
        $select_columns = $this->get('select_columns');
        if (!$select_columns) {
            $select_columns = '*';
        }
        $db_where = "where cp_number = '" . $cp_number . "'";

        $result['code'] = 'success';

        $data = Db::selectOnce($select_table, $select_columns, $db_where, '');
        //Log::debug($select_columns);
        //Log::debug($db_where);
        //Log::debug($data);
        $result['data'] = $data;

        // 권한 체크
        if (!$this->checkViewAuth($data)) {
            //Log::debug($data);
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
        }

        return $result;
    }

    // 해당 기업 제외 사업자등록번호 중복 확인 yllee 210205
    public function searchCompanyNumber($cp_number, $cp_id)
    {
        $this->initSelect();
        $select_table = "tbl_company";
        $select_columns = $this->get('select_columns');
        if (!$select_columns) {
            $select_columns = '*';
        }
        $db_where = "WHERE cp_number = '" . $cp_number . "'";
        if ($cp_id) {
            $db_where = " AND cp_id != '$cp_id'";
        }
        $result['code'] = 'success';
        $data = Db::selectOnce($select_table, $select_columns, $db_where, '');
        //Log::debug($select_columns);
        //Log::debug($db_where);
        //Log::debug($data);
        $result['data'] = $data;
        // 권한 체크
        if (!$this->checkViewAuth($data)) {
            //Log::debug($data);
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
        }
        return $result;
    }

    public function resetPass()
    {
        $cp_id = $_GET['cp_id'];

        $result = array();

        $arr['mb_pw'] = Format::encryptString(1234);
        $arr['mb_pw_time'] = _NOW_DATETIME_;

        if (Db::updateByArray('tbl_user', $arr, "WHERE cp_id = '$cp_id' AND (mb_level = 1 OR mb_level = 2)")) {
            $result['code'] = 'success';
            $result['msg'] = '비밀번호 초기화에 성공하였습니다.';
        } else {
            $result['code'] = 'fail';
            $result['msg'] = '비밀번호 초기화에 실패하였습니다.';
        }

        return $result;
    }

    protected function makeDbWhere()
    {
        $db_where = $this->getDefaultWhere();
        $sch_partner = $_GET['sch_partner'];

        if ($sch_partner) {
            if ($sch_partner == '-') {
                $db_where .= " AND (partner_name IS NULL OR partner_name = '')";
            } else {
                $db_where .= " AND partner_name LIKE '%$sch_partner%'";
            }
        }

        $this->set('db_where', $db_where);

        return $db_where;
    }

    protected function convertUpdate($arr)
    {
        global $member;
        $arr['upt_id'] = $member['mb_id'];
        $arr['upt_time'] = _NOW_DATETIME_;
        //Log::debug($arr);
        $mb_id = $arr['partner_id'];
        $cp_id = $arr['cp_id'];
        $data = Db::selectOnce('tbl_user', 'mb_name', "WHERE mb_id ='$mb_id' AND mb_level = '6'", '');
        Db::update('tbl_batch', "partner_id = '$mb_id'", "WHERE bt_company LIKE ('%$cp_id%')");
        $arr['partner_name'] = $data['mb_name'];

        // 사업자등록번호 숫자만 추출 yllee 240905
        $arr['cp_number'] = str_replace("-", "", $arr['cp_number']);

        return $arr;
    }
}
