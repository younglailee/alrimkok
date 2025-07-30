<?php
/**
 * 기본 오브젝트 클래스
 * @file    Object.class.php
 * @author  Alpha-Edu
 * @package module/core
 */
namespace sFramework;

class Object
{
    protected $values = array();
    protected $result = array();

    /**
     * Object constructor.
     */
    public function __construct()
    {
        if (!defined('_ALPHA_')) {
            exit;
        }
    }

    /**
     * 변수를 조회
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->values[$key];
    }

    /**
     * 변수를 저장
     * @param $key
     * @param $val
     */
    public function set($key, $val)
    {
        $this->values[$key] = $val;
    }

    /**
     * 변수를 삭제
     * @param $key
     */
    public function del($key)
    {
        unset($this->values[$key]);
    }

    /**
     * request parameter를 반환
     * @param $key
     * @param null $arr
     * @return string
     */
    protected function getRequestParameter($key, $arr = null)
    {
        $editor_columns = $this->get('editor_columns');
        $editor_column_arr = explode(',', $editor_columns);

        $value = ($_POST[$key]) ? $_POST[$key] : $_GET[$key];
        if (!$value && is_array($arr) && $arr[$key]) {
            $value = $arr[$key];
        }

        if (in_array($key, $editor_column_arr)) {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $value[$key] = Format::filterXss($val);
                }
            } else {
                $value = Format::filterXss($value);
            }
        } else {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $value[$key] = strip_tags($val);
                }
            } else {
                $value = strip_tags($value);
            }
        }

        return $value;
    }

    /**
     * Post or Get 변수를 조회
     * @param $str
     * @param string $method
     * @return array
     */
    protected function getParameters($str, $method = '')
    {
        $editor_columns = $this->get('editor_columns');
        $editor_column_arr = explode(',', $editor_columns);

        $key_arr = explode(',', $str);
        unset($arr);

        for ($i = 0; $i < count($key_arr); $i++) {
            $key = $key_arr[$i];
            $value = ($method == 'post') ? $_POST[$key] : $_GET[$key];

            if (in_array($key, $editor_column_arr)) {
                if (is_array($value)) {
                    unset($new_value);
                    foreach ($value as $key2 => $val2) {
                        $new_value[] = Format::filterXss($val2);
                    }
                    $value = $new_value;
                } else {
                    $value = Format::filterXss($value);
                }
            } else {
                if (is_array($value)) {
                    unset($new_value);
                    foreach ($value as $key2 => $val2) {
                        $new_value[] = strip_tags($val2);
                    }
                    $value = $new_value;
                } else {
                    $value = strip_tags($value);
                }
            }

            $arr[$key] = $value;
        }

        return $arr;
    }
}
