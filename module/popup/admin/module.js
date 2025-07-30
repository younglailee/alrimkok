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

    // editor
    oEditors.getById["pu_content"].exec("UPDATE_CONTENTS_FIELD", []);

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

/* 아이디 검사 */
function validateMemberId(flag_alert) {
    var write_form = document.write_form;
    var mb_id = write_form.mb_id.value;

    var check_form = document.check_form;
    check_form.mode.value = "validate_member_id";
    check_form.mb_id.value = mb_id;
    check_form.mb_pw.value = "";

    var flag = null;
    submitByAjax(check_form, function(result) {
        write_form.flag_mb_id.value = result.flag;
        $("#state_mb_id").text(result.msg);
        flag = result.flag;
        if (!flag && flag_alert) {
            alert(result.msg);
            write_form.mb_id.focus();
        }
    });

    return flag;
}

/* 패스워드 검사 */
function validateMemberPassword(flag_alert) {
    var write_form = document.write_form;
    var mb_pw = write_form.mb_pw.value;

    var check_form = document.check_form;
    check_form.mode.value = "validate_member_password";
    check_form.mb_id.value = "";
    check_form.mb_pw.value = mb_pw;

    var flag = null;
    submitByAjax(check_form, function(result) {
        write_form.flag_mb_pw.value = result.flag;
        $("#state_mb_pw").text(result.msg);
        flag = result.flag;
        if (!flag && flag_alert) {
            alert(result.msg);
            write_form.mb_pw.focus();
        }
    });

    return flag;
}