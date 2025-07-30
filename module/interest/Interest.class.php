<?php
/**
 * 관심분야 모듈 클래스
 * @file    Interest.class.php
 * @author  Alpha-Edu
 * @package interest
 */

namespace sFramework;

class Interest extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_interest';
    public static $pk = 'it_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'interest');
        $this->set('module_name', '관심분야');

        // 관심분야
        $this->set('it_area_arr', array(
            // a. 제조
            'a1' => '자동차',
            'a2' => '조선',
            'a3' => '철강',
            'a4' => '석유화학',
            'a5' => '반도체',
            'a6' => '플랜트',
            'a7' => '원천',
            'a8' => '섬유',
            'a9' => '시멘트',
            'a10' => '제지',
            'a11' => '방산',
            'a12' => '수산',
            'a13' => '농기계',
            'a14' => '중장비',
            'a15' => '디스플레이',
            'a16' => '우주항공',
            // b. 서비스
            'b1' => '금융',
            'b2' => '보험',
            'b3' => '통신',
            'b4' => '숙박',
            'b5' => '음식',
            'b6' => '레저',
            'b7' => '부동산',
            // c. IT신산업
            'c1' => '정보통신기기',
            'c2' => '가전',
            'c3' => '반도체',
            'c4' => '디스플레이',
            'c5' => '이차전지',
            'c6' => '바이오헬스',
            // d. 건설
            'd1' => '건설'
        ));
        // 유형
        $this->set('it_type_arr', array(
            't1' => '기술',
            't2' => '금융',
            't3' => '수출',
            't4' => '인력',
            't5' => '창업',
            't6' => '경영',
            't7' => '내수',
            't8' => '환경',
            't9' => '기타'
        ));
        // 관심정보
        $this->set('it_info_arr', array(
            'i1' => '정부지원사업',
            'i2' => '사업공모전',
            'i3' => '각 부처 R&D 사업'
        ));
    }

    public function selectInterest($us_id)
    {
        if ($us_id) {
            $data_table = $this->get('data_table');
            $db_where = "WHERE mb_id = '$us_id'";
            $data = Db::selectOnce($data_table, "*", $db_where, "");
        } else {
            $data = [];
        }
        return $data;
    }
}
