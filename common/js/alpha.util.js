/* Readonly 경고 */
function alertReadonly(obj) {
    var title = getInputTitle($(obj));
    var msg = title;
    if (getLastFinalSound(title)) {
        msg += "은";
    } else {
        msg += "는";
    }
    msg += " 직접 작성할 수 없는 항목입니다.";
    alert(msg);
}

/* 숫자 포맷 검사 */
let old_code = 0;
function checkNumberFormat(obj, e) {
    //return true;
    obj = $(obj);
    let value = obj.val();
    let code = e.keyCode;

    if (code == 8 ||   // backspace
        code == 9 ||   // tab
        code == 13 ||  // enter
        code == 16 ||  // shift
        code == 35 ||  // end
        code == 36 ||  // home
        code == 37 ||  // left
        code == 39 ||  // right
        code == 45 ||  // insert
        code == 46 ||  // delete
        code == 144 ||  // num lock
        (code >= 48 && code <= 57) ||             // number
        (code >= 96 && code <= 105)) {            // ten key
        return true;
    }
    // comma
    if (obj.hasClass("money") && code == 188) {
        return true;
    }
    // dot
    //console.log(code);

    // Ctrl + V 붙여넣기 단축키 허용
    if (old_code === 17 && code === 86) {
        return true;
    }
    old_code = code;

    if ((obj.hasClass("double") && code == 110 || obj.hasClass("double") && code == 190) && value.indexOf(".") == -1) {
        return true;
    }
    return false;
}

// 한글에 대해서는 상단 checkNumberFormat 함수가 적용되지 않아 추가 2024.09.04 silva
function checkNumberFormat2(obj) {
    obj = $(obj);
    var value = obj.val();

    var cleanedValue = value.replace(/[^0-9.,]/g, '');

    if (obj.hasClass('double')) {
        var parts = cleanedValue.split('.');
        if (parts.length > 2) {
            cleanedValue = parts[0] + '.' + parts.slice(1).join('');
        }
    }

    if (!obj.hasClass('money')) {
        cleanedValue = cleanedValue.replace(/,/g, '');
    }

    obj.val(cleanedValue);
}

/* 숫자 입력 전처리 */
function preprocessNumberInput(obj) {
    obj = $(obj);
    var value = obj.val();
    if (value == 0) {
        obj.val("").attr("value", "");
    } else if (obj.hasClass("money")) {
        value = delComma(value);
        obj.val(value).attr("value", value);
    }
}

/* 숫자 입력 후처리 */
function postprocessNumberInput(obj) {
    obj = $(obj);
    var value = obj.val();
    if (obj.hasClass("money")) {
        value = setComma(delComma(value));
    } else if (obj.hasClass("double")) {
        if (value.substring(0, 1) === ".") {
            value = "0" + value;
        }
    }
    /*
    if (value === "") {
        value = "0";
    }
    */
    obj.val(value).attr("value", value);
}

/* 날짜 입력 후처리 */
function postprocessDateInput(obj) {
    obj = $(obj);
    var value = obj.val();
    if (!value) {
        value = "0000-00-00";
    }
    value = value.replace(/\./g, "-");
    obj.val(value).attr("value", value);
}

/* Input Title or Label 반환 */
function getInputTitle(obj) {
    var title = obj.attr("name");
    if (obj.attr("title")) {
        title = obj.attr("title");
    } else if ($("label[for=" + obj.attr("id") + "]").text()) {
        title = $("label[for=" + obj.attr("id") + "]").text();
    }

    return title;
}

/* 마지막 글자의 종성을 반환 */
function getLastFinalSound(str) {
    return getFinalSound(str.substr(str.length - 1, 1));
}

/* 초성을 반환 */
function getInitialSound(char) {
    return String.fromCharCode(((char.charCodeAt(0) - parseInt("0xac00", 16)) / 28) / 21 + parseInt("0x1100", 16));
}

/* 중성을 반환 */
function getMedialSound(char) {
    return String.fromCharCode(((char.charCodeAt(0) - parseInt("0xac00", 16)) / 28) % 21 + parseInt("0x1161", 16));
}

/* 종성을 반환 */
function getFinalSound(char) {
    if ((char.charCodeAt(0) - parseInt("0xac00", 16)) % 28 + parseInt("0x11A8") - 1 != 4516 &&
        (char.charCodeAt(0) - parseInt("0xac00", 16)) % 28 + parseInt("0x11A8") - 1 != 4519) {
        return String.fromCharCode((char.charCodeAt(0) - parseInt("0xac00", 16)) % 28 + parseInt("0x11A8") - 1);
    }

    return null;
}

/* 천자리 마다 콤마를 세팅 */
function setComma(str) {
    var result = str;

    try {
        result = str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    } catch (e) {
    }

    return result;
}

