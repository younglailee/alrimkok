<?php
/**
 * 표준 모듈 클래스
 * @file    StandardModule.class.php
 * @author  Alpha-Edu
 * @package module/core
 */

namespace sFramework;

use function getimagesize;
use function is_array;

class StandardModule extends BaseObject
{
    /**
     * DB 정보 (Table, PK)
     * @var null
     */
    public static $data_table = null;
    public static $pk = null;

    /**
     * 모듈 환경설정
     */
    protected function setModuleConfig()
    {
        $this->set('module', null);
        $this->set('module_name', null);

        // DB
        $this->set('data_table', static::$data_table);
        $this->set('pk', static::$pk);

        // 검색
        $this->set('search_columns', null);
        $this->set('search_like_arr', null);
        $this->set('search_date_arr', null);
        $this->set('search_having_arr', null);
        $this->set('search_order_arr', null);

        // 정렬
        $this->set('order_column', null);
        $this->set('order_direct', 'DESC');
        $this->set('group_column', null);
        $this->set('order_arr', array(
            'reg_time' => '등록일'
        ));

        $this->set('cnt_page', 5);
        $this->set('cnt_rows', 10);
        $this->set('cnt_rows_arr', array(
            '10' => '10개씩',
            '30' => '30개씩',
            '50' => '50개씩',
            '100' => '100개씩'
        ));

        $page = $this->getRequestParameter('page');
        if (!$page) {
            $page = 1;
        }
        $this->set('page', $page);

        // 파일
        $this->set('file_table', 'tbl_file');
        $this->set('file_pk', 'fi_id');
        $this->set('max_file', 0);
        $this->set('no_uploads', 'php,asp,jsp,html,htm,js,css');

        // 썸네일
        $this->set('flag_use_thumb', false);
        $this->set('thumb_width', 0);
        $this->set('thumb_height', 0);
        $this->set('no_image', null);

        // 에디터
        $this->set('flag_use_editor', false);
        $this->set('editor_columns', null);

        // 기간
        unset($year_arr);
        for ($i = (_NOW_YEAR_ - 3); $i <= (_NOW_YEAR_ + 1); $i++) {
            $year_arr[$i] = $i . '년';
        }
        $this->set('year_arr', $year_arr);

        unset($month_arr);
        for ($i = 1; $i <= 12; $i++) {
            $month_arr[$i] = $i . '월';
        }
        $this->set('month_arr', $month_arr);

        $this->set('date_arr', array(
            Time::getAroundDate('-1d') => '어제',
            Time::getAroundDate('-7d') => '최근7일',
            Time::getAroundDate('-1m') => '최근한달'
        ));
    }

    /**
     * 클래스 초기화
     * 클래스 인스턴스 생성 후, 오버라이드 할 내용이 있다면 init() 를 오버라이드 해서 사용하세요.
     */
    public function init()
    {
        $this->setModuleConfig();
        // uid
        $pk = $this->get('pk');
        $uid = $this->getRequestParameter($pk);
        $this->set('uid', $uid);

        // cnt_rows
        $sch_cnt_rows = $this->getRequestParameter('sch_cnt_rows');
        if ($sch_cnt_rows) {
            $this->set('cnt_rows', $sch_cnt_rows);
        }

        // query string
        $this->makeQueryString();
    }

    /**
     * Select 초기화
     * List 또는 Detail 정보 조회에서 사용할 테이블의 columns와 tables를 선언합니다.
     */
    protected function initSelect()
    {
        $this->set('select_table', $this->get('data_table'));
        $this->set('select_columns', '*');
    }

    /**
     * Insert 초기화
     * POST 또는 GET에서 넘어온 변수를 자동으로 insert 하기 위해 선언합니다.
     * 수동으로 insert할 변수는 convertInsert()에서 지정하세요.
     * insert 성공 후, 리턴 URI와 메시지도 이곳에서 선언합니다.
     */
    protected function initInsert()
    {
        $this->set('insert_columns', null);
        $this->set('required_arr', null);
        $this->set('return_uri', './list.html');
        $this->set('success_msg', '정상적으로 등록하였습니다.');
    }

    /**
     * Update 초기화
     * POST 또는 GET에서 넘어온 변수를 자동으로 update 하기 위해 선언합니다.
     * 수동으로 update할 변수는 convertUpdate()에서 지정하세요.
     * update 성공 후, 리턴 URI와 메시지도 이곳에서 선언합니다.
     */
    protected function initUpdate()
    {
        $this->set('update_columns', null);
        $this->set('required_arr', null);
        $this->set('return_uri', './write.html');
        $this->set('success_msg', '정상적으로 수정하였습니다.');
    }

    /**
     * Delete 초기화
     * delete 성공 후, 리턴 URI와 메시지를 이곳에서 선언합니다.
     */
    protected function initDelete()
    {
        $this->set('fi_module', $this->get('module'));
        $this->set('return_uri', './list.html');
        $this->set('success_msg', '정상적으로 삭제하였습니다.');
    }

