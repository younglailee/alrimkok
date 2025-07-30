/* 검색폼 유효성 검사 */
function submitSearchForm(f) {
    if (!validateForm(f)) {
        return false;
    }
    return true;
}

/* 삭제폼 유효성 검사 */
function submitListForm(f) {

    if ($("input.list_checkbox", f).filter(":checked").length == 0) {
        alert("삭제할 자료를 선택해주세요.");
        return false;
    }

    return confirm("선택한 자료를 삭제하시겠습니까?");
}

/* 등록폼 유효성 검사 */
function submitWriteForm(f) {
    // 기본 유효성
    if (!validateForm(f)) {
        return false;
    }
    // 첨부파일
    if (f.mode.value == "update") {
        // 기존 파일 삭제
        if ($("#profile_img").val() && $("#del_profile_img").length > 0) {
            $("#del_profile_img").attr("checked", true);
        }

        for (var i = 1; i < 3; i++) {
            try {
                if ($("#atch_file" + i).val() && $("#del_file_" + i).length > 0) {
                    $("#del_file_" + i).attr("checked", true);
                }
            } catch(e) {}
        }
    }
    return true;
}