/* 천자리마다 존재하는 콤마를 제거 */
function delComma(str) {
    var result = str;

    try {
        result = str.toString().replace(/,/g, "");
    } catch (e) {
    }

    return result;
}

/* Placeholder 초기화 */
function initPlaceholder(obj) {
    var obj_placeholder = $(".placeholder", obj);
    for (var i = 0; i < obj_placeholder.length; i++) {
        checkPlaceholder(obj_placeholder.eq(i));
    }
}

/* Placeholder 체크 */
function checkPlaceholder(obj) {
    var value = $(obj).val();
    if (value) {
        $(obj).addClass("no_placeholder");
    } else {
        $(obj).removeClass("no_placeholder");
    }
}

/* 컨펌 후 동작 */
function confirmByButton(obj) {
    obj = $(obj);
    var title = obj.attr("title");
    var flag_change = obj.hasClass("btn_change");
    var msg = title;

    if (flag_change) {
        msg += " 상태로 변경하시겠습니까?";
    } else {
        msg += " 페이지로 이동하시겠습니까?";
    }

    return confirm(msg);
}

/* 전체 선택 토글 */
function toggleAllCheckbox(obj) {
    var obj = $(obj);
    var obj_parent = obj.parent();
    for (var i = 0; i < 5; i++) {
        if (obj_parent.find(".list_checkbox").length > 0) {
            break;
        }
        obj_parent = obj_parent.parent();
    }

    if (obj.is(":checked")) {
        obj_parent.find("input.list_checkbox").attr("checked", true);
    } else {
        obj_parent.find("input.list_checkbox").removeAttr("checked");
    }
}

function toggleAllCheckboxBU(obj) {
    var obj = $(obj);
    var obj_parent = obj.parent();
    for (var i = 0; i < 5; i++) {
        if (obj_parent.find(".list_bu_checkbox").length > 0) {
            break;
        }
        obj_parent = obj_parent.parent();
    }

    if (obj.is(":checked")) {
        obj_parent.find("input.list_bu_checkbox").attr("checked", true);
    } else {
        obj_parent.find("input.list_bu_checkbox").removeAttr("checked");
    }
}

/* 빠른 기간 세팅 */
function changeSearchPeriod(obj) {
    obj = $(obj);
    var arr = obj.attr("href").split("sch_s_date=");

    var sch_s_date = "";
    var sch_e_date = "";
    if (arr.length > 1) {
        arr = arr[1].split("&sch_e_date=");
        sch_s_date = arr[0];

        arr = arr[1].split("&");
        sch_e_date = arr[0];
    }

    var f = document.search_form;
    f.sch_s_date.value = sch_s_date;
    f.sch_e_date.value = sch_e_date;
    $("a.btn_change_period").not(obj).removeClass("active");
    obj.addClass("active");
}

/* 연도 선택 */
function choosePeriodYear() {
    var f = document.search_form;
    var sch_year = f.sch_year.value;
    if (!sch_year) {
        f.sch_month.value = "";
        $("#sch_month").trigger("change");
        return;
    }

    var sch_month = f.sch_month.value;
    setPeriodByMonth(sch_year, sch_month);
}

/* 월 선택 */
function choosePeriodMonth() {
    var f = document.search_form;
    var sch_year = f.sch_year.value;
    var sch_month = f.sch_month.value;

    if (!sch_year && sch_month) {
        f.sch_month.value = "";
        $("#sch_month").trigger("change");
        return;
    }

    setPeriodByMonth(sch_year, sch_month);
}

/* 검색 기간 세팅 */
function setPeriodByMonth(sch_year, sch_month) {
    var sch_s_date = "";
    var sch_e_date = "";
    if (sch_year && sch_month) {
        sch_s_date = sch_year + "-" + (sch_month < 10 ? "0" : "") + sch_month + "-01";
        var date = new Date(sch_s_date);
        date.setMonth(sch_month);
        date.setDate(0);

        var m = date.getMonth() * 1 + 1;
        var d = date.getDate() * 1;
        sch_e_date = date.getFullYear() + "-" + (m < 10 ? "0" : "") + m + "-" + (d < 10 ? "0" : "") + d;
    }

    if (sch_year && !sch_month) {
        sch_s_date = sch_year + "-01-01";
        sch_e_date = sch_year + "-12-31";
    }

    var f = document.search_form;
    f.sch_s_date.value = sch_s_date;
    f.sch_e_date.value = sch_e_date;
}

/* 정렬순서 변경 */
function changeSortOrder(obj) {
    obj = $(obj);
    var f = document.search_form;
    var name = obj.attr("name");
    var value = obj.val();

    $("input[name='" + name + "']", f).attr("value", value).val(value);
    if (f.onsubmit()) {
        f.submit();
    }
}

