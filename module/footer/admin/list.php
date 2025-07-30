<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */
use sFramework\FooterAdmin;
use sFramework\Html;
use sFramework\Format;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oFooter = new FooterAdmin();
$oFooter->init();
$pk = $oFooter->get('pk');

/* check auth */
if (!$oFooter->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oFooter->selectList();
$cnt_total = $oFooter->get('cnt_total');

$ft_is_display_arr = $oFooter->get('ft_is_display_arr');
/* search condition */
$search_like_arr = $oFooter->get('search_like_arr');
$search_date_arr = $oFooter->get('search_date_arr');
$query_string = $oFooter->get('query_string');
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

});
//]]>
</script>

<div id="">

    <!-- list -->
    <div class="list">

        <!-- list_header -->
        <div class="list_header">
            <div class="left">
                <i class="xi-file-text-o"></i> Total : <strong><?=number_format($cnt_total)?></strong> 건
            </div>

            <div class="right">

            </div>
        </div>
        <!-- //list_header -->

        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?=$query_string?>" />
        <input type="hidden" name="page" value="<?=$page?>" />
        </fieldset>

        <fieldset>
        <legend>자료목록</legend>
        <input type="hidden" name="mode" value="delete" />

        <!-- list_table -->
        <table class="list_table border" border="1">
        <colgroup>
        <col width="30" />
        <col width="100" />
        <col width="130" />
        <col width="*" />
        <col width="80" />
        <col width="60" />
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택" /></th>
            <th>순서변경</th>
            <th>이미지</th>
            <th>제목</th>
            <th>사용여부</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody id="carousel_tbody">
        <?php for ($i = 0; $i < count($list); $i++) { ?>
            <tr class="list_tr_<?=$list[$i]['odd']?>">
                <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?=$list[$i][$pk]?>" class="list_checkbox" title="선택/해제" /></td>
                <td>
                    <button type="button" onclick="changeOrder('up', this)" class="btn_change_order" title="위로"><i class="xi-caret-up"></i></button>
                    <button type="button" onclick="changeOrder('down', this)" class="btn_change_order" title="아래로"><i class="xi-caret-down"></i></button>
                </td>
                <td>
                    <a href="./write.html?<?=$pk?>=<?=$list[$i][$pk]?>&page=<?=$page?><?=$query_string?>"><img src="<?=$list[$i]['thumb_uri']?>" width="100" alt="<?=$list[$i]['img_alt']?>" /></a>
                </td>
                <td class=""><a href="./write.html?<?=$pk?>=<?=$list[$i][$pk]?>&page=<?=$page?><?=$query_string?>"><?=$list[$i]['ft_subject']?></a></td>
                <td class="state"><strong class="<?=$list[$i]['state_class']?>"><?=$ft_is_display_arr[$list[$i]['ft_is_display']]?></strong></td>
                <td class="button"><a href="./write.html?<?=$pk?>=<?=$list[$i][$pk]?>&page=<?=$page?><?=$query_string?>" class="sButton tiny" title="수정">수정</a></td>
            </tr>
        <?php } ?>
        <?=!count($list) ? Html::makeNoTd(7) : null?>
        </tbody>
        </table>
        <!-- //list_table -->

        <!-- list_footer -->
        <div class="list_footer">
            <div class="left">
                <button type="submit" class="sButton small">선택삭제</button>
            </div>
            <div class="right">
                <a href="./write.html?page=<?=$page?><?=$query_string?>" class="sButton small primary" title="등록">등록</a>
            </div>
        </div>
        <!-- //list_footer -->

        </form>

    </div>
    <!-- //list -->
</div>

<form name="order_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1" />
<input type="hidden" name="mode" value="change_order" />
<input type="hidden" name="direction" value="" />
<input type="hidden" name="<?=$pk?>" value="" />
</form>
