<?php
/**
 * 환경설정 모듈 클래스
 * @file    Setting.class.php
 * @author  Alpha-Edu
 * @package setting
 */

namespace sFramework;

class Setting extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_setting';
    public static $pk = 'st_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'setting');
        $this->set('module_name', '환경설정');

        // 참조 DB 테이블: 결제정보
        $this->set('setting_payment_table', 'tbl_setting_payment');

        // 관리감독자, 위험성평가 교육일정
        $this->set('setting_safe_table', 'tbl_setting_safe');

        // 검색
        $this->set('search_columns', 'mb_level,mb_no_login');
        $this->set('search_like_arr', array(
            'all' => '통합검색',
            'bl_name' => '이름',
            'bl_subject' => '제목'
        ));
        $this->set('search_date_arr', array(
            'bl_date' => '지급일',
            'reg_date' => '등록일'
        ));
        // 사용 구분
        $this->set('use_arr', array(
            'Y' => '사용'
        ));
        // 과정구분
        $this->set('cs_type_arr', array(
            'owner' => '사업주훈련',
            'tomocard' => '내일배움'
        ));
        // 과정 카테고리
        $this->set('cs_category_arr', array(
            'business' => '경영지원'
        ));
        $this->set('order_arr', array(
            's_time' => '시작일시',
            'e_time' => '종료일시'
        ));
        // 결제 모드
        $this->set('payment_mode_arr', array(
            'S' => '서비스',
            'T' => '테스트'
        ));
        // 결제 모드
        $this->set('tax_type_arr', array(
            'T' => '과세',
            'E' => '비과세',
            'C' => '복합과세'
        ));
        // 결제 모드
        $this->set('product_type_arr', array(
            'D' => '디지털',
            'R' => '실물'
        ));
        // 결제 방식
        $this->set('payment_type_arr', array(
            'D' => '무통장',
            'B' => '가상계좌',
            'C' => '신용카드'
        ));
        // 업종
        $this->set('sector_arr', array(
            'manufacturing' => '제조업',
            'service' => '기타업',
            'construction' => '건설업'
        ));
        // 신청가능현황
        $this->set('state_arr', array(
            'closed' => '마감',
            'open' => '신청가능'
        ));
        // 요일
        $this->set('week_arr', array(
            'Mon' => '월',
            'Tue' => '화',
            'Wed' => '수',
            'Thu' => '목',
            'Fri' => '금',
            'Sat' => '토',
            'Sun' => '일'
        ));
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $update_columns = 'name_eng,name_corporate,cp_number,cp_ceo,cp_tel,cp_fax,cp_email,establishment,cp_zip,cp_address,cp_address2,personal_manager,mail_order_no,select_login,';
        $update_columns .= 'send_sms,domain,captcha_use,captcha_id,auth_hp_code,auth_hp_pw,ipin_code,ipin_pw';
        // OTP 대신 본인인증 적용 DB 필드 추가 yllee 220204
        $update_columns .= ',otp_auth_chk,otp_auth_date,otp_auth_s_time,otp_auth_e_time';
        $target = $_POST['target'];

        if ($target == 'terms') {
            $update_columns = 'necessary,privacy,terms';
        } elseif ($target == 'exam') {
            $update_columns = 'midterm_notice,exam_notice,report_notice,report_standard';
            // 북러닝 유의사항 추가 silva 240318
            $update_columns .= ',book_exam_notice,book_report_notice';
            // 북러닝(환급) 유의사항 추가 yllee 241226
            $update_columns .= ',book_exam_caution,book_report_caution';
        } elseif ($target == 'payment') {
            $update_columns = 'payment_mode,tax_type,product_type,payment_type,shop_id,test_id,pay_course,bank,bank_account,refund_rule,notice,notice_v';
        }

        $this->set('update_columns', $update_columns);
        $this->set('required_arr', array());
        $this->set('return_uri', $_POST['return_uri']);
    }

    protected function convertDetail($data)
    {
        $data = parent::convertDetail($data);
        $us_id = $data['us_id'];
        $user_data = $this->selectUser($us_id);
        $data['us_name'] = $user_data['mb_name'];
        // 튜터 정보
        $tt_id = $data['tt_id'];
        $user_data = $this->selectUser($tt_id);
        $data['tt_name'] = $user_data['mb_name'];

        return $data;
    }

    public function selectUser($us_id)
    {
        if ($us_id) {
            $data_table = $this->get('user_table');
            $db_where = "WHERE mb_id = '$us_id'";
            $data = Db::selectOnce($data_table, "*", $db_where, "");
        } else {
            $data = [];
        }
        return $data;
    }

    public function selectPayment($sp_id)
    {
        $data_table = $this->get('setting_payment_table');
        $db_where = "WHERE sp_id = '$sp_id'";
        $data = Db::selectOnce($data_table, "*", $db_where, "");

        return $data;
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
        if (is_array($del_file_arr)) {
            for ($i = 0; $i < count($del_file_arr); $i++) {
                $this->deleteFile($del_file_arr[$i]);
            }
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
            'uri' => $this->get('return_uri'),
            'msg' => $this->get('success_msg')
        );

        return $result;
    }

    public function updateDataPay()
    {
        $uid = $this->get('uid');

        // 권한 체크
        if (!$uid || !$this->checkUpdateAuth($uid)) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }

        $this->initUpdate();

        $arr = $this->getParameters($this->get('update_columns'), 'post');
        $arr = $this->convertUpdate($arr);

        $result = $this->validateValues($arr);
        if ($result['code'] != 'success') {
            return $result;
        }

        $data_table = 'tbl_setting_payment';
        $pk = 'sp_id';
        unset($arr[$pk]);

        if (Db::updateByArray($data_table, $arr, "WHERE $pk = '$uid'")) {
            $result = $this->postUpdate($arr);
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '수정 과정에서 장애가 발생하였습니다.'
            );
        }

        return $result;
    }

    public function selectSafeList($type)
    {
        $data_table = $this->get('setting_safe_table');
        $db_where = "WHERE type = '$type'";
        $list = Db::select($data_table, "*", $db_where, "", "");

        return $list;
    }

}
