<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\NoticeAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';

/* init Class */
$oBoard = new NoticeAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');
$recipient = $_GET['recipients'];
$partner = $_GET['partner'];
$recipient = urldecode($recipient);
/* check auth */
if (!$oBoard->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}

/* data */
$uid = $oBoard->get('uid');
$data = $oBoard->selectDetail($uid);
if ($recipient) {
    $email_data = $oBoard->emailList($recipient);
    $email_list = implode(',', $email_data);
}
if ($partner) {
    $partner_data = $oBoard->emailList('', $partner);
    $p_email_list = implode(',', $partner_data);
}
$email_count = count($email_data);
$p_email_count = count($partner_data);
/* search condition */
$query_string = $oBoard->get('query_string');

//print_r($email_data);
//print_r($partner_data);
//print_r($cp_name);

/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_bgn = $oBoard->get('flag_use_bgn');
$flag_use_end = $oBoard->get('flag_use_end');
$flag_use_state = $oBoard->get('flag_use_state');
$flag_use_reg = $oBoard->get('flag_use_reg');

/* code */
$bd_is_notice_arr = $oBoard->get('bd_is_notice_arr');
$bd_is_display_arr = $oBoard->get('bd_is_display_arr');
if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}
if ($flag_use_state) {
    $bd_state_arr = $oBoard->get('bd_state_arr');
}
$view_partner = $oBoard->get('view_partner');
$view_tutor = $oBoard->get('view_tutor');
$view_partner = $oBoard->get('view_partner');
/* file */
if ($bd_code == 'bidding') {
    $oBoard->set('max_file', 5);
}
$max_file = $oBoard->get('max_file');
$file_list = $data['file_list'];
$doc_title = '기업 홍보메일 전송';

?>
<script type="text/javascript" src="<?= _EDITOR_URI_ ?>/js/HuskyEZCreator.js"></script>
<script type="text/javascript">
    //<![CDATA[
    $(function () {
        <?php if($bd_code != 'article') { ?>
        // 에디터 초기화
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "bd_content",
            sSkinURI: "<?=_EDITOR_URI_?>/SmartEditor2Skin.html",
            htParams: {
                bUseToolbar: true,
                bUseVerticalResizer: false,
                bUseModeChanger: true,
                nMinWidth: editor_min_width
            },
            fCreator: "createSEditor2",

        });
        <?php } ?>
    });
    var editor_min_width = 800;
    var oEditors = [];
    var max_file = "<?=$max_file?>";

    //document.write(decodeURIComponent("<?= $_GET['recipients']?>"))
    //]]>
    window.onload = function() {
        var input = document.getElementById("bd_subject");
        input.value = "(사업홍보)[알파에듀] ";  // 기본값 설정
        input.focus();  // 입력창에 포커스 맞춤
        input.setSelectionRange(input.value.length, input.value.length);  // 커서를 기본값 뒤로 이동
    };

    function disableSubmitButton(form) {
        var submitBtn = document.getElementById('send_button');

        // 버튼을 비활성화하고 텍스트 변경
        submitBtn.disabled = true;
        submitBtn.textContent = "전송 중.."; // 버튼의 텍스트 변경

        // 기존 onsubmit 함수를 호출하여 정상적으로 폼이 제출되도록 함
        return submitWriteFormMail(form); // 기존 onsubmit 함수를 그대로 호출
    }

</script>
<style>
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 150%; /* 툴팁이 체크박스 위에 나타나도록 설정 */
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .background {
        position: relative;
        display: inline-block;
    }

    .background .background-text {
        visibility: hidden;
        width: 200px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 150%; /* 툴팁이 체크박스 위에 나타나도록 설정 */
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .background:hover .background-text {
        visibility: visible;
        opacity: 1;
    }

</style>
<div id="<?= $module ?>">
    <div class="write">
        <form name="write_form" id="emailForm" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return disableSubmitButton(this)">
            <input type="hidden" name="mode" value="send_mail"/>
            <fieldset>
                <legend>기본정보</legend>
                <table class="write_table" border="1">
                    <caption>기본정보 입력 테이블</caption>
                    <colgroup>
                        <col width="160"/>
                        <col width="*"/>
                        <col width="160"/>
                        <col width="*"/>
                    </colgroup>
                        <?php
                        if ($partner) {
                             echo Html::makeInputTextInTable('해당기업('.$p_email_count . '기업)', 'email_list', $p_email_list, 'required', 100, 9999, 4);
                        } else {
                             echo Html::makeInputTextInTable('해당기업('.$email_count . '기업)', 'email_list', $email_list, 'required', 100, 9999, 4);
                        } ?>
                             <?= Html::makeInputTextInTable('제목', 'bd_subject', $data['bd_subject'], 'required', 100, 9999, 4) ?>
                        <div class="tooltip">
                            <input type="checkbox" id="scales" name="scales" checked /><label for="scales" style="font-size:14px;">수신거부메세지 포함</label>
                            <span class="tooltip-text">수신거부 메세지를 포함시키려면 체크해주세요</span>
                        </div>
                        &nbsp;&nbsp;&nbsp;
                        <div class="background">
                            <input type="checkbox" id="bg_scales" name="bg_scales" checked /><label for="bg_scales" style="font-size:14px;">배경프레임 포함</label>
                            <span class="background-text">배경프레임에 내용만 추가하려면 체크해주세요</span>
                        </div>
                        <?= Html::makeTextareaInTable('내용', 'bd_content', nl2br($data['bd_content']), 'required', 15, 120, 4);
                        for ($i = 0; $i < $max_file; $i++) {
                            ?>
                            <tr class="file">
                                <th><label for="atch_file<?= $i + 1 ?>">첨부파일 #<?= $i + 1 ?></label></th>
                                <td colspan="4">
                                    <input type="hidden" name="fi_type[]" value="default"/>
                                    <input type="file" name="atch_file[]" id="atch_file<?= $i + 1 ?>" value=""
                                           class="file"
                                           size="500" title="첨부파일 <?= $i + 1 ?>"/>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </fieldset>
            <div class="button">
                <button type="submit" id="send_button" class="sButton primary">전송</button>
                <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
            </div>
        </form>
    </div>
</div>
