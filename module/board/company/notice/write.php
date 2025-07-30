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
                    <?php
                    if ($bd_code != 'reference') {
                        ?>
                        <?= Html::makeInputTextInTable('제목', 'bd_subject', $data['bd_subject'], 'required', 100, 50, 3) ?>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <th class="required"><label for="bd_subject">제목</label></th>
                            <td colspan="3">
                                <?= Html::makeInputText('bd_subject', '제목', $data['bd_subject'], 'required', 50, 200) ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="required"><label for="bd_etc1">과정명</label></th>
                            <td colspan="3">
                                <input type="text" name="bd_etc1" id="cs_name" value="<?= $data['bd_etc1'] ?>"
                                       class="text required readonly" size="50" maxlength="10" title="과정명"
                                       readonly="readonly"/>
                                <input type="hidden" name="bd_etc2" id="cs_code" value="<?= $data['bd_etc2'] ?>"/>
                                <a href="./popup.search_course.html" class="btn_ajax sButton small" target="layer_popup"
                                   title="과정 검색">검색</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th class="required"><label for="bd_writer_name">작성자</label></th>
                        <td>
                            <?= Html::makeInputText('bd_writer_name', '작성자', $data['bd_writer_name'], 'required', 20, 10) ?>
                        </td>
                        <?php
                        if ($flag_use_reg) {
                            ?>
                            <th class="required"><label for="reg_date">작성일</label></th>
                            <td>
                                <?= Html::makeInputText('bd_reg_date', '작성일', $data['bd_reg_date'], 'required date', 10, 10) ?>
                            </td>
                            <?php
                        } else {
                            echo '<th></th><td></td>';
                        }
                        ?>
                    </tr>
                    <?php
                    if ($bd_code == 'notice' || $bd_code == 'safe' || $bd_code == 'snotice') {
                        ?>
                        <tr>
                            <th><label for="bd_is_notice">공지</label></th>
                            <td>
                                <?= Html::makeCheckbox('bd_is_notice', $bd_is_notice_arr, $data['bd_is_notice'], 1) ?>
                            </td>
                            <th class="required"><label for="bd_is_display">출력여부</label></th>
                            <td>
                                <?= Html::makeRadio('bd_is_display', $bd_is_display_arr, $data['bd_is_display'], 1) ?>
                            </td>
                        </tr>
                        <?php
                    } elseif ($bd_code == 'reference') {
                        // 과정 자료실, 출력여부에서 메인숨김 제거 yllee 230321
                        unset($bd_is_display_arr['A']);
                        ?>
                        <tr>
                            <th class="required"><label for="bd_is_display">출력여부</label></th>
                            <td colspan="3">
                                <?= Html::makeRadio('bd_is_display', $bd_is_display_arr, $data['bd_is_display'], 1) ?>
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
                        <?= Html::makeTextareaInTable('내용', 'bd_content', $data['bd_content'], 'required', 15, 120, 3) ?>
                        <?php
                        if ($bd_code != 'notice' && $bd_code != 'safe' && $bd_code != 'reference' && $bd_code != 'snotice') {
                            ?>
                            <tr>
                                <th><label for="bd_etc1">파트너 권한설정</label></th>
                                <td colspan="3">
                                    <input type="hidden" name="bd_etc2" id="bd_etc2" value="<?=$data['bd_etc2']?>"/>
                                    <?= Html::makeInputText('bd_etc1', '파트너 권한', $data['bd_etc1'], '', '60', '10000') ?>
                                    <a href="./popup.partner_name_search.html?num=0" class="btn_ajax sButton small"
                                       target="layer_popup"
                                       title="파트너 검색">선택</a>
                                    <?= Html::makeRadio('bd_etc3', $view_partner, $data['bd_etc3'], 1) ?>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="bd_etc4">튜터 권한설정</label></th>
                                <td colspan="3">
                                    <input type="hidden" name="bd_etc5" id="bd_etc5" value="<?=$data['bd_etc5']?>"/>
                                    <?= Html::makeInputText('bd_etc4', '튜터 권한', $data['bd_etc4'], '', '60', '10000') ?>
                                    <a href="./popup.tutor_name_search.html?num=0" class="btn_ajax sButton small"
                                       target="layer_popup"
                                       title="튜터 검색">선택</a>
                                    <?= Html::makeRadio('bd_etc6', $view_tutor, $data['bd_etc6'], 1) ?>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="bd_etc7">기업담당자<br> 권한설정</label></th>
                                <td colspan="3">
                                    <input type="hidden" name="bd_etc8" id="bd_etc8" value="<?=$data['bd_etc8']?>"/>
                                    <?= Html::makeInputText('bd_etc7', '파트너 권한', $data['bd_etc7'], '', '60', '10000') ?>
                                    <a href="./popup.company_name_search.html?num=0" class="btn_ajax sButton small"
                                       target="layer_popup"
                                       title="기업담당자 검색">선택</a>
                                    <?= Html::makeRadio('bd_etc9', $view_partner, $data['bd_etc9'], 1) ?>
                                </td>
                            </tr>
                            <?
                        } ?>
                        <?
                    }
                    if ($bd_code != 'article') {
                        for ($i = 0; $i < $max_file; $i++) {
                            ?>
                            <tr class="file">
                                <th><label for="atch_file<?= $i + 1 ?>">첨부파일 #<?= $i + 1 ?></label></th>
                                <td colspan="3">
                                    <input type="hidden" name="fi_type[]" value="default"/>
                                    <input type="file" name="atch_file[]" id="atch_file<?= $i + 1 ?>" value=""
                                           class="file"
                                           size="100" title="첨부파일 <?= $i + 1 ?>"/>
                                    <?php
                                    if ($file_list[$i]['fi_id']) {
                                        ?>
                                        <p>
                                            <input type="checkbox" name="del_file[]" id="del_file_<?= $i + 1 ?>"
                                                   value="<?= $file_list[$i]['fi_id'] ?>" class="checkbox"
                                                   title="기존파일 삭제"/>
                                            <label for="del_file_<?= $i + 1 ?>">기존파일삭제</label>
                                            <span>|</span>
                                            <a href="./download.html?fi_id=<?= $file_list[$i]['fi_id'] ?>"
                                               class="btn_download"
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
