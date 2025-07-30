<?
use sFramework\MemberPartner;
if(!defined('_ALPHA_')) { exit; }

/* init Class */
$oMember = new MemberPartner();
$oMember->init();
$module_name = $oMember->get('module_name');	// 모듈명
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

});
//]]>
</script>

<div id="<?=$module?>">

    <div class="write">

        <form name="update_password_form" method="post" action="./process.html" onsubmit="return submitUpdatePasswordForm(this)">

        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="mode" value="update_password" />
        </fieldset>

        <fieldset>
        <legend>등록/수정</legend>

        <table class="write_table" border="1">
        <caption>등록/수정</caption>
        <colgroup>
        <col width="200" />
        <col width="*" />
        </colgroup>
        <tbody>
        <tr>
            <th class="required">현재 비밀번호</th>
            <td>
                <input type="password" name="mb_pass" value="" class="text required" size="30" maxlength="20" title="현재 비밀번호">
            </td>
        </tr>
        <tr>
            <th class="required">신규 비밀번호</th>
            <td>
                <input type="password" name="new_pass" value="" class="text required" size="30" maxlength="20" title="신규 비밀번호">
                <p class="comment">-비밀번호 20자 이하의 영대/소문자, 숫자, 특수숫자만 입력 가능합니다.</p>
            </td>
        </tr>
        <tr>
            <th class="required">신규 비밀번호 확인</th>
            <td>
                <input type="password" name="new_pass2" value="" class="text required" size="30" maxlength="20" title="신규 비밀번호 확인">
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton primary">확인</button>
        </div>

        </form>
    </div>



</div>
