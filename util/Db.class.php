<?php
/**
 * DB 유틸리티 클래스
 * @file    Db.class.php
 * @author  Alpha-Edu
 * @package util
 */

namespace sFramework;

use function mysqli_fetch_array;
use const MYSQLI_ASSOC;

class Db
{
    /**
     * DB 커넥션 생성 후 리턴
     * @return \mysqli|null
     */
    private static function getConnection()
    {
        if (!defined('_DB_HOST_') || !defined('_DB_USER_') || !defined('_DB_PASSWORD_') || !defined('_DB_NAME_')) {
            return null;
        }
        if (!$link = mysqli_connect(_DB_HOST_, _DB_USER_, _DB_PASSWORD_)) {
            Html::printError('DB Connection Error');

            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            exit;
        }
        if (!mysqli_select_db($link, _DB_NAME_)) {
            Html::printError('DB Selection Error');
            exit;
        }

        return $link;
    }

    /**
     * 쿼리 실행: excuteQuery -> executeQuery 오타 수정, 주석 리턴 타입 변경 yllee 220725
     * @param $query
     * @param bool $flag_log_query
     * @return bool|\mysqli_result|void
     */
    private static function executeQuery($query, $flag_log_query = false)
    {
        $result = mysqli_query(self::getConnection(), $query);
        // DB 결과 로그
        if ($flag_log_query || (defined('_FLAG_LOG_QUERY_') && _FLAG_LOG_QUERY_)) {
            Log::query($query, $result);
        }
        return $result;
    }

