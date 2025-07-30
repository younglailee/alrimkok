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
    if (f.mode.value == "insert") {
        if (!$("#footer_img").val()) {
            alert("이미지를 등록해주세요.");
            return false;
        }
    }

    return true;
}

/* 순서 변경 */
function changeOrder(direction, obj) {

    var obj_tbody = $("#carousel_tbody");
    var obj_tr = $("> tr", obj_tbody);
    var cnt_tr = obj_tr.length;
    var idx = obj_tr.index($(obj).parent("td").parent("tr"));

    if (direction == "up" && idx == 0) {
        alert("더이상 순서를 올릴 수 없습니다.");
        return false;
    } else if (direction == "down" && idx == cnt_tr - 1) {
        alert("더이상 순서를 내릴 수 없습니다.");
        return false;
    }

    var ft_id = $(obj).parent("td").parent("tr").find("input[name='list_uid[]']").val();

    var f = document.order_form;
    f.direction.value = direction;
    f.ft_id.value = ft_id;

    submitByAjax(f, function(result) {
        if (direction == "up") {
            // 위로
            obj_tr.eq(idx).after(obj_tr.eq(idx-1));
        } else if (direction == "down") {
            // 아래로
            obj_tr.eq(idx).before(obj_tr.eq(idx * 1 + 1));
        }
    });
}

