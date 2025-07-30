<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\NoticeAdmin;
use sFramework\Html;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';

/* init Class */
$oBoard = new NoticeAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');

/* check auth */
if (!$oBoard->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}

/* data */
$uid = $oBoard->get('uid');
$data = $oBoard->selectDetail($uid);

/* search condition */
$query_string = $oBoard->get('query_string');

/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_bgn = $oBoard->get('flag_use_bgn');
$flag_use_end = $oBoard->get('flag_use_end');
$flag_use_state = $oBoard->get('flag_use_state');

/* code */
$bd_is_notice_arr = $oBoard->get('bd_is_notice_arr');
if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}
if ($flag_use_state) {
    $bd_state_arr = $oBoard->get('bd_state_arr');
}

/* file */
if ($bd_code == 'bidding') {
    $oBoard->set('max_file', 5);
}
$max_file = $oBoard->get('max_file');
$file_list = $data['file_list'];

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $txt_mode = '등록';
    global $member;
    $data = array(
        'bd_writer_name' => $member['mb_name'],
        'bd_reg_date' => _NOW_DATE_,
        'bd_state' => 'P'
    );
    if ($flag_use_category) {
        $data['bd_category'] = $bd_category_arr[0];
    }
} else {
    $mode = 'update';
    $txt_mode = '수정';
}
?>
<script type="text/javascript" src="<?= _EDITOR_URI_ ?>/js/HuskyEZCreator.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
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
        fCreator: "createSEditor2"
    });
    <? } ?>
});
var editor_min_width = 800;
var oEditors = [];
var max_file = "<?=$max_file?>";
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
        <h4><?= $txt_mode ?>하기</h4>
        <table class="write_table" border="1">
        <caption>기본정보 입력 테이블</caption>
        <colgroup>
        <col width="140"/>
        <col width="*"/>
        <col width="140"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <?= Html::makeInputTextInTable('제목', 'bd_subject', $data['bd_subject'], 'required', 100, 50, 3) ?>
        <tr>
            <th class="required"><label for="bd_writer_name">작성자</label></th>
            <td>
                <?= Html::makeInputText('bd_writer_name', '작성자', $data['bd_writer_name'], 'required', 20, 10) ?>
            </td>
            <th class="required"><label for="reg_date">작성일</label></th>
            <td>
                <?= Html::makeInputText('bd_reg_date', '작성일', $data['bd_reg_date'], 'required date', 10, 10) ?>
            </td>
        </tr>
        <?php
        if ($flag_use_category) {
            ?>
            <tr>
                <th class="required"><label for="bd_category">분류</label></th>
                <td colspan="3">
                    <?= Html::makeRadio('bd_category', $bd_category_arr, $data['bd_category']) ?>
                </td>
            </tr>
            <?php
        }
        if ($flag_use_bgn || !$flag_use_state) {
            ?>
            <tr>
                <?php
                if ($flag_use_bgn) {
                    ?>
                    <th class="required">진행기간</th>
                    <td<?= (!$flag_use_state) ? ' colspan="3"' : '' ?>>
                        <?= Html::makeInputText('bd_bgn_date', '진행시작일', $data['bd_bgn_date'], 'required date', 10, 10) ?>
                        <?= ($flag_use_end) ? ' ~ ' . Html::makeInputText('bd_end_date', '진행종료일', $data['bd_end_date'], 'required date', 10, 10) : '' ?>
                    </td>
                    <?php
                }
                if ($flag_use_state) {
                    ?>
                    <th class="required">상태</th>
                    <td<?= (!$flag_use_bgn) ? ' colspan="3"' : '' ?>>
                        <?= Html::makeRadio('bd_state', $bd_state_arr, $data['bd_state'], 1) ?>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
        if ($bd_code == 'article') {
            ?>
            <?= Html::makeInputTextInTable('링크주소', 'bd_content', $data['bd_content'], 'required', 100, 255, 3) ?>
            <?= Html::makeInputTextInTable('언론사', 'bd_etc1', $data['bd_etc1'], 'required', 100, 50, 3) ?>
            <?php
        } else {
            ?>
            <?= Html::makeTextareaInTable('내용', 'bd_content', $data['bd_content'], 'required', 10, 120, 3) ?>
            <?
        }
        if ($bd_code != 'article') {
            for ($i = 0; $i < $max_file; $i++) {
                ?>
                <tr class="file">
                    <th><label for="atch_file<?= $i + 1 ?>">첨부파일 #<?= $i + 1 ?></label></th>
                    <td colspan="3">
                        <input type="hidden" name="fi_type[]" value="default"/>
                        <input type="file" name="atch_file[]" id="atch_file<?= $i + 1 ?>" value="" class="file"
                               size="100" title="첨부파일 <?= $i + 1 ?>"/>
                        <?php
                        if ($file_list[$i]['fi_id']) {
                            ?>
                            <p>
                                <input type="checkbox" name="del_file[]" id="del_file_<?= $i + 1 ?>"
                                       value="<?= $file_list[$i]['fi_id'] ?>" class="checkbox" title="기존파일 삭제"/>
                                <label for="del_file_<?= $i + 1 ?>">기존파일삭제</label>
                                <span>|</span>
                                <a href="./download.html?fi_id=<?= $file_list[$i]['fi_id'] ?>" class="btn_download"
                                   target="_blank" title="새창 다운로드">
                                    <strong><?= $file_list[$i]['fi_name'] ?></strong>
                                    <span>(<?= $file_list[$i]['bt_fi_size'] ?>)</span>
                                </a>
                            </p>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="./list.html?page=<?= $page ?><?= $query_string ?>" class="sButton active" title="목록">목록</a>
        </div>
        </form>
    </div>
</div>
