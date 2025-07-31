<?php
/**
 * 운영자 모듈 클래스
 * @file    Popup.class.php
 * @author  Alpha-Edu
 * @package admin
 */

namespace sFramework;

use function count;

class Popup extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_popup';
    public static $pk = 'pu_id';

    /**
     * 모듈 환경설정
     */
    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'popup');
        $this->set('module_name', '팝업');
        $this->set('flag_use_editor', true);
        $this->set('editor_columns', 'pu_content');

        // 검색
        $this->set('search_columns', 'pu_is_display');
        $this->set('search_like_arr', array(
            'writer_name' => '작성자',
            'pu_subject' => '제목'
        ));

        $this->set('search_date_arr', array(
            'pu_bgn_date' => '시작일',
            'pu_end_date' => '종료일'
        ));

        // 정렬
        $cnt_rows_arr = $this->get('cnt_rows_arr');
        $cnt_rows_arr[1] = '1개씩';
        $this->set('cnt_rows_arr', $cnt_rows_arr);

        // 파일
        $this->set('max_file', 3);

        // 썸네일
        $this->set('flag_use_thumb', true);
        $this->set('thumb_width', 160);
        $this->set('thumb_height', 90);

        // code
        $this->set('pu_is_display_arr', array(
            'Y' => '출력',
            'N' => '숨김'
        ));
        $this->set('img_size', '400 * 480');
    }

    protected function initInsert()
    {
        parent::initInsert();

        $this->set('insert_columns', 'pu_subject,pu_content,pu_is_display,pu_is_login,pu_bgn_date,pu_bgn_time,pu_end_date,pu_end_time,pu_alt,pu_uri,pu_size_width,pu_size_height,pu_position_left,pu_position_top,writer_name');
        $this->set('required_arr', array(
            'pu_subject' => '제목',
            'pu_is_display' => '제목',
            'pu_position_left' => '좌측창위치',
            'pu_position_top' => '상단창위치',
            'pu_bgn_date' => '출력기간',
            'pu_end_date' => '출력기간'
        ));
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $this->set('update_columns', 'pu_subject,pu_content,pu_is_display,pu_is_login,pu_bgn_date,pu_bgn_time,pu_end_date,pu_end_time,pu_alt,pu_uri,pu_size_width,pu_size_height,pu_position_left,pu_position_top');
        $this->set('required_arr', array(
            'pu_subject' => '제목',
            'pu_is_display' => '제목',
            'pu_position_left' => '좌측창위치',
            'pu_position_top' => '상단창위치',
            'pu_bgn_date' => '출력기간',
            'pu_end_date' => '출력기간'
        ));
    }

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
                    $data['thumb_uri'] = $data['file_list'][0]['thumb_uri'];
                } else {
                    $data['thumb_uri'] = $this->get('no_image');
                }
            }
        }

        // 에디터 썸네일
        if ($data['file_list']) {
            if ($this->get('editor_columns') && count($data['file_list']) < 1) {
                $data['thumb_uri'] = $this->getThumbnailFromEditor($data);
            }
        }
        return $data;
    }

    public function selectDisplayList()
    {
        $data_table = $this->get('data_table');
        $order_column = $this->get('order_column');
        $db_where = "WHERE pu_is_display = 'Y' ";
        $db_where .= "AND ('" . _NOW_DATETIME_ . "' BETWEEN CONCAT(pu_bgn_date, ' ', pu_bgn_time) ";
        $db_where .= "AND CONCAT(pu_end_date, ' ', pu_end_time)) ";
        //echo $db_where;
        $list = Db::select($data_table, "*", $db_where, "", "");

        return $this->convertList($list);

    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);

        global $member;

        $arr['writer_name'] = $member['mb_name'];

        return $arr;
    }
}
