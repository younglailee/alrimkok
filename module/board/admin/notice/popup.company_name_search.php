<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\UserAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* init Class */

$oUser = new UserAdmin();
$oUser->init();

$list = $oUser->selectCompanyList();

$sch_keyword = $_GET['cs_name'];

?>
<script type="text/javascript">
    <![CDATA[
    $("#sch_btn").click(function () {
        var sch_text = $("#sch_text").val();

        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'sch_company',
                sch_text: sch_text
            },
            success: function (rs) {
                var list = rs.data;

                var html = "";
                for (var i = 0; i < list.length; i++) {
                    html += "<tr>";
                    html += "<td class='checkbox'>";
                    html += "<input type='checkbox' name='list_uid[]' value='" + list[i].mb_id + "' data-name='" + list[i].mb_name + "' class='list_checkbox' title='선택/해제'/>";
                    html += "</td>";
                    html += "<td>";
                    html += list[i].mb_id;
                    html += "</td>";
                    html += "<td>";
                    html += list[i].mb_name;
                    html += "</td>";
                    html += "</tr>";
                }

                $("#list_tbody").html(html);
            }
        });
    });

    $("#submit_btn").click(function () {
        var mb_id = "";
        var mb_name = "";

        var target = $(".list_checkbox");


        for (var i = 0; i < target.length; i++) {
            if (target[i].checked == true) {
                mb_id += target[i].value + ",";

                var target_mb_name = target[i].getAttribute('data-name');

                mb_name += target_mb_name + ",";
            }
        }

        $("#bd_etc8").val(mb_id);
        $("#bd_etc7").val(mb_name);

        closeLayerPopup();
    })
    ]]>
</script>
<div class="search">
    <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
            <legend><i class="xi-search"></i> 검색조건</legend>
            <input type="hidden" name="sch_order" value="<?= $sch_order ?>"/>
            <input type="hidden" name="sch_cnt_rows" value="<?= $sch_cnt_rows ?>"/>

            <table class="search_table" border="1">
                <caption>검색조건</caption>
                <colgroup>
                    <col width="90"/>
                    <col width="*"/>
                </colgroup>
                <tbody>
                <tr>
                    <th><label for="sch_text">이름</label></th>
                    <td>
                        <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text" size="30"
                               maxlength="30" title="검색어"/>
                    </td>
                </tr>
                <?php /*
        <tr>
            <th>권한</th>
            <td>
                <?=Html::makeRadio('sch_mb_level', $mb_level_arr, $sch_mb_level, 1)?>
            </td>
        </tr>
        <tr>
            <th>로그인허용</th>
            <td>
                <?=Html::makeCheckbox('sch_mb_no_login', $mb_no_login_arr, $sch_mb_no_login, 1)?>
            </td>
        </tr> */ ?>
                </tbody>
            </table>
        </fieldset>
        <div class="button">
            <button type="button" class="sButton info" id="sch_btn" title="검색">검 색</button>
        </div>
    </form>
</div>
<table class="list_table border odd">
    <colgroup>
        <col style="width: 30px">
        <col/>
        <col/>
    </colgroup>
    <thead>
    <tr>
        <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
        <th>아이디</th>
        <th>이름</th>
    </tr>
    </thead>
    <tbody id="list_tbody">
    <?php
    for ($i = 0; $i < count($list); $i++) {
        ?>
        <tr>
            <td class="checkbox">
                <input type="checkbox" name="list_uid[]" value="<?= $list[$i]['mb_id'] ?>"
                       data-name="<?= $list[$i]['mb_name'] ?>"
                       class="list_checkbox" title="선택/해제"/>
            </td>
            <td>
                <?= $list[$i]['mb_id'] ?>
            </td>
            <td>
                <?= $list[$i]['mb_name'] ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<div class="button" style="text-align: center; margin-top: 20px">
    <button type="button" id="submit_btn" class="sButton primary">확인</button>
    <button type="button" onclick="closeLayerPopup()" class="sButton">취소</button>
</div>
