/* 검색폼 유효성 검사 */
function submitSearchForm(f) {
    return validateForm(f);
}

/* 삭제폼 유효성 검사 */
function submitListForm(f) {
    if ($("input.list_checkbox", f).filter(":checked").length === 0) {
        alert("삭제할 자료를 선택해주세요.");
        return false;
    }
    return confirm("선택한 자료를 삭제하시겠습니까?");
}

/* 등록폼 유효성 검사 */
function submitWriteForm(f) {
    // 기본 유효성
    return validateForm(f);
}
