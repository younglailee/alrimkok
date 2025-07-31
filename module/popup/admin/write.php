<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */
//error_reporting(E_ALL & ~E_WARNING);ini_set('display_errors', '1');
use sFramework\Html;
use sFramework\PopupAdmin;
use sFramework\Time;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
global $layout, $module;
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oPopup = new PopupAdmin();
$oPopup->init();
$pk = $oPopup->get('pk');

/* check auth */
if (!$oPopup->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}

/* data */
$uid = $oPopup->get('uid');
$data = $oPopup->selectDetail($uid);

/* search condition */
$query_string = $oPopup->get('query_string');

/* code */
$pu_is_display_arr = $oPopup->get('pu_is_display_arr');

/* file */
$max_file = $oPopup->get('max_file');
$file_list = $data['file_list'][0];
$profile_img = $data['profile_img'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'mb_level' => 9,
        'mb_no_login' => 'N'
    );
} else {
    $mode = 'update';
}
$hour_arr = Time::makeHourArray('00:00', '24:00');
$img_size = $oPopup->get('img_size');
?>
<script type="text/javascript" src="<?= _EDITOR_URI_ ?>/js/HuskyEZCreator.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    /* 아이디 검사 */
    $("#mb_id").on("blur", function() {
        validateMemberId(false);
        //validateMemberId(false);
    });

    /* 비밀번호 검사 */
    $("#mb_pw").on("blur", function() {
        validateMemberPassword(false);
    });

    // 에디터 초기화
    nhn.husky.EZCreator.createInIFrame({
        oAppRef: oEditors,
        elPlaceHolder: "pu_content",
        sSkinURI: "<?=_EDITOR_URI_?>/SmartEditor2Skin.html",
        htParams: {
            bUseToolbar: true,
            bUseVerticalResizer: false,
            bUseModeChanger: true,
            nMinWidth: editor_min_width
        },
        fCreator: "createSEditor2"
    });
});

var editor_min_width = 800;
var oEditors = [];
//]]>
</script>

<div id="<?= $module ?>">
    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>
        <input type="hidden" name="<?= $pk ?>" value="<?= $data[$pk] ?>"/>
        <fieldset>
        <legend>기본정보</legend>
        <h4>기본사항</h4>
        <table class="write_table" border="1">
        <caption>기본정보 입력 테이블</caption>
        <colgroup>
        <col width="140"/>
        <col width="*"/>
        <col width="140"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th class="required">사용여부</th>
            <td colspan="3">
                <?= Html::makeRadio('pu_is_display', $pu_is_display_arr, $data['pu_is_display'], 1) ?>
                <?= Html::makeCheckbox('flag_live', array('Y' => '로그인 시 출력'), $data['pu_is_login'], 1) ?>
            </td>
        </tr>
        <tr>
            <th class="required">창위치</th>
            <td>
                좌측 <?= Html::makeInputText('pu_position_left', '좌측', $data['pu_position_left'], '', 10, 10) ?>%
                상단 <?= Html::makeInputText('pu_position_top', '상단', $data['pu_position_top'], '', 10, 10) ?>%
            </td>
            <th>창크기</th>
            <td>
                가로 <?= Html::makeInputText('pu_size_width', '가로', $data['pu_size_width'], '', 10, 10) ?>px
                세로 <?= Html::makeInputText('pu_size_height', '세로', $data['pu_size_height'], '', 10, 10) ?>px
            </td>
        </tr>
        <tr>
            <th class="required">출력기간</th>
            <td colspan="3">
                <?= Html::makeInputText('pu_bgn_date', '출력시작일', $data['pu_bgn_date'], 'required date', 10, 10) ?>
                <select name="pu_bgn_time" class="select" title="출력시작일">
                <?= Html::makeSelectOptions($hour_arr, $data['pu_bgn_time'], 1) ?>
                </select>
                ~
                <?= Html::makeInputText('pu_end_date', '출력종료일', $data['pu_end_date'], 'required date', 10, 10) ?>
                <select name="pu_end_time" class="select" title="출력종료일">
                <?= Html::makeSelectOptions($hour_arr, $data['pu_end_time'], 1) ?>
                </select>
            </td>
        </tr>
        <tr class="file">
            <th><label for="carousel_img"> 이미지</label></th>
            <td>
                <input type="hidden" name="fi_type[]" value="popup"/>
                <input type="file" name="atch_file[]" id="carousel_img" value="" class="file" size="80" title="이미지"/>
                <?php if ($file_list['fi_id']) { ?>
                    <p>
                        <a href="./download.html?fi_id=<?= $file_list['fi_id'] ?>" target="_blank"
                           title="새창 다운로드"><img src="<?= $file_list['thumb_uri'] ?>" alt="프로필이미지 썸네일"/></a>
                        <span>|</span>
                        <input type="checkbox" name="del_file[]" id="del_carousel_img"
                               value="<?= $file_list['fi_id'] ?>"
                               class="checkbox" title="기존파일삭제"/><label for="del_carousel_img">기존파일삭제</label>
                        <span>|</span>
                        <a href="./download.html?fi_id=<?= $file_list['fi_id'] ?>" class="btn_download"
                           target="_blank" title="새창 다운로드">
                            <strong><?= $file_list['fi_name'] ?></strong>
                            <span>(<?= $file_list['bt_fi_size'] ?>)</span>
                        </a>
                    </p>
                <?php } ?>
                <p class="comment">
                    이미지 권장사이즈(px) : <?= $img_size ?>
                </p>
            </td>
            <th>대체정보</th>
            <td>
                <?= Html::makeInputText('pu_alt', '대체정보', $data['pu_alt'], '', 50, 40) ?>
                <p class="comment">
                    대체정보는 이미지 출력 불가 시 출력됨.
                </p>
            </td>
        </tr>
        <?= Html::makeInputTextInTable('제목', 'pu_subject', $data['pu_subject'], 'required', 110, 90, 3) ?>
        <?= Html::makeTextareaInTable('내용', 'pu_content', $data['pu_content'], '', 6, 130, 3) ?>
        <?= Html::makeInputTextInTable('링크주소', 'pu_uri', $data['pu_uri'], '', 110, 255, 3) ?>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
            <?php if ($mode == 'update') { ?>
                <a href="./process.html?mode=delete&<?= $pk ?>=<?= $uid ?>&page=<?= $page ?><?= $query_string ?>"
                   class="sButton warning btn_delete" title="삭제">삭제</a>
            <?php } ?>
        </div>

        </form>
    </div>
</div>

<form name="check_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="mb_id" value=""/>
<input type="hidden" name="mb_pw" value=""/>
</form>
