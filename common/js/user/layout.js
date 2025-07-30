$(function () {

    var ww = window.innerWidth;


    //=============================================== dropDown
    $('.dropBtn').click(function (e) {
        e.preventDefault();
        $(this).closest('.dropDown').toggleClass('on');
    });

    var resizeTimeout;

    $(window).resize(function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            $('.dropBtn').closest('.dropDown').removeClass('on');
        }, 200);
    });


    //=============================================== wish
    $('.wish').each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            const mb_id = $('input[name="mb_id"]').val();
            const bz_id = $(this).data('bz');
            const wish = $(this);
            let state = 'unlike';
            if ($(this).hasClass('on')) {
                state = 'like';
            }

            if (!mb_id) {
                alert('로그인이 필요합니다.');
            } else {
                $.ajax({
                    url: "process.html",
                    type: "GET",
                    dataType: "json",
                    data: {
                        flag_json: '1',
                        mode: 'biz_like',
                        bz_id: bz_id,
                        state: state
                    },
                    success: function (rs) {
                        wish.toggleClass('on');
                        // wish02인 경우에만 이미지 src 변경
                        if (wish.hasClass('wish02')) {
                            const img = wish.find('img').eq(1);
                            if (wish.hasClass('on')) {
                                img.attr('src', '/common/img/user/icon/wish-on.svg');
                            } else {
                                img.attr('src', '/common/img/user/icon/wish-line.svg');
                            }
                        }
                    }
                });
            }
        });
    });


    //=============================================== charCount
    $('textarea').each(function () {
        var length = $(this).val().length;
        $(this).next('.txt-count').find('span').text(length);
    });
    $('textarea').on('input', function () {
        var length = $(this).val().length;
        $(this).next('.txt-count').find('span').text(length);
    });


    //=============================================== tab
    $('.tab-group').each(function () {
        const $group = $(this);
        $group.find('.tab > li').click(function () {
            const index = $(this).index();
            $group.find('.tab > li').removeClass('active');
            $(this).addClass('active');
            $group.find('.tab-panel').removeClass('active').hide();
            $group.find('.tab-panel').eq(index).addClass('active').show();
        });
    });

    $('.sub-tab-group').each(function () {
        const $group = $(this);
        $group.find('.sub-tab > li').click(function () {
            const index = $(this).index();
            $group.find('.sub-tab > li').removeClass('active');
            $(this).addClass('active');
            $group.find('.sub-tab-panel').removeClass('active').hide();
            $group.find('.sub-tab-panel').eq(index).addClass('active').show();
        });
    });


    //=============================================== prompt btn
    $('#prompt .btn01').click(function () {
        $(this).addClass('on');
    });


    //=============================================== noti-list
    $('.serch-open-btn').click(function () {
        $(this).closest('.list-search').addClass('active');
    });

    $('.list-search .closeBtn').click(function () {
        $(this).closest('.list-search').removeClass('active');
    });

    $('.sch').click(function (e) {
        if ($(e.target).closest('.sch-box').length > 0) {
            return;
        }

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.sch').removeClass('active');
            $(this).addClass('active');
        }
    });


    //=============================================== btn-upload
    // 공통 파일명 출력 함수
    function updateFileName(inputSelector) {
        $(inputSelector).on('change', function () {
            const fileName = $(this).val().split('\\').pop();
            const targetSelector = $(this).data('target');

            if (targetSelector) {
                const $label = $(targetSelector);
                $label.text(fileName || $label.data('placeholder') || '파일을 등록해주세요.');
            }
        });
    }


    //=============================================== quick
    updateQuickBehavior();

    $(document).on('click', '#quick .title', function () {
        if (window.innerWidth < 1280) {
            $(this).siblings('ul').toggle();
        }
    });

    $(window).on('resize', function () {
        updateQuickBehavior();

        // 리사이즈 시 1280 이상이면 무조건 ul 보이게
        if (window.innerWidth >= 1280) {
            $('#quick').find('ul').show();
        } else {
            $('#quick').find('ul').hide();
        }
    });


    //=============================================== join-mail
    const domainSelect = document.getElementById("managerEmailDomain");
    const customDomainInput = document.getElementById("managerEmailCustomDomain");

    if (domainSelect) {
        domainSelect.addEventListener("change", function () {
            if (this.value === "manual") {
                customDomainInput.style.display = "inline-block";
                customDomainInput.focus();
            } else {
                customDomainInput.style.display = "none";
            }
        });
    }

    const domainSelect02 = document.getElementById("userEmailDomain");
    const customDomainInput02 = document.getElementById("userEmailCustomDomain");

    if (domainSelect02) {
        domainSelect02.addEventListener("change", function () {
            if (this.value === "manual") {
                customDomainInput02.style.display = "inline-block";
                customDomainInput02.focus();
            } else {
                customDomainInput02.style.display = "none";
            }
        });
    }

    const domainSelect03 = document.getElementById("userEmailDomain_modi");
    const customDomainInput03 = document.getElementById("userEmailCustomDomain_modi");

    if (domainSelect03) {
        domainSelect03.addEventListener("change", function () {
            if (this.value === "manual") {
                customDomainInput03.style.display = "inline-block";
                customDomainInput03.focus();
            } else {
                customDomainInput03.style.display = "none";
            }
        });
    }


    //=============================================== join-mail
    /*
    $('#checkDuplicateBtn, #checkDuplicateBtn02').on('click', function () {
        var id_possible;
        var currentPath = window.location.pathname;
        if (currentPath === '/page/join/info.html') {
            id_possible = 'possible';
        }

        if (id_possible === 'possible') {
            alert('사용할 수 있는 아이디입니다.');
        } else {
            alert('이미 사용 중인 아이디입니다.');
        }
    });
    */

    //=============================================== mp-mobile
    function updateFormDisplay() {
        ww = window.innerWidth;
        if (ww <= 1279) {
            $('.mpForm01, .mpForm02, .mpForm03').hide();

            var activeIndex = $('.index-ul.mobile li.active').index();

            if (activeIndex === 0) {
                $('.mpForm01').show();
            } else if (activeIndex === 1) {
                $('.mpForm02').show();
            } else if (activeIndex === 2) {
                $('.mpForm03').show();
            }
        } else {
            $('.mpForm01, .mpForm02, .mpForm03').show();
        }
    }

    updateFormDisplay();

    $(window).resize(function () {
        updateFormDisplay();
    });


    //=============================================== 디데이 apply-mark
    $('#mp .dday .list').each(function () {
        var $this = $(this);
        var txt = $this.find('.badge-day').text().trim();  // "D-20"
        var txtDay = txt.split('-'); // ["D", "20"]

        if (txtDay.length > 1) {
            var day = parseInt(txtDay[1], 10); // 20

            // day 값이 유효할 경우
            if (!isNaN(day)) {
                // 조건부 클래스 추가
                if (day <= 3) {
                    $this.addClass('mark-on');
                }

                // <p class="mark"> 안에 있는 <span>에 day 값 설정
                $this.find('.mark span').text(day);
            }
        }
    });


    //=============================================== 접수진행 pop
    $('#mp .applyForm').on('click', '.pop-txt-btn', function (e) {
        e.stopPropagation();

        const $thisPop = $(this).closest('.list').find('.pop-txt');
        const isVisible = $thisPop.is(':visible');

        $('#mp .pop-txt').hide();

        if (!isVisible) {
            $thisPop.show();
        }
    });

    $('#mp .applyForm').on('click', '.pop-txt', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', function () {
        $('#mp .pop-txt').hide();
    });


    //=============================================== nt-list 삭제
    $('.nt-list li').each(function () {
        $(this).find('.delBtn').click(function () {
            $(this).closest('li').remove();
        });
    });

    //=============================================== pop
    let $currentMemoBtn = null;
    const DEFAULT_MEMO_TEXT = '공고에 관련된 중요한 내용을 메모해보세요 <span>ex) 나중에 지원서 작성하기</span>';

    // 팝업 열기
    $('.memo-btn').on('click', function () {
        const mb_id = $('input[name="mb_id"]').val();
        const bz_id = $(this).data('bz');

        if (!mb_id) {
            alert('로그인이 필요합니다.');
        } else {
            $currentMemoBtn = $(this);
            const $li = $currentMemoBtn.closest('.memo');
            if ($li.hasClass('memo-on')) {
                $('#memo_txt').val($currentMemoBtn.text().trim());
            } else {
                $('#memo_txt').val('');
            }

            $('.btn-save').attr('data-bz', bz_id);

            $('.pop-up').show();
        }
    });

    // 저장 버튼 클릭 시
    $('.pop-up.pop-memo .btn-save').on('click', function () {
        if (!$currentMemoBtn) return;

        const memoText = $('#memo_txt').val().trim();
        const $li = $currentMemoBtn.closest('.memo');
        const bz_id = $(this).data('bz');

        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'save_memo',
                bz_id: bz_id,
                memoText: memoText
            },
            success: function(result) {
                if (memoText !== '') {
                    $li.addClass('memo-on');
                    $currentMemoBtn.text(memoText);
                } else {
                    $li.removeClass('memo-on');
                    $currentMemoBtn.html(DEFAULT_MEMO_TEXT);
                    $('#memo_txt').val('');
                }
                location.reload();
            }
        });
        //closeMemoPopup();
    });

    // 닫기 함수
    function closeMemoPopup() {
        $('.pop-up').hide();
        $currentMemoBtn = null;
    }

    // 닫기/취소/배경 클릭 시 닫기
    $('.pop-up .pop-close').on('click', closeMemoPopup);
    $('.pop-up .bg').on('click', closeMemoPopup);
    $('.pop-up .btn-cancel').on('click', closeMemoPopup);

    // 부계정관리 신규등록 팝업 열기
    /*
    $('#mp .btn_add').click(function () {
        $('.pop-up.new').show();
    });
    */
    // 비밀번호 재설정
    $('#resetPwBtn').click(function () {
        $('.pop-up.pop-pw').show();
    });

    //=============================================== apply-tab nt-end
    $('#mp .apply-tab > li').click(function () {
        const target = $(this).data('target');

        location.href = "./" + target + "_list.html";
    });

    $(document).ready(function () {
        const initialIndex = $('#mp .apply-tab > li.active').index();
        if (initialIndex === 2) {
            $('#mp .applyForm select').hide();
        } else {
            $('#mp .applyForm select').show();
        }
    });


    //=============================================== mp 모바일 조건검색
    $('#mp .filter-btn p').click(function (e) {
        e.stopPropagation();
        $('.ft-wrap').toggle();
    });

    $('#mp .ft-btn').click(function (e) {
        e.stopPropagation();
        $('.ft-wrap').hide();
    });

    $('#mp .ft-wrap').click(function (e) {
        e.stopPropagation();
    });

    $(document).click(function () {
        $('.ft-wrap').hide();
    });


    //=============================================== 찜한공고 wish 해제 시 공고 삭제
    $('#mp.wish-noti .nt-list > li').each(function () {
        $(this).find('.wish.on').click(function (e) {
            //$(this).closest('li').remove();
            e.preventDefault();
            e.stopPropagation();
            const mb_id = $('input[name="mb_id"]').val();
            const bz_id = $(this).data('bz');
            const wish = $(this);
            let state = 'unlike';
            if ($(this).hasClass('on')) {
                state = 'like';
            }

            if (!mb_id) {
                alert('로그인이 필요합니다.');
            } else {
                $.ajax({
                    url: "process.html",
                    type: "GET",
                    dataType: "json",
                    data: {
                        flag_json: '1',
                        mode: 'biz_like',
                        bz_id: bz_id,
                        state: state
                    },
                    success: function (rs) {
                        if(state === 'like'){
                            wish.removeClass('on');
                        } else if (state === 'unlike'){
                            wish.addClass('on');
                        }
                        // wish02인 경우에만 이미지 src 변경
                        if (wish.hasClass('wish02')) {
                            const img = wish.find('img').eq(1);
                            if (wish.hasClass('on')) {
                                img.attr('src', '/common/img/user/icon/wish-on.svg');
                            } else {
                                img.attr('src', '/common/img/user/icon/wish-line.svg');
                            }
                        }
                    }
                });
            }
        });
    });


    //=============================================== 찜한공고 달력/리스트 선택 시 링크 이동
    const linkSelectEl = document.getElementById("linkSelect");

    if (linkSelectEl) {
        linkSelectEl.addEventListener("change", function () {
            const url = this.value;
            if (url) {
                window.location.href = url;
            }
        });
    }


    //=============================================== sub-list-chk
    $('#listCheckAll').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.list-check').prop('checked', isChecked);
    });

    $(document).on('change', '.list-check', function () {
        const allChecked = $('.list-check').length === $('.list-check:checked').length;
        $('#listCheckAll').prop('checked', allChecked);
    });


    //=============================================== 지원현황 내역 삭제
    $('#noti-del-all').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.del-check').prop('checked', isChecked);
    });

    $(document).on('change', '.del-check', function () {
        const allChecked = $('.del-check').length === $('.del-check:checked').length;
        $('#noti-del-all').prop('checked', allChecked);
    });


    //=============================================== 마이페이지 > 지원현황 > 최종선정 버튼
    $('.nt-end .write-btn').each(function () {
        $(this).click(function () {
            $(this).closest('li').remove();
        });
    });


});


