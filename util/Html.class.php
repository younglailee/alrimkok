<?php
/**
 * HTML 유틸리티 클래스
 * @file    Html.class.php
 * @author  Alpha-Edu
 * @package util
 */

namespace sFramework;

use function strlen;

class Html
{
    /**
     * 기본 HTML 구조를 기반으로 콘텐츠 출력
     * @param $title
     * @param $content
     */
    public static function printDefaultHtml($title, $content)
    {
        echo '<!doctype html>' . "\n";
        echo '<html lang="ko" xml:lang="ko">' . "\n";
        echo '<head>' . "\n";
        echo '<meta charset="utf-8">' . "\n";
        echo '<title>' . $title . '</title>' . "\n";
        echo '</head>' . "\n";
        echo '<body>' . "\n";
        echo '<p>' . $content . '</p>' . "\n";
        echo '</body>' . "\n";
        echo '</html>' . "\n";
        exit;
    }

    /**
     * 에러 메시지 출력
     * @param $content
     */
    public static function printError($content)
    {
        if (!defined('_FLAG_DISPLAY_ERROR_') || !_FLAG_DISPLAY_ERROR_) {
            $content = '';
        }
        self::printDefaultHtml('Error', $content);
    }

    /**
     * 페이지 이동
     * @param $uri
     * @param bool $flag_top
     */
    public static function movePage($uri, $flag_top = true)
    {
        $content = '<haed>' . "\n";
        $content .= '<meta name="robots" content="noindex"/>' . "\n";
        $content .= '</head>' . "\n";
        $content .= '<script type="text/javascript">' . "\n";
        $content .= '//<![CDATA[' . "\n";
        if ($flag_top) {
            $content .= 'top.';
        }
        $content .= 'location.replace("' . $uri . '");' . "\n";
        $content .= '//]]>' . "\n";
        $content .= '</script>' . "\n";
        $content .= '<noscript>' . "\n";
        $content .= '<p>' . "\n";
        $content .= '이동할 페이지 : ' . $uri . '<br />' . "\n";
        $content .= '<a href="' . $uri . '" title="페이지 이동">이동하기</a>' . "\n";
        $content .= '</p>' . "\n";
        $content .= '</noscript>';

        self::printDefaultHtml('페이지 이동', $content);
    }

    /**
     * 경고 출력
     * @param $msg
     * @param string $uri
     * @param bool $flag_move
     */
    public static function alert($msg, $uri = '', $flag_move = true)
    {
        $content = '<script type="text/javascript">' . "\n";
        $content .= '//<![CDATA[' . "\n";
        $content .= 'alert("' . str_replace("\n", "\\n", $msg) . '");' . "\n";
        if ($flag_move) {
            if ($uri) {
                $content .= 'top.location.replace("' . $uri . '");' . "\n";
            } else {
                $content .= 'history.back(-1);' . "\n";
            }
        }
        $content .= '//]]>' . "\n";
        $content .= '</script>' . "\n";
        $content .= '<noscript>' . "\n";
        $content .= '<p>' . "\n";
        $content .= nl2br($msg);
        if ($uri) {
            $content .= '<br />이동할 페이지 : ' . $uri . '<br />' . "\n";
            $content .= '<a href="' . $uri . '" title="페이지 이동">이동하기</a>' . "\n";
        }
        $content .= '</p>' . "\n";
        $content .= '</noscript>';

        // 경고 -> 메시지 문구 수정(포스트백 발생 시 경고 문구 노출로 인한) yllee 180705
        self::printDefaultHtml('메시지', $content);
    }

    /**
     * 결과를 통한 후처리
     * @param $result
     * @param $flag_json
     */
    public static function postprocessFromResult($result, $flag_json = '')
    {
        if (!$flag_json) {
            $uri = $result['uri'];
            $msg = $result['msg'];

            if ($msg) {
                self::alert($msg, $uri);
            } elseif ($uri) {
                self::movePage($uri);
            }
        }
    }

