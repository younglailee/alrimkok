<?php
/**
 * @file    intranet.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
global $is_member, $layout_uri;
if ($is_member) {
    Html::alert('현재 로그인 중입니다.', $layout_uri . '/page/main.html');
}
$ck_save_id = Session::getCookie('ck_save_id');
$return_uri = $_GET['return_uri'];
?>
<style>
.form-wrap input {margin-bottom:10px;}
#btn_wrap {margin-top:36px;}
</style>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $("#mb_name").focus();

    $("#findIdBtn").click(function(e) {
        e.preventDefault();

        let mb_name = $("#mb_name");
        let mb_name_val = mb_name.val();
        let mb_hp = $("#mb_hp");
        let mb_hp_val = mb_hp.val();

        if (!mb_name_val) {
            alert('이름을 입력해주세요.');
            mb_name.focus();
            return;
        }
        if (!mb_hp_val) {
            alert('연락처를 입력해주세요.');
            mb_hp.focus();
            return;
        }
        $.ajax({
            url: "process.html",
            type: "GET",
            dataType: "json",
            data: {
                flag_json: '1',
                mode: 'findId',
                mb_name: mb_name_val,
                mb_hp: mb_hp_val
            },
            success: function(result) {
                let data = result.data;
                console.log(result);
                if (!data) {
                    alert('등록된 회원정보가 존재하지않습니다.');
                } else {
                    alert('등록된 ' + mb_hp_val + '번호로 아이디를 전송하였습니다.')
                    let html = '<button type="button" class="loginBtn" onclick="loginPage()">로그인</button>';
                    $("#btn_wrap").html(html);
                }
            }
        });
    })
});

function loginPage() {
    location.href = "./login.html";
}
//]]>
</script>
<section id="login" class="contents find">
    <div class="container">
        <h2 class="sec-title">아이디 찾기</h2>
        <p class="sec-txt">가입 시 입력한 회원정보를 넣어주세요</p>

        <form>
        <div class="form-wrap">
            <fieldset>
            <legend>아이디 찾기</legend>

            <label for="mb_name">이름</label>
            <input type="text" name="mb_name" id="mb_name" placeholder="이름을 입력해주세요">

            <label for="mb_hp">연락처</label>
            <input type="tel" name="mb_hp" id="mb_hp" placeholder="연락처를 입력해주세요">
            <div id="btn_wrap">
                <button id="findIdBtn" class="loginBtn" type="submit">확인</button>
            </div>
            <div class="lg-bottom">
                <p>비밀번호가 기억이 안나시나요?</p>
                <a href="./find_pw.html">비밀번호 찾기</a>
            </div>
            </fieldset>
        </div>

        </form>
    </div>
</section>
