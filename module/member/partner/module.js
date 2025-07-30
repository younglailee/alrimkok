/* 로그인 폼 검증 */
function submitLoginForm(f) {
    return validateForm(f);
}

/* 패스워드 변경폼 유효성 검사 */
function submitUpdatePasswordForm(f) {

    if(!validateForm(f)) {
        return false;
    }

    if(f.mb_pass.value == f.new_pass.value) {
        alert("신규 비밀번호는 현재 비밀번호와 다르게 설정해야 합니다.");
        f.new_pass.focus;
        return false;
    }

    if(f.new_pass.value != f.new_pass2.value) {
        alert("비밀번호가 일치하지 않습니다.");
        f.new_pass2.focus();
        return false;
    }

    return true;
}