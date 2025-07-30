<?php
/**
 * 공지사항 모듈 클래스
 * @file    Notice.class.php
 * @author  Alpha-Edu
 * @package board
 */

namespace sFramework;
class Notice extends Board
{
    protected function setModuleConfig()
    {
        parent::setModuleConfig();
        // 파일
        $this->set('max_file', 3);
        // 에디터
        $this->set('flag_use_editor', true);
        $this->set('editor_columns', 'bd_content');
        // code
        $this->set('bd_code_arr', array(
            // 분류 X, 상태 X
            'notice' => '공지사항',
            'law' => '관계법령',
            'government' => '정부3.0정보공개',
            'statistics' => '통계·정책',
            'news' => '소식함',
            'article' => '언론기사',
            // 분류 X, 상태 O
            'bidding' => '입찰공고',
            'recruit' => '채용공고',
            'request' => '연수·워크숍',
            // 분류 O, 상태 X
            'management' => '경영공시',
            'education' => '교육·세미나',
            'program' => '문화예술교육프로그램',
            'faq' => '자주하는질문',
            'owner' => '과정문의'
        ));
        $bd_code = $this->get('bd_code');
        // 분류: 서식자료
        if ($bd_code == 'form') {
            $this->set('bd_category_arr', array('일반서식', '매뉴얼'));
        } elseif ($bd_code == 'notice') {
            $this->set('bd_category_arr', array('공지', '이벤트', '당첨자발표'));
        }
        // 상태
        if ($bd_code == 'bidding' || $bd_code == 'recruit') {
            $this->set('bd_state_arr', array(
                'W' => '예정',
                'P' => '진행',
                'E' => '종료'
            ));
        } elseif ($bd_code == 'training') {
            // 연수워크숍 게시판 상태 배열 yllee 180906
            $this->set('bd_state_arr', array(
                'W' => '대기',
                'P' => '진행',
                'E' => '마감'
            ));
            $this->set('flag_use_tel', true);
            $this->set('flag_use_email', true);
        } elseif ($bd_code == 'request') {
            // 연수원크숍신청 게시판 상태 배열 yllee 180906
            $this->set('bd_state_arr', array(
                '' => '접수중',
                'E' => '접수완료',
                'W' => '대기',
                'C' => '취소'
            ));
            $this->set('flag_use_tel', true);
            $this->set('flag_use_email', true);
        }
        // 섬네일
        if ($bd_code == 'video') {
            $this->set('flag_use_thumb', true);
            $this->set('thumb_width', 280);
            $this->set('thumb_height', 170);
        }

        if ($bd_code == 'news') {
            $this->set('flag_use_etc1', true);
            $this->set('flag_use_etc2', true);
            $this->set('flag_use_etc3', true);
            $this->set('flag_use_etc4', true);
            $this->set('flag_use_etc5', true);
            $this->set('flag_use_etc6', true);
            $this->set('flag_use_etc7', true);
            $this->set('flag_use_etc8', true);
            $this->set('flag_use_etc9', true);

            $this->set('view_partner', array(
                'Y' => '공개',
                'N' => '비공개',
                'A' => '전체공개'
            ));

            $this->set('view_tutor', array(
                'Y' => '공개',
                'N' => '비공개',
                'A' => '전체공개'
            ));
        }

        $search_like_arr = array(
            'bd_subject' => '제목',
            'bd_content' => '내용',
            'bd_writer_name' => '작성자명'
        );

        $search_like_arr_ref = array(
            'bd_subject' => '제목',
            'bd_etc1' => '과정명'
        );

        $this->set('bd_code_arr', array(
            'news' => '소식함',
            'owner' => '과정문의',
        ));

        $this->set('bd_auth_arr',array(
            'admin' => '관리자',
            'partner' => '파트너',
            'tutor' => '튜터',
            'company' => '기업담당자',
        ));

        $this->set('bd_is_display_arr', array(
            'Y' => '출력',
            'N' => '숨김',
            'A' => '메인숨김'
        ));

        $this->set('search_like_arr', $search_like_arr);

        if ($bd_code == 'reference') {
            $this->set('search_like_arr', $search_like_arr_ref);
            $this->set('flag_use_etc1', true);
            $this->set('flag_use_etc2', true);
            // 카테고리 다시 활성화 yllee 240829
            // 과정자료실 카테고리 필수값 제외: 박민주 요청 yllee 240402
            //$this->set('flag_use_category', true);
            // 과정자료실 게시판은 미리 설정하면 안됨. 주석 처리 yllee 241010
            //$this->set('flag_use_category', false);
        }

        //$this->set('order_column', "bd_is_notice DESC, CONCAT(bd_reg_date, ' ', bd_reg_time)");

        $this->set('flag_use_etc10', true);
    }

    protected function setBoardConfig()
    {
        parent::setBoardConfig();
        $bd_code = $this->get('bd_code');
        $this->set('flag_use_notice', true);
        $this->set('flag_use_display', true);
        $this->set('flag_use_reg', true);
        $live = $_POST['live'];
        if($live == 'reference'){
            $this->set('flag_use_category', true);
            $this->set('bd_code','reference');
        }

        $book = $_POST['book'];
        if($book == 'reference'){
            $this->set('flag_use_category', true);
            $this->set('bd_code','reference');
        }
        // 분류
        if ($bd_code == 'form' || $bd_code == 'faq' || $bd_code == 'program') {
            $this->set('flag_use_category', true);
        }
        if ($bd_code == 'article') {
            $this->set('flag_use_etc1', true);
        }
        if ($bd_code == 'bidding' || $bd_code == 'recruit' || $bd_code == 'program') {
            $this->set('flag_use_bgn', true);    // 시작일
            $this->set('flag_use_end', true);    // 시작일
            $this->set('flag_use_state', true);     // 상태
            $this->set('flag_use_etc1', true); // 채용공고 구분 wkkim 180425
        }
        // 연수워크숍 게시판 추가 필드 yllee 180906
        if ($bd_code == 'training') {
            $this->set('flag_use_bgn', true);
            $this->set('flag_use_end', true);
            $this->set('flag_use_state', true);
            $this->set('flag_use_etc1', true);
            $this->set('flag_use_etc2', true);
            $this->set('flag_use_etc3', true);
            $this->set('flag_use_etc4', true);
            $this->set('flag_use_etc5', true);
        } elseif ($bd_code == 'request') {
            // 연수원크숍신청 게시판 상태 배열 yllee 180906
            $this->set('flag_use_state', true);
        }

        if ($bd_code == 'reference') {
            $this->set('flag_use_etc1', true);
            $this->set('flag_use_etc2', true);
        }

        $this->set('flag_use_date', true);     // 출력용 일자 (정렬)
    }

    protected function convertDetail($data)
    {
        $data = parent::convertDetail($data);
        if ($this->get('flag_use_state')) {
            $data['state_class'] = 'bd_state_' . $data['bd_state'];
            $bd_state_arr = $this->get('bd_state_arr');
            $bd_state = $data['bd_state'];
            $data['txt_bd_state'] = $bd_state_arr[$bd_state];
        }
        // 마감일 출력 포맷 yllee 180906
        $data['txt_end_date'] = Format::beautifyEndDate($data['bd_end_date']);

        return $data;
    }
}
