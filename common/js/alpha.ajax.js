/* 링크를 Ajax를 이용하여 출력 */
function getContentsbyAjax(obj) {
    console.log($(obj).attr("href"));
    $.ajax({
        url: $(obj).attr("href"),
        type: "get",
        dataType: "json",
        data: "flag_json=1",
        cache: false,
        async: false,
        success: function(result) {
            console.log(result);
            postAjaxAction(result, $(obj).attr("target"), $(obj).attr("title"), $(obj).attr("class"), null);
        },
        error: function() {
            alert("서버와의 통신 중 장애가 발생하였습니다.");
        }
    });
}

/* 폼 전송을 Ajax를 통해서 처리 */
function submitByAjax(f, callback_func) {
    var f_target = f.target;

    if ($(f).attr("enctype") === "multipart/form-data") {
        // 첨부파일을 전송해야 할 경우, iframe 을 통해서 전송
        $ifm = $("<iframe />");
        $ifm.css({
            width: "100px",
            height: "100px"
        }).attr({
            id: "ajax_iframe",
            name: "ajax_iframe"
        }).appendTo($(f));

        f.target = "ajax_iframe";
        setTimeout(function() {
            $ifm.on("load", function() {
                var result = JSON.parse($ifm.contents().text());
                postAjaxAction(result, f_target, f.title, $(f).attr("class"), callback_func);
                $ifm.remove();
            });
            f.submit();
            f.target = f_target;
        }, 100);
    } else {
        $.ajax({
            url: f.action,
            type: f.method,
            dataType: "json",
            data: $(f).serialize(),
            cache: false,
            async: false,
            success: function(result) {
                postAjaxAction(result, f_target, f.title, $(f).attr("class"), callback_func);
            },
            error: function(request, status, error) {
                console.log(request);
                console.log(status);
                console.log(error);
                $.post("/ajax_error.php", {'code': request.status, 'message': request.responseText, 'error': error});
                alert("서버와의 통신 중 장애가 발생하였습니다.");
            },
            complete: function() {

            }
        });
    }
}

/* Ajax 통신 이후 처리 */
function postAjaxAction(result, target, title, classes, callback_func) {
    if (result.code == "failure") {
        alert(result.msg);
    } else if (result.code === "success") {
        if (target) {
            if (target === "layer_popup") {
                target = "layer_content";
                var size_arr = getLayerPopupSize(classes);
                resizeLayerPopup(size_arr[0], size_arr[1], title);
                openLayerPopup();
            } else if (target.substring(0, 10) === "layer_page") {
                closeLayerPopup();
                openLayerPage(target.replace("layer_page", ""));
            }
            //console.log(target);
            //console.log(result);
            insertContent($("#" + target), result.content);
        }

        if (typeof callback_func == "function") {
            callback_func(result);
        }
    }
}

/* 레이어 팝업 열기 */
function openLayerPopup() {
    $("#layer_back, #layer_popup").addClass("open");
}

/* 레이어 팝업 닫기 */
function closeLayerPopup() {
    $("#layer_back, #layer_popup").removeClass("open");
    $("#layer_content").html("");
}

/* 레이어 페이지 열기 */
function openLayerPage(layer_no) {
    $("#layer_page" + layer_no).addClass("open").find("div.container").scrollTop(0);
}

/* 레이어 페이지 닫기 */
function closeLayerPage(layer_no) {
    $("#layer_page" + layer_no).removeClass("open");
}

/* 레이어 팝업 사이즈 반환 */
function getLayerPopupSize(class_name) {
    var layer_size = "size_600x600";
    var class_arr = class_name.split(" ");
    var size_fmt = /^size_\d{3,4}x\d{3,4}$/;
    for (var i = 0; i < class_arr.length; i++) {
        if (size_fmt.test(class_arr[i])) {
            layer_size = class_arr[i];
            break;
        }
    }

    return layer_size.replace("size_", "").split("x");
}

/* 레이어 팝업 리사이징 */
function resizeLayerPopup(layer_width, layer_height, title) {
    var layer_margin_top = (layer_height / 2) * (-1);
    var layer_margin_left = (layer_width / 2) * (-1);
    var layer_content_height = layer_height - 90;

    var obj_layer = $("#layer_popup");
    obj_layer.css({
        width: layer_width + "px",
        height: layer_height + "px",
        marginTop: layer_margin_top + "px",
        marginLeft: layer_margin_left + "px"
    });
    $("#layer_content").css("height", layer_content_height + "px");

    // layer title
    if (title) {
        obj_layer.find("h1").text(title);
    }
}
