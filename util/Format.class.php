<?php
/**
 * 포맷 유틸리티 클래스
 * @file    Format.class.php
 * @author  Alpha-Edu
 * @package util
 */

namespace sFramework;

class Format
{
    /**
     * XSS 필터링
     * @param $str
     * @return string
     */
    public static function filterXss($str)
    {
        global $oFilter;
        if (!isset($oFilter)) {
            return $str;
        }

        $str = str_replace('<br>', '<br />', $str);
        $str = stripslashes($str);
        $str = $oFilter->purify($str);
        return addslashes($str);
    }

    /**
     * 빈값 없이 리턴
     * @param $str
     * @param string $null
     * @return string
     */
    public static function getWithoutNull($str, $null = '-')
    {
        if (!$str || $str == '--' || $str == '0000-00-00' || $str == '0000-00-00 00:00:00') {
            $str = $null;
        }
        return $str;
    }

    /**
     * 텍스트 자르기
     * @param $str
     * @param $len
     * @param string $suffix
     * @return string
     */
    public static function cutString($str, $len, $suffix = '..')
    {
        $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
        $str_len = count($arr_str);

        if ($str_len >= $len) {
            $slice_str = array_slice($arr_str, 0, $len);
            $str = join("", $slice_str);

            $result = $str . ($str_len > $len ? $suffix : '');
        } else {
            $str = join("", $arr_str);
            $result = $str;
        }

        return $result;
    }

    /**
     * 마스킹 처리된 문자열 리턴
     * @param $str
     * @param int $term
     * @param string $mask
     * @return string
     */
    public static function maskString($str, $term = 0, $mask = '*')
    {
        $str_len = strlen($str);
        $new_str = '';
        for ($i = 0; $i < $str_len; $i++) {
            $chr = substr($str, $i, 1);
            if ($i == 0 || $i == $str_len - 1 || $chr == '.' || $chr == '-' || ($term > 0 && $i % $term == 0)) {
                $new_str .= $chr;
            } else {
                $new_str .= $mask;
            }
        }

        return $new_str;
    }

    /**
     * 숫자를 보기 좋게 출력
     * @param $val
     * @param int $point
     * @return int|string
     */
    public static function beautifyNumber($val, $point = 0)
    {
        try {
            $result = number_format($val, $point);
        } catch (Exception $e) {
            $result = 0;
        }

        return $result;
    }

    /**
     * 일시를 보기 좋게 출력
     * @param $date_time
     * @param bool $flag_timestamp
     * @param bool $flag_abbrev
     * @return string
     */
    public static function beautifyDateTime($date_time, $flag_timestamp = false, $flag_abbrev = false)
    {
        if (!$flag_timestamp) {
            $date_time = strtotime($date_time);
        }

        if ($flag_abbrev) {
            $result = date('Y.m.d', $date_time);

            $week = date('w', $date_time);
            $week_arr = explode(',', '일,월,화,수,목,금,토');
            $result .= ' (' . $week_arr[$week] . ') ';

            $result .= date('A h:i', $date_time);

        } else {
            $result = date('Y년 m월 d일', $date_time);

            $week = date('w', $date_time);
            $week_arr = explode(',', '일,월,화,수,목,금,토');
            $result .= ' (' . $week_arr[$week] . ') ';

            $result .= date('A h시 i분', $date_time);
        }

        return $result;
    }

    /**
     * 마감일 출력 포맷 yllee 180906
     * @param $date_time
     * @return string
     */
    public static function beautifyEndDate($date_time)
    {
        $date_time = strtotime($date_time);

        $result = date('n.j', $date_time);

        $week = date('w', $date_time);
        $week_arr = explode(',', '일,월,화,수,목,금,토');
        $result .= '(' . $week_arr[$week] . ')';

        return $result;
    }

    /**
     * 파일 용량을 보기 좋게 출력
     * @param $file_size
     * @return null|string
     */
    public static function beautifyFileSize($file_size)
    {
        $bf_size = null;
        $unit_arr = explode(',', ',k,m,g,t');
        for ($i = 0; $i < count($unit_arr); $i++) {
            if ($file_size < 1024) {
                $bf_size = $file_size . $unit_arr[$i];
                break;
            }
            $file_size = round($file_size * 10 / 1024) / 10;
            if ($i < 2) {
                $file_size = round($file_size);
            }
        }

        return $bf_size;
    }

    /**
     * 문자열 암호화
     * @param $str
     * @return string
     */
    public static function encryptString($str)
    {
        // 현 LMS 회원 비밀번호 암호화와 동일하게 세팅 yllee, silva 200818
        //$str = hash('sha256', 'sFramework_' . $str, true);
        $str = hash('sha256', $str);
        //$str = base64_encode($str);

        return $str;
    }