    /**
     * 부모창 새로고침하며 팝업창 닫기
     * @param string $msg
     * @param string $uri
     */
    public static function closeWithRefresh($msg = '', $uri = '')
    {
        $content = '<script type="text/javascript">' . "\n";
        $content .= '//<![CDATA[' . "\n";
        if ($msg) {
            $content .= 'alert("' . str_replace("\n", "\\n", $msg) . '");' . "\n";
        }

        $content .= 'var href = ';
        if ($uri) {
            $content .= '"' . $uri . '"';
        } else {
            $content .= 'opener.location.href';
        }
        $content .= ';' . "\n";
        $content .= 'opener.location.replace(href);' . "\n";
        $content .= 'window.close();' . "\n";
        $content .= '//]]>' . "\n";
        $content .= '</script>' . "\n";
        $content .= '<noscript>' . "\n";
        $content .= '<p>' . "\n";
        if ($msg) {
            $content .= nl2br($msg);
        }
        if ($uri) {
            $content .= '<br />이동할 페이지 : ' . $uri . '<br />' . "\n";
            $content .= '<a href="' . $uri . '" title="페이지 이동">이동하기</a>' . "\n";
        }
        $content .= '</p>' . "\n";
        $content .= '</noscript>';

        self::printDefaultHtml('알림', $content);
    }

    /**
     * 팝업창 닫기
     * @param string $msg
     */
    public static function closeWindow($msg = '')
    {
        //$content = '<script type="text/javascript" src="/common/js/jquery-1.8.3.min.js"></script>' . "\n";
        $content = '<script type="text/javascript">' . "\n";
        $content .= '//<![CDATA[' . "\n";
        if ($msg) {
            //$content .= 'alert("' . str_replace("\n", "\\n", $msg) . '");' . "\n";
            /*
            $content .= '$(window).on("beforeunload", function() {' . "\n";
            $content .= 'var msg = "' . str_replace("\n", "\\n", $msg) . '";' . "\n";
            $content .= 'var ua  = navigator.userAgent.toLowerCase();' . "\n";
            $content .= 'if ((navigator.appName == "Netscape" && ua.indexOf("trident") != -1) || (ua.indexOf("msie") != -1)) {' . "\n";
            $content .= 'confirm(msg);' . "\n";
            $content .= '} else {' . "\n";
            $content .= 'return confirm(msg);' . "\n";
            $content .= '}' . "\n";
            $content .= '});' . "\n";
            */
            $content .= 'let msg = "' . str_replace("\n", "\\n", $msg) . '";' . "\n";
            $content .= 'if (confirm(msg)) {' . "\n";
            $content .= 'window.close();' . "\n";
            $content .= '}' . "\n";
        }
        //$content .= 'window.close();' . "\n";
        $content .= '//]]>' . "\n";
        $content .= '</script>' . "\n";
        $content .= '<noscript>' . "\n";
        $content .= '<p>' . "\n";
        if ($msg) {
            $content .= nl2br($msg);
        }
        $content .= '</p>' . "\n";
        $content .= '</noscript>';

        self::printDefaultHtml('알림', $content);
    }

    /**
     * 네이티브 브릿지 호출
     * @param $native_uri
     * @param string $return_uri
     */
    public static function callNative($native_uri, $return_uri = '')
    {
        $uri = 'native://' . $native_uri . '/' . urlencode($return_uri);
        $content = '<script type="text/javascript">' . "\n";
        $content .= '//<![CDATA[' . "\n";
        $content .= 'top.location.replace("';
        if (defined('_IS_WEBVIEW_') && _IS_WEBVIEW_) {
            $content .= $uri;
        } else {
            $content .= $return_uri;
        }
        $content .= '")' . "\n";
        $content .= '//]]>' . "\n";
        $content .= '</script>' . "\n";
        $content .= '<noscript>' . "\n";
        $content .= '<p>' . "\n";
        $content .= '이동할 페이지 : ' . $uri . '<br />' . "\n";
        $content .= '<a href="' . $uri . '" title="페이지 이동">이동하기</a>' . "\n";
        $content .= '</p>' . "\n";
        $content .= '</noscript>';

        self::printDefaultHtml('네이티브 호출', $content);
    }

