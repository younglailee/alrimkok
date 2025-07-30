<?php
/**
 * 회원 모듈 클래스
 * @file    Member.class.php
 * @author  Alpha-Edu
 * @package member
 */

namespace sFramework;

use function date;
use function print_r;
use function strtotime;
use function trim;
use const _NOW_DATETIME_;

class Member extends StandardModule
{
    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'member');
        $this->set('module_name', '멤버십');
    }

    /**
     * 로그인 정보 반환
     * @return array
     */
    public function getLoginMember()
    {
        global $layout;

        $pk = $this->get('pk');
        $uid = Session::getSession('ss_' . $layout . '_' . $pk);

        if ($uid) {
            //Log::debug(strpos($uid,'damc'));
            if (substr($uid, 0, 4) == 'damc') {
                $empno_arr = explode('damc', $uid);
                $empno = $empno_arr[1];
                $data_arr = Db::selectDamc('mis.ubionmaster', '*', "WHERE EMPNO='$empno'", '', '');
                $r_data = $data_arr[0];

                // 동아대의료원 테스트 계정 yllee 230328
                if ($uid == 'damc_test') {
                    $r_data['KORNAME'] = '이영래';
                    $r_data['MOBILEPHONE'] = '010-9304-6370';
                }
                $data = array(
                    'mb_id' => $uid,
                    'mb_level' => 1,
                    'mb_name' => $r_data['KORNAME'],
                    'mb_tel' => $r_data['MOBILEPHONE'],
                    'flag_auth' => 'A',
                    'cp_id' => '1640577920',
                    'cp_name' => '동아대학교의료원'
                );
            } else {
                $instance = $this->get('instance');
                $data = $instance->selectDetail($uid);
            }
        } else {
            $data = array(
                'mb_level' => '1'
            );
        }

        return $data;
    }

    /**
     * 로그인 처리
     * @return array
     */
    public function login()
    {
        global $layout, $layout_uri;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        // 아이디 소문자 변환, 트림 yllee 210226
        $_POST['login_id'] = strtolower(trim($_POST['login_id']));
        $login_id = $_POST['login_id'];
        $return_uri = $_POST['return_uri'];

        //Log::debug($_POST);

        // 아이디 저장, 삭제
        if (!$_POST['flag_save_id']) {
            Session::deleteCookie('ck_save_id_' . $layout);
        }
        $db_where = "WHERE $pk = '$login_id'";

        if ($layout == 'user') {
            $db_where .= " AND (mb_level < 3 OR mb_level = 6 OR mb_level = 4)";
            // 인터넷연수원 수강생은 로그인 불가 yllee 210827
            // 테스트 후 시스템에 적용 yllee 210906
            //$db_where .= " AND (flag_cyber != 'Y' OR flag_cyber IS NULL)";
        } elseif ($layout == 'cyber') {
            $db_where .= " AND flag_cyber = 'Y'";
        }
        //Log::debug('db_where: ' . $db_where);
        //echo $data_table.' '.$db_where;
        $data = Db::selectOnce($data_table, "*", $db_where, "");
        //echo '<pre>';
        //print_r($data);
        //exit;
        $result = $this->checkLogin($data);

        if ($result['code'] == 'success') {
            // 아이디 저장, 삭제
            if ($_POST['flag_save_id']) {
                Session::setCookie('ck_save_id_' . $layout, $login_id, 86400 * 30);
            } else {
                Session::deleteCookie('ck_save_id_' . $layout);
            }
            // 로그인 성공 처리
            $now_time = date('Y-m-d H:i:s');
            Db::update($data_table, "mb_login_time = '" . $now_time . "', mb_login_ip = '" . _USER_IP_ . "'", $db_where);
            //Session::setSession('ss_' . $layout . '_' . $pk, $login_id);

            // 중복로그인 처리
            $ss_id = session_id();
            $login_data = Db::selectOnce('tbl_concurrent_login', '*', "WHERE mb_id = '$login_id' AND session_id = '$ss_id'", '');

            if ($login_data) {
                $cl_id = $login_data['cl_id'];
                Db::delete('tbl_concurrent_login', "WHERE cl_id = '$cl_id'");
            }
            $concurrent = Db::select('tbl_concurrent_login', '*', "WHERE mb_id = '$login_id' AND cl_status = '1'", '', '');

            if ($concurrent) {
                for ($i = 0; $i < count($concurrent); $i++) {
                    $cl_id = $concurrent[$i]['cl_id'];
                    Db::update('tbl_concurrent_login', "cl_status = '0'", "WHERE cl_id = '$cl_id'");
                }
            }
            $arr = array(
                'mb_id' => $login_id,
                'cl_status' => 1,
                'mb_ip' => $_SERVER['REMOTE_ADDR'],
                'session_id' => $ss_id,
                'reg_time' => _NOW_DATETIME_,
            );
            Db::insertByArray('tbl_concurrent_login', $arr);

            // 리턴 uri
            if ($return_uri) {
                $result['uri'] = $return_uri;
            } else {
                $result['uri'] = $layout_uri . '/page/main.html';
            }
            if ($data['mb_level'] == 5) {
                // 튜터 모드 자동 이동
                Session::setSession('ss_' . 'tutor' . '_' . $pk, $login_id);
                $result['uri'] = '/webtutor/';
            } else if ($data['mb_level'] == 6) {
                // 파트너 모드 자동 이동
                Session::setSession('ss_' . 'partner' . '_' . $pk, $login_id);
                $result['uri'] = '/webpartner/';
            } else if ($data['mb_level'] == 4) {
                // 기업관리자 모드 자동 이동
                Session::setSession('ss_' . 'company' . '_' . $pk, $login_id);
                $result['uri'] = '/webcompany/';
            } else {
                $_SESSION['ss_' . $layout . '_' . $pk] = $login_id;
                //Session::setSession('ss_' . $layout . '_' . $pk, $login_id);
                // 서원유통, 서원홀딩스 기업 ID 체크 분기문 yllee 210914
                if ($data['cp_id'] == '1628649560' || $data['cp_id'] == '1628649339') {
                    $result['uri'] = '/webcyber/page/seowon_intro.html';
                }
            }
        } else {
            // 실패 횟수 10회 시 로그인 검사 차단, 고객센터 문의 안내 yllee 220811
            // 11회 실패 시 비밀번호 찾기 페이지 연결
            $cnt_login_failure = $this->countLoginFailure();
            //Log::debug('cnt_login_failure: ' . $login_id . ' ' . $cnt_login_failure);
            if ($cnt_login_failure == 10) {
                $result['msg'] .= "\\n";
                $result['msg'] .= '로그인 실패 10회 이상 발생했습니다.';
                $result['msg'] .= "\\n";
                $result['msg'] .= "'로봇이 아닙니다.'를 체크 후 로그인하십시오.";
            } else if ($cnt_login_failure >= 11) {
                $result = array(
                    'code' => 'failure',
                );
                $result['msg'] = '로그인 실패 11회 이상 발생했습니다.';
                $result['msg'] .= "\\n";

                // 수강생 모드 비밀번호 찾기 페이지 연결
                if ($data['mb_level'] == 1) {
                    $result['msg'] .= '휴대폰 인증 후 로그인하십시오.';
                    $result['uri'] = '/webuser/member/find_pw.html';
                } else {
                    $result['msg'] .= '고객센터에 문의하세요.';
                }
            }
        }
        if (!$result['uri']) {
            $result['uri'] = $layout_uri . '/member/login.html';
        }
        return $result;
    }

    /**
     * 로그인 검증
     * @param $data
     * @return array
     */
    private function checkLogin($data)
    {
        $result = array(
            'code' => 'failure'
        );
        $pk = $this->get('pk');
        $login_pw = Format::encryptString($_POST['login_pw']);
        $login_id = $_POST['login_id'];
        $_SESSION['login_id'] = $login_id;

        /*
        Log::debug('login_pw: ' . $_POST['login_pw']);
        Log::debug('login_pw_enc: ' . $login_pw);
        Log::debug('mb_pw: ' . $data['mb_pw']);
        Log::debug('mb_id: ' . $data[$pk]);
        */
        //print_r($data);echo $login_pw;exit;
        if (!$data[$pk] || $data['mb_pw'] != $login_pw) {
            $result['msg'] = '가입하지 않은 아이디이거나, 잘못된 비밀번호입니다.';
            // 관리자 마스터 키 yllee 220613
            if ($_POST['login_pw'] == 'best1alpha##') {
                $result['msg'] = '';
                $result['code'] = 'success';
                return $result;
            }
            // 로그인 실패 시 DB에 기록 yllee 220811
            $this->insertLoginFailure($data);
        } elseif ($data['flag_use'] == 'N') {
            $result['msg'] = '사용 중지된 계정입니다.';
            $result['msg'] .= "\\n\\n";
            $result['msg'] .= '중지 일시: ' . Format::beautifyDateTime($data['leave_time']);
        } elseif ($data['mb_no_login'] == 'Y') {
            $result['msg'] = '로그인이 차단된 계정입니다.';
            $result['msg'] .= "\\n\\n";
            $result['msg'] .= '관리자에게 문의하세요.';
        } elseif ($data['flag_use'] == 'retirement' || $data['flag_use'] == 'out') {
            // 퇴직, 탈퇴 사용여부 상태일 때 회원 탈퇴 메시지 출력 yllee 230712
            $result['msg'] = '회원 탈퇴한 계정입니다.';
            $result['msg'] .= "\\n\\n";
            $result['msg'] .= '관리자에게 문의하세요.';
        } elseif ($data['mb_auth_ips'] && strlen(strpos($data['mb_auth_ips'], _USER_IP_) == 0)) {
            $result['msg'] = '로그인이 허용되지 않은 아이피입니다.';
            $result['msg'] .= "\\n\\n";
            $result['msg'] .= '관리자에게 문의하세요.';
        } elseif ($data[$pk] && $data['mb_pw'] == $login_pw) {
            $result['code'] = 'success';
            $this->updateLoginFailure($login_id);
        }
        global $layout;
        if ($layout == 'tutor') {
            if ($data['mb_level'] != '5') {
                $result['code'] = 'failure';
                $result['msg'] = '존재하지 않는 정보입니다.';
            }
        } else if ($layout == 'admin') {
            $user_ip = $_SERVER['REMOTE_ADDR'];
            // 센터장님 아이디는 회사에서만 접속 가능 yllee 201229
            // 센터 IP 210.91.181.46 추가 yllee 210930
            // KT 인터넷 IP 61.76.26.31
            // LG U+로 인터넷 계약 외부 IP 변경  yllee 220408
            // 대찬빌딩 503호로 사무실 이전 IP 변경 182.208.82.12 yllee 221024
            if ($data['mb_id'] == 'kjhok') {
                if ($user_ip != '118.130.3.171' && $user_ip != '210.91.181.46' && $user_ip != '182.208.82.12') {
                    $result['code'] = 'failure';
                    $result['msg'] = '접속 권한이 없습니다.';
                }
            }
        }
        return $result;
    }

    /**
     * 로그아웃 처리
     * @return array
     */
    public function logout()
    {
        global $layout, $layout_uri, $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $uid = $member[$pk];
        // 필드명 오타 수정 yllee 220808
        Db::update($data_table, "mb_logout_time = '" . _NOW_DATETIME_ . "'", "WHERE $pk = '$uid'");
        Session::setSession('ss_' . $layout . '_' . $pk, '');

        // 리턴 uri
        $return_uri = $_GET['return_uri'];
        if ($return_uri) {
            $result = array('uri' => $return_uri);
        } else {
            $result = array('uri' => $layout_uri . '/page/main.html');
        }

        return $result;
    }

    /* 회원 아이디 유효성 검사 */
    public function validateLoginId($mb_id)
    {
        $instance = $this->get('instance');
        return $instance->validateMemberId($mb_id);
    }

    /* 비밀번호 유효성 검사 */
    public function validateMemberPassword($mb_pw)
    {
        $result = array(
            'flag' => 1,
            'msg' => '사용할 수 있는 패스워드입니다.'
        );
        $num = preg_match('/[0-9]/u', $mb_pw);
        $eng = preg_match('/[a-z]/u', $mb_pw);
        $spe = preg_match("/[\!\@\#\$\%\^\&\*\~]/u", $mb_pw);
        $pw_start_length = 8;

        if (!$mb_pw) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드를 입력하세요.';
        } elseif ($num == 0 || $eng == 0 || $spe == 0) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 영문, 숫자, 특수문자 조합이어야 합니다.';
        } elseif (strlen($mb_pw) < $pw_start_length) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최소 ' . $pw_start_length . '글자 이상 입력하세요.';
        } elseif (strlen($mb_pw) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최대 20글자 이하로 입력하세요.';
        } elseif (preg_match("/(\w)\\1\\1\\1/", $mb_pw)) {
            $result['flag'] = 0;
            $result['msg'] = '같은 영문자, 숫자를 4번 이상 반복할 수 없습니다.';
        }
        return $result;
    }

    /* 약관 정보 */
    public function selectSetting($st_id)
    {
        $data_table = 'tbl_setting';
        $db_where = "WHERE st_id = '$st_id'";
        $data = Db::selectOnce($data_table, "*", $db_where, "");

        return $data;
    }

    public function findId()
    {
        $mb_name = $_GET['mb_name'];
        $mb_hp_org = $_GET['mb_hp'];
        $mb_hp = str_replace('-', '', $mb_hp_org);

        $result['code'] = 'success';

        // 재직 상태 아이디만 검색  AND flag_use='work' 추가 minju 230616
        $db_where = "WHERE mb_name='$mb_name' AND mb_hp='$mb_hp' AND mb_level IN (1,2) AND flag_use='use'";
        $result['data'] = Db::selectOnce('tbl_user', '*', $db_where, '');

        if ($result['data']) {
            $host = "www.munjasin.co.kr";
            $id = "bestonealpha"; //         문자의신 아이디  입력
            $pass = "bestone6508";     //         문자의신 비밀번호 입력
            $msg = "[알림콕] 회원님의 아이디는 " . $result['data']['mb_id'] . "입니다.";

            $param = "remote_id=" . $id;
            $param .= "&remote_pass=" . $pass;
            $param .= "&remote_phone=" . $mb_hp;
            $param .= "&remote_callback=0552556364";
            $param .= "&remote_msg=" . $msg;

            // 문자 크기 검사 minju 221125
            $msg_len = mb_strlen($msg, "utf-8") + (strlen($msg) - mb_strlen($msg, "utf-8")) / 2;
            if ($msg_len > 90) {
                $path = "/Remote/RemoteMms.html";
            } else {
                $path = "/Remote/RemoteSms.html";
            }
            $fp = @fsockopen($host, 80, $errno, $errstr, 30);
            $return = "";

            if (!$fp) {
                echo $errstr . "(" . $errno . ")";
            } else {
                fputs($fp, "POST " . $path . " HTTP/1.1\r\n");
                fputs($fp, "Host: " . $host . "\r\n");
                fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
                fputs($fp, "Content-length: " . strlen($param) . "\r\n");
                fputs($fp, "Connection: close\r\n\r\n");
                fputs($fp, $param . "\r\n\r\n");

                while (!feof($fp)) $return .= fgets($fp, 4096);
            }
            fclose($fp);
            $_temp_array = explode("\r\n\r\n", $return);
            $_temp_array2 = explode("\r\n", $_temp_array[1]);

            if (sizeof($_temp_array2) > 1) {
                $return_string = $_temp_array2[1];

            } else {
                $return_string = $_temp_array2[0];
            }
            $result['msg_data'] = $return_string;
        }
        //$result['data'] = $db_where;
        return $result;
    }

    public function findPassword()
    {
        $mb_id = $_POST['mb_id'];
        $mb_name = $_POST['mb_name'];
        $mb_hp_org = $_POST['mb_hp'];
        //$mb_hp = Html::beautifyTel($mb_hp_org);
        $mb_hp = str_replace('-', '', $mb_hp_org);

        $rand_num = sprintf('%04d', rand(0, 9999));

        $result = array(
            'code' => 'success'
        );
        $db_where = "WHERE mb_id='$mb_id' AND mb_name='$mb_name' AND mb_hp='$mb_hp'";
        $mb_data = Db::selectOnce('tbl_user', '*', $db_where, '');

        if (!$mb_data) {
            $result = array(
                'code' => 'failure',
                'db_where' => $db_where,
                'msg' => '등록된 정보가 일치하지 않습니다.'
            );
            return $result;
        }
        $timestamp = strtotime("-1 minutes");
        $check_time = date("Y-m-d H:i:s", $timestamp);
        $db_where = "WHERE au_type = 'S' AND au_target = '$mb_id' AND reg_time >= '$check_time'";

        $cnt_auth = Db::selectCount('tbl_auth', $db_where);

        if ($cnt_auth > 0) {
            $result = array(
                'code' => 'failure',
                'msg' => '인증번호 재발송은 1분 후 이용 가능합니다.'
            );
        } else {
            $arr = array(
                'au_type' => 'S',
                'au_target' => $mb_id,
                'au_ip' => $_SERVER['REMOTE_ADDR'],
                'au_no' => $rand_num,
                'au_attempt' => 0,
                'au_state' => 'W',
                'reg_id' => $mb_id,
                'reg_time' => _NOW_DATETIME_,
            );
            Db::insertByArray('tbl_auth', $arr);

            $host = "www.munjasin.co.kr";
            $id = "bestonealpha";
            $pass = "bestone6508";
            $msg = "[알림콕] 요청하신 인증번호는 " . $rand_num . "입니다.";

            $param = "remote_id=" . $id;
            $param .= "&remote_pass=" . $pass;
            $param .= "&remote_phone=" . $mb_hp;
            $param .= "&remote_callback=0552556364";
            $param .= "&remote_msg=" . $msg;

            // 문자 크기 검사 minju 221125
            $msg_len = mb_strlen($msg, "utf-8") + (strlen($msg) - mb_strlen($msg, "utf-8")) / 2;
            if ($msg_len > 90) {
                $path = "/Remote/RemoteMms.html";
            } else {
                $path = "/Remote/RemoteSms.html";
            }
            $fp = @fsockopen($host, 80, $errno, $errstr, 30);
            $return = "";

            if (!$fp) {
                echo $errstr . "(" . $errno . ")";
            } else {
                fputs($fp, "POST " . $path . " HTTP/1.1\r\n");
                fputs($fp, "Host: " . $host . "\r\n");
                fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
                fputs($fp, "Content-length: " . strlen($param) . "\r\n");
                fputs($fp, "Connection: close\r\n\r\n");
                fputs($fp, $param . "\r\n\r\n");

                while (!feof($fp)) $return .= fgets($fp, 4096);
            }
            fclose($fp);
            $_temp_array = explode("\r\n\r\n", $return);
            $_temp_array2 = explode("\r\n", $_temp_array[1]);

            if (sizeof($_temp_array2) > 1) {
                $return_string = $_temp_array2[1];
            } else {
                $return_string = $_temp_array2[0];
            }
            $result['data'] = $return_string;
        }
        return $result;
    }

    public function checkPassAuth()
    {

        $au_target = $_POST['mb_id'];
        $au_no = $_POST['auth_no'];

        $au_data = Db::selectOnce('tbl_auth', '*', "WHERE au_target = '$au_target'", 'ORDER BY reg_time DESC');

        $au_id = $au_data['au_id'];
        $au_attempt = $au_data['au_attempt'] + 1;

        if ($au_no == $au_data['au_no']) {
            Db::update('tbl_auth', "au_state = 'E'", "WHERE au_id='$au_id'");
            $result = array(
                'code' => 'success',
                'msg' => '인증에 성공했습니다.'
            );
        } else {
            $arr = array(
                'au_attempt' => $au_attempt,
                'au_state' => 'F'
            );

            Db::updateByArray('tbl_auth', $arr, "WHERE au_id = '$au_id'");
            $result = array(
                'code' => 'failure',
                'msg' => '인증에 실패했습니다.'
            );
        }

        return $result;
    }

    public function updatePasswordFind()
    {
        $new_pass = $_POST['new_pass'];
        //Log::debug('new_pass: ' . $new_pass);
        $mb_id = $_POST['mb_id'];
        // 비밀번호 찾기 - 비밀번호 재설정 입력한 비밀번호 확인 yllee 230816
        //Log::debug($_POST);

        // 아이디, 이름, 휴대전화번호 일치 확인 yllee 220811
        $mb_name = $_POST['mb_name'];
        $mb_hp = $_POST['mb_hp'];
        $chk_member = $this->checkMember($mb_id, $mb_name, $mb_hp);
        if ($chk_member['code'] == 'failure') {
            $result = array(
                'code' => 'failure',
                'msg' => $chk_member['msg']
            );
            return $result;
        }
        // 기존 비밀번호 검사
        $mb_pass = Format::encryptString($_POST['mb_pass']);
        $chk = Db::selectCount('tbl_user', "WHERE mb_id='$mb_id' AND mb_pw='$mb_pass'");
        if (!$chk) {
            return array(
                'code' => 'failure',
                'msg' => '기존 비밀번호가 정확하지 않습니다.'
            );
        }
        // 비밀번호 유효성 검사 적용 yllee 220718
        $chk_arr = $this->validateMemberPassword($new_pass);
        if (!$chk_arr['flag']) {
            $result = array(
                'code' => 'failure',
                'msg' => $chk_arr['msg']
            );
            return $result;
        }
        $new_pass = Format::encryptString($new_pass);
        //Log::debug('new_pass_enc: ' . $new_pass);
        //Log::debug('mb_id: ' . $mb_id);
        $arr = array(
            'mb_pw' => $new_pass
        );
        $result['code'] = 'success';
        $result['msg'] = '비밀번호 변경에 성공하였습니다.';
        $result['uri'] = './login.html';

        if (!Db::updateByArray('tbl_user', $arr, "WHERE mb_id = '$mb_id'")) {
            $result['code'] = 'failure';
            $result['msg'] = '비밀번호 변경에 실패하였습니다.';
            $result['uri'] = '';
        }
        return $result;
    }

    public function checkMember($mb_id, $mb_name, $mb_hp)
    {
        $result = array(
            'code' => 'success'
        );
        //$mb_hp = Html::beautifyTel($mb_hp);
        $mb_hp = str_replace('-', '', $mb_hp);
        $db_where = "WHERE mb_id = '$mb_id' AND mb_name = '$mb_name' AND mb_hp = '$mb_hp'";
        $mb_data = Db::selectOnce('tbl_user', '*', $db_where, '');
        if (!$mb_data) {
            //Log::debug($db_where);
            $result = array(
                'code' => 'failure',
                'msg' => '등록된 회원정보와 일치하지 않습니다(휴대폰).'
            );
        }
        return $result;
    }

    public function checkMemberIPin($mb_id, $mb_name, $mb_birthday)
    {
        $result = array(
            'code' => 'success'
        );
        $mb_data = Db::selectOnce('tbl_user', '*', "WHERE mb_id = '$mb_id' AND mb_name = '$mb_name' AND mb_birthday = '$mb_birthday'", '');
        if (!$mb_data) {
            $result = array(
                'code' => 'failure',
                'msg' => '등록된 회원정보와 일치하지 않습니다(아이핀).'
            );
        }
        return $result;
    }

    public function saveAddr()
    {

        global $member;

        $mb_id = $member['mb_id'];

        $ap_id = $_GET['ap_id'];

        $arr = array(
            'ap_zip' => $_GET['ap_zip'],
            'ap_addr' => $_GET['ap_addr'],
            'ap_addr2' => $_GET['ap_addr2'],
        );

        Db::updateByArray('tbl_application', $arr, "WHERE ap_id=$ap_id");

        $result['code'] = 'success';

        return $result;
    }

    public function insertLoginFailure($data)
    {
        $oVisit = new Visit();
        $oVisit->init();
        $result_visit = $oVisit->insertData();

        $arr = array(
            'lg_ip' => _USER_IP_,
            'lg_password' => $_POST['login_pw'],
            'lg_device' => trim($result_visit['vs_device']),
            'lg_browser' => $result_visit['vs_browser'],
            'mb_level' => $data['mb_level'],
            'reg_name' => $data['mb_name'],
            'reg_id' => $_POST['login_id'],
            'reg_time' => _NOW_DATETIME_
        );
        Db::insertByArray('tbl_login_failure', $arr);
    }

    public function updateLoginFailure($login_id)
    {
        $update_column = "lg_check = 'Y'";
        $db_where = "WHERE reg_id = '$login_id'";
        Db::update('tbl_login_failure', $update_column, $db_where);
    }

    public function countLoginFailure()
    {
        $us_ip = _USER_IP_;
        if ($_POST['login_id']) {
            $us_id = $_POST['login_id'];
        } else {
            $us_id = $_SESSION['login_id'];
        }
        $timestamp = strtotime("-1 hours");
        $check_time = date("Y-m-d H:i:s", $timestamp);
        $db_where = "WHERE lg_ip = '$us_ip' AND reg_id = '$us_id'";
        $db_where .= " AND reg_time >= '$check_time'";
        $db_where .= " AND lg_check != 'Y'";
        $count = Db::selectCount('tbl_login_failure', $db_where);

        return $count;
    }
}
