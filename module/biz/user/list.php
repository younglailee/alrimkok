<?php

use sFramework\BizUser;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
global $layout_uri;
global $member;


/* init Class */
$oBiz = new Bizuser();
$oBiz->init();
$pk = $oBiz->get('pk');

/* check auth */
if (!$oBiz->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* list */
$list = $oBiz->selectList() ?? [];
$cnt_total = $oBiz->get('cnt_total');
/* search condition */
$search_like_arr = $oBiz->get('search_like_arr');
$search_date_arr = $oBiz->get('search_date_arr');
$query_string = $oBiz->get('query_string');
/* pagination */
$page = $oBiz->get('page');
$page_arr = $oBiz->getPageArray();

$bz_region_arr = $oBiz->get('bz_region_arr');
$bz_category_arr = $oBiz->get('bz_category_arr');

$sch_bz_institution = $_GET['sch_bz_institution'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<section id="noti-list" class="contents">
    <div class="container">
        <h2 class="sec-title">사업공고</h2>
        <!-- 검색 PC -->
        <div class="list-search pc">
            <input type="hidden" name="mb_id" value="<?= $member['mb_id'] ?>"/>
            <p class="title">원하는 조건으로 공고를 찾아보세요</p>
            <form action="./list.html" method="get" name="search_form">
                <div class="sch-wrap tab-group">
                    <div class="sch sch01 sch">
                        <ul class="tab">
                            <li class="sch-type active">유형</li>
                            <li class="sch-type">사업분야</li>
                        </ul>
                        <p class="help">*중복선택 가능</p>
                        <div class="tab-content sch-box">
                            <div class="tab-panel tab-panel01 active">
                                <?php
                                for ($i = 0; $i < count($bz_category_arr); $i++) { ?>
                                    <label class="noti-check">
                                        <input type="checkbox" name="sch_bz_category[]" data-target="유형"
                                               value="<?= $bz_category_arr[$i] ?>"/><span><?= $bz_category_arr[$i] ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                            <div class="tab-panel tab-panel02">
                                <div class="sub-tab-group">
                                    <ul class="sub-tab">
                                        <li class="active">제조</li>
                                        <li class="">서비스</li>
                                        <li class="">IT신산업</li>
                                        <li class="">건설</li>
                                    </ul>
                                    <div class="sub-tab-panel active">
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="자동차"/><span>자동차</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="조선"/><span>조선</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="철강"/><span>철강</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="석유화학"/><span>석유화학</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="반도체"/><span>반도체</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="플랜트"/><span>플랜트</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="원천"/><span>원천</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="섬유"/><span>섬유</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="시멘트"/><span>시멘트</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="제지"/><span>제지</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="방산"/><span>방산</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="수산"/><span>수산</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="농기계"/><span>농기계</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="중장비"/><span>중장비</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="디스플레이"/><span>디스플레이</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="우주항공"/><span>우주항공</span></label>
                                    </div>
                                    <div class="sub-tab-panel">
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="금융"/><span>금융</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="보험"/><span>보험</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="통신"/><span>통신</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="숙박"/><span>숙박</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="음식"/><span>음식</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="레저"/><span>레저</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="부동산"/><span>부동산</span></label>
                                    </div>
                                    <div class="sub-tab-panel">
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="가전"/><span>가전</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="정보통신기기"/><span>정보통신기기</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="반도체"/><span>반도체</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="디스플레이"/><span>디스플레이</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="이차전지"/><span>이차전지</span></label>
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="바이오헬스"/><span>바이오헬스</span></label>
                                    </div>
                                    <div class="sub-tab-panel">
                                        <label class="noti-check"><input type="checkbox" name="sch_bz_field[]" data-target="분야"
                                                                         value="건설"/><span>건설</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sch sch02">
                        <p class="sch-type active">지역</p>
                        <div class="sch-box">
                            <?php
                            $key_arr = array_keys($bz_region_arr);
                            for ($i = 0; $i < count($key_arr); $i++) { ?>
                                <label class="noti-check">
                                    <input type="checkbox" name="sch_bz_region[]"
                                           value="<?= $key_arr[$i] ?>"/><span><?= $key_arr[$i] ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sch sch03">
                        <div class="top">
                            <p class="sch-type">주관기관</p>
                            <input type="text" name="sch_bz_institution" placeholder="검색어를 입력하세요." value="<?=$sch_bz_institution?>"/>
                        </div>

                        <div class="mid">
                            <p class="sch-type">마감일자</p>
                            <div class="sch-box">
                                <input id="startDate" name="sch_s_date" class="date-input" placeholder="시작일"> -
                                <input id="endDate" name="sch_e_date" class="date-input" placeholder="종료일">
                            </div>
                        </div>
                        <div class="btt">
                            <div class="btn resetBtn" onclick="location.href='./list.html'">초기화</div>
                            <div class="btn schBtn">검색</div>
                        </div>
                    </div>
                    <ul class="choice"></ul>
                </div>
            </form>
        </div>
        <!-- // 검색 PC -->
        <!-- 검색 Mobile -->
        <div class="list-search mobile">
            <div class="serch-open-btn">
                <span class=" icon-wrap"><img class="of-ct" src="/common/img/user/icon/search.svg" alt="검색"/></span>
                원하는 조건으로 공고를 찾아보세요
            </div>
            <div class="sch-wrap">
                <p class="title">원하는 조건으로 공고를 찾아보세요</p>
                <div class="sch">
                    <p class="sch-type">유형</p>
                    <div class="sch-box">
                        <?php
                        for ($i = 0; $i < count($bz_category_arr); $i++) { ?>
                            <label class="noti-check">
                                <input type="checkbox" name="type[]"
                                       value="<?= $bz_category_arr[$i] ?>"/><span><?= $bz_category_arr[$i] ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>

                <div class="sch sch-area">
                    <p class="sch-type">사업분야</p>
                    <div class="sch-box">
                        <div class="sub-tab-group">
                            <ul class="sub-tab">
                                <li class="active">제조</li>
                                <li class="">서비스</li>
                                <li class="">IT신산업</li>
                                <li class="">건설</li>
                            </ul>
                            <div class="sub-tab-panel active">
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="자동차"/><span>자동차</span>
                                </label>
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="조선"/><span>조선</span>
                                </label>
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="철강"/><span>철강</span>
                                </label>
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="석유화학"/><span>석유화학</span>
                                </label>
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="제조:반도체"/><span>반도체</span>
                                </label>
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="플랜트"/><span>플랜트</span>
                                </label>
                                <label class="noti-check">
                                    <input type="checkbox" name="type_manufacturing[]" value="원천"/><span>원천</span>
                                </label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="섬유"/><span>섬유</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="시멘트"/><span>시멘트</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="제지"/><span>제지</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="방산"/><span>방산</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="수산"/><span>수산</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="농기계"/><span>농기계</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="중장비"/><span>중장비</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="제조:디스플레이"/><span>디스플레이</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_manufacturing[]"
                                                                 value="우주항공"/><span>우주항공</span></label>
                            </div>
                            <div class="sub-tab-panel">
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="금융"/><span>금융</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="보험"/><span>보험</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="통신"/><span>통신</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="숙박"/><span>숙박</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="음식"/><span>음식</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="레저"/><span>레저</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_service[]"
                                                                 value="부동산"/><span>부동산</span></label>
                            </div>
                            <div class="sub-tab-panel">
                                <label class="noti-check"><input type="checkbox" name="type_it[]"
                                                                 value="가전"/><span>가전</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_it[]" value="정보통신기기"/><span>정보통신기기</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_it[]"
                                                                 value="IT신산업:반도체"/><span>반도체</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_it[]"
                                                                 value="IT신산업:디스플레이"/><span>디스플레이</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_it[]"
                                                                 value="이차전지"/><span>이차전지</span></label>
                                <label class="noti-check"><input type="checkbox" name="type_it[]"
                                                                 value="바이오헬스"/><span>바이오헬스</span></label>
                            </div>
                            <div class="sub-tab-panel">
                                <label class="noti-check"><input type="checkbox" name="type_construction[]"
                                                                 value="건설"/><span>건설</span></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sch">
                    <p class="sch-type">지역</p>
                    <div class="sch-box">
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="전국"/><span>전국</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="인천"/><span>인천</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="서울"/><span>서울</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="경기"/><span>경기</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="강원"/><span>강원</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="충북"/><span>충북</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="충남"/><span>충남</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="세종"/><span>세종</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="대전"/><span>대전</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="전북"/><span>전북</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="전남"/><span>전남</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="광주"/><span>광주</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="경남"/><span>경남</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="경북"/><span>경북</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="대구"/><span>대구</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="울산"/><span>울산</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="부산"/><span>부산</span></label>
                        <label class="noti-check"><input type="checkbox" name="area[]"
                                                         value="제주"/><span>제주</span></label>
                    </div>
                </div>

                <div class="sch">
                    <div class="top">
                        <p class="sch-type">주관기관</p>
                        <div class="sch-box">
                        </div>
                    </div>
                </div>

                <div class="date-wrap">
                    <input id="startDate02" class="date-input" placeholder="시작일"> -
                    <input id="endDate02" class="date-input" placeholder="종료일">
                </div>

                <ul class="choice"></ul>

                <div class="btn-wrap">
                    <div class="btn resetBtn">초기화</div>
                    <div class="btn schBtn">검색</div>
                </div>
            </div>

            <div class="closeBtn"><img class="of-ct" src="/common/img/user/icon/close.svg" alt="닫기"/></div>
        </div>
        <!-- // 검색 Mobile -->

        <!-- 공고 리스트 -->
        <div class="tab-group2">
            <ul class="list-tab tab">
                <li class="active" data-href="list">전체 공고</li>
                <li data-href="recommend">추천 공고</li>
                <li data-href="recent">최근 본 공고</li>
                <li data-href="like">찜한 공고</li>
            </ul>

            <div class="total"><p>전체 <span><?= $cnt_total ?></span>건</p>
                <p>현재 <span><?= $page ?></span>/<?= ceil($cnt_total / 10) ?></p></div>
            <div class="tab-panel active">
                <ul class="nt-list">
                    <?php for ($i = 0; $i < count($list); $i++) {
                        $bz_e_datetime = $list[$i]['bz_apply_e_datetime'];
                        $bz_e_date = substr($bz_e_datetime, 0, 10);

                        $today = new DateTime(date('Y-m-d'));
                        $endDate = new DateTime($bz_e_date);
                        $interval = $today->diff($endDate);
                        $daysLeft = (int)$interval->format('%r%a'); // 음수도 포함해서 정수로 변환

                        $d_day = "D-" . $daysLeft;
                        ?>
                        <li class="list">
                            <a href="./view.html?bz_id=<?= $list[$i]['bz_id'] ?>">
                                <p><span class="badge-human badge"><?= $list[$i]['bz_category'] ?></span><span
                                            class="badge-display badge"><?= $list[$i]['bz_field'] ?></span><span
                                            class="badge-day badge"><?= $d_day ?></span></p>
                                <p class="title clamp c2"><?= $list[$i]['bz_title'] ?></p>
                                <div class="info">
                                    <p class="start-date">시작일자
                                        <span><?= substr($list[$i]['bz_apply_s_datetime'], 0, 10) ?></span></p>
                                    <p class="end-date">마감일자
                                        <span><?= substr($list[$i]['bz_apply_e_datetime'], 0, 10) ?></span></p>
                                    <p class="where"><?= $list[$i]['bz_institution'] ?></p>
                                </div>
                                <p class="wish <?=$list[$i]['is_like'] == 1 ? 'on' : ''?>" data-bz="<?=$list[$i]['bz_id']?>"></p>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if($cnt_total < 1) {
                       echo Html::makeNoLi('데이터가 없습니다.');
                    }?>
                </ul>
            </div>
        </div>
        <?= Html::makePaginationUser($page_arr, $query_string) ?>
    </div>
</section>
<!--<form action="./list.html" name="search_form" method="get">
    <input type="hidden" name="sch_bz_region"/>
    <input type="hidden" name="sch_bz_category"/>
    <input type="hidden" name="sch_bz_field"/>
    <input type="hidden" name="sch_bz_region"/>
    <input type="hidden" name="sch_bz_s_date"/>
    <input type="hidden" name="sch_bz_e_date"/>
</form>-->

<script src="/common/js/user/choice.js"></script>

<!-- 일정 날짜 선택 -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ko.js"></script>
<script>
    // 오늘 날짜를 YYYY-MM-DD 형식으로 반환
    function getTodayString() {
        const today = new Date();
        return today.toISOString().split('T')[0];
    }

    // input 요소에 placeholder 설정
    const startDateInput = document.getElementById("startDate02");
    startDateInput.placeholder = getTodayString();
    const endDateInput = document.getElementById("endDate02");
    endDateInput.placeholder = getTodayString();

    const sch_s_date = <?= isset($sch_s_date) ? 'new Date("' . $sch_s_date . '")' : 'null' ?>;
    const sch_e_date = <?= isset($sch_e_date) ? 'new Date("' . $sch_e_date . '")' : 'null' ?>;

    const startPicker = flatpickr("#startDate", {
        locale: "ko",
        dateFormat: "Y-m-d",
        altInput: false,
        disableMobile: true,
        defaultDate: sch_s_date,
        onChange: function (selectedDates, dateStr) {
            endPicker.set('minDate', dateStr);
        }
    });

    const endPicker = flatpickr("#endDate", {
        locale: "ko",
        dateFormat: "Y-m-d",
        altInput: false,
        disableMobile: true,
        defaultDate: sch_e_date,
        onChange: function (selectedDates, dateStr) {
            startPicker.set('maxDate', dateStr);
        }
    });

    const startPicker02 = flatpickr("#startDate02", {
        locale: "ko",
        dateFormat: "Y-m-d",
        altInput: false,
        disableMobile: true,
        defaultDate: sch_s_date,
        onChange: function (selectedDates, dateStr) {
            endPicker.set('minDate', dateStr);
        },
        onReady: function (selectedDates, dateStr, instance) {
            instance.input.placeholder = getTodayString();
        }
    });

    const endPicker02 = flatpickr("#endDate02", {
        locale: "ko",
        dateFormat: "Y-m-d",
        altInput: false,
        disableMobile: true,
        defaultDate: sch_e_date,
        onChange: function (selectedDates, dateStr) {
            startPicker.set('maxDate', dateStr);
        },
        onReady: function (selectedDates, dateStr, instance) {
            instance.input.placeholder = getTodayString();
        }
    });

    $(".schBtn").on('click', function (e) {
        const f = $('form[name=search_form]');
        f.submit();
    })

    // PHP에서 GET 파라미터를 JS 변수로 넘기기
    const schBzCategoryFromGet = <?= json_encode($_GET['sch_bz_category'] ?? []) ?>;
    const schBzFieldFromGet = <?= json_encode($_GET['sch_bz_field'] ?? []) ?>;
    const schBzRegionFromGet = <?= json_encode($_GET['sch_bz_region'] ?? []) ?>;

    $(document).ready(function () {
        $.each(schBzCategoryFromGet, function (index, value) {
            const $checkbox = $('input[type="checkbox"][name="sch_bz_category[]"][value="' + value + '"]');

            if ($checkbox.length) {
                // 이미 체크된 상태라면 클릭 이벤트 안 먹히므로, 먼저 체크 해제
                if ($checkbox.prop('checked')) {
                    $checkbox.prop('checked', false);
                }

                // 클릭 이벤트 강제로 발생시켜 연동된 UI 로직도 실행되게 함
                $checkbox.trigger('click');
            }
        });

        $.each(schBzFieldFromGet, function (index, value) {
            const $checkbox = $('input[type="checkbox"][name="sch_bz_field[]"][value="' + value + '"]');

            if ($checkbox.length) {
                // 이미 체크된 상태라면 클릭 이벤트 안 먹히므로, 먼저 체크 해제
                if ($checkbox.prop('checked')) {
                    $checkbox.prop('checked', false);
                }

                // 클릭 이벤트 강제로 발생시켜 연동된 UI 로직도 실행되게 함
                $checkbox.trigger('click');
            }
        });

        $.each(schBzRegionFromGet, function (index, value) {
            const $checkbox = $('input[type="checkbox"][name="sch_bz_region[]"][value="' + value + '"]');

            if ($checkbox.length) {
                // 이미 체크된 상태라면 클릭 이벤트 안 먹히므로, 먼저 체크 해제
                if ($checkbox.prop('checked')) {
                    $checkbox.prop('checked', false);
                }

                // 클릭 이벤트 강제로 발생시켜 연동된 UI 로직도 실행되게 함
                $checkbox.trigger('click');
            }
        });
    });

    $(".list-tab li").on('click', function (e){
        e.preventDefault();
        const target = $(this).data('href');

        location.href = "./" + target + ".html";
    })
</script>

<style>
    .no_data {font-size: 30px; margin: 0 auto}
</style>
