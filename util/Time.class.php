<?php
/**
 * 시간 유틸리티 클래스
 * @file    Time.class.php
 * @author  Alpha-Edu
 * @package util
 */

namespace sFramework;

class Time
{
    /**
     * 시간 코드 생성
     * @return string
     */
    public static function makeTimeCode()
    {
        $str = date('ymhHis') . substr(microtime(), 2, 3);

        $code = chr(intval(substr($str, 0, 2)) + 65);  // 년
        $code .= chr(intval(substr($str, 2, 2)) + 65);  // 월
        $code .= substr($str, 4, 2);                    // 일
        $code .= chr(intval(substr($str, 6, 2)) + 65);  // 시
        $code .= substr($str, 8, 1);                    // 분
        $code .= chr(intval(substr($str, 9, 1)) + 65);
        $code .= chr(intval(substr($str, 10, 1)) + 65); // 초
        $code .= substr($str, 11, 1);
        $code .= chr(intval(substr($str, 12, 1)) + 65); // 밀리초
        $code .= substr($str, 13, 2);

        // 난수
        $code .= chr(rand(65, 90));
        $code .= chr(rand(65, 90));
        $code .= rand(0, 9);
        $code .= chr(rand(65, 90));
        $code .= chr(rand(65, 90));
        $code .= rand(0, 9);
        $code .= chr(rand(65, 90));
        $code .= chr(rand(65, 90));

        return $code;
    }

    /**
     * 시간 해시 생성
     * @return string
     */
    public static function makeTimeHash()
    {
        return md5(date('ymhHis') . substr(microtime(), 2, 3) . '_' . strval(rand(0, 99999999)));
    }

    /**
     * 전후 날짜 반환
     * @param $days
     * @param null $date
     * @return string
     */
    public static function getAroundDate($days, $date = null)
    {
        if (!$date) {
            $date = _NOW_DATE_;
        }
        $time = strtotime($date);
        $y = date('Y', $time);
        $m = date('n', $time);
        $d = date('j', $time);

        $op = substr($days, 0, 1);
        if ($op != '+' && $op != '-') {
            $days = '+' . $days;
        }

        $unit = strtolower(substr($days, -1));
        if ($unit == 'd' || $unit == 'm' || $unit == 'y') {
            $days = substr($days, 0, strlen($days) - 1);
        } else {
            $unit = 'd';
        }

        $days = intval($days);
        if ($unit == 'd') {
            $d += $days;
        } elseif ($unit == 'm') {
            $m += $days;
        } elseif ($unit == 'y') {
            $y += $days;
        }

        $around_time = mktime(0, 0, 0, $m, $d, $y);

        return date('Y-m-d', $around_time);
    }

    /**
     * 시간 배열 생성
     * @param string $s_time
     * @param string $e_time
     * @param int $gap
     * @return array
     */
    public static function makeHourArray($s_time = '00:00', $e_time = '24:00', $gap = 30)
    {
        $arr = explode(':', $s_time);
        $s_time = $arr[0] * 60 + $arr[1];

        $arr = explode(':', $e_time);
        $e_time = $arr[0] * 60 + $arr[1];

        unset($arr);
        while ($s_time <= $e_time) {
            $hour = floor($s_time / 60);
            if ($hour < 10) {
                $hour = '0' . $hour;
            }

            $minute = $s_time % 60;
            if ($minute < 10) {
                $minute = '0' . $minute;
            }

            $time = $hour . ':' . $minute;
            $arr[$time] = $time;

            $s_time += $gap;
        }

        return $arr;
    }

    public static function calculateIntervals($str_time1, $str_time2, $unit)
    {
        $interval = strtotime($str_time2) - strtotime($str_time1);

        if ($unit == 'i') {
            $interval = floor($interval / 60);
        } elseif ($unit == 'h') {
            $interval = floor($interval / (60 * 60));
        } elseif ($unit == 'd') {
            $interval = floor($interval / (60 * 60 * 24));
        } elseif ($unit == 'm') {
            $interval = floor($interval / (60 * 60 * 24 * 30));
        } elseif ($unit == 'y') {
            $interval = floor($interval / (60 * 60 * 24 * 365));
        }

        return $interval;
    }

    public static function makeMonthlyArray($date = '', $flag_normal = false)
    {

        if (!$date) {
            $date = _NOW_DATE_;
        }
        $this_time = strtotime($date);
        unset($cal_arr);

        $cal_title = date('Y년 m월', $this_time);
        $cal_arr['title'] = $cal_title;

        $this_year = date('Y', $this_time);
        $this_month = date('m', $this_time);
        $this_day = '1';

        $start_time = mktime(0, 0, 0, $this_month, 1, $this_year);
        $prev_time = mktime(0, 0, 0, $this_month - 1, $this_day, $this_year);
        $cal_arr['prev_date'] = date('Y-m-d', $prev_time);

        $s_week = date('w', $start_time);

        $next_time = mktime(0, 0, 0, $this_month + 1, $this_day, $this_year);
        $cal_arr['next_date'] = date('Y-m-d', $next_time);
        $last_time = mktime(0, 0, 0, $this_month + 1, 1, $this_year) - 24 * 3600;

        $now_time = strtotime(date('Y-m-d'));
        $seq_time = $start_time - $s_week * 24 * 3600;

        unset($date_arr);
        for ($i = 0; $i < 6; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $day = date('d', $seq_time);
                $week = date('w', $seq_time);

                $class = '';
                if ($seq_time > $last_time) {
                    $flag_break = true;
                    $seq_time -= 24 * 3600;
                    break;
                } elseif ($seq_time < $start_time) {
                    $day = '';
                    $class = '';
                } elseif ($seq_time < $now_time && !$flag_normal) {
                    $class = 'none';
                } elseif ($seq_time == $now_time) {
                    $class = 'today on';
                } elseif ($week == 0) {
                    $class = 'sun';
                } elseif ($week == 6) {
                    $class = 'sat';
                }

                $date_arr[$i][$j] = array(
                    'time' => $seq_time,
                    'date' => date('Y-m-d', $seq_time),
                    'day' => $day,
                    'week' => $week,
                    'class' => $class
                );

                $seq_time += 24 * 3600;
            }

            if ($flag_break) {
                if ($j > 0) {
                    for ($k = $j; $k < 7; $k++) {
                        $seq_time += 24 * 3600;

                        $day = '';
                        $week = date('w', $seq_time);
                        $class = 'none';

                        $date_arr[$i][$k] = array(
                            'time' => $seq_time,
                            'date' => date('Y-m-d', $seq_time),
                            'day' => $day,
                            'week' => $week,
                            'class' => $class
                        );
                    }
                }

                break;
            }
        }
        $cal_arr['date'] = $date_arr;

        return $cal_arr;
    }

    // 초를 'HH:mm:ss' 형태로 환산
    public static function getTimeFromSeconds($seconds)
    {
        $h = sprintf("%02d", intval($seconds) / 3600);
        $tmp = $seconds % 3600;
        $m = sprintf("%02d", $tmp / 60);
        $s = sprintf("%02d", $tmp % 60);

        //return $h . ':' . $m . ':' . $s;
        return $m . '분 ' . $s . '초';
    }
}
