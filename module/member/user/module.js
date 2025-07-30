/* 로그인 폼 검증 */
function submitLoginForm(f) {
    return validateForm(f);
}

/* 비주얼 더보기 열기 */
function openMoreVisual(obj) {
    $(obj).parent("div").parent("div").find("> div.more").addClass("open");
}

/* 비주얼 더보기 닫기 */
function closeMoreVisual(obj) {
    $(obj).parent("div").parent("div").removeClass("open");
}

/* 등록폼 유효성 검사 */
function submitWriteForm(f) {
    // 회원가입 버튼 클릭 시 버튼 비활성화 처리 yllee 210706
    var obj = $("#join_submit");
    obj.disabled = true;
    // 기본 유효성
    if (!validateForm(f)) {
        obj.disabled = false;
        return false;
    }
    // 패스워드 검사
    if (f.mb_pw.value && f.mb_pw.value != f.mb_pw2.value) {
        alert("비밀번호가 정확하지 않습니다.");
        f.mb_pw2.focus();
        obj.disabled = false;
        return false;
    }
    if (f.mode.value == "insert" || f.mb_pw.value) {
        // 패스워드 검사
        if (!validateMemberPassword(1)) {
            obj.disabled = false;
            return false;
        }
    }
    // 전화번호 유효성 검사
    let regex = /^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/;
    var tel_num = f.mb_hp.value;
    //console.log(tel_num);
    if (!regex.test(tel_num)) {
        alert("등록이 불가능한 전화번호입니다.");
        f.mb_hp.focus();
        obj.disabled = false;
        return false;
    }
    // 이메일 직접입력 검사
    if (f.mb_email2.value === 'manual') {
        if (f.managerEmailCustomDomain.value === "") {
            alert("이메일 도메인 입력이 되지 않습니다.");
            f.managerEmailCustomDomain.focus();
            obj.disabled = false;
            return false;
        }
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

// 가입정보 수정 폼 검사
function submitModifyForm(f) {
    if (!validateForm(f)) {
        return false;
    }
    // 전화번호 유효성 검사
    let regex = /^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/;
    var tel_num = f.mb_hp.value;
    if (!regex.test(tel_num)) {
        alert("등록이 불가능한 전화번호입니다.");
        f.mb_hp.focus();
        obj.disabled = false;
        return false;
    }
    // 주력분야 사업(it_area[]) 체크 여부 검사
    let checkedCount = $('input[name="it_area[]"]:checked', f).length;
    if (checkedCount === 0) {
        alert("주력분야 사업을 하나 이상 선택해야 합니다.");
        return false;
    }
    // 유형 체크 여부 검사
    checkedCount = $('input[name="it_type[]"]:checked', f).length;
    if (checkedCount === 0) {
        alert("유형을 하나 이상 선택해야 합니다.");
        return false;
    }
    // 주요 관심정보 체크 여부 검사
    checkedCount = $('input[name="it_info[]"]:checked', f).length;
    if (checkedCount === 0) {
        alert("주요 관심정보를 하나 이상 선택해야 합니다.");
        return false;
    }
    return true;
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
        var state = $("#state_mb_pw1");
        state.text(result.msg);
        flag = result.flag;
        if (flag) {
            state.addClass("on");
        } else {
            state.removeClass("on");
        }
        if (!flag && flag_alert) {
            alert(result.msg);
            write_form.mb_pw.focus();
        }
    });
    return flag;
}

/* 텍스트 필수값 검사 */
function validateReqTexts(obj) {
    var value = delComma(obj.val());

    if (!isNaN(value)) {
        value = value * 1;
    }
    if (!value || value == "" || value == "0") {
        return false;
    }

    return true;
}

/*파일 수정시 input파일 변화*/
function onchang_file(file_id, fi_id, val) {
    var inputfile = document.getElementById(file_id);
    var inputfilecomplete = document.getElementById(file_id + '_complete');
    var inputfilelabel = document.getElementById(file_id + '_label');
    var delete_input = document.getElementById(file_id + '_del');
    inputfile.name = 'atch_file[]';
    inputfile.style.width = "100%";
    inputfile.style.height = "23px";
    inputfile.style.position = "static";
    inputfile.style.margin = "0";
    inputfilecomplete.innerHTML = "";
    inputfilelabel.style.display = "none";
    delete_input.value = fi_id;
    var inputst = document.getElementById(val);
    inputst.value = "file_s";
}

/*파일 제출시 접수 및 보완으로 변환*/
function val_to_file_s(val) {
    var inputst = document.getElementById(val);
    inputst.value = "file_s";
}

function checkId() {
    let mb_id = $("#mb_id").val();

    if (!mb_id) {
        alert('아이디를 입력해주세요');
        return;
    }
    $.ajax({
        url: "process.html",
        type: "GET",
        dataType: "json",
        data: {
            flag_json: '1',
            mode: 'search_id',
            mb_id: mb_id
        },
        success: function(result) {
            console.log(result);
            if (!result.data) {
                alert("사용가능한 아이디입니다.");
                result_check = true;
            } else {
                alert("이미 사용중인 아이디입니다.");
            }
        }
    });
}

function maxLengthCheck(object) {
    if (object.value.length > object.maxLength) {
        //object.maxLength : 매게변수 오브젝트의 maxlength 속성 값입니다.
        object.value = object.value.slice(0, object.maxLength);
    }
}

$(function() {
    // 부계정관리 정보수정 팝업 열기
    $('#mp .t-pop').click(function() {
        let $row = $(this).closest('tr'); // 클릭한 셀의 행을 찾음
        let mb_id = $row.find('input[name="list_uid[]"]').val();
        let sub_mode = "";
        if (mb_id) {
            sub_mode = "modify";
        }
        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'sub_popup',
                sub_mode: sub_mode,
                mb_id: mb_id
            },
            success: function(result) {
                insertContent($("#sub_popup"), result.content);
            }
        })
        $('.pop-up.new').show();
    });
    // 부계정관리 신규등록 팝업 열기
    $('#mp .btn_add').click(function() {
        let sub_mode = "new";

        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'sub_popup',
                sub_mode: sub_mode
            },
            success: function(result) {
                insertContent($("#sub_popup"), result.content);
            }
        })
        $('.pop-up.new').show();
    });

});