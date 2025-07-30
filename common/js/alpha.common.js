$(document).ready(function() {
    /* 기본 이벤트 바인딩 */
    $(document).on("click", "input.readonly", function(e) {
        // 읽기 전용
        alertReadonly(this);
        e.preventDefault();
    }).on("keydown", "input.number, input.money, input.double", function(e) {
        // 숫자 유효성
        if (!checkNumberFormat(this, e)) {
            e.preventDefault();
        }
    }).on("focus", "input.number, input.money, input.double", function() {
        // 숫자 입력 전처리
        preprocessNumberInput(this);
    }).on("blur", "input.number, input.money, input.double", function() {
        // 숫자 입력 후처리
        postprocessNumberInput(this);
    }).on("blur", "input.date, input.birthday", function() {
        // 날짜 입력 후처리
        postprocessDateInput(this);
    }).on("focus", "input.placeholder", function() {
        // Placeholder 세팅
        $(this).addClass("no_placeholder");
    }).on("blur", "input.placeholder", function(){
        // Placeholder 확인 후 해제
        checkPlaceholder(this);
    }).on("click", ".btn_confirm", function(e) {
        // 컨펌 후 동작
        if (!confirmByButton(this)) {
            e.preventDefault();
        }
    }).on("click", ".btn_delete", function(e) {
        // 삭제 컨펌
        if (!confirm("정말 삭제하시겠습니까?")) {
            e.preventDefault();
        }
    }).on("click", ".btn_incomplete", function(e) {
        // 준비중
        alert("준비중입니다.");
        e.preventDefault();
    }).on("click", "#all_checkbox", function() {
        // 전체 선택
        toggleAllCheckbox(this);
    }).on("click", "#all_checkbox2", function() {
        // 전체 선택
        toggleAllCheckboxBU(this);
    }).on("change", ".change_order", function() {
        // 정렬 순서 변경
        changeSortOrder(this);
    }).on("click", ".btn_change_period", function(e) {
        // 기간 세팅
        changeSearchPeriod(this);
        e.preventDefault();
    }).on("change", "#sch_year", function(e) {
        // 연도 선택
        choosePeriodYear();
    }).on("change", "#sch_month", function(e) {
        // 월 선택
        choosePeriodMonth();
    }).on("click", ".btn_open_popup", function(e) {
        // 링크를 팝업으로 열기
        openPopup(this);
        e.preventDefault();
    }).on("click", ".btn_ajax", function(e) {
        // 링크의 내용을 Ajax를 통하여 출력
        getContentsbyAjax(this);
        e.preventDefault();
    });
});