/* 팝업창 열기 */
function openPopup(obj) {
    obj = $(obj);
    var url = obj.attr("href");
    var title = obj.attr("title");

    var popup_size = "size_640x480";

    var class_arr = obj.attr("class").split(" ");
    var size_fmt = /^size_\d{3,4}x\d{3,4}$/;
    for (var i = 0; i < class_arr.length; i++) {
        if (size_fmt.test(class_arr[i])) {
            popup_size = class_arr[i];
            break;
        }
    }

    var size_arr = popup_size.replace("size_", "").split("x");
    var popup_width = size_arr[0];
    var popup_height = size_arr[1];

    try {
        window.open(url, title, "scrollbars=yes,width=" + popup_width + ",height=" + popup_height + ",top=10,left=20");
    } catch (e) {
        window.open(url, "", "scrollbars=yes,width=" + popup_width + ",height=" + popup_height + ",top=10,left=20");
    }
}

/* 내용을 요소 안에 삽입 */
function insertContent(obj, content) {
    obj.html(content);
    initContent(obj);
}

/* 로딩 후 JS 초기화 */
function initContent(obj) {
    // 한글 ime-mode
    $("input.text, textarea.textarea", obj).not("#login_id").css("ime-mode", "active");

    // 읽기 전용
    $("input.readonly", obj).prop("readonly", true);
    $("select.readonly > option", obj).not(":selected").prop("disabled", true);

    // 숫자 처리
    $("input.number, input.money, input.double", obj).css("ime-mode", "disabled");

    // placeholder
    initPlaceholder(obj);

    // sButton
    initSButton(obj);

    // uniform
    try {
        $("select.select", obj).uniform();
    } catch (e) {
        //console.log("jquery-uniform Error");
    }

    // datepicker
    try {
        if ($.datepicker) {
            $.datepicker.regional["ko"] = {
                closeText: "닫기",
                prevText: "이전달",
                nextText: "다음달",
                currentText: "오늘",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: false,
                yearRange: "c-99:c+99",
                maxDate: "+" + 365 * 10 + "d",
                minDate: "-" + 365 * 10 + "d",
                monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
                dayNames: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"],
                dayNamesShort: ["일", "월", "화", "수", "목", "금", "토"],
                dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
                weekHeader: "Wk",
                dateFormat: "yy-mm-dd",
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: "",
                showOn: "button",
                buttonImage: "/common/js/jquery-ui-1.11.4/images/btn_calendar.gif",
                buttonImageOnly: true,
                buttonText: "Select date"
            };

            $.datepicker.setDefaults($.datepicker.regional["ko"]);
            $("input.date", obj).datepicker();
            $("input.birthday", obj).datepicker({
                maxDate: "+ 0d",
                minDate: "-" + 365 * 100 + "d"
            });
        }
    } catch (e) {
        console.log("jQuery-ui Error")
    }
}

/* sButton 초기화 */
function initSButton(obj) {
    var obj_sButton = $(".sButton", obj);
    for (var i = 0; i < obj_sButton.length; i++) {
        var content = "<span class='sButton-container'><span class='sButton-bg'>";
        if (obj_sButton.eq(i).hasClass("icon")) {
            content += "<span class='icon'></span>";
        }
        content += "<span class='text'>" + obj_sButton.eq(i).text() + "</span></span></span>";

        obj_sButton.eq(i).html(content);
    }
}

/* 네이티브 브릿지 호출 */
function callNative(param) {
    try {
        if (!is_webview) {
            alert("앱에서만 실행 가능한 기능입니다.");
            return false;
        }
    } catch (e) {
    }

    $("#native_bridge").prop("src", "native://" + param);
}

/* 백버튼 트리거 */
function triggerBackButton() {
    // 레이어 팝업 검사
    if ($("#layer_popup").hasClass("open")) {
        closeLayerPopup();
        return;
    }

    for (var i = 10; i > 0; i--) {
        var obj_layer = $("#layer_page" + i);
        if (obj_layer.hasClass("open")) {
            obj_layer.removeClass("open");
            return;
        }
    }

    // 백 URL 있는지 검사
    var back_url = $("#btn_back").attr("href");
    if (back_url) {
        location.replace(back_url);
    } else {
        callNative("confirmExit");
    }
}

/* 금액 천단위 콤바 기능 */
function moneyFormat(obj) {
    const input = obj;
    input.addEventListener('keyup', function(e) {
        let value = e.target.value;
        value = Number(value.replaceAll(',', ''));
        if (isNaN(value)) {
            input.value = "";
        } else {
            input.value = value.toLocaleString('ko-KR');
        }
        if (value === 0) {
            input.value = "";
        }
    })
}

/* 숫자 천단위 콤바 기능 */
function numberFormat(obj) {
    const input = obj;
    input.addEventListener('keyup', function(e) {
        let value = e.target.value;
        value = Number(value.replaceAll(',', ''));
        if (isNaN(value)) {
            input.value = "";
        } else {
            input.value = value.toLocaleString('ko-KR');
        }
        if (value === 0) {
            input.value = "";
        }
    })
}