<?php
/**
 * 자유게시판 모듈 클래스
 * @file    Receipt.class.php
 * @author  Alpha-Edu
 * @package board
 */

namespace sFramework;

class Qna extends Board
{
    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        // 파일
        $this->set('max_file', 3);
        // code
        $this->set('bd_code_arr', array(
            'clean' => '클린신고센터',
            'proposal' => '건의사항'
        ));

        $this->set('bd_category_arr', array(
            'homepage' => '1:1문의',
            'classroom' => '학습문의',
            'book' => '북러닝'
        ));

        $this->set('bd_state_arr', array(
            'W' => '답변대기',
            'E' => '답변완료',
            'C' => '삭제',
        ));

        $this->set('search_like_arr', array(
            'all' => '통합검색',
            'bd_subject' => '제목',
            'bd_content' => '내용',
            'bd_writer_name' => '작성자명',
            'reg_id' => '아이디'
        ));
    }

    protected function setBoardConfig()
    {
        parent::setBoardConfig();

        // flag
        $this->set('flag_use_tel',true);
        $this->set('flag_use_answer', true);   // 답변
        $this->set('flag_use_state', true);    // 상태
        $this->set('flag_use_reg', true);   // 작성일
        $this->set('flag_use_etc1', true);
        $this->set('flag_use_category', true);
    }

    protected function convertDetail($data)
    {
        $data = parent::convertDetail($data);

        // bd_state
        $data['state_class'] = 'bd_state_' . $data['bd_state'];

        return $data;
    }

    protected function makeSmsMessage($data, $mode)
    {
        $msg = '';
        if (defined('_HOMEPAGE_TITLE_')) {
            $msg .= '[' . _HOMEPAGE_TITLE_ . '] ';
        }

        if ($mode == 'reply') {
            if ($data['bd_code'] == 'clean') {
                $msg .= '작성하신 클린신고게시판에 답변이 등록되었습니다.';
            } elseif ($data['bd_code'] == 'proposal') {
                $msg .= '작성하신 건의사항에 답변이 등록되었습니다.';
            }
        }

        return $msg;
    }

    protected function initInsert()
    {
        parent::initInsert();

        $this->set('bd_code','qna');

        $this->set('insert_columns', 'bd_subject,bd_writer_name,bd_writer_tel,bd_writer_email,bd_content,bd_reg_date,bd_state,bd_category,bd_etc1');
        $this->set('required_arr', array(
            'bd_subject' => '제목',
            'bd_content' => '내용'
        ));

        $list_mode = $_POST['bd_category'];
        if($list_mode == 'classroom' || $list_mode == 'book'){
            $this->set('return_uri', './qna_view.html');
        }
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $this->set('bd_code','qna');

        $this->set('insert_columns', 'bd_subject,bd_writer_name,bd_writer_tel,bd_writer_email,bd_content,bd_reg_date,bd_state,bd_category,bd_etc1');
        $this->set('required_arr', array(
            'bd_subject' => '제목',
            'bd_content' => '내용'
        ));

        $list_mode = $_POST['bd_category'];
        if($list_mode == 'classroom' || $list_mode == 'book'){
            $this->set('return_uri', './qna_view.html');
        }

    }
}
