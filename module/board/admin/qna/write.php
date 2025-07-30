<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */

use sFramework\QnaAdmin;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
global $layout, $bd_code, $member, $module;
$page = ($_POST['page']) ?: $_GET['page'];
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';

/* init Class */
$oBoard = new QnaAdmin();
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
$(document).ready(function() {
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

    /* tag tree */
    var obj_tag_tree = $("#tag_tree");
    $("input[type=checkbox]", obj_tag_tree).on("click", function(e) {
        checkTagTree(this);
    });

});
/* 태그 변경 */
function toggleTagTree(obj) {
    $(obj).parent("li").toggleClass("open");
}
var editor_min_width = 800;
var oEditors = [];
var max_file = "<?=$max_file?>";
//]]>
</script>
<style>
#tag_tree {}
#tag_tree > p {}

#tag_tree ul { display:none; margin:10px 20px;}
#tag_tree ul li { }
#tag_tree > ul { display:block; margin-left:0; }
#tag_tree li.open > ul { display:block; }
#tag_tree li > button { display:inline-block; margin:0; padding:0; background:none; border:0; cursor:pointer; font-size:13px; text-align:center; }
#tag_tree li > button > i.xi-plus-square { display:inline-block; }
#tag_tree li > button > i.xi-minus-square { display:none; }
#tag_tree li.open > button > i.xi-plus-square { display:none; }
#tag_tree li.open > button > i.xi-minus-square { display:inline-block; }

</style>
<div id="<?= $module ?>">
    <div class="write">

        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
              onsubmit="return submitWriteForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        </fieldset>

        <fieldset>
        <legend>기본정보</legend>
        <input type="hidden" name="mode" value="<?= $mode ?>"/>
        <input type="hidden" name="<?= $pk ?>" value="<?= $data[$pk] ?>"/>

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
            <th class=""><label for="bd_category">제목강조</label></th>
            <td colspan="3">
                <input type="radio" name="" id="" class="radio bd_category" value="Text Bold" checked="checked"/><label
                    for=""><strong class="bold">Text Bold</strong></label>
                <input type="radio" name="" id="" class="radio bd_category" value="ICON1"/><label for=""><strong
                        class="icon1"><img src="../../../layout/admin/img/ico_icon1.gif" alt="별표"/></strong> </label>
                <input type="radio" name="" id="" class="radio bd_category" value="ICON2"/><label for=""><strong
                        class="icon2"><img src="../../../layout/admin/img/ico_icon4.gif" alt="체크"/></strong> </label>
                <input type="radio" name="" id="" class="radio bd_category" value="ICON3"/><label for=""><strong
                        class="icon3"><img src="../../../layout/admin/img/ico_icon2.gif" alt="중요"/></strong> </label>
                <input type="radio" name="" id="" class="radio bd_category" value="ICON3"/><label for=""><strong
                        class="icon4"><img src="../../../layout/admin/img/ico_icon3.gif" alt="필독"/></strong> </label>
            </td>
        </tr>
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
        <tr>
            <th>공지여부</th>
            <td colspan="3">
                <?= Html::makeCheckbox('bd_is_notice', $bd_is_notice_arr, $data['bd_is_notice'], 1) ?>
                <span class="comment">체크할 경우, 게시판 목록의 최상단에 출력됩니다.</span>
            </td>
        </tr>
        <tr>
            <th class="required">년도선택</th>
            <td colspan="3">
                <select class="select">
                <option value="">2019년</option>
                <option value="">2020년</option>
                </select>
                <p class="comment">연도 선택 시 공지할 수 있는 기관을 선택할 수 있습니다.</p>
            </td>
        </tr>
        <tr>
            <th class="required">기관선택</th>
            <td colspan="3">
                <div id="tag_tree">
                    <p>
                        <button type="button" onclick="openAllTag()" class="sButton tiny">+모두펼치기</button>
                        <button type="button" onclick="closeAllTag()" class="sButton tiny">-모두접기</button>
                    </p>
                    <ul>
                    <li>
                        <button type="button" onclick="toggleTagTree(this)"><i class="xi-plus-square"></i><i
                                class="xi-minus-square"></i></button>
                        <input type="checkbox" name="" id="" class="checkbox" value="1"/>
                        <label for="">{사업명}</label>
                        <ul>
                        <li>
                            <input type="checkbox" name="" id="" class="checkbox" value=""/>
                            <label for="">{기관명A}</label>
                        </li>
                        <li>
                            <input type="checkbox" name="" id="" class="checkbox" value=""/>
                            <label for="">{기관명B}</label>
                        </li>
                        </ul>
                    </li>
                    </ul>
                </div>
            </td>
        </tr>

        <?= Html::makeTextareaInTable('내용', 'bd_content', $data['bd_content'], 'required', 20, 120, 3) ?>
        <tr class="file">
            <th><label for="atch_file{n}">첨부파일 #n</label></th>
            <td colspan="3">
                <input type="hidden" name="fi_type[]" value="default"/>
                <input type="file" name="atch_file[]" id="atch_file{n}" value="" class="file" size="100"
                       title="첨부파일 {n}"/>
            </td>
        </tr>
        <tr class="file">
            <th><label for="atch_file{n}">첨부파일 #n</label></th>
            <td colspan="3">
                <input type="hidden" name="fi_type[]" value="default"/>
                <input type="file" name="atch_file[]" id="atch_file{n}" value="" class="file" size="100"
                       title="첨부파일 {n}"/>
            </td>
        </tr>
        <tr class="file">
            <th><label for="atch_file{n}">첨부파일 #n</label></th>
            <td colspan="3">
                <input type="hidden" name="fi_type[]" value="default"/>
                <input type="file" name="atch_file[]" id="atch_file{n}" value="" class="file" size="100"
                       title="첨부파일 {n}"/>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="/webadmin/page/intranet_notic.html" class="sButton active" title="목록">목록</a>
        </div>

        </form>
    </div>
</div>
<!-- //<?= $module ?> -->
