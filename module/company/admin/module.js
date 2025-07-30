var check_cp_num_result = false;
/* 검색폼 유효성 검사 */
function submitSearchForm(f) {
    if (!validateForm(f)) {
        return false;
    }
    return true;
}

function submitListForm(f) {

    if ($("input.list_checkbox", f).filter(":checked").length == 0) {
        alert("삭제할 자료를 선택해주세요.");
        return false;
    }

    return confirm("삭제하시겠습니까?");
}

/* 등록폼 유효성 검사 */
function submitWriteForm(f) {
    // 기본 유효성
    if (!validateForm(f)) {
        return false;
    }
    var mode = $("#mode").val();
    //console.log(mode);

    if (mode === 'update') {
        check_cp_num_result = true;
    }
    if (check_cp_num_result === false) {
        alert('사업자등록번호 중복확인을 해주세요.')
        return false;
    }
    // 이메일 형식 검사
    let obj_email = $(".email", f);
    let email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
    for (let i = 0; i < obj_email.length; i++) {
        if (!email_regex.test(obj_email.val())) {
            return false;
        } else {
            return true;
        }
    }
    // 첨부파일
    if (mode === "update") {
        // 기존 파일 삭제
        if ($("#profile_img").val() && $("#del_profile_img").length > 0) {
            $("#del_profile_img").attr("checked", true);
        }

        for (var i = 1; i < 3; i++) {
            try {
                if ($("#atch_file" + i).val() && $("#del_file_" + i).length > 0) {
                    $("#del_file_" + i).attr("checked", true);
                }
            } catch (e) {
            }
        }
    }
    return true;
}

/* 등록폼 유효성 검사 */
function submitWriteFormMail(f) {

    // editor
    oEditors.getById["bd_content"].exec("UPDATE_CONTENTS_FIELD", []);
    // 기본 유효성
    if (!validateForm(f)) {
        return false;
    }

    var mode = $("#mode").val();

    console.log(mode);

    if (mode === 'update') {
        check_cp_num_result = true;
    }

    // 첨부파일
    if (mode === "update") {
        // 기존 파일 삭제
        if ($("#profile_img").val() && $("#del_profile_img").length > 0) {
            $("#del_profile_img").attr("checked", true);
        }

        for (var i = 1; i < 3; i++) {
            try {
                if ($("#atch_file" + i).val() && $("#del_file_" + i).length > 0) {
                    $("#del_file_" + i).attr("checked", true);
                }
            } catch (e) {
            }
        }
    }
    return true;
}

function check_cp_num() {
    let cp_number = $("#cp_number").val();
    // 사업자등록번호 하이픈 제거
    cp_number = cp_number.replace(/-/g, '');

    if (!cp_number) {
        alert('사업자등록번호를 입력해주세요');
        return;
    }
    $.ajax({
        url: "process.html",
        type: "GET",
        dataType: "json",
        data: {
            flag_json: '1',
            mode: 'search_cp_num',
            cp_number: cp_number
        },
        success: function(result) {

            if (!result.data) {
                alert("사용가능한 사업자등록번호입니다.");
                check_cp_num_result = true;
            } else {
                alert("이미 사용중인 사업자등록번호입니다.");
                check_cp_num_result = false;
            }
        }
    });
}
