$(function () {

    var ww = window.innerWidth;

    // 도 지역 클래스 이름들
    const regionBgMap = {
        gyeongnam: 'gyeongnam',
        gyeongbuk: 'gyeongbuk',
        jeonbuk: 'jeonbuk',
        jeonnam: 'jeonnam',
        chungnam: 'chungnam',
        chungbuk: 'chungbuk',
        gangwon: 'gangwon',
        gyeonggi: 'gyeonggi',
    };

    // 광역시 클래스 매핑
    const regionTxtMap = {
        'bg-busan': 'txt-busan',
        'bg-daegu': 'txt-daegu',
        'bg-ulsan': 'txt-ulsan',
        'bg-gwangju': 'txt-gwangju',
        'bg-jeju': 'txt-jeju',
        'bg-seoul': 'txt-seoul',
        'bg-incheon': 'txt-incheon',
        'bg-daejeon': 'txt-daejeon',
        'bg-sejong': 'txt-sejong',
    };

    // 드롭다운 지역명 -> regionId 매핑
    const nameToRegionIdMap = {
        '서울': 'seoul',
        '부산': 'busan',
        '대구': 'daegu',
        '인천': 'incheon',
        '광주': 'gwangju',
        '대전': 'daejeon',
        '울산': 'ulsan',
        '세종': 'sejong',
        '제주': 'jeju',
        '경기도': 'gyeonggi',
        '강원': 'gangwon',
        '충남': 'chungnam',
        '충북': 'chungbuk',
        '경남': 'gyeongnam',
        '경북': 'gyeongbuk',
        '전남': 'jeonnam',
        '전북': 'jeonbuk',
    };

    const metropolitanIds = [
        'seoul', 'busan', 'daegu', 'incheon',
        'gwangju', 'daejeon', 'ulsan', 'jeju', 'sejong'
    ];

    // 클래스 이름으로 regionId 구분을 위한 매핑
    const bgTxtToRegionId = {};
    Object.keys(regionBgMap).forEach(regionId => {
        bgTxtToRegionId['bg-' + regionId] = regionId;
        bgTxtToRegionId['txt-' + regionId] = regionId;
        bgTxtToRegionId[regionId] = regionId;
    });

    Object.entries(regionTxtMap).forEach(([bgClass, txtClass]) => {
        const regionId = bgClass.replace('bg-', '');
        bgTxtToRegionId[bgClass] = regionId;
        bgTxtToRegionId[txtClass] = regionId;
    });

    // 클릭 로직을 하나의 함수로 통합
    // function handleRegionClick(regionId, isMetropolitan) {
    //     if (isMetropolitan) {
    //         // 모든 광역시 active 제거
    //         Object.entries(regionTxtMap).forEach(([otherBg, otherTxt]) => {
    //             $('.' + otherBg).removeClass('active');
    //             $('.' + otherTxt).removeClass('active');
    //         });
    //
    //         // 도 관련 active 제거 및 show
    //         //$('.region').removeClass('active');
    //         $('.region').each(function () {
    //             const $el = $(this);
    //             const currentClass = $el.attr('class') || '';
    //             const newClass = currentClass
    //                 .split(' ')
    //                 .filter(c => c !== 'active')
    //                 .join(' ');
    //             $el.attr('class', newClass);
    //         });
    //         Object.keys(regionBgMap).forEach(id => {
    //             $('.bg-' + id).removeClass('active').show();
    //             $('.txt-' + id).removeClass('active');
    //         });
    //
    //         // 현재 광역시 active 추가
    //         const $el = $('path.bg-' + regionId);
    //         $el.attr('class', $el.attr('class') + ' active');
    //         //$('.bg-' + regionId).addClass('active');
    //         $('.txt-' + regionId).addClass('active');
    //     } else {
    //         // 도 관련 요소들 active 제거
    //         //$('.region').removeClass('active');
    //         $('.region').each(function () {
    //             const $el = $(this);
    //             const currentClass = $el.attr('class') || '';
    //             const newClass = currentClass
    //                 .split(' ')
    //                 .filter(c => c !== 'active')
    //                 .join(' ');
    //             $el.attr('class', newClass);
    //         });
    //         Object.keys(regionBgMap).forEach(id => {
    //             $('.bg-' + id).removeClass('active').show();
    //             $('.txt-' + id).removeClass('active');
    //         });
    //
    //         // 광역시 관련 요소들 active 제거
    //         Object.entries(regionTxtMap).forEach(([bgClass, txtClass]) => {
    //             $('.' + bgClass).removeClass('active');
    //             $('.' + txtClass).removeClass('active');
    //         });
    //
    //         // 선택된 도의 배경만 숨기기
    //         $('.bg-' + regionId).hide();
    //
    //         // 현재 도 관련 요소들에 active 추가
    //         const $el = $('path.' + regionId);
    //         $el.attr('class', $el.attr('class') + ' active');
    //         //$('.' + regionId).addClass('active');
    //         $('.txt-' + regionId).addClass('active');
    //     }

        // 리스트 필터링
    //     $('.scrollwrap li').hide(); // 모든 항목 숨기기
    //     const matchedItems = $('.scrollwrap li.' + regionId);
    //     if(ww <= 1279){
    //         matchedItems.slice(0, 4).show(); // 모바일 최대 4개만 표시
    //     }else {
    //         matchedItems.slice(0, 8).show(); // PC 최대 8개만 표시
    //     }
    //
    // }

    function removeClassFromSvgElements(selector, className) {
        $(selector).each(function () {
            const $el = $(this);
            const currentClass = $el.attr('class') || '';
            const newClass = currentClass
                .split(' ')
                .filter(c => c !== className)
                .join(' ');
            $el.attr('class', newClass);
        });
    }

    function addClassToSvgElement(selector, className) {
        const $el = $(selector);
        const currentClass = $el.attr('class') || '';
        if (!currentClass.includes(className)) {
            $el.attr('class', currentClass + ' ' + className);
        }
    }

    function handleRegionClick(regionId, isMetropolitan) {
        if (isMetropolitan) {
            // 1. 모든 광역시 active 제거
            Object.entries(regionTxtMap).forEach(([otherBg, otherTxt]) => {
                removeClassFromSvgElements('path.' + otherBg, 'active');
                $('.' + otherTxt).removeClass('active');
            });

            // 2. 도 관련 active 제거 및 show
            removeClassFromSvgElements('.region', 'active');
            Object.keys(regionBgMap).forEach(id => {
                removeClassFromSvgElements('path.bg-' + id, 'active');
                $('path.bg-' + id).show();
                $('.txt-' + id).removeClass('active');
            });

            // 3. 현재 광역시 active 추가
            addClassToSvgElement('path.bg-' + regionId, 'active');
            $('.txt-' + regionId).addClass('active');
        } else {
            // 1. 도 관련 요소들 active 제거
            removeClassFromSvgElements('.region', 'active');
            Object.keys(regionBgMap).forEach(id => {
                removeClassFromSvgElements('path.bg-' + id, 'active');
                $('path.bg-' + id).show();
                $('.txt-' + id).removeClass('active');
            });

            // 2. 광역시 관련 요소들 active 제거
            Object.entries(regionTxtMap).forEach(([bgClass, txtClass]) => {
                removeClassFromSvgElements('path.' + bgClass, 'active');
                $('.' + txtClass).removeClass('active');
            });

            // 3. 선택된 도의 배경 숨기기
            $('path.bg-' + regionId).hide();

            // 4. 현재 도 관련 요소들 active 추가
            addClassToSvgElement('path.' + regionId, 'active');
            $('.txt-' + regionId).addClass('active');
        }

        // 5. 리스트 필터링
        $('.scrollwrap li').hide(); // 모든 항목 숨기기
        const matchedItems = $('.scrollwrap li.' + regionId);
        if (ww <= 1279) {
            matchedItems.slice(0, 4).show(); // 모바일 최대 4개
        } else {
            matchedItems.slice(0, 8).show(); // PC 최대 8개
        }

        const cnt = $('li.' + regionId).length;

        $('.total > span').text(cnt);
    }


    // 지도 영역 클릭 이벤트 (도 + 광역시)
    $('[class*="bg-"], [class*="txt-"], .region').on('click', function () {
        const classList = $(this).attr('class').split(/\s+/);
        let regionId = null;

        for (let cls of classList) {
            if (bgTxtToRegionId[cls]) {
                regionId = bgTxtToRegionId[cls];
                break;
            }
        }

        if (!regionId) return;

        const isMetropolitan = metropolitanIds.includes(regionId);
        handleRegionClick(regionId, isMetropolitan);

        // 역방향 매핑
        const regionIdToNameMap = Object.fromEntries(
            Object.entries(nameToRegionIdMap).map(([name, id]) => [id, name])
        );

        // handleRegionClick 내부 마지막 부분
        const regionText = regionIdToNameMap[regionId] || '전국지도선택';
        $('.map-dropDown .dropBtn').text(regionText);
    });

    // 드롭다운 li 클릭 이벤트
    $('.dropDown.map-dropDown .dropCon li').on('click', function () {
        const regionName = $(this).text().trim();
        const regionId = nameToRegionIdMap[regionName];
        if (!regionId) return;

        const isMetropolitan = metropolitanIds.includes(regionId);
        handleRegionClick(regionId, isMetropolitan);
    });
});