    /**
     * 쿼리 실행(동아대학교)
     * @param $query
     * @param bool $flag_log_query
     * @return resource|void
     */
    private function executeQueryDamc($query, $flag_log_query = false)
    {
        $agent_host = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=14.43.51.13)(Port=1551)))(CONNECT_DATA=(SID=dmlpemr1)))';
        $agent_user = 'alp_user';
        $agent_password = 'alp_user';
        if (!$link = oci_connect($agent_user, $agent_password, $agent_host, 'AL32UTF8')) {
            $e = oci_error();
            echo $e['message'];
            exit;
        }
        return $link;
    }

    /**
     * 쿼리 실행(LX)
     * @param $query
     * @param bool $flag_log_query
     * @return bool|\mysqli_result|void
     */
    private function executeQueryLx($query, $flag_log_query = false)
    {
        $agent_host = '106.10.100.36';
        $agent_user = 'best1alpha';
        $agent_password = 'dkfvkDPEB@1035M';
        if (!$link = mysqli_connect($agent_host, $agent_user, $agent_password)) {
            Html::printError('DB Connection Error..');
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            exit;
        }
        if (!mysqli_select_db($link, 'best1alpha')) {
            Html::printError('DB Selection Error');
            exit;
        }
        $result = mysqli_query($link, $query);
        // DB 결과 로그
        if ($flag_log_query || (defined('_FLAG_LOG_QUERY_') && _FLAG_LOG_QUERY_)) {
            Log::query($query, $result);
        }
        return $result;
    }

    /**
     * 쿼리 실행(LXN)
     * @param $query
     * @param bool $flag_log_query
     * @return bool|\mysqli_result|void
     */
    private function executeQueryLxn($query, $flag_log_query = false)
    {
        $agent_host = '106.10.100.36';
        $agent_user = 'best1alpha';
        $agent_password = 'dkfvkDPEB@1035M';
        if (!$link = mysqli_connect($agent_host, $agent_user, $agent_password)) {
            Html::printError('DB Connection Error.');
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            exit;
        }
        if (!mysqli_select_db($link, 'best1alpha')) {
            Html::printError('DB Selection Error');
            exit;
        }
        $result = mysqli_query($link, $query);
        // DB 결과 로그
        if ($flag_log_query || (defined('_FLAG_LOG_QUERY_') && _FLAG_LOG_QUERY_)) {
            Log::query($query, $result);
        }
        return $result;
    }

    /**
     * Select 쿼리 실행
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param $limit
     * @param bool $flag_log_query
     * @return array|null
     */
    public static function select($table, $column, $where, $order, $limit, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' ' . $limit . ';';
        $result = (new Db)->executeQuery($query, $flag_log_query);
        $list = null;
        while ($data = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $list[] = $data;
        }
        return $list;
    }

    /**
     * Select 쿼리 실행(동아대학교)
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param $limit
     * @param bool $flag_log_query
     * @return array|null
     */
    public static function selectDamc($table, $column, $where, $order, $limit, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' ' . $limit;
        // 동아대의료원 오라클 13번 서버 장애로 12번 서버로 접속, SID도 변경 dmlpemr1 -> dmlpemr2 yllee 230125
        $agent_host = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=14.43.51.12)(Port=1551)))(CONNECT_DATA=(SID=dmlpemr2)))';
        // 알파에듀 오라클 계정 복구되어 다시 사용 yllee 220526
        $agent_user = 'alp_user';
        $agent_password = 'alphaedu1!';
        // 로그인 시 오라클 오류 발생: ORA-01017: invalid username/password; logon denied
        // 기존 아이디 alp_user 로그인이 되지 않아 uniedu 변경함 yllee 220525
        $agent_user = 'uniedu';
        $agent_password = 'uniedudamc1!';
        if (!$conn = oci_connect($agent_user, $agent_password, $agent_host, 'AL32UTF8')) {
            $e = oci_error();
            echo $e['message'];
            exit;
        }
        $stmt = oci_parse($conn, $query);
        // DB 결과 로그
        if ($flag_log_query || (defined('_FLAG_LOG_QUERY_') && _FLAG_LOG_QUERY_)) {
            Log::query($query, $stmt);
        }
        oci_execute($stmt);
        $list = null;
        while ($data = oci_fetch_array($stmt)) {
            $list[] = $data;
        }
        oci_free_statement($stmt);
        oci_close($conn);

        return $list;
    }

    /**
     * Select 쿼리 실행(LX)
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param $limit
     * @param bool $flag_log_query
     * @return array|null
     */
    public static function selectLx($table, $column, $where, $order, $limit, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' ' . $limit . ';';
        $result = (new Db)->executeQueryLx($query, $flag_log_query);
        $list = null;
        while ($data = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $list[] = $data;
        }
        return $list;
    }

    /**
     * Select 쿼리 실행(LX)
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param $limit
     * @param bool $flag_log_query
     * @return array|null
     */
    public static function selectLxn($table, $column, $where, $order, $limit, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' ' . $limit . ';';
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        $list = null;
        while ($data = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $list[] = $data;
        }
        return $list;
    }

    /**
     * Select limit 1 쿼리 실행
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param bool $flag_log_query
     * @return array
     */
    public static function selectOnce($table, $column, $where, $order, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' limit 1;';
        $result = (new Db)->executeQuery($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data;
    }

    /**
     * Select limit 1 쿼리 실행(동아대학교)
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param bool $flag_log_query
     * @return array
     */
    public static function selectOnceDamc($table, $column, $where, $order, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' LIMIT 1';
        //Log::debug($query);
        $agent_host = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=14.43.51.13)(Port=1551)))(CONNECT_DATA=(SID=dmlpemr1)))';
        $agent_user = 'alp_user';
        $agent_password = 'alp_user';
        if (!$conn = oci_connect($agent_user, $agent_password, $agent_host, 'AL32UTF8')) {
            $e = oci_error();
            echo $e['message'];
            exit;
        }
        $stmt = oci_parse($conn, $query);
        // DB 결과 로그
        if ($flag_log_query || (defined('_FLAG_LOG_QUERY_') && _FLAG_LOG_QUERY_)) {
            Log::query($query, $stmt);
        }
        oci_execute($stmt);
        $list = null;
        $data = oci_fetch_array($stmt);
        oci_free_statement($stmt);
        oci_close($conn);

        return $data;
    }

    /**
     * Select limit 1 쿼리 실행(LX)
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param bool $flag_log_query
     * @return array
     */
    public static function selectOnceLx($table, $column, $where, $order, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' limit 1;';
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data;
    }

    /**
     * Select limit 1 쿼리 실행(LXN)
     * @param $table
     * @param $column
     * @param $where
     * @param $order
     * @param bool $flag_log_query
     * @return array
     */
    public static function selectOnceLxn($table, $column, $where, $order, $flag_log_query = false)
    {
        $query = 'SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ' ' . $order . ' limit 1;';
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data;
    }

    /**
     * Select count() 쿼리 실행
     * @param $table
     * @param $where
     * @param string $group_column
     * @param string $having
     * @param bool $flag_log_query
     * @return mixed
     */
    public static function selectCount($table, $where, $group_column = '', $having = '', $flag_log_query = false)
    {
        if ($group_column) {
            $query = 'SELECT COUNT(*) AS cnt ' . 'FROM ( ';
            $query .= 'SELECT COUNT(' . $group_column . ') FROM ' . $table . ' ';
            $query .= $where . ' ' . $having;
            $query .= ' ) T ;';
        } else {
            $query = 'SELECT COUNT(*) AS cnt ' . 'FROM ' . $table . ' ' . $where . ';';
        }
        $result = (new Db)->executeQuery($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data['cnt'];
    }

    /**
     * Select count() 쿼리 실행(LX)
     * @param $table
     * @param $where
     * @param string $group_column
     * @param string $having
     * @param bool $flag_log_query
     * @return mixed
     */
    public static function selectCountLx($table, $where, $group_column = '', $having = '', $flag_log_query = false)
    {
        if ($group_column) {
            $query = 'SELECT COUNT(*) AS cnt ' . 'FROM ( ';
            $query .= 'SELECT COUNT(' . $group_column . ') FROM ' . $table . ' ';
            $query .= $where . ' ' . $having;
            $query .= ' ) T ;';
        } else {
            $query = 'SELECT COUNT(*) AS cnt ' . 'FROM ' . $table . ' ' . $where . ';';
        }
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data['cnt'];
    }

    /**
     * Select count() 쿼리 실행(LXN)
     * @param $table
     * @param $where
     * @param string $group_column
     * @param string $having
     * @param bool $flag_log_query
     * @return mixed
     */
    public static function selectCountLxn($table, $where, $group_column = '', $having = '', $flag_log_query = false)
    {
        if ($group_column) {
            $query = 'SELECT COUNT(*) AS cnt ' . 'FROM ( ';
            $query .= 'SELECT COUNT(' . $group_column . ') FROM ' . $table . ' ';
            $query .= $where . ' ' . $having;
            $query .= ' ) T ;';
        } else {
            $query = 'SELECT COUNT(*) AS cnt ' . 'FROM ' . $table . ' ' . $where . ';';
        }
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data['cnt'];
    }

    /**
     * Insert 쿼리 실행
     * @param $table
     * @param $column
     * @param $value
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function insert($table, $column, $value, $flag_log_query = false)
    {
        $query = 'INSERT ' . 'INTO ' . $table . ' (' . $column . ') VALUES(' . $value . ');';
        $result = (new Db)->executeQuery($query, $flag_log_query);

        return $result;
    }

    /**
     * Insert 쿼리 실행(LX)
     * @param $table
     * @param $column
     * @param $value
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function insertLx($table, $column, $value, $flag_log_query = false)
    {
        $query = 'INSERT ' . 'INTO ' . $table . ' (' . $column . ') VALUES(' . $value . ');';
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);

        return $result;
    }

    /**
     * Insert 쿼리 실행(LXN)
     * @param $table
     * @param $column
     * @param $value
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function insertLxn($table, $column, $value, $flag_log_query = false)
    {
        $query = 'INSERT ' . 'INTO ' . $table . ' (' . $column . ') VALUES(' . $value . ');';
        $result = (new Db)->executeQueryLxn($query, $flag_log_query);

        return $result;
    }

    /**
     * 배열을 이용한 Insert 쿼리 실행
     * @param $table
     * @param $arr
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function insertByArray($table, $arr, $flag_log_query = false)
    {
        $column = '';
        $value = '';
        $seq = 0;
        foreach ($arr as $key => $val) {
            if ($seq > 0) {
                $column .= ',';
                $value .= ',';
            }
            $column .= $key;
            $value .= "'$val'";
            $seq++;
        }
        return self::insert($table, $column, $value, $flag_log_query);
    }

    /**
     * 배열을 이용한 Insert 쿼리 실행(LX)
     * @param $table
     * @param $arr
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function insertByArrayLx($table, $arr, $flag_log_query = false)
    {
        $column = '';
        $value = '';
        $seq = 0;
        foreach ($arr as $key => $val) {
            if ($seq > 0) {
                $column .= ',';
                $value .= ',';
            }
            $column .= $key;
            $value .= "'$val'";
            $seq++;
        }
        return self::insertLx($table, $column, $value, $flag_log_query);
    }

    /**
     * 배열을 이용한 Insert 쿼리 실행(LXN)
     * @param $table
     * @param $arr
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function insertByArrayLxn($table, $arr, $flag_log_query = false)
    {
        $column = '';
        $value = '';
        $seq = 0;
        foreach ($arr as $key => $val) {
            if ($seq > 0) {
                $column .= ',';
                $value .= ',';
            }
            $column .= $key;
            $value .= "'$val'";
            $seq++;
        }
        return self::insertLxn($table, $column, $value, $flag_log_query);
    }

    /**
     * Update 쿼리 실행
     * @param $table
     * @param $column_value
     * @param $where
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function update($table, $column_value, $where, $flag_log_query = false)
    {
        // WHERE 문이 있을 때만 실행하도록, 전체 데이터가 변경되는 것 방지 yllee 221208
        if ($where) {
            $query = 'UPDATE ' . $table . ' SET ' . $column_value . ' ' . $where . ';';
            $result = (new Db)->executeQuery($query, $flag_log_query);
        }
        return $result;
    }

    /**
     * Update 쿼리 실행(LX)
     * @param $table
     * @param $column_value
     * @param $where
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function updateLx($table, $column_value, $where, $flag_log_query = false)
    {
        // WHERE 문이 있을 때만 실행하도록, 전체 데이터가 변경되는 것 방지 yllee 221208
        if ($where) {
            $query = 'UPDATE ' . $table . ' SET ' . $column_value . ' ' . $where . ';';
            $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        }
        return $result;
    }

    /**
     * Update 쿼리 실행(LXN)
     * @param $table
     * @param $column_value
     * @param $where
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function updateLxn($table, $column_value, $where, $flag_log_query = false)
    {
        // WHERE 문이 있을 때만 실행하도록, 전체 데이터가 변경되는 것 방지 yllee 221208
        if ($where) {
            $query = 'UPDATE ' . $table . ' SET ' . $column_value . ' ' . $where . ';';
            //Log::debug($query);
            $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        }
        return $result;
    }

    /**
     * 배열을 이용한 Update 쿼리 실행
     * @param $table
     * @param $arr
     * @param $where
     * @param bool $flag_log_query
     */
    public static function updateByArray($table, $arr, $where, $flag_log_query = false)
    {
        $column_value = '';
        $seq = 0;
        foreach ($arr as $key => $val) {
            if (!$key) {
                continue;
            }
            if ($seq > 0) {
                $column_value .= ', ';
            }
            $column_value .= "$key = '$val'";
            $seq++;
        }
        return self::update($table, $column_value, $where, $flag_log_query);
    }

    /**
     * 배열을 이용한 Update 쿼리 실행(LX)
     * @param $table
     * @param $arr
     * @param $where
     * @param bool $flag_log_query
     */
    public static function updateByArrayLx($table, $arr, $where, $flag_log_query = false)
    {
        $column_value = '';
        $seq = 0;
        foreach ($arr as $key => $val) {
            if (!$key) {
                continue;
            }
            if ($seq > 0) {
                $column_value .= ', ';
            }
            $column_value .= "$key = '$val'";
            $seq++;
        }
        return self::updateLx($table, $column_value, $where, $flag_log_query);
    }

    /**
     * 배열을 이용한 Update 쿼리 실행(LXN)
     * @param $table
     * @param $arr
     * @param $where
     * @param bool $flag_log_query
     */
    public static function updateByArrayLxn($table, $arr, $where, $flag_log_query = false)
    {
        $column_value = '';
        $seq = 0;
        foreach ($arr as $key => $val) {
            if (!$key) {
                continue;
            }
            if ($seq > 0) {
                $column_value .= ', ';
            }
            $column_value .= "$key = '$val'";
            $seq++;
        }
        return self::updateLxn($table, $column_value, $where, $flag_log_query);
    }

    /**
     * Delete 쿼리 실행
     * @param $table
     * @param $where
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function delete($table, $where, $flag_log_query = false)
    {
        $query = 'DELETE ' . 'FROM ' . $table . ' ' . $where . ';';
        $result = (new Db)->executeQuery($query, $flag_log_query);
        return $result;
    }

    /**
     * Delete 쿼리 실행(LX)
     * @param $table
     * @param $where
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function deleteLx($table, $where, $flag_log_query = false)
    {
        // WHERE 문이 있을 때만 실행하도록, 전체 데이터가 삭제되는 것 방지 yllee 221208
        if ($where) {
            $query = 'DELETE ' . 'FROM ' . $table . ' ' . $where . ';';
            $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        }
        return $result;
    }

    /**
     * Delete 쿼리 실행(LXN)
     * @param $table
     * @param $where
     * @param bool $flag_log_query
     * @return bool|\mysqli_result
     */
    public static function deleteLxn($table, $where, $flag_log_query = false)
    {
        // WHERE 문이 있을 때만 실행하도록, 전체 데이터가 삭제되는 것 방지 yllee 221208
        if ($where) {
            $query = 'DELETE ' . 'FROM ' . $table . ' ' . $where . ';';
            $result = (new Db)->executeQueryLxn($query, $flag_log_query);
        }
        return $result;
    }

    /**
     * Password 쿼리 실행
     * @param $str
     * @param bool $flag_log_query
     * @return mixed
     */
    public static function password($str, $flag_log_query = false)
    {
        $query = 'SELECT ' . "PASSWORD(' " . $str . "') AS pw;";
        $result = (new Db)->executeQuery($query, $flag_log_query);

        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data['pw'];
    }

    /**
     * Password 쿼리 실행
     * @param $str
     * @param bool $flag_log_query
     * @return mixed
     */
    public static function oldPassword($str, $flag_log_query = false)
    {
        $query = 'SELECT ' . "OLD_PASSWORD(' " . $str . "') AS pw;";
        $result = (new Db)->executeQuery($query, $flag_log_query);
        $data = mysqli_fetch_array($result, MYSQLI_ASSOC);

        return $data['pw'];
    }

    /**
     * Select Union 쿼리 실행 yllee 220722
     * @param $table
     * @param $table_b
     * @param $column
     * @param $where
     * @param $order
     * @param $limit
     * @param bool $flag_log_query
     * @return array|null
     */
    public static function selectUnion($table, $table2, $column, $where, $order, $limit, $flag_log_query = false)
    {
        $query = '(SELECT ' . $column . ' FROM ' . $table . ' ' . $where . ') UNION ';
        $query .= '(SELECT ' . $column . ' FROM ' . $table2 . ' ' . $where . ')';
        $query .= ' ' . $order . ' ' . $limit . ';';

        $result = (new Db)->executeQuery($query, $flag_log_query);
        $list = null;
        while ($data = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $list[] = $data;
        }
        return $list;
    }

    /**
     * 트랜잭션 시작
     */
    public function beginTransaction()
    {
        $link = $this->getConnection();
        mysqli_begin_transaction($link);
        return $link;
    }

    /**
     * 트랜잭션 커밋
     */
    public function commitTransaction($link)
    {
        mysqli_commit($link);
    }

    /**
     * 트랜잭션 롤백
     */
    public function rollbackTransaction($link)
    {
        mysqli_rollback($link);
    }

    /**
     * 프로시저 호출(데이터)
     */
    public static function callProcedureOnce($procedure, $params = [], $flag_log_query = false)
    {
        // 파라미터를 콤마로 구분하여 문자열로 변환
        $placeholders = implode(',', array_map(function ($param) {
            return "'$param'";
        }, $params));

        // 저장 프로시저 호출 쿼리 생성
        $query = "CALL $procedure($placeholders)";

        // 쿼리 실행 (executeQuery 함수 호출)
        $result = self::executeQuery($query, $flag_log_query);

        // 결과 가져오기
        if ($result) {
            $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            return $data;
        }

        return false;
    }

    /**
     * 프로시저 호출(리스트)
     */
    public static function callProcedure($procedure, $params = [], $flag_log_query = false)
    {
        // 파라미터를 콤마로 구분하여 문자열로 변환
        $placeholders = implode(',', array_map(function ($param) {
            return "'$param'";
        }, $params));

        // 저장 프로시저 호출 쿼리 생성
        $query = "CALL $procedure($placeholders)";

        // 쿼리 실행 (executeQuery 함수 호출)
        $result = self::executeQuery($query, $flag_log_query);

        // 결과 가져오기
        if ($result) {
            $list = null;
            while ($data = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $list[] = $data;
            }
            return $list;
        }

        return false;
    }

}
