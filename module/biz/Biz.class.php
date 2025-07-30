<?php
/**
 * 사용자 모듈 클래스
 * @file    User.class.php
 * @author  Alpha-Edu
 * @package user
 */

namespace sFramework;

use Exception;
use sFramework\Api;
use sFramework\Db;
use sFramework\File;
use sFramework\Format;
use sFramework\Html;
use sFramework\StandardModule;
use function array_merge;
use function count;
use function implode;
use function is_array;
use function preg_match;
use function strlen;
use function strpos;
use function strtolower;
use const _NOW_DATETIME_;

class Biz extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_biz_notice';
    public static $pk = 'bz_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'biz_notice');
        $this->set('module_name', '공고');
        $this->set('max_file', 5);

        // 검색
        $this->set('search_columns', 'mb_level,flag_auth,flag_use,flag_test,flag_tomocard');
        $this->set('search_like_arr', array(
            'all' => '통합검색',
            'mb_name' => '이름',
            'mb_id' => '아이디',
            'mb_tel' => '연락처',
            'cp_name' => '기업명',
        ));
        $this->set('search_date_arr', array(
            'reg_time' => '등록일',
            'mb_login_time' => '최근로그인'
        ));
        $this->set('order_arr', array(
            'reg_time' => '가입일',
            'mb_login_time' => '최근로그인'
        ));
        // code
        $this->set('mb_level_arr', array(
            '1' => '수강생',
            '5' => '튜터',
            '4' => '기업관리자',
            '6' => '파트너',
            '3' => '프리랜서',
        ));

        $this->set('flag_tomocard_arr', array(
            'Y' => '가입',
            'N' => '미가입'
        ));
        $this->set('flag_use_arr', array(
            'work' => '재직',
            'retirement' => '퇴직',
            'LOA' => '휴직',
            'out' => '탈퇴'
        ));

        $this->set('sm_state_arr', array(
            '0000' => '전송성공',
            '0001' => '접속에러',
            '0002' => '인증에러',
            '0003' => '잔여콜수 없음',
            '0004' => '메시지 형식에러',
            '0005' => '콜백번호 에러',
            '0006' => '수신번호 개수 에러',
            '0007' => '예약시간 에러',
            '0008' => '잔여콜수 부족',
            '0009' => '전송실패',
            '0010' => '이미지없음',
            '0011' => '이미지전송오류',
            '0012' => '메시지 길이오류',
            '0030' => '발신번호 사전등록 미등록',
            '0033' => '발신번호 형식에러',
            '0080' => '발송제한',
            '6666' => '일시차단',
            '9999' => '요금미납',
        ));

        $this->set('flag_test_arr', array(
            'Y' => '사용',
            'N' => '미사용'
        ));

        $this->set('flag_tomocard_arr', array(
            'Y' => '사용',
            'N' => '미사용'
        ));

        $this->set('flag_auth_arr', array(
            'Y' => '인증',
            'N' => '미인증',
            'A' => '수기인증'
        ));

        $this->set('mb_stu_type_arr', array(
            '002' => '구직자',
            '003' => '채용예정자',
            '006' => '전직/이직예정자',
            '007' => '자사근로자',
            '008' => '타사근로자',
            '013' => '일용근로자',
            '983' => '취득예정자(일용포함)',
            '984' => '고용유지훈련',
            '985' => '적용제외근로자'
        ));

        $this->set('mb_irregular_type_arr', array(
            '000' => '비정규직해당없음',
            '012' => '파견근로자',
            '013' => '일용근로자',
            '014' => '기간제근로자',
            '020' => '단기간근로자',
            '021' => '무급휴업/휴직자',
            '022' => '임의가입자영업자',
            '987' => '분류불능'
        ));
        // 부서: 서원유통
        // 기장서부점 추가 yllee 240529
        $this->set('sw_depart_arr', array(
            '001' => '감만점',
            '002' => '감천점',
            '003' => '경주동부점',
            '004' => '경주용강점',
            '005' => '경주황성점',
            '006' => '고성점',
            '007' => '공산1팀',
            '008' => '공산2팀',
            '009' => '공산3팀',
            '010' => '관리팀',
            '011' => '구산점',
            '012' => '구포점',
            '013' => '금사점',
            '014' => '기장서부',
            '015' => '기장점',
            '016' => '김해삼계점',
            '017' => '김해외동점',
            '018' => '김해점',
            '019' => '남부민점',
            '020' => '남산점',
            '021' => '농산부',
            '022' => '뉴창원점',
            '023' => '다대점',
            '024' => '당리점',
            '025' => '대구점',
            '026' => '델리사업부',
            '027' => '동부지역농산물집배송장',
            '028' => '마산석전점',
            '029' => '마산점',
            '030' => '마산호계점',
            '031' => '물금역점',
            '032' => '물류부',
            '033' => '밀양가곡점',
            '034' => '반송점',
            '035' => '반여점',
            '036' => '사직점',
            '037' => '사천점',
            '038' => '삼문점',
            '039' => '삼방점',
            '040' => '삼천포점',
            '041' => '생식품본부검품팀',
            '042' => '생식품본부관리팀',
            '043' => '서대신점',
            '044' => '서부지역농산물집배송장',
            '045' => '서진주점',
            '046' => '수산부',
            '047' => '시설부',
            '048' => '신다대점',
            '049' => '신촌점',
            '050' => '신평점',
            '051' => '안강점',
            '052' => '양산범어점',
            '053' => '양산북부점',
            '054' => '양산북정점',
            '055' => '언양점',
            '056' => '연제점',
            '057' => '영도동삼점',
            '058' => '영도봉래점',
            '059' => '영업팀',
            '060' => '영천점',
            '061' => '온천점',
            '062' => '운영부',
            '063' => '울산구영점',
            '064' => '울산남창점',
            '065' => '울산천상점',
            '066' => '울산화봉점',
            '067' => '임원실',
            '068' => '장림점',
            '069' => '장승포점',
            '070' => '장유관동점',
            '071' => '점포개발팀',
            '072' => '정관점',
            '073' => '중부지역농산물집배송장',
            '074' => '진영점',
            '075' => '진주금산점',
            '076' => '진주점',
            '077' => '진주주약점',
            '078' => '진주호탄점',
            '079' => '진해점',
            '080' => '창녕점',
            '081' => '창원동읍점',
            '082' => '창원반지점',
            '083' => '청도점',
            '084' => '초량점',
            '085' => '축산부',
            '086' => '충무공점',
            '087' => '통영점',
            '088' => '통영죽림점',
            '089' => '판촉실',
            '090' => '포항문덕점',
            '091' => '포항우현점',
            '092' => '포항유강점',
            '093' => '포항죽도점',
            '094' => '해운대점',
            '095' => '현풍점',
            '096' => '화명점',
            '097' => '안전보건관리팀',
            '098' => '부곡점',
            '099' => '옥포점',
            '100' => '진해경화점',
            '101' => '진해풍호점',
            '102' => '기장서부점'
        ));
        // 부서: 서원홀딩스
        $this->set('sh_depart_arr', array(
            '001' => '경리부',
            '002' => '기획감사실',
            '003' => '물류부',
            '004' => '법무실',
            '005' => '부속실',
            '006' => '임원실',
            '007' => '전산실',
            '008' => '총무부',
            '009' => '축산가공부',
            '010' => '해외사업팀'
        ));
        // 부서: 엑스퍼트 yllee 220830
        $this->set('expt_depart_arr', array(
            '001' => '인천'
        ));
        // 부서: (주) 중원이엔아이 minju 230705
        // 성산 추가(배가람 요청) yllee 240704
        $this->set('jw_depart_arr', array(
            '001' => '본사',
            '002' => '군산',
            '003' => '남부',
            '004' => '대구',
            '005' => '동부',
            '006' => '마산',
            '007' => '여수',
            '008' => '온산',
            '009' => '울산',
            '010' => '울주',
            '011' => '의창',
            '012' => '중부',
            '013' => '창원',
            '014' => '칠서',
            '015' => '함안',
            '016' => '광양',
            '017' => '성산'
        ));
        // 부서: (주)알파에듀 yllee 230911
        $this->set('alpha_depart_arr', array(
            '001' => '관리부',
            '002' => '경영기획팀',
            '003' => '교육운영팀',
            '004' => '산업안전보건교육센터',
            '005' => '기업부설연구소'
        ));
        // 부서: (주)한양이엔지 geosan 240226
        // 114 기흥/화성 삼성물산 GH FAB 마감공사 현장 추가(배가람 요청) yllee 240704
        $this->set('hye_depart_arr', array(
            '001' => '경영지원실 기획팀',
            '002' => '경영지원실 인사팀',
            '003' => '경영지원실 경영 기타',
            '004' => '경영지원실 IT팀',
            '005' => '경영지원실 재경팀',
            '006' => '경영지원실 외주개발팀',
            '007' => '경영지원실 구매팀',
            '008' => 'ESG안전품질센터',
            '009' => 'ESG안전품질센터 시설팀',
            '010' => '제조물류센터',
            '011' => 'WELD MASTER CENTER',
            '012' => '제조물류센터 (청주 shop장)',
            '013' => '제조물류센터 (청주 공장제조)',
            '014' => '제조물류센터 (Hook-up 공장제조)',
            '015' => '제조물류센터 (장비팀)',
            '016' => '관리담당 대표이사실',
            '017' => '영업담당 대표이사실',
            '018' => '하이테크BU',
            '019' => '하이테크BU HT사업관리',
            '020' => '하이테크BU HT사업관리 (기술지원 pool)',
            '021' => '하이테크BU SEC PU',
            '022' => '평택 삼성전자 P3-Ph1 Hook Up (2022년)',
            '023' => '평택 삼성전자 P3 Ph2 Hook Up (2022년)',
            '024' => '평택 삼성전자 P3 Ph3 Hook Up (2022년) ',
            '025' => '평택 삼성전자 P3-Ph3,4 5D Hook Up 설계 (2023년)',
            '026' => '평택 삼성전자 5D Hook Up 설계 유지보수 (2024년)',
            '027' => '기흥/평택 삼성전자 Chemical 유지보수 (2024년)',
            '028' => '화성 삼성전자 H1 Hook Up 유지보수 (2024년)',
            '029' => '화성 삼성전자 H2 Hook Up 유지보수 (2024년)',
            '030' => '평택 삼성전자 Hook Up 유지보수 (2024년)',
            '031' => '하이테크BU 중부 PU',
            '032' => '천안 삼성물산 C3 GAS&PCW&GCS 배관공사 (2022년)',
            '033' => '천안 삼성전자 HBM C3,C4 PJT Hook-Up (2023년) ',
            '034' => '천안/온양 삼성전자 연간단가 Hook Up 유지보수 (2024년)',
            '035' => '하이테크BU Hynix PU',
            '036' => '이천 SK Hynix M16 Ph-2 UT배관공사 ( Bulk GAS, PGS )',
            '037' => '이천 SK 하이닉스 M10 Hook-Up 공사 (2022년)',
            '038' => '이천 SK 하이닉스 M14 Hook-Up 공사 (2022년)',
            '039' => '이천 SK 하이닉스 M16 PH-2 Hook-Up 공사 (2022년)',
            '040' => '청주 SK 하이닉스 M15 PH-2 Hook-Up 공사 (2022년)',
            '041' => '청주 SK Hynix M15 HBM Project UT 배관공사(2023년)',
            '042' => '이천 SK Hynix M16 EUV 구간 UT 배관공사 (2023년)',
            '043' => '하이테크BU HT현장시공연구',
            '044' => 'EPC BU',
            '045' => 'EPC BU PCM1팀',
            '046' => 'EPC BU PCM1팀 (지원 Pool)',
            '047' => '울산 린데수소에너지 LH2 PJT 기계배관 설치공사',
            '048' => '구미 SKEE SK실트론 N-Project HVAC공사 PKG3',
            '049' => '기흥 LGK NRD-K OSBL 공사',
            '050' => '탕정 린데코리아 A6 Bulk Gas 공사',
            '051' => '인천 TOK첨단재료 R&D 프로젝트',
            '052' => '양산 Ecolab Project Capacitor',
            '053' => '진천 한화솔루션 Clean room 구축공사 및 Utility 배관 공사',
            '054' => '탕정 LEK SP-85 ASU 기계배관 공사',
            '055' => '구미 LGD AP3 Auto L PJT 대응 CR&UT공사',
            '056' => '청주 SK에코플랜트 스마트에너지센터(발전소) 수폐수처리시설(EP)',
            '057' => '울산 SK에코엔지니어링 Project WT&WWT System 시운전',
            '058' => 'EPC BU 설계팀',
            '059' => 'EPC BU 토목/건축팀',
            '060' => 'EPC BU 모듈총괄PU',
            '061' => 'EPC BU 모듈PU',
            '062' => '기흥 삼성물산 NRD-K PJT 일반배관공사 2공구',
            '063' => '평택 삼성물산 P4 PH1 Module 공사',
            '064' => '기흥/화성 삼성물산 GH-Retrofit 2차 (2023년)',
            '065' => '평택 삼성물산 FAB Retrofit (2023년)',
            '066' => 'EPC BU PCM2팀',
            '067' => 'EPC BU 품질/안전팀',
            '068' => 'EPC BU 영업팀',
            '069' => 'EPC BU 영업팀 환경에너지',
            '070' => 'EPC BU 영업팀 견적',
            '071' => 'EPC BU 영업팀 산업플랜트',
            '072' => 'EPC BU 우주항공PU',
            '073' => '우주항공BU 사업관리',
            '074' => '부품개발 및 설계-시험',
            '075' => '대전 우나스텔라 발사체 개발 지원 용역',
            '076' => '누리호 고도화사업 부품개발 및 시험',
            '077' => '시스템BU',
            '078' => '시스템BU 기술연구소',
            '079' => '시스템BU 기술연구소 연구2팀(설계)',
            '080' => '시스템BU 기술연구소 연구1팀(개발)',
            '081' => '시스템BU 기술연구소 연구3팀(제어)',
            '082' => '시스템BU 기술연구소 설계팀',
            '083' => '시스템BU 제조기술부 자동제어팀',
            '084' => '시스템BU 제조기술부 생산관리팀',
            '085' => '시스템BU 제조기술부 제조팀',
            '086' => '시스템BU 제조기술부 자재관리팀',
            '087' => '시스템BU 사업관리팀',
            '088' => '시스템BU SYS영업부',
            '089' => '시스템BU 장비영업부 해외영업팀',
            '090' => '시스템BU 장비영업부 기술지원 Pool',
            '091' => '시스템BU 장비영업부 운영팀',
            '092' => '장비 기흥 삼성 SPOT JOB (2023년)',
            '093' => '장비 화성 삼성 SPOT JOB (2023년)',
            '094' => '장비 수원 삼성종합기술원 SPOT JOB (2023년)',
            '095' => '장비 화성 삼성 15L CCSS H2SO4 LPC 투자',
            '096' => '장비 화성 삼성 S3L CCSS 방폭 불합리 개선',
            '097' => '장비 수원 삼성종합기술원 파일럿2동 NH4OH 투자',
            '098' => '장비 화성 삼성 Chemical LPC Box 투자',
            '099' => '시스템BU 장비영업부 국내영업팀',
            '100' => '시스템BU 장비영업부 슬러리팀',
            '101' => '장비 평택 삼성 P4-PH1 CCSS 설비 투자',
            '102' => '시스템BU 품질보증팀',
            '103' => '전략경영팀',
            '104' => '장비 화성 삼성 S3V CMP 3GAP PCMP3205 투자',
            '105' => '장비 화성 삼성 SPOT JOB (2023년)',
            '106' => '장비 평택 삼성 P3-PH4 CCSS 제작 설치공사',
            '107' => '장비 평택 삼성 P4-PH1 CCSS 설비 투자',
            '108' => '화성 삼성전자 5D Hook Up 설계 유지보수 (2023년)',
            '109' => '경영지원실',
            '110' => '경영지원실 인사팀 (인력 Pool)',
            '111' => '천안 삼성물산 C2,C4 GAS&PCW 마감공사(2024)',
            '112' => '시스템BU 공정혁신부',
            '113' => '포항 포스코 산업가스 공급설비 신설공사',
            '114' => '기흥/화성 삼성물산 GH FAB 마감공사',
            '115' => '제조물류센터 (물류창고)',
            '116' => '고흥 우주센터 연소기-터보펌프 시험설비 운용 용역',
            '117' => '장비 기흥 삼성 NRD-K PJT CCSS 설비 투자',
            '118' => '시스템BU 공정혁신부 공정PI팀',
            '119' => '시스템BU 기술연구소 연구4팀(자동화)',
            '120' => '시스템BU 장비영업부',
            '121' => '장비 평택 삼성 P1D CHEMICAL LPC 투자',
            '122' => '장비 평택 삼성 P1L 폐인산 HF Flushing System 투자',
            '123' => '장비 평택 삼성 P1F 24년 V8~V10 HSN4.0 대응 투자',
            '124' => '장비 화성 삼성 SPOT JOB (2024년)',
            '125' => '장비 기흥 삼성 V-TF 전력 반도체 개발 CCSS 투자',
            '126' => '장비 수원 삼성종합기술원 SPOT JOB (2024년)',
            '127' => '장비 화성 삼성 15L GOST 공정 대응 HCL 공급 설비 투자',
            '128' => '장비 화성 삼성 15L D1b 전환 대응 CCSS 공급 장치 철거',
            '129' => '장비 평택 삼성 P4-PH1 SLURRY 설비 투자',
            '130' => '시스템BU 공정혁신부 TF',
            '131' => '평택 삼성전자 P4 Ph1,2 5D Hook Up 설계 (2024년)',
            '132' => '평택 삼성전자 P4 Ph1 Hook Up (2024년)',
            '133' => '청주 Sk Hynix M15 Hook-Up공사 (2024년)',
            '134' => '이천 SK Hynix M16 Hook-Up공사 (2024년)',
            '135' => '이천 Sk Hynix P&T4 Hook-Up 공사 (2024년)',
            '136' => '진천 한화솔루션 TANDEM Utility 배관 및 Hook-up 공사',
            '137' => '용인 린데코리아 NRD-K ISBL 설치공사',
            '138' => '평택 LEK C1 기계, 배관 및 소방설치 공사',
            '139' => '미국 GA QCELLS REDEEMER PJT(한국분 급여)',
            '140' => '우주항공BU'
        ));
        // 부서: 한국전기연구원 기술창업센터 yllee 240510
        $this->set('keri_depart_arr', array(
            '001' => '주식회사 지유',
            '002' => '(주)디인사이트'
        ));
        // 부서: 창원대학교 산학협력단 창업보육센터 yllee 240724
        $this->set('changwon_depart_arr', array(
            '001' => '이노메스',
            '002' => '헥스(주)',
            '003' => '한국유에이기술(주)',
            '004' => '(주)앤드메이드',
            '005' => '다움텍',
            '006' => '주승다휴공작소',
            '007' => '주식회사 벨고',
        ));
        // 부서: 주식회사 재영텍 by 배가람 yllee 240829
        // 부서: 신공장TF팀 추가 주식회사 재영텍 by 배가람 geosan 241010
        $this->set('jaeyoungtech_depart_arr', array(
            '001' => 'EHS팀',
            '002' => 'EHS팀(폐수처리조)',
            '003' => '개발/품질팀',
            '004' => '개발팀',
            '005' => '경영기획담당',
            '006' => '구매물류Part',
            '007' => '구미1공장 생산담당',
            '008' => '기획팀',
            '009' => '생산1팀',
            '010' => '생산1팀(생산직)',
            '011' => '생산2팀',
            '012' => '생산2팀(생산직)',
            '013' => '생산관리Part',
            '014' => '생산기술팀',
            '015' => '생산기술팀(공정분석원)',
            '016' => '생산본부',
            '017' => '설비팀',
            '018' => '설비팀(보전반)',
            '019' => '영업팀',
            '020' => '인사총무팀',
            '021' => '인사총무팀(실습)',
            '022' => '자금IR Part',
            '023' => '재경팀',
            '024' => '품질팀',
            '025' => '신공장TF팀'
        ));
        $this->set('flag_book_arr', array(
            'Y' => '사용',
            'N' => '미사용',
        ));
        $this->set('flag_live_arr', array(
            'Y' => '사용',
            'N' => '미사용',
        ));
        $stu_page = $this->getRequestParameter('stu_page');
        if (!$stu_page) {
            $stu_page = 1;
        }
        $this->set('stu_page', $stu_page);

        $this->set('bz_region_arr', array(
            '전국' => 'all',
            '인천' => 'incheon',
            '서울' => 'seoul',
            '경기' => 'gyeonggi',
            '강원' => 'gangwon',
            '충북' => 'chungbuk',
            '충남' => 'chungnam',
            '세종' => 'sejong',
            '대전' => 'daejeon',
            '전북' => 'jeonbuk',
            '전남' => 'jeonnam',
            '광주' => 'gwangju',
            '경남' => 'gyeongnam',
            '경북' => 'gyeongbuk',
            '대구' => 'daegu',
            '울산' => 'ulsan',
            '부산' => 'busan',
            '제주' => 'jeju'
        ));

        $this->set('bz_category_arr', array(
            '기술',
            '금융',
            '수출',
            '인력',
            '창업',
            '경영',
            '내수',
            '환경',
            '기타'
        ));

        $this->set('bz_field_big_arr', array(
            '제조',
            '서비스',
            'IT신산업',
            '건설'
        ));
    }

    protected function initInsert()
    {
        parent::initInsert();
        $insert_columns = 'mb_id,mb_pw,mb_level,mb_name,mb_birthday,mb_resident_num,mb_email,mb_tel,mb_direct_line';
        $insert_columns .= ',cp_id,cp_name,mb_zip,mb_addr,mb_addr2,mb_stu_type,mb_position,mb_irregular_type,mb_cost_business_num,flag_tomocard,flag_use,flag_auth,flag_test,flag_sms,flag_cyber';
        $insert_columns .= ',mb_memo,flag_book,sw_depart,flag_live';
        $this->set('insert_columns', $insert_columns);
        $this->set('required_arr', array(
            'mb_name' => '이름',
            'mb_id' => '아이디',
            'mb_pw' => '비밀번호',
            'mb_birthday' => '주민등록번호(앞자리)',
            'mb_resident_num' => '주민등록번호(뒷자리)',
            'mb_tel' => '전화번호',
            'mb_level' => '권한구분',
            'flag_use' => '사용여부'
        ));
        //'cp_id' => '구분'

        if ($_POST['book'] == 'book') {
            $this->set('return_uri', '/webadmin/book/user_list.html');
        }
        if ($_POST['live'] == 'book') {
            $this->set('return_uri', '/webadmin/live/user_list.html');
        }
    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);

        $stu_type = $arr['mb_stu_type'];
        $irregular_type = $arr['mb_irregular_type'];

        $cp_id_arr = $_POST['cp_id_arr'];
        $cp_name_arr = $_POST['cp_name_arr'];

        $cp_id = $cp_id_arr;
        $cp_name = $cp_name_arr;

        if (is_array($cp_id_arr)) {
            $cp_id = implode('|', $cp_id_arr);
        }
        if (is_array($cp_name_arr)) {
            $cp_name = implode('|', $cp_name_arr);
        }
        $arr['cp_id'] = $cp_id;
        $arr['cp_name'] = $cp_name;

        $stu_type_count = strlen($stu_type);
        $irregular_type_count = strlen($irregular_type);

        if ($stu_type_count == 1) {
            $arr['mb_stu_type'] = '00' . $stu_type;
        } elseif ($stu_type_count == 2) {
            $arr['mb_stu_type'] = '0' . $stu_type;
        }
        if ($irregular_type_count == 1) {
            $arr['mb_irregular_type'] = '00' . $irregular_type;
        } elseif ($irregular_type_count == 2) {
            $arr['mb_irregular_type'] = '0' . $irregular_type;
        }
        if ($_POST['mode'] == 'excel') {
            if (!$arr['mb_pw']) {
                $arr['mb_pw'] = 1234;
            }
        }
        // 패스워드
        $arr['mb_pw'] = Format::encryptString($arr['mb_pw']);
        $arr['mb_pw_time'] = _NOW_DATETIME_;

        // 주민번호
        $arr['emon_res_no'] = $arr['mb_resident_num'];
        $arr['mb_resident_num'] = Format::encrypt($arr['mb_resident_num']);

        $arr['mb_tel'] = Html::beautifyTel($arr['mb_tel']);
        $arr['mb_direct_line'] = Html::beautifyTel($arr['mb_direct_line']);

        // 이름 공백 제거
        $arr['mb_name'] = trim($arr['mb_name']);

        return $arr;
    }

    protected function postInsert($arr)
    {
        // uid 구하기
        global $member;

        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $data = Db::selectOnce($data_table, $pk, "WHERE reg_id = '" . $member['mb_id'] . "'", "ORDER BY reg_time DESC");
        $uid = $data[$pk];
        $arr[$pk] = $uid;

        $mb_level = $arr['mb_level'];

        if ($mb_level == 4) {
            $staff_name = $arr['mb_name'];
            $staff_position = $arr['mb_position'];
            $staff_mail = $arr['mb_email'];
            $cp_id = $arr['cp_id'];

            $updateResult = Db::update('tbl_company', "staff_name='" . $staff_name . "', staff_position='" . $staff_position . "', staff_email='" . $staff_mail . "'", "WHERE cp_id = '" . $cp_id . "'");
            if (!$updateResult) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '등록 과정에서 장애가 발생하였습니다.'
                );
                return $result;
            }
        }

        if (!$uid) {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
            return $result;
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

        $mb_id = $arr['mb_id'];

        Db::update('tbl_user', "emon_res_no = ''", "WHERE mb_id = '$mb_id'");

        return $result;
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $update_columns = 'mb_id,mb_level,mb_name,mb_birthday,mb_resident_num,mb_email,mb_tel,mb_direct_line';
        $update_columns .= ',cp_id,cp_name,mb_zip,mb_addr,mb_addr2,mb_stu_type,mb_position,mb_irregular_type';
        $update_columns .= ',mb_cost_business_num,flag_tomocard,flag_use,flag_auth,flag_test,flag_sms';
        $update_columns .= ',mb_hp,sw_depart';
        $this->set('update_columns', $update_columns);
        $this->set('required_arr', array(
            'mb_name' => '이름',
            'mb_id' => '아이디',
            'mb_birthday' => '주민등록번호(앞자리)',
            'mb_resident_num' => '주민등록번호(뒷자리)',
            'mb_tel' => '전화번호',
            'mb_level' => '권한구분',
            'flag_use' => '사용여부'
        ));
    }

    protected function convertUpdate($arr)
    {
        $arr = parent::convertUpdate($arr);

        $mb_level = $arr['mb_level'];

        $cp_id_arr = $_POST['cp_id_arr'];
        $cp_name_arr = $_POST['cp_name_arr'];

        $cp_id = '';
        $cp_name = '';

        if (is_array($cp_id_arr)) {
            $cp_id = implode('|', $cp_id_arr);
        }
        if (is_array($cp_name_arr)) {
            $cp_name = implode('|', $cp_name_arr);
        }
        $arr['cp_id'] = $cp_id;
        $arr['cp_name'] = $cp_name;

        if ($mb_level == 4) {
            $staff_name = $arr['mb_name'];
            $staff_position = $arr['mb_position'];
            $staff_mail = $arr['mb_email'];

            for ($i = 0; $i < count($cp_id_arr); $i++) {
                $updateResult = Db::update('tbl_company', "staff_name='" . $staff_name . "', staff_position='" . $staff_position . "', staff_email='" . $staff_mail . "'", "WHERE cp_id = '" . $cp_id_arr[$i] . "'");
                if (!$updateResult) {
                    $result = array(
                        'code' => 'failure',
                        'msg' => '등록 과정에서 장애가 발생하였습니다.'
                    );
                    return $result;
                }
            }
        }
        // 패스워드
        $mb_pw = $_POST['mb_pw'];
        if ($mb_pw) {
            $arr['mb_pw'] = Format::encryptString($mb_pw);
            $arr['mb_pw_time'] = _NOW_DATETIME_;
        }
        $resident_num = $arr['mb_resident_num'];

        $arr['mb_resident_num'] = Format::encrypt($resident_num);
        $arr['emon_res_no'] = $resident_num;

        $arr['mb_tel'] = Html::beautifyTel($arr['mb_tel']);

        // 이름 공백 제거
        $arr['mb_name'] = trim($arr['mb_name']);

        //Log::debug($arr);
        return $arr;
    }

    public function validateMemberId($mb_id)
    {
        $result = array(
            'flag' => 1,
            'msg' => '사용할 수 있는 아이디입니다.'
        );
        if (!$mb_id) {
            $result['flag'] = 0;
            $result['msg'] = '아이디를 입력하세요.';
        } elseif (preg_match("/[^0-9a-zA-Z_-]+/i", $mb_id)) {
            $result['flag'] = 0;
            $result['msg'] = '영문자, 숫자, _, - 만 입력하세요.';
        } elseif (strlen($mb_id) < 4) {
            $result['flag'] = 0;
            $result['msg'] = '최소 4글자 이상 입력하세요.';
        } elseif (strlen($mb_id) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '아이디는 최대 20글자 이하 입력하세요.';
        } elseif (strpos('admin,root,sysop,manager', strtolower($mb_id)) > -1) {
            $result['flag'] = 0;
            $result['msg'] = '사용 불가능한 아이디입니다.';
        } else {
            $id_chk = Db::selectCount($this->get('data_table'), "where mb_id = '$mb_id'");
            if ($id_chk > 0) {
                $result['flag'] = 0;
                $result['msg'] = '이미 사용중인 아이디입니다.';
            }
        }
        return $result;
    }

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

        if ($_POST['mode'] != 'excel') {
            $result = $this->validateValues($arr);
            if ($result['code'] != 'success') {
                return $result;
            }
        }

        $mb_id = $arr['mb_id'];

        if (Db::selectOnce('tbl_user', '*', "WHERE mb_id = '$mb_id'", '')) {
            $result = array(
                'code' => 'failure',
                'msg' => '중복된 아이디입니다.'
            );
            return $result;
        }

        $mb_level = $arr['mb_level'];
        $cp_id = $arr['cp_id'];


        if ($mb_level == 4) {
            if (!$cp_id) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '기업관리자의 경우 기업을 선택해야합니다.'
                );
                return $result;
            }
            $mb_check = Db::selectOnce("tbl_user", "*", "WHERE mb_level = '$mb_level' AND cp_id = '$cp_id'", "");
            if ($mb_check) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '해당 기업의 기업관리자가 이미 존재합니다.'
                );
                return $result;
            }
        }

        $data_table = $this->get('data_table');
        if (Db::insertByArray($data_table, $arr)) {
            // 이몬 API 기반 데이터 수집 시스템: 회원 정보 데이터 전송 yllee 220426
            if ($mb_level == 1) {
                //$api_result = Api::userHist($arr, 'C');
                Api::userHist($arr, 'C');
                //Log::debug('api_result C');
                //Log::debug($api_result);
            }
            $result = $this->postInsert($arr);
        } else {
            $result = array(
                'code' => 'failure',
                'msg' => '등록 과정에서 장애가 발생하였습니다.'
            );
        }

        return $result;
    }

    public function updateData()
    {
        $uid = $this->get('uid');
        // 권한 체크
        if (!$uid || !$this->checkUpdateAuth($uid)) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
            return $result;
        }
        $this->initUpdate();
        $arr = $this->getParameters($this->get('update_columns'), 'post');
        //Log::debug($this->get('update_columns'));
        //Log::debug($arr);
        $mb_level = $arr['mb_level'];
        $cp_id = $arr['cp_id'];

        if ($mb_level == 4) {
            // 같은 기업의 아이디가 다른 기업 담당자 있는지 검사 yllee 230601
            $db_where = "WHERE mb_level = '$mb_level' AND cp_id = '$cp_id' AND mb_id = '$uid'";
            $mb_check = Db::selectOnce("tbl_user", "*", $db_where, "");
            if ($mb_check) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '해당 기업의 기업담당자가 이미 존재합니다.'
                );
                return $result;
            }
            $mb_email = $arr['mb_email'];
            if ($_POST['mb_email_old'] != $mb_email) {
                Db::update('tbl_company', "staff_email = '$mb_email'", "WHERE cp_id='$cp_id'");
            }
            // 담당자 이름 변경 시 기업리스트 교육담당자 이름 자동 변경 기능 minju 230626
            $mb_name = $arr['mb_name'];
            if ($_POST['mb_name_old'] != $mb_name) {
                Db::update('tbl_company', "staff_name = '$mb_name'", "WHERE cp_id='$cp_id'");
            }
        }
        $arr = $this->convertUpdate($arr);

        $result = $this->validateValues($arr);
        if ($result['code'] != 'success') {
            return $result;
        }
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        unset($arr[$pk]);

        // 업데이트 테스트 minju
        /*
        Log::debug("민주 업데이트 테스트_uesr");
        Log::debug($data_table);
        Log::debug($arr);
        Log::debug($pk . '=' . $uid);
        */
        if (Db::updateByArray($data_table, $arr, "WHERE $pk = '$uid'")) {
            // 이몬 API 기반 데이터 수집 시스템: 회원 정보 데이터 전송 yllee 220426
            if ($mb_level == 1) {
                $arr['mb_id'] = $uid;
                $api_result = Api::userHist($arr, 'U');
                //Log::debug('api_result U');
                //Log::debug($api_result);
            }
            // 회원정보 수정 시 엘엑스 DB tbl_progress 수정 minju 230322
            if ($data_table == 'tbl_user') {
                $pArr = array();
                $pArr['us_name'] = $arr['mb_name'];
                $pArr['us_birth'] = $arr['mb_birthday'];
                $pArr['us_hp'] = $arr['mb_tel'];
                $pArr['sw_depart'] = $arr['sw_depart'];
                $pArr['upt_id'] = $arr['upt_id'];
                $pArr['upt_time'] = $arr['upt_time'];

                Db::updateByArrayLxn('tbl_progress', $pArr, "WHERE us_id = '$uid'");
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
        for ($i = 0; $i < count($del_file_arr); $i++) {
            $this->deleteFile($del_file_arr[$i]);
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

        if ($_POST['book'] == 'book') {
            $result = array(
                'code' => 'success',
                'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&page=' . $page . $query_string . '&book=book',
                'msg' => $this->get('success_msg')
            );
        }

        if ($_POST['live'] == 'live') {
            $result = array(
                'code' => 'success',
                'uri' => $this->get('return_uri') . '?' . $pk . '=' . $uid . '&page=' . $page . $query_string . '&live=live',
                'msg' => $this->get('success_msg')
            );
        }

        $mb_id = $arr['mb_id'];

        Db::update('tbl_user', "emon_res_no = ''", "WHERE mb_id = '$mb_id'");

        $uArr = array(
            'us_name' => $arr['mb_name'],
            'us_hp' => $arr['mb_tel'],
            'us_birth' => $arr['mb_birthday'],
        );

        Db::updateByArray('tbl_progress', $uArr, "WHERE us_id = '$mb_id'");

        $mb_level = $arr['mb_level'];
        $mb_name = $arr['mb_name'];

        if ($mb_level == '6') {
            Db::update('tbl_company', "partner_name = '$mb_name'", "WHERE partner_id='$mb_id'");
        }

        return $result;
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
            $mb_data = $this->searchMemberId($uid);

            // 권한 체크
            if ($uid && !$this->checkUpdateAuth($uid)) {
                $result = array(
                    'code' => 'failure',
                    'msg' => '권한이 없습니다.'
                );
                return $result;
            }
            // 해당 수강이 포함된 기수의 인원 수 재계산: 기수 수강생 명단 삭제 전 실행 yllee 220721
            $bu_list = Db::select("tbl_batch_user", "bu_bt_code", "WHERE bu_mb_id = '$uid'", "", "");

            $result = $this->deleteRows($uid);
            if ($result['code'] != 'success') {
                return $result;
            } else {
                // 해당 수강이 포함된 기수의 인원 수 재계산: 회원 데이터 삭제 시 트리거 작동 후 실행
                if ($mb_data['mb_level'] == '1') {
                    for ($j = 0; $j < count($bu_list); $j++) {
                        $bt_code = $bu_list[$j]['bu_bt_code'];
                        $bt_where = "WHERE bu_bt_code = '$bt_code'";
                        $bt_list = Db::select('tbl_batch_user', "*", $bt_where, "", "");
                        $bt_count = count($bt_list);
                        // 수료 인원 재계산 yllee 220725
                        $count_complete = 0;
                        for ($k = 0; $k < count($bt_list); $k++) {
                            if ($bt_list[$k]['flag_complete'] == 'Y') {
                                $count_complete++;
                            }
                        }
                        $db_where = "WHERE bt_code = '$bt_code'";
                        $column_value = "bt_count = $bt_count, bt_completion_member_c = $count_complete";
                        Db::update('tbl_batch', $column_value, $db_where);
                    }
                }
            }
            if ($mb_data['mb_level'] == '4') {
                $cp_id = $mb_data['cp_id'];
                $cp_id_arr = explode("|", $cp_id);

                for ($j = 0; $j < count($cp_id_arr); $j++) {
                    $updateResult = Db::update('tbl_company', "staff_name='', staff_position='', staff_email=''", "WHERE cp_id = '" . $cp_id_arr[$j] . "'");
                    if (!$updateResult) {
                        $result = array(
                            'code' => 'failure',
                            'msg' => '등록 과정에서 장애가 발생하였습니다.'
                        );
                        return $result;
                    }
                }
            } elseif ($mb_data['mb_level'] == '6') {
                $arr = array(
                    'partner_id' => '',
                    'partner_name' => ''
                );
                Db::updateByArray('tbl_company', $arr, "WHERE partner_id='$uid'");
            } elseif ($mb_data['mb_level'] == '1') {
                $mb_data['emon_res_no'] = Format::decrypt($mb_data['mb_resident_num']);
                $api_result = Api::userHist($mb_data, 'D');
                //Log::debug('api_result D');
                //Log::debug($api_result);
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

    protected function deleteRows($uid)
    {
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');

        $mb_data = Db::selectOnce('tbl_user', '*', "WHERE mb_id = '$uid'", '');

        $resident_num = Format::decrypt($mb_data['mb_resident_num']);

        Db::update('tbl_user', "emon_res_no = '$resident_num'", "WHERE mb_id = '$uid'");

        $pr_list = Db::select('tbl_progress', "*", "WHERE us_id='$uid'", '', '');

        if ($pr_list[0]['pr_id']) {
            $result = array(
                'code' => 'failure',
                'msg' => '해당기업 기수에 수강생정보가 아직 남아있습니다.'
            );
            return $result;
        }

        if (!Db::delete($data_table, "WHERE $pk = '$uid'")) {
            $result = array(
                'code' => 'failure',
                'msg' => '삭제 과정에 문제가 발생하였습니다.'
            );
            return $result;
        } else {
            // 회원 삭제 시 엘엑스DB progress 삭제 코드 minju 230424
            Db::deleteLxn('tbl_progress', "WHERE us_id = '$uid'");
        }

        for ($i = 0; $i < count($pr_list); $i++) {
            $bt_id = $pr_list[$i]['bt_id'];

            $bt_count = Db::selectCount('tbl_progress', "WHERE bt_id='$bt_id'");

            Db::update('tbl_batch', "bt_count='$bt_count'", "WHERE bt_id='$bt_id'");
        }

        $result = array(
            'code' => 'success'
        );

        return $result;
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

    public function validateMemberPassword($mb_pw)
    {
        $result = array(
            'flag' => 1,
            'msg' => '사용할 수 있는 패스워드입니다.'
        );
        $num = preg_match('/[0-9]/u', $mb_pw);
        $eng = preg_match('/[a-z]/u', $mb_pw);
        $spe = preg_match("/[\!\@\#\$\%\^\&\*]/u", $mb_pw);
        $pw_start_length = 8;

        if (!$mb_pw) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드를 입력하세요.';
        } elseif ($num == 0 || $eng == 0 || $spe == 0) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 영대/소문자, 숫자, 특수문자 조합이어야 합니다.';
        } elseif (strlen($mb_pw) < $pw_start_length) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최소 ' . $pw_start_length . '글자 이상 입력하세요.';
        } elseif (strlen($mb_pw) > 20) {
            $result['flag'] = 0;
            $result['msg'] = '패스워드는 최대 20글자 이하로 입력하세요.';
        } elseif (preg_match("/(\w)\\1\\1\\1/", $mb_pw)) {
            $result['flag'] = 0;
            $result['msg'] = '같은 영문자, 숫자를 4번 이상 반복할 수 없습니다.';
        }
        return $result;
    }

    public function selectPartnerList()
    {
        $db_where = "WHERE mb_level = '6'";

        return Db::select('tbl_user', '*', $db_where, 'ORDER BY mb_name ASC', '');
    }

    public function selectPartnerListAjax()
    {
        $db_where = "WHERE mb_level = '6'";
        $sch_text = $_GET['sch_text'];
        $result['code'] = 'success';

        if ($sch_text) {
            $db_where .= " AND mb_name LIKE '%$sch_text%'";
        }

        $result['data'] = Db::select('tbl_user', '*', $db_where, '', '');

        return $result;
    }

    public function selectTutorList()
    {
        $db_where = "WHERE mb_level = '5'";

        return Db::select('tbl_user', '*', $db_where, 'ORDER BY mb_name', '');
    }

    public function selectTutorListAjax()
    {
        $db_where = "WHERE mb_level = '5'";

        $sch_text = $_GET['sch_text'];

        $result['code'] = 'success';

        if ($sch_text) {
            $db_where .= " AND mb_name LIKE '%$sch_text%'";
        }

        $result['data'] = Db::select('tbl_user', '*', $db_where, '', '');

        return $result;
    }

    public function selectCompanyList()
    {
        $db_where = "WHERE mb_level = '4'";

        return Db::select('tbl_user', '*', $db_where, '', '');
    }

    public function selectCompanyListAjax()
    {
        $db_where = "WHERE mb_level = '4'";

        $sch_text = $_GET['sch_text'];

        $result['code'] = 'success';

        if ($sch_text) {
            $db_where .= " AND mb_name LIKE '%$sch_text%'";
        }

        $result['data'] = Db::select('tbl_user', '*', $db_where, '', '');

        return $result;
    }

    public function searchPartnerId($mb_id)
    {
        return Db::selectOnce('tbl_user', '*', "WHERE mb_id = '" . $mb_id . "' AND mb_level = '6'", '');
    }

    public function searchMemberId($mb_id)
    {
        return Db::selectOnce('tbl_user', '*', "WHERE mb_id = '" . $mb_id . "'", '');
    }

    public function selectListMbId($mb_id)
    {
        //$select_table = 'tbl_progress';
        $select_table = 'tbl_batch_user a JOIN tbl_batch b ON a.bu_bt_id = b.bt_id AND a.bu_cs_code = b.cs_code';
        $select_columns = '*';
        //$db_where = "WHERE us_id = '$mb_id'";
        // CRM > 수강이력에서 북러닝 제외 yllee 240829
        $db_where = "WHERE a.bu_mb_id='$mb_id' AND b.bt_type!='book'";
        $db_having = $this->get('db_having');
        //$db_order = 'ORDER BY a.reg_time DESC';
        $db_order = 'ORDER BY b.bt_s_date DESC';
        /*
        global $member;
        if($member['mb_id'] == 'silva') {
            $select_table = "(SELECT bt_s_date,bt_e_date,rate_progress,cs_id,cs_code,bt_code,bt_type,bt_id,rep_score,flag_complete from tbl_progress WHERE us_id='$mb_id' AND bt_e_date >= '$today' UNION SELECT bt_s_date,bt_e_date,rate_progress,cs_id,cs_code,bt_code,bt_type,bt_id,rep_score,flag_complete from tbl_book_progress WHERE us_id='$mb_id' AND bt_e_date >= '$today') A";
            $db_order = "ORDER BY A.bt_s_date DESC";
            $db_where = "";
        }
        */
        $list = Db::select($select_table, $select_columns, $db_where, $db_having . ' ' . $db_order, '');
        $cnt_total = count($list);
        $this->set('cnt_total', $cnt_total);
        $page = $this->get('page');
        $cnt_rows = $this->get('cnt_rows');
        $db_limit = 'LIMIT ' . ($page - 1) * $cnt_rows . ', ' . $cnt_rows;
        $list = Db::select($select_table, "*", $db_where, $db_having . ' ' . $db_order, $db_limit);

        for ($i = 0; $i < count($list); $i++) {
            $bt_code = $list[$i]['bt_code'];
            $cs_code = $list[$i]['cs_code'];
            $exam_year = '';
            /*
             * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
            $bt_end_date = $list[$i]['bt_e_date'];
            $bt_end_date_arr = explode('-', $bt_end_date);
            $bt_end_year = $bt_end_date_arr[0];
            $now_year = date('Y');
            if ($now_year > $bt_end_year) {
                $exam_year = '_' . $bt_end_year;
            }
            */
            $exam_mid_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_midterm' AND er_cs_code='$cs_code' AND flag_delete IS NULL";
            $exam_fin_where = "WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_final' AND er_cs_code='$cs_code' AND flag_delete IS NULL";
            $report_where = "WHERE rr_mb_id = '$mb_id' AND rr_bt_code='$bt_code' AND rr_cs_code='$cs_code' AND rr_submit_time IS NOT NULL";
            $exam_mid_data = Db::selectOnce('tbl_exam_result' . $exam_year, '*', $exam_mid_where, '');
            // 마지막 시험 데이터 기준으로 정렬 yllee 240415
            $exam_fin_order = "ORDER BY er_id DESC";
            $exam_fin_data = Db::selectOnce('tbl_exam_result' . $exam_year, '*', $exam_fin_where, $exam_fin_order);
            $report_data = Db::selectOnce('tbl_report_result', '*', $report_where, '');
            $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_code = '$cs_code'", '');

            //Log::debug("WHERE er_mb_id = '$mb_id' AND er_bt_code='$bt_code' AND er_type = 'exam_final' AND er_cs_code='$cs_code' AND flag_delete IS NULL");
            //Log::debug($exam_fin_data);

            $list[$i]['mid_data'] = $exam_mid_data;
            $list[$i]['fin_data'] = $exam_fin_data;
            $list[$i]['report_data'] = $report_data;
            $list[$i]['cs_data'] = $cs_data;
            // 진도율
            $pr_where = "WHERE bt_code = '$bt_code' AND cs_code = '$cs_code' AND us_id = '$mb_id'";
            $pr_data = Db::selectOnce('tbl_progress', '*', $pr_where, '');
            if (!$pr_data) {
                $pr_data = Db::selectOnceLxn('tbl_progress', '*', $pr_where, '');
            }
            $list[$i]['rate_progress'] = $pr_data['rate_progress'];
            $list[$i]['pr_id'] = $pr_data['pr_id'];
        }
        return $this->convertList($list);
    }

    public function getMemberList($mb_level)
    {
        $this->initSelect();
        $select_table = "tbl_user";
        $select_columns = $this->get('select_columns');

        $db_where = "where mb_level = '" . $mb_level . "'";

        $result['code'] = 'success';

        $data = Db::select($select_table, $select_columns, $db_where, '', '');

        $result['data'] = $data;

        // 권한 체크
        if (!$this->checkViewAuth($data)) {
            $result = array(
                'code' => 'failure',
                'msg' => '권한이 없습니다.'
            );
        }

        return $result;
    }

    public static function makePagination($arr, $query_string = '', $a_arr_class = '')
    {
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

    protected function makeDbWhere()
    {
        $db_where = $this->getDefaultWhere();

        $this->set('db_where', $db_where);

        return $db_where;
    }

    public function resetPW()
    {
        $mb_id_arr = $_GET['mb_id_arr'];

        $result['code'] = 'success';

        for ($i = 0; $i < count($mb_id_arr); $i++) {
            $arr = array(
                'mb_pw' => Format::encryptString(1234)
            );

            Db::updateByArray('tbl_user', $arr, "WHERE mb_id = '$mb_id_arr[$i]'");
        }

        return $result;
    }

    public function updateAuth()
    {
        $mb_id_arr = $_GET['mb_id_arr'];
        $auth = $_GET['auth'];

        $result['code'] = 'success';

        for ($i = 0; $i < count($mb_id_arr); $i++) {
            $arr = array(
                'flag_auth' => $auth
            );

            Db::updateByArray('tbl_user', $arr, "WHERE mb_id = '$mb_id_arr[$i]'");
        }

        return $result;
    }

    public function progressOc()
    {
        $result['code'] = 'success';

        $mb_id = $_GET['mb_id'];
        $cs_id = $_GET['cs_id'];
        $bt_code = $_GET['bt_code'];

        $cs_data = Db::selectOnce('tbl_course', 'cs_code', "WHERE cs_id = '$cs_id'", '');
        $oc_data = Db::select('tbl_occasion', '*', "WHERE oc_uid = '$cs_id'", 'ORDER BY oc_num', '');
        //$bt_data = Db::selectOnce('tbl_batch', 'bt_e_date', "WHERE bt_code = '$bt_code'", '');

        $cs_code = $cs_data['cs_code'];
        $target_table = 'tbl_time';
        /*
         * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
        $bt_e_date = $bt_data['bt_e_date'];
        $bt_e_year = substr($bt_e_date, 0, 4);
        //$timestamp = strtotime("-14 days");
        //$end_date = date('Y-m-d', $timestamp);
        // 2021년 데이터까지 tbl_time_2021 형식의 테이블로 이동 시킴 yllee 220504
        $end_date = '2021-12-31';
        //Log::debug("$bt_e_date <= $end_date");
        if ($bt_e_date <= $end_date) {
            $target_table .= '_' . $bt_e_year;
        }
        */
        for ($i = 0; $i < count($oc_data); $i++) {
            $oc_num = $oc_data[$i]['oc_num'];
            $where_progress = "WHERE tm_bt_code = '$bt_code' AND tm_cs_code = '$cs_code' AND tm_mb_id = '$mb_id' AND tm_oc_num='$oc_num'";
            $tm_data = Db::selectOnce($target_table, '*', $where_progress, '');
            if (!$tm_data) {
                $tm_data = Db::selectOnceLxn($target_table, '*', $where_progress, '');
            }
            $pg_list = Db::select('tbl_page', 'SUM(pg_time) as sum', "WHERE cs_id='$cs_id' AND oc_num='$oc_num'", '', '');
            $oc_data[$i]['oc_time'] = $pg_list[0]['sum'];
            $page_info = unserialize($tm_data['tm_record']);

            $studied_page_total = 0;
            $page_cnt = 1;
            $first_time = '';
            $last_time = '';

            if (!empty($page_info)) {
                foreach ($page_info as $page) {
                    if ($page_cnt == 1) {
                        $first_time = $page['date'];
                    }
                    $page_cnt++;
                    if ($page['check'] == '1') {
                        $studied_page_total++;
                        $last_time = $page['date'];
                    }
                }
            }
            $tm_data['first_time'] = $first_time;
            $tm_data['last_time'] = $last_time;

            if ($tm_data['tm_pg_num'] != $studied_page_total) {
                $tm_data['tm_pg_num'] = $studied_page_total;
            }
            $oc_data[$i]['tm_data'] = $tm_data;
        }
        // 진도율 추가 예정
        $result['data'] = $oc_data;

        return $result;
    }

    public function selectTime()
    {
        $mb_id = $this->get('mb_id');
        $oc_num = $this->get('oc_num');
        $bt_code = $this->get('bt_code');
        $cs_id = $this->get('cs_id');

        //$bt_data = Db::selectOnce('tbl_batch', 'bt_e_date', "WHERE bt_code = '$bt_code'", '');
        $cs_data = Db::selectOnce('tbl_course', 'cs_code', "WHERE cs_id = '$cs_id'", '');

        $cs_code = $cs_data['cs_code'];
        $target_table = 'tbl_time';
        /*
         * 이전년도 데이터 호출 시 DB 서버 부하로 주석처리 yllee 221207
        $bt_e_date = $bt_data['bt_e_date'];
        $bt_e_year = substr($bt_e_date, 0, 4);
        //$timestamp = strtotime("-14 days");
        //$end_date = date('Y-m-d', $timestamp);
        // 2021년 데이터까지 tbl_time_2021 형식의 테이블로 이동 시킴 yllee 220504
        $end_date = '2021-12-31';

        if ($bt_e_date <= $end_date) {
            $target_table .= '_' . $bt_e_year;
        }
        */
        $db_where = "WHERE tm_cs_code = '$cs_code' AND tm_mb_id = '$mb_id' AND tm_oc_num = '$oc_num' AND tm_bt_code = '$bt_code'";
        $list = Db::selectOnce($target_table, '*', $db_where, "");
        // 데이터가 없을 경우 엘엑스 DB 호출 yllee 220503
        if (!$list) {
            $list = Db::selectOnceLxn($target_table, '*', $db_where, "");
        }
        $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_code = '$cs_code'", '');

        $list['cs_name'] = $cs_data['cs_name'];
        $list['pg_data'] = Db::select('tbl_page', '*', "WHERE cs_id = '$cs_id' AND oc_num = '$oc_num'", 'ORDER BY pg_num', '');

        return $list;
    }

    public function selectPage()
    {
        $cs_id = $this->get('cs_id');
        $oc_num = $this->get('oc_num');

        $pg_list = Db::select('tbl_page', '*', "WHERE cs_id = '$cs_id' AND oc_num = '$oc_num'", 'ORDER BY pg_num', '');

        $cs_data = Db::selectOnce('tbl_course', '*', "WHERE cs_id = '$cs_id'", '');

        $list['cs_name'] = $cs_data['cs_name'];

        $list['pg_list'] = $pg_list;

        return $list;
    }

    public function selectListSms($us_id)
    {
        // 220916 분기로 문자 발송 내역 DB 테이블 분리함 yllee 220916
        $db_where = "WHERE us_id = '$us_id'";
        $sms_list_base = Db::select('tbl_sms', '*', $db_where, 'ORDER BY sm_id DESC', '');
        $sms_list_2022 = Db::select('tbl_sms_2022', '*', $db_where, 'ORDER BY sm_id DESC', '');

        $sms_list = array();
        if ($sms_list_base && $sms_list_2022) {
            $sms_list = array_merge($sms_list_base, $sms_list_2022);
        } elseif (!$sms_list_base) {
            $sms_list = $sms_list_2022;
        } elseif (!$sms_list_2022) {
            $sms_list = $sms_list_base;
        }
        return $sms_list;
    }

    public function sendSms()
    {
        $remote_phone = $_GET['remote_phone'];
        $remote_name = $_GET['remote_name'];
        $remote_msg = $_GET['remote_msg'];
        $mb_tel = Html::beautifyTel($remote_phone);

        $host = "www.munjasin.co.kr";
        $id = "bestonealpha";
        $pass = "bestone6508";

        $param = "remote_id=" . $id;
        $param .= "&remote_pass=" . $pass;
        $param .= "&remote_phone=" . $mb_tel;
        $param .= "&remote_name=" . $remote_name;
        $param .= "&remote_callback=0552556364";
        $param .= "&remote_msg=" . $remote_msg;
        //$param .= "&remote_subject=[알파에듀] 개강안내";
        $path = "/Remote/RemoteSms.html";

        if (mb_strwidth($remote_msg, 'UTF-8') > 90) {
            $path = "/Remote/RemoteMms.html";
        }
        $fp = @fsockopen($host, 80, $errno, $errstr, 30);
        $return = "";

        if (!$fp) {
            echo $errstr . "(" . $errno . ")";
        } else {
            fputs($fp, "POST " . $path . " HTTP/1.1\r\n");
            fputs($fp, "Host: " . $host . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($param) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $param . "\r\n\r\n");

            while (!feof($fp)) $return .= fgets($fp, 4096);
        }
        fclose($fp);

        $_temp_array = explode("\r\n\r\n", $return);
        $_temp_array2 = explode("\r\n", $_temp_array[1]);

        if (sizeof($_temp_array2) > 1) {
            $return_string = $_temp_array2[1];

        } else {
            $return_string = $_temp_array2[0];
        }
        $result['code'] = 'success';
        $result['data'] = $return_string;

        $return_string_arr = explode('|', $return_string);
        $mb_id_arr = explode('|', $_GET['mb_id']);

        for ($i = 0; $i < count($mb_id_arr); $i++) {
            $mb_id = $mb_id_arr[$i];
            global $member;

            if ($mb_id) {
                $insert_arr = array(
                    'us_id' => $mb_id,
                    'sm_content' => $remote_msg,
                    'sm_state' => $return_string_arr[0],
                    'reg_id' => $member['mb_id'],
                    'reg_time' => _NOW_DATETIME_
                );
                Db::insertByArray('tbl_sms', $insert_arr);
            }
        }
        return $result;
    }

    public function selectListIds($mb_ids)
    {
        $mb_id_arr = explode('|', $mb_ids);

        $db_mb_id = implode("','", $mb_id_arr);

        $db_where = "WHERE mb_id IN('$db_mb_id')";

        return Db::select('tbl_user', '*', $db_where, '', '');
    }

    public function makePlan($bz_id = '', $bz_prompt = '')
    {
        if($bz_id == ''){
            $bz_id = $_POST['bz_id'];
        }

        if($bz_prompt == ''){
            $bz_prompt = $_POST['bz_prompt'];
        }

        $biz_data = Db::selectOnce('tbl_biz_notice','*',"WHERE bz_id='$bz_id'",'');
        $biz_name = $biz_data['bz_name'];
        $biz_overview = $biz_data['bz_overview'];
        $biz_support_content = $biz_data['bz_support_content'];

        $gpt_prompt = <<<EOD
공고문 정보와 사업 아이템 정보를 제공할 테니, 이를 바탕으로 아래 항목에 맞는 실제 사업계획서 내용을 작성해 주세요.  
작성 내용은 실제 제안서처럼 현실적이고 설득력 있게 작성하며, 반드시 JSON 형식으로만 응답하세요.

📂 공고문 정보:
- 공고제목: $biz_name  
- 사업개요: $biz_overview  
- 지원내용: $biz_support_content

📂 사업 아이템 설명:
- $bz_prompt

📌 작성 대상 항목 구조 안내 (참고용):

제안개요  
 - 제안목적  
 - 수행범위  
 - 제안의 특징 및 장점  

제안사 일반  
 - 일반현황  
 - 조직 및 인원  

사업수행부문  
 - 개요  
 - 추진목표 및 전략  
 - 주요 사업내용  
 - 세부과제별 추진방안  
 - 결과물 제출계획  

사업관리부문  
 - 추진일정 계획  
 - 업무보고 및 검토계획  
 - 수행조직 및 업무분장  
 - 참여인력 및 이력사항  

성과관리  
 - 사업관리  
 - 산출물관리  
 - 예산계획  

⚠️ 아래 조건을 반드시 지켜주세요:
- 실제 사업계획서처럼 자연스럽고 완전한 문장으로 작성하세요.
- 전문가가 직접 작성한 것 같은 내용으로 작성하세요.
- JSON 객체로만 응답하세요. 코드 블록(```)은 절대 사용하지 마세요.
- key는 반드시 아래에 명시된 **영문 키**만 사용하고, 순서는 자유입니다.
- value는 모두 **문장 형식의 텍스트**로 작성하세요. (리스트나 개조식 ❌)

📂 JSON 출력 키:

- proposal_purpose: 제안목적  
- execution_scope: 수행범위  
- advantages: 제안의 특징 및 장점  
- company_status: 일반현황  
- organization_and_staff: 조직 및 인원  
- project_summary: 개요  
- strategy: 추진목표 및 전략  
- main_content: 주요 사업내용  
- detailed_plan: 세부과제별 추진방안  
- schedule: 추진일정 계획  
- reporting_plan: 업무보고 및 검토계획  
- task_assignment: 수행조직 및 업무분장  
- personnel_info: 참여인력 및 이력사항  
- admin_management: 사업관리  
- output_management: 산출물 관리  
- budget_plan: 예산계획

📌 예시는 필요 없으며, 반드시 위 항목들을 포함한 JSON만 출력하세요.


EOD;

        $api_key = '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'; // GPT API 키
        $gpt_response = $this->requestGpt($gpt_prompt, $api_key);

        // 결과 파싱
        $plan_json = json_decode($gpt_response, true);

        $this->savePlan($plan_json, $bz_id);

        return $plan_json;
    }

    private function requestGpt($prompt, $api_key)
    {
        $post_fields = [
            'model' => 'gpt-4-turbo', // 또는 'gpt-4-turbo'
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1500
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) return null;

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? null;
    }

    private function savePlan($plan_result, $bz_id){
        global $member;
        $mb_id = $member['mb_id'];
        $bp_data = $this->getDataProposal($bz_id);
        $bp_id = $bp_data['bp_id'];

        $arr = array(
            'mb_id' => $mb_id,
            'bz_id' => $bz_id,
            'bp_purpose' => $plan_result['proposal_purpose'],
            'bp_scope' => $plan_result['execution_scope'],
            'bp_advantages' => $plan_result['advantages'],
            'bp_status' => $plan_result['company_status'],
            'bp_organization' => $plan_result['organization_and_staff'],
            'bp_summary' => $plan_result['project_summary'],
            'bp_strategy' => $plan_result['strategy'],
            'bp_content' => $plan_result['main_content'],
            'bp_plan' => $plan_result['detailed_plan'],
            'bp_schedule' => $plan_result['schedule'],
            'bp_reporting' => $plan_result['reporting_plan'],
            'bp_task' => $plan_result['task_assignment'],
            'bp_personnel' => $plan_result['personnel_info'],
            'bp_management' => $plan_result['admin_management'],
            'bp_output' => $plan_result['output_management'],
            'bp_budget' => $plan_result['budget_plan'],
        );

        if($bp_id){
            $arr['upt_id'] = $mb_id;
            $arr['upt_time'] = _NOW_DATETIME_;

            Db::updateByArray('tbl_biz_proposal',$arr,"WHERE bp_id='$bp_id'");
        } else{
            $arr['reg_id'] = $mb_id;
            $arr['reg_time'] = _NOW_DATETIME_;

            Db::insertByArray('tbl_biz_proposal',$arr);
        }
    }

    public function getDataProposal($bz_id){
        global $member;
        $mb_id = $member['mb_id'];
        return Db::selectOnce('tbl_biz_proposal','*',"WHERE mb_id='$mb_id' AND bz_id='$bz_id'",'');
    }
}
