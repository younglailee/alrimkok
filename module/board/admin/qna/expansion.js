/* 검색폼 유효성 검사 */
function submitSearchForm(f) {
    if (!validateForm(f)) {
        return false;
    }

    return true;
}

/* 등록폼 유효성 검사 */
function submitWriteForm(f) {

    // editor
    oEditors.getById["bd_content"].exec("UPDATE_CONTENTS_FIELD", []);

    // 기본 유효성
    if (!validateForm(f)) {
        return false;
    }

    // 첨부파일
    if (f.mode.value == "update") {
        // 기존 파일 삭제
        for (var i = 0; i < max_file; i++) {
            try {
                if ($("#atch_file" + i).val() && $("#del_file_" + i).length > 0) {
                    $("#del_file_" + i).attr("checked", true);
                }
            } catch(e) {}
        }
    }

    return true;
}

/* 답변폼 유효성 검사 */
function submitReplyForm(f) {
    // 기본 유효성
    if (!validateForm(f)) {
        return false;
    }

    return true;
}