    /**
     * 한글 1글자의 유니코드를 반환
     * @param $ch
     * @return bool|int|mixed
     */
    public static function getUnicode($ch)
    {
        // PHP 7.4부터는 배열과 문자열에 대한 오프셋 접근을 대괄호 []로만 해야 하며, 중괄호 {}로 배열이나 문자열을 참조할 수 없게 되었음.
        $n = ord($ch[0]);

        if ($n < 128) {
            return $n;
        }

        if ($n < 192 || $n > 253) {
            return false;
        }

        $arr = array(1 => 192,
            2 => 224,
            3 => 240,
            4 => 248,
            5 => 252,
        );

        foreach ($arr as $key => $val) {
            if ($n >= $val) {
                // 중괄호 {} -> 대괄호 []
                $char[] = ord($ch[$key]) - 128;
                $range = $val;
            } else {
                break;
            }
        }

        $retval = ($n - $range) * pow(64, count($char));

        foreach ($char as $key => $val) {
            $pow = count($char) - ($key + 1);
            $retval += $val * pow(64, $pow);
        }

        return $retval;
    }

    /**
     * 마지막 종성이 있는지 검사
     * @param $str
     * @return int
     */
    public static function checkLastFinalSound($str)
    {
        $str = mb_convert_encoding($str, 'UTF-16BE', 'UTF-8');
        $str = str_split(substr($str, strlen($str) - 2));
        $code_point = (ord($str[0]) * 256) + ord($str[1]);
        if ($code_point < 44032 || $code_point > 55203) {
            return 0;
        }
        return ($code_point - 44032) % 28;
    }

    /**
     * 난수 문자열 생성
     * @param int $len
     * @param int $str_type
     * @param int $src_type
     * @return string
     */
    public static function makeRandString($len = 6, $str_type = 0, $src_type = 1)
    {
        srand((double)microtime() * 1000000);
        $src = '0123456789';
        if ($str_type > 0) {
            $src .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($src_type > 1) {
            $src .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $src_arr = str_split($src);
        $last_idx = count($src_arr) - 1;

        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $src_arr[mt_rand(0, $last_idx)];
        }

        return $str;
    }

    public static function encrypt($str, $secret_key='sFramework_', $secret_iv='sFramework_')
    {
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 32);

        return str_replace("=", "", base64_encode(
                openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv))
        );
    }

    public static function decrypt($str, $secret_key='sFramework_', $secret_iv='sFramework_')
    {
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 32);

        return openssl_decrypt(
            base64_decode($str), "AES-256-CBC", $key, 0, $iv
        );
    }

    // 암호화 함수
    public static function aes_encrypt($plaintext, $password)
    {
        // 보안을 최대화하기 위해 비밀번호를 해싱한다.

        $password = hash('sha256', $password, true);

        // 용량 절감과 보안 향상을 위해 평문을 압축한다.

        $plaintext = gzcompress($plaintext);

        // 초기화 벡터를 생성한다.

        $iv_source = defined('MCRYPT_DEV_URANDOM') ? MCRYPT_DEV_URANDOM : MCRYPT_RAND;
        $iv = mcrypt_create_iv(32, $iv_source);

        // 암호화한다.

        $ciphertext = mcrypt_encrypt('rijndael-256', $password, $plaintext, 'cbc', $iv);

        // 위변조 방지를 위한 HMAC 코드를 생성한다. (encrypt-then-MAC)

        $hmac = hash_hmac('sha256', $ciphertext, $password, true);

        // 암호문, 초기화 벡터, HMAC 코드를 합하여 반환한다.

        return base64_encode($ciphertext . $iv . $hmac);
    }

    public static function aes_decrypt($ciphertext, $password)
    {
        // 초기화 벡터와 HMAC 코드를 암호문에서 분리하고 각각의 길이를 체크한다.

        $ciphertext = @base64_decode($ciphertext, true);
        if ($ciphertext === false) return false;
        $len = strlen($ciphertext);
        if ($len < 64) return false;
        $iv = substr($ciphertext, $len - 64, 32);
        $hmac = substr($ciphertext, $len - 32, 32);
        $ciphertext = substr($ciphertext, 0, $len - 64);

        // 암호화 함수와 같이 비밀번호를 해싱한다.

        $password = hash('sha256', $password, true);

        // HMAC 코드를 사용하여 위변조 여부를 체크한다.

        $hmac_check = hash_hmac('sha256', $ciphertext, $password, true);
        if ($hmac !== $hmac_check) return false;

        // 복호화한다.

        $plaintext = @mcrypt_decrypt('rijndael-256', $password, $ciphertext, 'cbc', $iv);
        if ($plaintext === false) return false;

        // 압축을 해제하여 평문을 얻는다.

        $plaintext = @gzuncompress($plaintext);
        if ($plaintext === false) return false;

        // 이상이 없는 경우 평문을 반환한다.

        return $plaintext;
    }

}
