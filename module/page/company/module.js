// 출근
function attendOffice(us_id, us_name) {
    if (confirm("출근 체크하시겠습니까?")) {
        var f = document.commute_form;
        f.mode.value = "in";
        f.us_id.value = us_id;
        f.us_name.value = us_name;

        submitByAjax(f, function(result) {
            if (result.code === "success") {
                alert("정상적으로 출근 처리되었습니다.");
            }
        });
    }
}

// 퇴근
function getOffWork(us_id, us_name) {
    if (confirm("퇴근 체크하시겠습니까?")) {
        var f = document.commute_form;
        f.mode.value = "out";
        f.us_id.value = us_id;
        f.us_name.value = us_name;

        submitByAjax(f, function(result) {
            if (result.code === "success") {
                alert("정상적으로 퇴근 처리되었습니다.");
            }
        });
    }
}