    /**
     * 쿼리 스트링 생성
     */
    protected function makeQueryString()
    {
        $query_string = $this->getRequestParameter('query_string');
        if (!$query_string) {
            // 검색 칼럼 추가: 게시판 카테고리, 프로그램 분야, 연령 yllee 180905
            $add_search_columns = ',like,text,date,s_date,e_date,year,month,having,s_having,e_having,order,cnt_rows';
            $add_search_columns .= ',bc_category,bd_etc1,bd_etc2';
            $arr = explode(',', $this->get('search_columns') . $add_search_columns);

            for ($i = 0; $i < count($arr); $i++) {
                $column = $arr[$i];
                $key = str_replace('.', '_', 'sch_' . $column);

                $val = $this->get($key);
                if (!$val) {
                    $val = $this->getRequestParameter($key);
                }

                if ($val) {
                    if (is_array($val)) {
                        // 빈원소 제거
                        $val = array_values(array_filter(array_map('trim', $val)));
                        for ($j = 0; $j < count($val); $j++) {
                            $query_string .= '&' . $key . '[]=' . urlencode($val[$j]);
                        }
                    } else {
                        $query_string .= '&' . $key . '=' . urlencode($val);
                    }
                }
            }
        }


        $this->set('query_string', $query_string);
    }

    /**
     * 쿼리 스트링을 배열로 반환
     * @return array
     */
    protected function getQueryStringArray()
    {
        $query_string = $this->get('query_string');
        $query_string = str_replace('?', '&', $query_string);
        $tmp_arr = explode('&', $query_string);
        unset($qs_arr);
        for ($i = 0; $i < count($tmp_arr); $i++) {
            if (!$tmp_arr[$i]) {
                continue;
            }

            $arr = explode('=', $tmp_arr[$i]);
            if ($arr[1]) {
                $qs_arr[$arr[0]] = $arr[1];
            }
        }

        return $qs_arr;
    }

    /**
     * 유효성 검사
     * @param $arr
     * @return array
     */
    protected function validateValues($arr)
    {
        // validate required columns
        $result = null;

        $required_arr = $this->get('required_arr');
        if (is_array($required_arr)) {
            foreach ($required_arr as $key => $val) {
                if (!$arr[$key]) {
                    $result = array(
                        'code' => 'failure',
                        'msg' => $val
                    );

                    if (Format::checkLastFinalSound($val)) {
                        $result['msg'] .= '을';
                    } else {
                        $result['msg'] .= '를';
                    }
                    $result['msg'] .= ' 입력해주세요.';
                    return $result;
                }
            }
        }

        $result = array(
            'code' => 'success'
        );

        return $result;
    }

