/* 검색폼 유효성 검사 */
function submitSearchForm(f) {
    if (!validateForm(f)) {
        return false;
    }

    return true;
}
