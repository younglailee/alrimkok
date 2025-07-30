<?php
/**
 * @file    write.php
 * @author  Alpha-Edu
 */
use sFramework\FooterAdmin;
use sFramework\Html;
use sFramework\Time;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* set URI */
$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oFooter = new FooterAdmin();
$oFooter->init();
$pk = $oFooter->get('pk');

/* check auth */
if (!$oFooter->checkWriteAuth()) {
    Html::alert('권한이 없습니다.');
}

/* data */
$uid = $oFooter->get('uid');
$data = $oFooter->selectDetail($uid);

/* search condition */
$query_string = $oFooter->get('query_string');

/* code */
$ft_is_display_arr = $oFooter->get('ft_is_display_arr');

/* file */
$max_file = $oFooter->get('max_file');
$file_list = $data['file_list'];
$img_size = $oFooter->get('img_size');

/* mode */
if (!$uid || !$data[$pk]) {
    $mode = 'insert';
    $data = array(
        'cr_bgn_date'   => _NOW_DATE_
    );

    foreach ($ft_is_display_arr as $key => $val) {
        $data['ft_is_display'] = $key;
        break;
    }
} else {
    $mode = 'update';
}

$hour_arr = Time::makeHourArray('00:00', '24:00');
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

});
//]]>
</script>

<div id="">

    <div class="write">
        <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data" onsubmit="return submitWriteForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?=$query_string?>" />
        <input type="hidden" name="page" value="<?=$page?>" />
        </fieldset>

        <fieldset>
        <legend>기본정보</legend>
        <input type="hidden" name="mode" value="<?=$mode?>" />
        <input type="hidden" name="<?=$pk?>" value="<?=$data[$pk]?>" />
        <input type="hidden" name="ft_order" value="<?=$data['ft_order']?>" />

        <h4>기본사항</h4>
        <table class="write_table" border="1">
        <caption>기본정보 입력 테이블</caption>
        <colgroup>
        <col width="140" />
        <col width="*" />
        </colgroup>
        <tbody>
        <?= Html::makeInputTextInTable('제목', 'ft_subject', $data['ft_subject'], 'required', 80, 50) ?>
        <?= Html::makeInputTextInTable('링크주소', 'ft_uri', $data['ft_uri'], '', 80, 255) ?>
        <tr>
            <th class="required">출력여부</th>
            <td>
                <?= Html::makeRadio('ft_is_display', $ft_is_display_arr, $data['ft_is_display'], 1) ?>
            </td>
        </tr>
        <tr class="file">
            <th><label for="carousel_img"> 이미지</label></th>
            <td>
                <input type="hidden" name="fi_type[]" value="footer"/>
                <input type="file" name="atch_file[]" id="footer_img" value="" class="file" size="80" title="이미지"/>
                <?php if ($file_list[0]['fi_id']) { ?>
                    <p>
                        <a href="./download.html?fi_id=<?=$file_list[0]['fi_id']?>" target="_blank" title="새창 다운로드"><img src="<?=$file_list[0]['thumb_uri']?>" alt="프로필이미지 썸네일"/></a>
                        <span>|</span>
                        <input type="checkbox" name="del_file[]" id="del_carousel_img" value="<?= $file_list[0]['fi_id'] ?>"
                               class="checkbox" title="기존파일삭제"/><label for="del_carousel_img">기존파일삭제</label>
                        <span>|</span>
                        <a href="./download.html?fi_id=<?=$file_list[0]['fi_id']?>" class="btn_download" target="_blank" title="새창 다운로드">
                            <strong><?= $file_list[0]['fi_name'] ?></strong>
                            <span>(<?= $file_list[0]['bt_fi_size'] ?>)</span>
                        </a>
                    </p>
                <?php } ?>
                <p class="comment">
                    이미지 권장사이즈(px) : <?=$img_size?><br/>
                    비율이 맞지않은 이미지를 업로드할 경우, 이미지가 일부 잘릴 수 있습니다.
                </p>
            </td>
        </tr>
        <tr>
            <th>대체정보</th>
            <td>
                <?= Html::makeInputText('ft_alt', '대체정보', $data['ft_alt'], '', 80, 50) ?>
                <p class="comment">
                    대체정보를 작성하지 않을 경우, 제목이 대체정보로 제공됩니다.
                </p>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
            <a href="./list.html?page=<?=$page?><?=$query_string?>" class="sButton active" title="목록">목록</a>
            <?php if ($mode == 'update') { ?>
                <a href="./process.html?mode=delete&<?=$pk?>=<?=$uid?>&page=<?=$page?><?=$query_string?>" class="sButton warning btn_delete" title="삭제">삭제</a>
            <?php } ?>
        </div>

        </form>
    </div>
</div>
<!-- //<?=$module?> -->