    /**
     * Data 등록
     * @return array
     */
    public function insertData()
    {
        // 권한 체크
        if (!$this->checkWriteAuth()) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }
        $this->initInsert();
        $arr = $this->getParameters($this->get('insert_columns'), 'post');
        $arr = $this->convertInsert($arr);
        $result = $this->validateValues($arr);
        if ($result['code'] != 'success') {
            return $result;
        }
        $data_table = $this->get('data_table');
        if (Db::insertByArray($data_table, $arr)) {
            $result = $this->postInsert($arr);
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
        }
        return $result;
    }

    /**
     * 등록 후 처리
     * @param $arr
     * @return array
     */
    protected function postInsert($arr)
    {
        global $member;

        // 로그인 정보가 있을 때만 실행 yllee 221123
        if ($member['mb_id']) {
            $data_table = $this->get('data_table');
            $pk = $this->get('pk');
            $db_where = "WHERE reg_id = '" . $member['mb_id'] . "'";
            $db_order = "ORDER BY reg_time DESC";

            // 방문 데이터 제외: DB 서버 부하 발생으로 제외 처리 yllee 230314
            $uid = '';
            if ($data_table != 'tbl_visit') {
                $data = Db::selectOnce($data_table, $pk, $db_where, $db_order);
                $uid = $data[$pk];
                $arr[$pk] = $uid;
            }
            if (!$uid) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '등록 과정에서 장애가 발생하였습니다.'
                );
                return $result;
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
                'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid,
                'msg' => $this->get('success_msg'),
                $pk => $uid
            );
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '로그인 정보가 없어 처리할 수 없습니다.'
            );
        }
        return $result;
    }

    /**
     * 등록 자료 변환
     * @param $arr
     * @return array
     */
    protected function convertInsert($arr)
    {
        global $member;

        $arr['reg_id'] = $member['mb_id'];
        $arr['reg_time'] = _NOW_DATETIME_;

        return $arr;
    }

    /**
     * Data 수정
     * @return array
     */
    public function updateData()
    {
        $uid = $this->get('uid');
        // uid 값이 없을 경우 post 값 적용
        if (!$uid) {
            $uid = $_POST['uid'];
            $this->set('uid', $uid);
        }
        // 권한 체크
        if (!$uid || !$this->checkUpdateAuth($uid)) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.' . $uid
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

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        unset($arr[$pk]);
        //Log::debug($data_table);
        //Log::debug($arr);
        //Log::debug($pk);
        //Log::debug($uid);

        // 업데이트 테스트 minju
        //        Log::debug("민주 업데이트 테스트_course/company");
        //        Log::debug($data_table);
        //        Log::debug($arr);
        //        Log::debug($pk . '=' . $uid);
        if (Db::updateByArray($data_table, $arr, "WHERE $pk = '$uid'")) {
            // 과정정보 수정 시 엘엑스 DB tbl_progress 수정 minju 230322
            if ($data_table == 'tbl_course') {
                $pArr = array();
                $pArr['cs_code'] = $arr['cs_code'];
                $pArr['cs_name'] = $arr['cs_name'];
                $pArr['upt_id'] = $arr['upt_id'];
                $pArr['upt_time'] = $arr['upt_time'];

                Db::updateByArrayLxn('tbl_progress', $pArr, "WHERE cs_id = '$uid'");
            }
            // 기업정보 수정 시 엘엑스 DB tbl_progress 수정 minju 230322
            if ($data_table == 'tbl_company') {
                $pArr = array();
                $pArr['cp_name'] = $arr['cp_name'];
                $pArr['upt_id'] = $arr['upt_id'];
                $pArr['upt_time'] = $arr['upt_time'];

                Db::updateByArrayLxn('tbl_progress', $pArr, "WHERE cp_id = '$uid'");
            }

            $result = $this->postUpdate($arr);
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '수정 과정에서 장애가 발생하였습니다.'
            );
        }

        return $result;
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
        $page = $this->get('page');
        $query_string = $this->get('query_string');
        if ($query_string) {
            $query_string = '&' . $query_string;
        }
        $result = array(
            'code' => 'success',
            'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&page=' . $page . $query_string,
            'msg' => $this->get('success_msg')
        );
        return $result;
    }

    /**
     * 첨부파일 삭제
     * @param $fi_id
     */
    protected function deleteFile($fi_id)
    {
        $file_table = $this->get('file_table');
        $file_pk = $this->get('file_pk');

        // 원본파일 삭제
        $data = Db::selectOnce($file_table, "fi_path", "WHERE $file_pk = '$fi_id'", "");
        $file_path = $data['fi_path'];
        @unlink($file_path);

        // 썸네일 삭제
        $point_idx = strrpos($file_path, '.');

        $thumb_list = glob(trim(substr($file_path, 0, $point_idx)) . '_thumb_*');
        if (is_array($thumb_list)) {
            foreach ($thumb_list as $thumb_path) {
                unlink($thumb_path);
            }
        }

        Db::delete($file_table, "WHERE $file_pk = '$fi_id'");
    }

    /**
     * 수정 자료 변환
     * @param $arr
     * @return array
     */
    protected function convertUpdate($arr)
    {
        global $member;

        $arr['upt_id'] = $member['mb_id'];
        $arr['upt_time'] = _NOW_DATETIME_;

        return $arr;
    }

    /**
     * 상세보기 데이터 반환
     * @param $uid
     * @return array
     */
    public function selectDetail($uid)
    {
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');

        $this->initSelect();
        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');

        if ($select_table != $data_table && strpos($select_table, 'join') > -1) {
            $db_where = "where a.$pk = '$uid'";
        } else {
            $db_where = "where $pk = '$uid'";
        }

        $data = Db::selectOnce($select_table, $select_columns, $db_where, '');

        // 권한 체크
        if (!$this->checkViewAuth($data)) {
            //Log::debug($data);
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }
        return $this->convertDetail($data);
    }

    public function deleteData()
    {
        $this->initDelete();
        $list_uid_arr = $_POST['list_uid'];

        $uid = $this->get('uid');
        if (!$list_uid_arr && $uid) {
            $list_uid_arr = array(
                '0' => $uid
            );
        }

        $file_table = $this->get('file_table');
        $fi_module = $this->get('fi_module');

        for ($i = 0; $i < count($list_uid_arr); $i++) {
            $uid = $list_uid_arr[$i];

            // 권한 체크
            if ($uid && !$this->checkUpdateAuth($uid)) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '권한이 없습니다.'
                );
                return $result;
            }

            $result = $this->deleteRows($uid);
            if ($result['code'] != 'success') {
                return $result;
            }

            // 첨부파일 삭제
            if ($this->get('max_file')) {
                Db::delete($file_table, "WHERE fi_module = '$fi_module' AND fi_uid = '$uid'");
                $dir_path = $this->makeUploadDirectory($fi_module, $uid);
                File::deleteDirectory($dir_path);
            }
        }

        return $this->postDelete();
    }

    protected function postDelete()
    {
        $page = $this->get('page');
        $query_string = $this->get('query_string');
        if ($query_string) {
            $query_string = '&' . $query_string;
        }

        $result = array(
            'code' => 'success',
            'msg' => $this->get('success_msg'),
            'uri' => $this->get('return_uri') . '?page=' . $page . $query_string
        );

        return $result;
    }

    protected function deleteRows($uid)
    {
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');

        if (!Db::delete($data_table, "WHERE $pk = '$uid'")) {
            $result = array(
                'code' => 'failure',
                'msg' => '삭제 과정에 문제가 발생하였습니다.'
            );
            return $result;
        }

        $result = array(
            'code' => 'success'
        );

        return $result;
    }

    /**
     * 상세보기 데이터 변환
     * @param $data
     * @return array
     */
    protected function convertDetail($data)
    {
        // 일시
        $data['reg_date'] = substr($data['reg_time'], 0, 10);
        $data['bt_reg_date'] = str_replace('-', '.', $data['reg_date']);
        $data['reg_datetime'] = substr($data['reg_time'], 0, 16);
        $data['bt_reg_datetime'] = Format::beautifyDateTime($data['reg_time']);

        if (!$data['upt_time'] || $data['upt_time'] == '0000-00-00 00:00:00') {
            $data['upt_time'] = $data['reg_time'];
        }
        $data['upt_date'] = substr($data['upt_time'], 0, 10);
        $data['bt_upt_date'] = str_replace('-', '.', $data['upt_date']);
        $data['upt_datetime'] = substr($data['upt_time'], 0, 16);
        $data['bt_upt_datetime'] = Format::beautifyDateTime($data['upt_time']);

        // 코드
        $code_column_arr = explode(',', $this->get('code_columns'));
        for ($i = 0; $i < count($code_column_arr); $i++) {
            $code_column = $code_column_arr[$i];
            $code_arr = $this->get($code_column . '_arr');
            if (is_array($code_arr)) {
                $data['txt_' . $code_column] = $code_arr[$data[$code_column]];
            }
        }

        // 첨부파일 처리
        if ($this->get('max_file')) {
            $pk = $this->get('pk');
            $uid = $data[$pk];

            $data['file_list'] = $this->getFileList($uid);
            if ($data['file_list']) {
                $data['cnt_file'] = count($data['file_list']);
            } else {
                $data['cnt_file'] = 0;
            }
            if ($this->get('flag_use_thumb')) {
                if ($data['file_list'][0]['thumb_uri']) {
                    //Log::debug($data['file_list']);
                    // gif, jpg, png 에 대해서만 적용
                    $size = @getimagesize($data['file_list'][0]['fi_path']);
                    //Log::debug($size);
                    if ($size[2] < 1 || $size[2] > 3) {
                        if ($data['file_list'][1]['thumb_uri']) {
                            $data['thumb_uri'] = $data['file_list'][1]['thumb_uri'];
                        }
                    } else {
                        $data['thumb_uri'] = $data['file_list'][0]['thumb_uri'];
                    }
                    //$data['thumb_uri'] = $data['file_list'][0]['thumb_uri'];
                } else {
                    $data['thumb_uri'] = $this->get('no_image');
                }
            }
        }

        // 에디터 썸네일
        if ($this->get('editor_columns') && $data['cnt_file'] < 1) {
            $data['thumb_uri'] = $this->getThumbnailFromEditor($data);
        }

        return $data;
    }

    /**
     * 첨부파일 목록 반환
     * @param $uid
     * @param null $module
     * @return array|null
     */
    protected function getFileList($uid, $module = null)
    {
        if (!$module) {
            $module = $this->get('module');
        }

        $file_table = $this->get('file_table');
        $file_pk = $this->get('file_pk');
        $list = Db::select($file_table, '*', "WHERE fi_module = '$module' AND fi_uid = '$uid'", "ORDER BY $file_pk ASC", "");
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                // 파일 용량
                $list[$i]['bt_fi_size'] = Format::beautifyFileSize($list[$i]['fi_size']) . 'b';

                // 원본 경로
                if (defined('_UPLOAD_PATH_') && defined('_UPLOAD_URI_')) {
                    $list[$i]['fi_uri'] = str_replace(_UPLOAD_PATH_, _UPLOAD_URI_, $list[$i]['fi_path']);
                }

                // 썸네일
                if ($this->get('flag_use_thumb')) {
                    $list[$i]['thumb_uri'] = $this->makeThumbnail($list[$i]);
                }
            }
        }

        return $list;
    }

    /**
     * 썸네일 생성
     * @param $data
     * @return string
     */
    protected function makeThumbnail($data)
    {
        $thumb_width = $this->get('thumb_width');
        $thumb_height = $this->get('thumb_height');
        $no_image = $this->get('no_image');

        // 파일 존재 검사
        $source_file = $data['fi_path'];
        if (!file_exists($source_file)) {
            return $no_image;
        }

        // 확장자 검사
        $file_ext = strtolower(substr(strrchr($data['fi_name'], '.'), 1));
        if (!strpos('jpg,gif,png', $file_ext) > -1) {
            return $no_image;
        }

        // 썸네일 경로
        $slush_idx = strrpos($source_file, '/');
        $point_idx = strrpos($source_file, '.');
        $src_name = trim(substr($source_file, $slush_idx + 1, $point_idx - $slush_idx - 1));
        $point_ext = trim(substr($source_file, $point_idx, strlen($source_file) - $point_idx));
        $thumb_name = $src_name . '_thumb_' . $thumb_width . 'x' . $thumb_height;
        $thumb_path = str_replace($src_name . $point_ext, $thumb_name . $point_ext, $source_file);

        /*
        Log::debug($thumb_path);
        Log::debug($source_file);
        Log::debug($thumb_width);
        */

        // 썸네일 존재 검사
        if (!file_exists($thumb_path)) {
            $thumb = File::makeThumbnail($source_file, $thumb_path, $thumb_width, $thumb_height, true, true);
            if (!$thumb) {
                return str_replace(_UPLOAD_PATH_, _UPLOAD_URI_, $source_file);
            }
        }

        return str_replace(_UPLOAD_PATH_, _UPLOAD_URI_, $thumb_path);
    }

    /**
     * 에디터로 부터 썸네일 반환
     * @param array $data
     * @return string
     */
    protected function getThumbnailFromEditor($data)
    {
        $thumb_uri = '';

        $editor_column_arr = explode(',', $this->get('editor_columns'));
        $pk = $this->get('pk');
        $fi_uid = $data[$pk];
        if ($fi_uid && count($editor_column_arr) > 0 && defined('_UPLOAD_PATH_') && defined('_ROOT_PATH_')) {
            $upload_dir = str_replace(_ROOT_PATH_, '', _UPLOAD_PATH_);
            //$pattern = "/<img[^>]*src=\\\\[\'\"]?([^>\'\"]+[^>\'\"]+)\\\\[\'\"]?[^>]*>/";
            $pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/";
            unset($update_arr);
            for ($i = 0; $i < count($editor_column_arr); $i++) {
                $editor_column = $editor_column_arr[$i];
                unset($match_arr);
                preg_match_all($pattern, $data[$editor_column], $match_arr);
                if (count($match_arr[1]) > 0) {
                    for ($j = 0; $j < count($match_arr[1]); $j++) {
                        $img_arr = parse_url($match_arr[1][$j]);
                        $tmp_src = $img_arr['path'];
                        if (strpos($tmp_src, $upload_dir) > -1) {
                            $data['fi_path'] = _ROOT_PATH_ . $tmp_src;
                            $data['fi_name'] = _ROOT_PATH_ . $tmp_src;
                            $thumb = $this->makeThumbnail($data);
                            if ($thumb) {
                                return $thumb;
                            }
                        }
                    }
                }
            }
        }

        return $thumb_uri;
    }

    /**
     * 목록 집계
     * @return int
     */
    public function countTotal()
    {
        $this->initSelect();

        // table
        $select_table = $this->get('select_table');

        // where
        $db_where = $this->makeDbWhere();

        // having
        $group_column = $this->get('group_column');
        $db_having = $this->makeDbHaving();

        $cnt_total = Db::selectCount($select_table, $db_where, $group_column, $db_having);

        return $cnt_total;
    }

    /**
     * 목록 데이터 반환
     * @return array
     */
    public function selectList()
    {
        // count
        $cnt_total = $this->countTotal();
        $this->set('cnt_total', $cnt_total);

        $select_table = $this->get('select_table');
        $select_columns = $this->get('select_columns');
        $db_where = $this->get('db_where');
        //Log::debug($db_where);
        $db_having = $this->get('db_having');

        $page = $this->get('page');

        $cnt_rows = $this->get('cnt_rows');

        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;

        $db_order = $this->makeDbOrder();

        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, $db_limit);

        return $this->convertList($list);
    }

    /**
     * 목록 데이터 변환
     * @param $list
     * @return array
     */
    protected function convertList($list)
    {
        $cnt_total = $this->get('cnt_total');
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');

        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                // 기본 변환
                $list[$i] = $this->convertDetail($list[$i]);

                // 번호
                $list[$i]['no'] = number_format($cnt_total - ($page - 1) * $cnt_rows - $i);
                $list[$i]['odd'] = $i % 2;
            }
        }

        return $list;
    }

    /**
     * 조회 조건 생성 (오버라이드용)
     * @return string
     */
    protected function makeDbWhere()
    {
        $db_where = $this->getDefaultWhere();
        $this->set('db_where', $db_where);

        return $db_where;
    }

    /**
     * 조회 기본 조건 반환
     * @return string
     */
    protected function getDefaultWhere()
    {
        $db_where = 'WHERE 1 = 1';
        $qs_arr = $this->getQueryStringArray();

        // 일치 검색
        $search_columns = $this->get('search_columns');
        // 기본 검색 조건도 LIKE 조건 검색 yllee 181012
        $search_columns_like_arr = $this->get('search_columns_like_arr');

        if ($search_columns) {
            $sch_arr = explode(',', $search_columns);
            for ($i = 0; $i < count($sch_arr); $i++) {
                $column = $sch_arr[$i];
                $key = str_replace('.', '_', 'sch_' . $column);

                $val = $this->get($key);
                if (!$val) {
                    $val = $this->getRequestParameter($key, $qs_arr);
                }

                if ($val) {
                    if (is_array($val)) {
                        // 빈원소 제거
                        $val = array_values(array_filter(array_map('trim', $val)));
                        if (count($val) == 1) {
                            $db_where .= " AND $column = '" . $val[0] . "' ";
                        } elseif (count($val) > 1) {
                            $str_in = implode("', '", $val);
                            $db_where .= " AND $column in ('" . $str_in . "') ";
                        }
                    } else {
                        // 검색 필드 LIKE 조건 검색 비교 yllee 181012
                        // search_columns_like_arr 배열 존재 여부 확인 yllee 250725
                        if (is_array($search_columns_like_arr)) {
                            if (array_key_exists($column, $search_columns_like_arr)) {
                                $db_where .= " AND $column LIKE '%" . $val . "%' ";
                            } else {
                                $db_where .= " AND $column = '$val' ";
                            }
                        }
                    }
                }
            }
        }

        // 포함 검색
        $search_like_arr = $this->get('search_like_arr');
        if (is_array($search_like_arr)) {
            $sch_like = $this->get('sch_like');
            if (!$sch_like) {
                $sch_like = $this->getRequestParameter('sch_like', $qs_arr);
            }
            $this->set('sch_like', $sch_like);

            $sch_text = $this->get('sch_text');
            if (!$sch_text) {
                $sch_text = $this->getRequestParameter('sch_text', $qs_arr);
            }
            $this->set('sch_text', $sch_text);

            if ($sch_like && $sch_text) {
                if ($sch_like == 'all') {
                    unset($like_arr);
                    unset($search_like_arr['all']);
                    foreach ($search_like_arr as $k => $v) {
                        $like_arr[] = " $k LIKE '%$sch_text%' ";
                    }
                    $str_in = implode('OR', $like_arr);
                    $db_where .= " AND (" . $str_in . ") ";
                } elseif (strpos($sch_like, ',') > -1) {
                    $sch_like = explode(',', $sch_like);
                    unset($like_arr);
                    foreach ($sch_like as $k) {
                        if (array_key_exists($k, $search_like_arr)) {
                            $like_arr[] = " $k LIKE '%$sch_text%' ";
                        }
                    }

                    if (count($like_arr) > 0) {
                        $str_in = implode('OR', $like_arr);
                        $db_where .= " AND (" . $str_in . ") ";
                    }
                } elseif (array_key_exists($sch_like, $search_like_arr)) {
                    $db_where .= " AND $sch_like LIKE '%$sch_text%' ";
                }
            }
        }

        // 기간 검색
        $search_date_arr = $this->get('search_date_arr');
        if (is_array($search_date_arr)) {
            $sch_date = $this->get('sch_date');
            if (!$sch_date) {
                $sch_date = $this->getRequestParameter('sch_date', $qs_arr);
            }
            $this->set('sch_date', $sch_date);

            $sch_s_date = $this->get('sch_s_date');
            if (!$sch_s_date) {
                $sch_s_date = $this->getRequestParameter('sch_s_date', $qs_arr);
            }
            $this->set('sch_s_date', $sch_s_date);

            $sch_e_date = $this->get('sch_e_date');
            if (!$sch_e_date) {
                $sch_e_date = $this->getRequestParameter('sch_e_date', $qs_arr);
            }
            $this->set('sch_e_date', $sch_e_date);

            if ($sch_date && array_key_exists($sch_date, $search_date_arr)) {
                if ($sch_s_date && $sch_s_date != '0000-00-00') {
                    $db_where .= " AND $sch_date >= '$sch_s_date 00:00:00' ";
                }
                if ($sch_e_date && $sch_e_date != '0000-00-00') {
                    $db_where .= " AND $sch_date <= '$sch_e_date 23:59:59' ";
                }
            }
        }

        return $db_where;
    }

    /**
     * 그룹 조건 생성 (오버라이드용)
     * @return string
     */
    protected function makeDbHaving()
    {
        $db_having = $this->getDefaultHaving();
        $this->set('db_having', $db_having);

        return $db_having;
    }

    /**
     * 그룹 기본 조건 반환
     * @return string
     */
    protected function getDefaultHaving()
    {
        $group_column = $this->get('group_column');
        if (!$group_column) {
            return '';
        }
        $db_having = 'GROUP BY ' . $group_column;

        $search_having_arr = $this->get('search_having_arr');
        if (is_array($search_having_arr)) {
            $qs_arr = $this->getQueryStringArray();

            $sch_having = $this->get('sch_having');
            if (!$sch_having) {
                $sch_having = $this->getRequestParameter('sch_having', $qs_arr);
            }
            $this->set('sch_having', $sch_having);

            $sch_s_having = $this->get('sch_s_having');
            if (!$sch_s_having) {
                $sch_s_having = $this->getRequestParameter('sch_s_having', $qs_arr);
            }
            $this->set('sch_s_having', $sch_s_having);

            $sch_e_having = $this->get('sch_e_having');
            if (!$sch_e_having) {
                $sch_e_having = $this->getRequestParameter('sch_e_having', $qs_arr);
            }
            $this->set('sch_e_having', $sch_e_having);

            if ($sch_having && array_key_exists($sch_having, $search_having_arr)) {
                $db_having = ' HAVING 1 = 1 ';
                if ($sch_s_having) {
                    $db_having .= " AND $sch_having >= '$sch_s_having' ";
                }
                if ($sch_e_having) {
                    $db_having .= " AND $sch_having <= '$sch_e_having' ";
                }
            }
        }

        return $db_having;
    }

    /**
     * 정렬 조건 생성 (오버라이드용)
     * @return string
     */
    protected function makeDbOrder()
    {
        $db_order = $this->getDefaultOrder();
        $this->set('db_order', $db_order);

        return $db_order;
    }

    /**
     * 정렬 기본 조건 반환
     * @return string
     */
    protected function getDefaultOrder()
    {
        $order_column = $this->get('order_column');
        $order_direct = $this->get('order_direct');
        if (!$order_column) {
            $order_column = 'reg_time';
            $this->set('order_column', $order_column);
        }

        $select_table = $this->get('select_table');
        $data_table = $this->get('data_table');
        if ($select_table != $data_table &&
            strpos(strtoupper($select_table), 'JOIN') > -1 &&
            strpos(strtoupper($order_column), 'A.') < 0) {
            $order_column = 'A.' . $order_column;
        }

        $db_order = 'ORDER BY ' . $order_column . ' ' . $order_direct . ' ';

        return $db_order;
    }

    /**
     * 페이지네이션 배열 반환
     * @return array
     */
    public function getPageArray()
    {
        $cnt_total = $this->get('cnt_total');
        $cnt_rows = $this->get('cnt_rows');
        $cnt_page = $this->get('cnt_page');
        $page = $this->get('page');

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
                'page' => ($now_group - 1) * $cnt_page,
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
                'class' => ($tmp_page == $page) ? 'on now' : ''
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

    /**
     * 페이지네이션 배열 반환
     * @return array
     */
    public function getMobilePageArray()
    {
        $cnt_total = $this->get('cnt_total');
        $cnt_rows = $this->get('cnt_rows');
        $cnt_page = $this->get('cnt_page');
        $page = $this->get('page');

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
                'subject' => '<i class="fa fa-angle-double-left"></i>',
                'class' => 'arrow begin'
            );

            $page_arr[] = array(
                'page' => ($now_group - 2) * $cnt_page + 1,
                'title' => '이전',
                'subject' => '<i class="fa fa-angle-left"></i>',
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
                'subject' => number_format($tmp_page),
                'class' => ($tmp_page == $page) ? 'on' : ''
            );
        }

        // 다음&끝
        if ($now_group < $total_group) {
            $page_arr[] = array(
                'page' => $now_group * $cnt_page + 1,
                'title' => '다음',
                'subject' => '<i class="fa fa-angle-right"></i>',
                'class' => 'arrow next'
            );

            $page_arr[] = array(
                'page' => $total_page,
                'title' => '끝',
                'subject' => '<i class="fa fa-angle-double-right"></i>',
                'class' => 'arrow end'
            );
        }

        return $page_arr;
    }

    protected function uploadFiles($fi_uid, $fi_module = null)
    {
        if (!$fi_module) {
            $fi_module = $this->get('module');
        }

        //Log::debug($_FILES);
        //Log::debug($fi_module);

        if ($fi_module) {
            $dir_path = $this->makeUploadDirectory($fi_module, $fi_uid);
            $atch_file_arr = $_FILES['atch_file'];
            $file_type_arr = $_POST['fi_type'];
            $max_file = $this->get('max_file');

            // 첨부파일 개수 오류 수정 yllee & jepark 191120
            //for ($i = 0; $i < count($atch_file_arr); $i++) {
            for ($i = 0; $i < $max_file; $i++) {
                if (!$atch_file_arr['tmp_name'][$i]) {
                    continue;
                }
                $atch_file = array(
                    'tmp_name' => $atch_file_arr['tmp_name'][$i],
                    'name' => $atch_file_arr['name'][$i],
                    'type' => $atch_file_arr['type'][$i],
                    'size' => $atch_file_arr['size'][$i],
                    'error' => $atch_file_arr['error'][$i]
                );
                $file_name = $this->makeStoredFileName($file_type_arr[$i]);
                $result = $this->uploadFile($atch_file, $dir_path, $file_name);
                if ($result['code'] == 'success') {
                    global $member;

                    $size_arr = @getimagesize($result['file_path']);
                    //if ($size_arr['2'] == '2') {
                    if ($size_arr[2] == '2' || $size_arr[2] == '3') {
                        $fi_img_width = $size_arr['0'];
                        $fi_img_height = $size_arr['1'];
                    } else {
                        $fi_img_width = 0;
                        $fi_img_height = 0;
                    }
                    // 파일명 '(홀따옴표) -> ’(아포스트로피) 변환 yllee 190812
                    $fi_name = str_replace("'", "’", $atch_file['name']);

                    $file_arr = array(
                        'fi_module' => $fi_module,
                        'fi_uid' => $fi_uid,
                        'fi_path' => $result['file_path'],
                        'fi_name' => $fi_name,
                        'fi_type' => $file_type_arr[$i],
                        'fi_size' => $atch_file['size'],
                        'fi_img_width' => $fi_img_width,
                        'fi_img_height' => $fi_img_height,
                        'fi_cnt_download' => 0,
                        'reg_id' => $member['mb_id'],
                        'reg_time' => _NOW_DATETIME_
                    );
                    Db::insertByArray($this->get('file_table'), $file_arr);
                }
            }
        }
    }

    protected function uploadFile($atch_file, $dir_path, $file_name = null, $flag_rewrite = false)
    {
        $result = array(
            'code' => 'failure'
        );

        if (!is_uploaded_file($atch_file['tmp_name'])) {
            return $result;
        }

        $file_ext = strtolower(substr(strrchr($atch_file['name'], '.'), 1));
        if (strpos($this->get('no_uploads'), $file_ext) > -1) {
            return $result;
        }

        File::makeDirectory($dir_path);
        if (!$file_name) {
            $file_name = strtolower(substr($atch_file['name'], 0, strrpos($atch_file['name'], '.')));
        }
        $file_path = $dir_path . '/' . $file_name . '.' . $file_ext;

        if (!$flag_rewrite) {
            $seq = 1;
            while (true) {
                if (file_exists($file_path)) {
                    $file_path = $dir_path . '/' . $file_name . '_' . $seq . '.' . $file_ext;
                    $seq++;
                } else {
                    break;
                }
            }
        }

        move_uploaded_file($atch_file['tmp_name'], $file_path);
        chmod($file_path, 0707);

        $result = array(
            'code' => 'success',
            'file_path' => $file_path
        );

        return $result;
    }

    /**
     * 첨부파일 다운로드
     * @param int $fi_id
     * @return array
     */
    public function downloadFile($fi_id)
    {
        // 파일 정보
        $file_table = $this->get('file_table');
        $file_pk = $this->get('file_pk');
        $data = Db::selectOnce($file_table, "fi_path, fi_name, fi_size, fi_uid", "WHERE $file_pk = '$fi_id'", "");

        //Log::debug($this->checkDownloadAuth($data['fi_uid']));

        if (!$this->checkDownloadAuth($data['fi_uid'])) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }

        // 다운로드 수 증가
        Db::update($file_table, "fi_cnt_download = fi_cnt_download + 1", "WHERE $file_pk = '$fi_id'");

        $file_path = $data['fi_path'];
        $file_name = urlencode($data['fi_name']);


        if (!is_file($file_path) || !file_exists($file_path)) {
            Html::alert('파일이 존재하지 않습니다.');
        }

        // 다운로드 실행
        if (preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])) {
            header("content-type: doesn/matter");
            header("content-length: " . filesize("$file_path"));
            header("content-disposition: attachment; filename=\"$file_name\"");
            header("content-transfer-encoding: binary");
        } else {
            header("content-type: file/unknown");
            header("content-length: " . filesize("$file_path"));
            header("content-disposition: attachment; filename=\"$file_name\"");
            header("content-description: php generated data");
        }
        header("pragma: no-cache");
        header("expires: 0");
        flush();


        $fp = fopen($file_path, 'rb');
        $download_rate = 10;
        while (!feof($fp)) {
            print fread($fp, round($download_rate * 1024));
            flush();
            usleep(1000);
        }
        fclose($fp);
        flush();

        exit;
    }

    /**
     * 다운로드 권한 검사
     * @param $fi_uid
     * @return bool
     */
    public function checkDownloadAuth($fi_uid)
    {
        if (!$fi_uid) {
            return false;
        }

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $data = Db::selectOnce($data_table, "*", "WHERE $pk = '$fi_uid'", '');
        if (!$data[$pk]) {
            return false;
        }
        return $this->checkViewAuth($data);
    }

    /**
     * 파일 저장명 생성
     * @param null $file_type
     * @return string
     */
    protected function makeStoredFileName($file_type = null)
    {
        return Time::makeTimeHash();
    }

    /**
     * 업로드 디렉토리 생성
     * @param $module
     * @param $uid
     * @return string
     */
    protected function makeUploadDirectory($module, $uid)
    {
        if (!defined('_UPLOAD_PATH_')) {
            return null;
        }

        $dir_path = _UPLOAD_PATH_ . '/' . $module . '/' . substr($uid, 0, 1) . '/' . $uid;
        File::makeDirectory($dir_path);

        return $dir_path;
    }

    protected function moveEditorImages($arr, $editor_columns = null, $fi_module = null)
    {
        if (!$editor_columns) {
            $editor_columns = $this->get('editor_columns');
        }

        if (!$fi_module) {
            $fi_module = $this->get('module');
        }

        $editor_column_arr = explode(',', $editor_columns);
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $fi_uid = $arr[$pk];

        if ($fi_uid && count($editor_column_arr) > 0 && $fi_module && defined('_ROOT_PATH_') && defined('_UPLOAD_PATH_')) {
            $upload_dir = str_replace(_ROOT_PATH_, '', _UPLOAD_PATH_);
            $dir_path = $this->makeUploadDirectory($fi_module, $fi_uid);
            $pattern = "/<img[^>]*src=\\\\[\'\"]?([^>\'\"]+[^>\'\"]+)\\\\[\'\"]?[^>]*>/";
            unset($update_arr);
            for ($i = 0; $i < count($editor_column_arr); $i++) {
                $editor_column = $editor_column_arr[$i];
                unset($match_arr);
                preg_match_all($pattern, $arr[$editor_column], $match_arr);
                if (count($match_arr[1]) > 0) {
                    $update_arr[$editor_column] = $arr[$editor_column];
                    for ($j = 0; $j < count($match_arr[1]); $j++) {
                        $img_arr = parse_url($match_arr[1][$j]);
                        $tmp_src = $img_arr['path'];
                        if (strpos($tmp_src, $upload_dir) > -1) {
                            countinue;
                        } else {
                            $img_name = basename($tmp_src);
                            $tmp_path = _EDITOR_PATH_ . '/' . $tmp_src;

                            // 디렉토리
                            $new_path = $dir_path . '/' . $img_name;
                            $new_src = str_replace(_UPLOAD_PATH_, _UPLOAD_URI_, $new_path);

                            @copy($tmp_path, $new_path);
                            @unlink($tmp_path);
                            $update_arr[$editor_column] = str_replace($tmp_src, $new_src, $update_arr[$editor_column]);
                        }
                    }
                }
            }
            if (is_array($update_arr)) {
                if (count($update_arr) > 0) {
                    Db::updateByArray($data_table, $update_arr, "WHERE $pk = '$fi_uid'");
                }
            }
        }
    }

    /**
     * 목록보기 권한 검사
     * @return bool
     */
    public function checkListAuth()
    {
        return true;
    }

    /**
     * 상세보기 권한 검사
     * @param $data
     * @return bool
     */
    public function checkViewAuth($data)
    {
        global $is_root, $member;

        // 관리자이면 통과
        if ($is_root) {
            return true;
        }

        // 작성자가 본인이면 통과
        if ($data['reg_id'] == $member['mb_id']) {
            return true;
        }

        // 비밀글이 아니라면 통과
        if ($data['is_secret'] != 'Y') {
            return true;
        }

        return false;
    }

    /**
     * 등록 권한 검사
     * @return bool
     */
    public function checkWriteAuth()
    {
        global $is_root;

        // 관리자이면 통과
        if ($is_root) {
            return true;
        }

        return false;
    }

    /**
     * 수정 권한 검사
     * @param $uid
     * @return bool
     */
    public function checkUpdateAuth($uid)
    {
        global $is_root, $member;
        //Log::debug('----------------------------------');
        //Log::debug($is_root);
        //Log::debug($member);
        //Log::debug($uid);
        //Log::debug('----------------------------------');

        // 관리자이면 통과
        if ($is_root) {
            return true;
        }

        // 작성자가 본인이면 통과
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        if (Db::selectCount($data_table, "WHERE $pk = '$uid' and reg_id = '" . $member['mb_id'] . "'")) {
            return true;
        }

        return false;
    }
}