//=============================================== quick
function updateQuickBehavior() {
    ww = window.innerWidth;

    if (ww >= 1280) {
        $('#quick').removeClass('on');
        $('#quick').find('ul').show();
        $(window).off('scroll.quick'); // 이벤트 중복 방지
    } else {
        $(window).on('scroll.quick', function () {
            if ($(this).scrollTop() <= 60) {
                $('#quick').removeClass('on');
                $('#quick').find('ul').hide();
            } else {
                $('#quick').addClass('on');
            }
        });
    }
}


//=============================================== join-chk
document.addEventListener('DOMContentLoaded', function () {
    const agreeAll = document.querySelector('input[name="agree_all"]');
    const allCheckboxes = document.querySelectorAll(
        '.required-terms input[type="checkbox"]:not([name="agree_all"]), .optional-terms input[type="checkbox"]'
    );

    if (agreeAll) {
        agreeAll.addEventListener('change', function () {
            allCheckboxes.forEach(function (checkbox) {
                checkbox.checked = agreeAll.checked;
            });
        });

        allCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                if ([...allCheckboxes].some(cb => !cb.checked)) {
                    agreeAll.checked = false;
                } else if ([...allCheckboxes].every(cb => cb.checked)) {
                    agreeAll.checked = true;
                }
            });
        });
    }


    //=============================================== 모바일 메뉴
    $('.menuBtn').click(function (e) {
        $('#menu').addClass('on');
        $('#openMenu').addClass('on');
    });
    $('.closeBtn').click(function (e) {
        $('#menu').removeClass('on');
        $('#openMenu').removeClass('on');
    });
});


