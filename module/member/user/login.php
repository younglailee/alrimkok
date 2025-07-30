<?php
/**
 * @page    Login
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\MemberUser;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
global $is_member;;
global $layout_uri;;
if ($is_member) {
    Html::alert('현재 로그인 중입니다.', $layout_uri . '/page/main.html');
}
$ck_save_id = Session::getCookie('ck_save_id_user');
$return_uri = $_GET['return_uri'];
if (!$return_uri) {
    $return_uri = '/webuser/page/main.html';
}
/*
global $member;
print_r($member);
*/
?>
<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
//<![CDATA[
$(function() {
    $("#login_id").focus();
});
//]]>
</script>
<style>
.mmb_login input[type="submit"] { width:450px; height:60px; font-size:18px; color:#FFFFFF; background:#2544B6; }
.mmb_login > p:nth-child(2) { float:left; width:100%; margin-bottom:40px; padding-bottom:40px; font-size:18px; text-align:center; border-bottom:none; }
.mmb_login input[type="text"], .mmb_login input[type="password"] { width:450px; height:50px; margin-bottom:10px; padding:0 20px; box-sizing:border-box; font-size:16px; background:#FFFFFF; border:1px solid #DDDDDD; }
.mmb_login form { float:left; margin:0; text-align:center; }
.mmb_login label { display:block; margin:10px 0 30px; float:left; padding-left:30px; font-size:16px; /*background: url("../img/member/checkOff.jpg") 0 1px no-repeat;*/ }
.mmb_login > p { display:block; height:50px; margin:10px 0 30px; float:right; font-size:16px; }
.login_area {width:720px; margin:72px 0 150px 0;}
#cmm_sub > div {margin-bottom:0;}
</style>
<section id="login" class="contents">
    <div class="container">
        <h2 class="sec-title">로그인</h2>
        <form name="login_form" method="post" action="./process.html"
              onsubmit="return submitLoginForm(this)">
        <input type="hidden" name="mode" value="login"/>
        <div class="form-wrap">
            <fieldset>
            <legend>로그인</legend>
            <label for="login_id">아이디</label>
            <input type="text" name="login_id" id="login_id" class="required" placeholder="아이디">
            <label for="login_pw">비밀번호</label>
            <input type="password" name="login_pw" id="login_pw" class="required" placeholder="비밀번호">
            <button id="bt_login" class="loginBtn" type="submit">로그인</button>
            <ul class="lg-bottom">
            <li><a href="./join.html">회원가입</a></li>
            <li><a href="./find_id.html">아이디찾기</a></li>
            <li><a href="./find_pw.html">비밀번호찾기</a></li>
            </ul>
            </fieldset>
        </div>
        </form>
    </div>
</section>