    /**
     * tr > td 노데이터 생성
     * @param $colspan
     * @param string $msg
     * @return string
     */
    public static function makeNoTd($colspan, $msg = '데이터가 없습니다.')
    {
        $result = '<tr>' . "\n";
        $result .= "\t" . '<td class="no_data" colspan="' . $colspan . '">' . $msg . '</td>' . "\n";
        $result .= '</tr>';

        return $result;
    }

    /**
     * li 노데이터 생성
     * @param string $msg
     * @return string
     */
    public static function makeNoLi($msg = '데이터가 없습니다.')
    {
        $result = '<li class="no_data">' . $msg . '</li>';

        return $result;
    }

    /**
     * p 노데이터 생성
     * @param string $msg
     * @return string
     */
    public static function makeNoP($msg = '데이터가 없습니다.')
    {
        $result = '<p class="no_data">' . $msg . '</p>';

        return $result;
    }

    /**
     * 페이지네이션 생성
     * @param $arr
     * @param string $query_string
     * @return string
     */
    public static function makePagination($arr, $query_string = '', $a_arr_class = '')
    {
        $query_string = preg_replace('/page=[0-9]+/', '', $query_string);

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

    /**
     * 페이지네이션 생성
     * @param $arr
     * @param string $query_string
     * @return string
     */
    public static function makeMobilePageNation($arr, $query_string = '')
    {
        $query_string = preg_replace('/page=[0-9]+/', '', $query_string);

        $result = '';
        for ($i = 0; $i < count($arr); $i++) {
            $result .= '<li';
            if ($arr[$i]['class']) {
                $result .= ' class="' . $arr[$i]['class'] . '"';
            }
            $result .= '><a href="?page=' . $arr[$i]['page'] . $query_string;
            $result .= '" title="' . $arr[$i]['title'] . ' 페이지">' . $arr[$i]['subject'];
            $result .= '</a></li>' . "\n";
        }

        return $result;
    }

    /**
     * Ajax 방식으로 페이지네이션 생성
     * @param $arr
     * @param string $query_string
     * @param string $href
     * @param string $target
     * @param string $title
     * @return string
     */
    public static function makeAjaxPagination($arr, $query_string = '', $href = '', $target = '', $title = '')
    {
        $query_string = preg_replace('/page=[0-9]+/', '', $query_string);

        $result = '';
        for ($i = 0; $i < count($arr); $i++) {
            $result .= '<li';
            if ($arr[$i]['class']) {
                $result .= ' class="' . $arr[$i]['class'] . '"';
            }
            $result .= '><a href="' . $href . '?page=' . $arr[$i]['page'] . $query_string;
            $result .= '" class="btn_ajax" target="' . $target;
            $result .= '" title="' . $title . '">' . $arr[$i]['title'];
            $result .= '</a></li>' . "\n";
        }

        return $result;
    }

    /**
     * <Input type="text" /> 생성
     * @param $name
     * @param string $title
     * @param string $value
     * @param string $class
     * @param int $size
     * @param int $maxlength
     * @return string
     */
    public static function makeInputText($name, $title = '', $value = '', $class = '', $size = 0, $maxlength = 0)
    {
        $result = '<input type="text" name="' . $name . '" id="' . $name . '" ';
        $result .= 'value="' . $value . '" class="text';
        if ($class) {
            $result .= ' ' . $class;
        }
        $result .= '" ';

        if ($size) {
            $result .= ' size="' . $size . '"';
        }

        if ($maxlength) {
            $result .= ' maxlength="' . $maxlength . '"';
        }

        if ($title) {
            $result .= ' title="' . $title . '"';
        }

        $result .= ' />';

        return $result;
    }

    /**
     * <textarea /> 생성
     * @param $name
     * @param string $title
     * @param string $value
     * @param string $class
     * @param int $rows
     * @param int $cols
     * @return string
     */
    public static function makeTextarea($name, $title = '', $value = '', $class = '', $rows = 0, $cols = 0)
    {
        $result = '<textarea name="' . $name . '" id="' . $name . '" class="textarea';
        if ($class) {
            $result .= ' ' . $class;
        }
        $result .= '" ';

        if ($rows) {
            $result .= ' rows="' . $rows . '"';
        }

        if ($cols) {
            $result .= ' cols="' . $cols . '"';
        }

        if ($title) {
            $result .= ' title="' . $title . '"';
        }

        $result .= '>' . $value . '</textarea>';

        return $result;
    }

    /**
     * tr > td 생성
     * @param $th
     * @param $td
     * @param bool $flag_required
     * @param int $colspan
     * @return string
     */
    public static function makeTrTd($th, $td, $flag_required = false, $colspan = 0)
    {
        $result = '<tr>' . "\n";
        $result .= "\t" . '<th';
        if ($flag_required) {
            $result .= ' class="required"';
        }
        $result .= '>' . $th . '</th>';
        $result .= "\t" . '<td';
        if ($colspan) {
            $result .= ' colspan="' . $colspan . '"';
        }
        $result .= '>' . $td . '</td>';
        $result .= '</tr>';

        return $result;
    }

    /**
     * dl > dd 생성
     * @param $dt
     * @param $dd
     * @param bool $flag_required
     * @param int $colspan
     * @return string
     */
    public static function makeDtDd($dt, $dd, $flag_required = false, $colspan = 0)
    {
        $result = '<dl>' . "\n";
        $result .= "\t" . '<dt';
        if ($flag_required) {
            $result .= ' class="required"';
        }
        $result .= '>' . $dt . '</dt>';
        $result .= "\t" . '<dd';
        if ($colspan) {
            $result .= ' colspan="' . $colspan . '"';
        }
        $result .= '>' . $dd . '</dd>';
        $result .= '</dl>';

        return $result;
    }

    /**
     * select >option 생성
     * @param $arr
     * @param $value
     * @param int $opt
     * @return null|string
     */
    public static function makeSelectOptions($arr, $value, $opt = 0)
    {
        if (!is_array($arr)) {
            return null;
        }

        $result = '';
        foreach ($arr as $key => $val) {
            $opt_value = $val;
            $opt_text = $val;
            if ($opt == 1) {
                $opt_value = (string)$key;
            } elseif ($opt == 2) {
                $opt_value = $val;
                $opt_text = $key;
            }

            $result .= '<option value="' . $opt_value . '"';
            if ((string)$value == $opt_value) {
                $result .= ' selected="selected"';
            }
            $result .= '>' . $opt_text . '</option>' . "\n";
        }

        return $result;
    }

    /**
     * <input type="radio" /> 생성
     * @param $name
     * @param $arr
     * @param $value
     * @param int $opt
     * * @param $class
     * @return null|string
     */
    public static function makeRadio($name, $arr, $value, $opt = 0, $class = '')
    {
        if (!is_array($arr)) {
            return null;
        }

        $result = '';
        foreach ($arr as $key => $val) {
            $opt_name = $name . '_' . $key;
            $opt_value = $val;
            $opt_text = $val;
            if ($opt == 1) {
                $opt_value = $key;
            } elseif ($opt == 2) {
                $opt_value = $val;
                $opt_text = $key;
            }

            $result .= '<input type="radio" name="' . $name . '" ';
            $result .= 'id="' . $opt_name . '" class="radio ' . $name . $class . '" ';
            $result .= 'value="' . $opt_value . '"';
            if ($value == $opt_value) {
                $result .= ' checked="checked"';
            }
            $result .= ' /><label for="' . $opt_name . '">' . $opt_text . '</label>' . "\n";
        }

        return $result;
    }

    /**
     * <input type="checkbox" /> 생성
     * @param $name
     * @param $arr
     * @param $value
     * @param int $opt
     * @param $class
     * @return null|string
     */
    public static function makeCheckbox($name, $arr, $value, $opt = 0, $class = '')
    {
        if (!is_array($arr)) {
            return null;
        }

        if (is_array($value)) {
            $val_arr = $value;
        } else {
            $val_arr = explode('|', $value);
        }
        $result = '';
        foreach ($arr as $key => $val) {
            $opt_name = $name . '_' . $key;
            $opt_value = $val;
            $opt_text = $val;
            if ($opt == 1) {
                $opt_value = $key;
            } elseif ($opt == 2) {
                $opt_value = $val;
                $opt_text = $key;
            }

            $result .= '<input type="checkbox" name="' . $name;
            if (count($arr) > 1) {
                $result .= '[]';
            }
            $result .= '" id="' . $opt_name . '" class="checkbox ' . $name . $class . '" ';
            $result .= 'value="' . $opt_value . '"';

            if (count($arr) > 1 && in_array($opt_value, $val_arr)) {
                $result .= ' checked="checked"';
            } elseif (count($arr) < 2 && $opt_value == $val_arr[0]) {
                $result .= ' checked="checked"';
            }
            $result .= ' /><label for="' . $opt_name . '">' . $opt_text . '</label>' . "\n";
        }

        return $result;
    }

    /**
     * Table > Input 생성
     * @param $title
     * @param $name
     * @param string $value
     * @param string $class
     * @param int $size
     * @param int $maxlength
     * @param int $colspan
     * @return string
     */
    public static function makeInputTextInTable($title, $name, $value = '', $class = '', $size = 0, $maxlength = 0, $colspan = 0)
    {
        $th = '<label for="' . $name . '">' . $title . '</label>';
        $td = self::makeInputText($name, $title, $value, $class, $size, $maxlength);

        $flag_required = false;
        if (strpos($class, 'required') > -1) {
            $flag_required = true;
        }

        return self::makeTrTd($th, $td, $flag_required, $colspan);
    }

    /**
     * Table > Input 생성
     * @param $title
     * @param $name
     * @param string $value
     * @param string $class
     * @param int $size
     * @param int $maxlength
     * @param int $colspan
     * @return string
     */
    public static function makeInputTextInDl($title, $name, $value = '', $class = '', $size = 0, $maxlength = 0, $colspan = 0)
    {
        $th = '<label for="' . $name . '">' . $title . '</label>';
        $td = self::makeInputText($name, $title, $value, $class, $size, $maxlength);

        $flag_required = false;
        if (strpos($class, 'required') > -1) {
            $flag_required = true;
        }

        return self::makeDtDd($th, $td, $flag_required, $colspan);
    }

    /**
     * Table > Textarea 생성
     * @param $title
     * @param $name
     * @param string $value
     * @param string $class
     * @param int $rows
     * @param int $cols
     * @param int $colspan
     * @return string
     */
    public static function makeTextareaInTable($title, $name, $value = '', $class = '', $rows = 0, $cols = 0, $colspan = 0)
    {
        $th = '<label for="' . $name . '">' . $title . '</label>';
        $td = self::makeTextarea($name, $title, $value, $class, $rows, $cols);

        $flag_required = false;
        if (strpos($class, 'required') > -1) {
            $flag_required = true;
        }

        return self::makeTrTd($th, $td, $flag_required, $colspan);
    }

    /**
     * Table > Textarea 생성
     * @param $title
     * @param $name
     * @param string $value
     * @param string $class
     * @param int $rows
     * @param int $cols
     * @param int $colspan
     * @return string
     */
    public static function makeTextareaInDl($title, $name, $value = '', $class = '', $rows = 0, $cols = 0, $colspan = 0)
    {
        $th = '<label for="' . $name . '">' . $title . '</label>';
        $td = self::makeTextarea($name, $title, $value, $class, $rows, $cols);

        $flag_required = false;
        if (strpos($class, 'required') > -1) {
            $flag_required = true;
        }

        return self::makeDtDd($th, $td, $flag_required, $colspan);
    }

    /**
     * 빠른 기간 선택 반환
     * @param $date_arr
     * @param $sch_s_date
     * @param $sch_e_date
     * @param string $href
     * @param string $classes
     * @return string
     */
    public static function makePeriodAnchor($date_arr, $sch_s_date, $sch_e_date, $with_today = true, $flag_use_all = true, $href = './list.html', $classes = 'sButton tiny')
    {
        if (!is_array($date_arr)) {
            return null;
        }

        if ($with_today) {
            unset($new_date_arr);
            foreach ($date_arr as $key => $val) {
                $new_key = date('Y-m-d', strtotime($key) + 24 * 3600);
                $new_date_arr[$new_key] = $val;
            }
            $date_arr = $new_date_arr;
        }

        $result = '';
        $seq = 0;
        $first_key = null;
        foreach ($date_arr as $key => $val) {
            if (!$seq) {
                $first_key = $key;
            } else {
                $result .= '<a href="' . $href . '?sch_s_date=' . $key;
                $result .= '&sch_e_date=' . $first_key . '" class="btn_change_period';
                if ($classes) {
                    $result .= ' ' . $classes;
                }
                if ($sch_s_date == $key && $sch_e_date == $first_key) {
                    $result .= ' active';
                }
                $result .= '">' . $val . '</a>' . "\n";
            }

            $seq++;
        }

        if ($flag_use_all) {
            $result .= '<a href="' . $href . '?sch_s_date=&sch_e_date=" class="btn_change_period';
            if ($classes) {
                $result .= ' ' . $classes;
            }
            if ($sch_s_date == '' && $sch_e_date == '') {
                $result .= ' active';
            }
            $result .= '">전체</a>' . "\n";
        }

        return $result;
    }

    /**
     * 텍스트 자르기
     * @param string $str
     * @param string $len
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
     * 전화번호 보기 좋게 출력
     * @param $tel
     * @return mixed|string
     */
    public static function beautifyTel($tel)
    {
        $tel = str_replace('-', '', $tel);
        $tel = str_replace('.', '', $tel);
        $tel = str_replace('(', '', $tel);
        $tel = str_replace(')', '', $tel);
        $tel = str_replace(' ', '', $tel);

        if (strlen($tel) == 8) {
            $result = substr($tel, 0, 4) . '-' . substr($tel, 4, 4);
        } elseif (strlen($tel) == 10 || strlen($tel) == 9) {
            // 지역번호 02 처리: 9자리 전화번호도 포함 yllee 191218
            if (substr($tel, 0, 2) == '02') {
                if (strlen($tel) == 9) {
                    $result = substr($tel, 0, 2) . '-' . substr($tel, 2, 3) . '-' . substr($tel, 5, 4);
                } else {
                    $result = substr($tel, 0, 2) . '-' . substr($tel, 2, 4) . '-' . substr($tel, 6, 4);
                }
            } else {
                $result = substr($tel, 0, 3) . '-' . substr($tel, 3, 3) . '-' . substr($tel, 6, 4);
            }
        } elseif (strlen($tel) == 11) {
            $result = substr($tel, 0, 3) . '-' . substr($tel, 3, 4) . '-' . substr($tel, 7, 4);
        } else {
            $result = $tel;
        }
        return $result;
    }

    public static function printNullText($str)
    {
        if (!$str) {
            $str = '-';
        }
        return $str;
    }

    // https, http 전환
    public static function httpsRedirect($ssl = false)
    {
        $https = array(
            'HTTP_X_FORWARDED_PROTO' => 'HTTPS',
            'HTTP_X_SSL' => 'ON',
            'HTTPS' => 'ON',
            'SSL' => 'ON'
        );
        $protocol = 'https://';
        foreach ($https as $q => $w) {
            if (strtoupper($_SERVER[$q]) === $w) {
                $protocol = false;
                break;
            }
        }
        if ($ssl === true) {
            $protocol = (false === $protocol) ? 'http://' : false;
        }
        if (false !== $protocol) {
            header('HTTP/1.0 301 Moved Permanently');
            header('Location: ' . $protocol .

                $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            die();
        }
    }
    // http 사용자를 https 로 redirect 처리
    //httpsRedirect();
    // https 사용자를 http 로 redirect 처리
    //httpsRedirect(true);

}