//=============================================== 사업공고 초기화 버튼
document.addEventListener("DOMContentLoaded", function () {
    const resetButtons = document.querySelectorAll(".resetBtn");

    resetButtons.forEach(button => {
        button.addEventListener("click", function () {
            // 모든 체크박스 해제
            const checkboxes = document.querySelectorAll(".list-search input[type='checkbox']");
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // 오늘 날짜 구하기
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`; // ← 바뀐 부분

            // 날짜 input 초기화 및 오늘 날짜로 설정
            const dateInputs = document.querySelectorAll(".date-input");
            dateInputs.forEach(input => {
                input.value = formattedDate;
            });

            // 선택된 항목 표시 영역 초기화 (선택 사항)
            const choiceLists = document.querySelectorAll(".choice");
            choiceLists.forEach(list => {
                list.innerHTML = "";
            });
        });
    });
});


//=============================================== 1:1 문의 파일 버튼
function updateFileName(input) {
    const fileBox = document.querySelector('.inquiry_file-box');
    const fileName = input.files[0] ? input.files[0].name : '';
    const fileNameSpan = fileBox.querySelector('.file-name');
    const fileRemoveSpan = fileBox.querySelector('.file-remove');

    if (fileName) {
        fileNameSpan.textContent = fileName;
        fileRemoveSpan.style.display = 'inline';
    } else {
        fileNameSpan.textContent = '';
        fileRemoveSpan.style.display = 'none';
    }
}

function removeFile() {
    const input = document.getElementById('inquiry_file');
    input.value = ''; // 파일 선택 해제
    const fileNameSpan = document.querySelector('.file-name');
    fileNameSpan.textContent = '';
    const fileRemoveSpan = document.querySelector('.file-remove');
    fileRemoveSpan.style.display = 'none';
}