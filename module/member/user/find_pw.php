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
global $member, $layout_uri;
//print_r($member);
if ($member['mb_id']) {
    Html::alert('현재 로그인 중입니다.', $layout_uri . '/page/main.html');
}
$ck_save_id = Session::getCookie('ck_save_id');
$return_uri = $_GET['return_uri'];
?>
<style>
@media screen and (min-width:416px) {
    #mb_id {margin-bottom:10px;}
    #auth_td {display:none;}
    #login .form-wrap input#auth_no {width:60%;}
    .auth_time {width:60px;margin-left:25px;}
    .loginBtn {margin-top:10px}
;

    .mmb_login {
        width:720px;
        height:460px;
    }
    .mmb_login form {clear:both;}
    .mmb_login table.tbl_find {height:120px}
    .mmb_login table.tbl_find span {font-family:"notoMedium", sans-serif;font-size:16px}
    .mmb_login table.tbl_find tr > td:first-child {width:120px}
    .mmb_login div#btn_wrap button {width:500px;height:50px;font-family:"notoMedium", sans-serif;font-size:20px;color:white;background:#f8575f;}

    .mmb_login table.tbl_find input#auth_btn {height:50px;font-family:"notoMedium", sans-serif;color:#2342B5;border:1px solid #2342B5;position:relative;top:-5px}
    .mmb_login table.tbl_find input#auth_btn:hover {color:#2342B5;background:#E8EBF8;}
    .mmb_login table.tbl_find input#add_time {height:50px;font-family:"notoMedium", sans-serif;color:#848484;border:1px solid #DDDDDD;position:relative;top:-5px}
    .mmb_login table.tbl_find input#add_time:hover {color:#848484;background:#DDDDDD;}
    .mmb_login label {display:none;}

    #btn_wrap {margin-top:10px;}

    #cmm_sub > div {width:50%; margin:72px 0 150px 0; }
}

.con .popup {position:fixed;left:50%;top:50%;z-index:99999;width:700px;margin:-320px 0 0 -400px;padding:60px 100px;box-sizing:border-box;background:white;}
.con .popup > button {position:absolute; right:5px; top:-35px; padding-bottom:5px; text-align:left; color:white;}
.con .popup > button::after {position:absolute; left:0; bottom:0; display:block; width:30px; height:1px; content:""; background:#d6d6d6;}
.con .popup p {padding-bottom:30px; font-family:"notoBold"; font-size:34px; text-align:center;}
.con .popup input { width:500px; height:50px; margin-bottom:10px; padding:0 20px; box-sizing:border-box; font-size:16px; color:#848484; background:#Ffffff; border:1px solid #DDDDDD; }
.con .popup ul {margin-bottom:10px; font-family:"notoDemiLight"; color:#888888;}
.con .popup div {position:relative; text-align:center;}
.con.bgOn::after {position:fixed; left:0; top:0; z-index:9999; display:block; width:100%; height:100%; content:""; background:black; opacity:0.8;}
.file-input > p {margin:8px 0 0 180px;}

.pc { display:block; }
.mo { display:none; }
.con .popup {margin:20px 0 0 0;transform:translate(-50%, -50%);height:auto;}

@media all and (max-width:1024px) {
    .con .popup { padding:40px 30px 0; max-width:320px; width:90%; max-height:500px; height:80vh; overflow-y:auto; box-sizing:border-box; }
    .con .popup p { font-size:24px; }
    .con .popup input { width:100%; }
    .con .popup ul { margin:5px 0 15px; }
    .con .popup div { margin-top:20px; height:100px; }
    .con .popup div button { height:60px; font-size:16px; }
    .con .popup > button {
        top:10px; right:12px; width:20px; height:20px; font-size:13px; line-height:20px; text-align:center; cursor:pointer;
        border-radius:0.25rem; background-color:lightgray; position:absolute; color:#222;
    }
    .con .popup > button::after { display:none; }
    .mo {display:block;}
}

@media all and (max-width:359px) {
    .con .popup { padding:40px 20px 0; width:95%; }
}
</style>
<script type="text/javascript">
//<![CDATA[
var interval = null;
$(function() {
    var check_auth = false;
    var time_click = true;

    if ('<?=$_GET['mode']?>' === 'fail') {
        $(".mmb_mypage").addClass("bgOn");
        $("[data-popup]").show();
    }
    $("#login_id").focus();

    $("#auth_btn").click(function() {
        var mb_id = $("#mb_id").val();
        var mb_name = $("#mb_name").val();
        var mb_hp = $("#mb_hp").val();

        if (!mb_id) {
            alert('아이디를 입력해주세요.');
            $("#mb_id").focus();
            return;
        }
        if (!mb_name) {
            alert('이름을 입력해주세요.');
            $("#mb_name").focus();
            return;
        }
        if (!mb_hp) {
            alert('휴대전화번호를 입력해주세요.');
            $("#mb_hp").focus();
            return;
        }
        if (confirm(mb_hp + '번으로 인증번호를 전송하시겠습니까?')) {
            $.ajax({
                url: "process.html",
                type: "POST",
                dataType: "json",
                data: {
                    flag_json: 1,
                    mb_id: mb_id,
                    mb_name: mb_name,
                    mb_hp: mb_hp,
                    mode: 'find_password'
                },
                success: function(rs) {
                    //console.log(rs);
                    if (rs.code === 'failure') {
                        alert(rs.msg);
                    } else {
                        alert('인증번호를 전송하였습니다.');
                        dailyMissionTimer(3);
                    }
                    check_auth = true;
                }
            });
        }
    })

    $("#findPwBtn").click(function(e) {
        e.preventDefault();
        var auth_min = $("#minutes").text();
        var auth_sec = $("#seconds").text();
        var mb_id = $("#mb_id").val();
        var auth_no = $("#auth_no").val();
        var mb_name = $("#mb_name").val();
        var mb_hp = $("#mb_hp").val();

        if (!check_auth) {
            alert('인증번호를 발급받아주세요.');
        } else if (!auth_no) {
            alert('인증번호를 입력해주세요.');
            $("#auth_no").focus();
        } else {
            if (auth_min === 0 && auth_sec === 0) {
                alert('인증시간이 초과하였습니다.');
                return;
            }
            $.ajax({
                url: "process.html",
                type: "POST",
                dataType: "json",
                data: {
                    flag_json: 1,
                    mb_id: mb_id,
                    auth_no: auth_no,
                    mode: 'check_auth'
                },
                success: function(rs) {
                    alert(rs.msg);

                    if (rs.code === 'success') {
                        $("#popID").val(mb_id);
                        $("#popTel").val(mb_hp);
                        $("#popName").val(mb_name);
                        $(".con").addClass("bgOn");
                        $("[data-popup]").show();
                    }
                }
            })
        }
    })

    $("#add_time").click(function() {
        if (check_auth) {
            if (time_click) {
                clearInterval(interval);
                dailyMissionTimer(3);
                time_click = false;
            } else {
                alert('인증시간 연장은 한번만 가능합니다.');
            }
        } else {
            alert('인증번호를 발급받아주세요.')
        }
    })

    $("[data-popClose]").click(function() {
        $(".con").removeClass("bgOn");
        $("[data-popup]").hide();
    });
});

function dailyMissionTimer(duration) {
    let timer = duration * 60;
    let minutes, seconds;

    let auth_td = $("#auth_td");
    auth_td.addClass("input-wrap");
    auth_td.show();

    if (typeof interval !== 'undefined') {
        clearInterval(interval);
    }
    interval = setInterval(function() {
        minutes = parseInt(timer / 60 % 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        $('#minutes').text(minutes);
        $('#seconds').text(seconds);

        if (--timer < 0) {
            timer = 0;
            clearInterval(interval);
        }
    }, 1000);
}

//]]>
</script>
<section id="login" class="contents find ">
    <div class="container">
        <h2 class="sec-title">비밀번호 찾기</h2>
        <p class="sec-txt">가입 시 입력한 회원정보를 넣어주세요</p>
        <form>
        <div class="form-wrap">
            <fieldset>
            <legend>비밀번호 찾기</legend>
            <label for="mb_id">아이디</label>
            <input type="text" name="mb_id" id="mb_id" placeholder="아이디를 입력해주세요">
            <label for="mb_name">이름</label>
            <input type="text" name="mb_name" id="mb_name" placeholder="이름을 입력해주세요">
            <div class="input-wrap">
                <label for="mb_hp">연락처</label>
                <input type="tel" name="mb_hp" id="mb_hp" placeholder="연락처를 입력해주세요">
                <button type="button" id="auth_btn" class="get_number">인증번호</button>
            </div>
            <div id="auth_td">
                <label for="auth_no">인증번호</label>
                <input type="tel" name="auth_no" id="auth_no" placeholder="인증번호를 입력해주세요">
                <span class="auth_time" id="minutes">03</span>:<span class="auth_time" id="seconds">00</span>
                <button type="button" id="add_time" class="delay_btn">연장하기</button>
            </div>
            <div id="findPwBtn" class="loginBtn">비밀번호 재설정</div>
            </fieldset>
        </div>
        </form>
    </div>
</section>

<div class="con">
    <form action="./process.html" method="post" name="pass_modi_form" onsubmit="return validateForm(this)">
    <input type="hidden" name="mode" value="update_password_find">
    <input type="hidden" name="mb_id" id="popID" value="">
    <input type="hidden" name="mb_hp" id="popTel" value="">
    <input type="hidden" name="mb_name" id="popName" value="">
    <div class="popup" data-popup style="display:none;">
        <button type="button" class="pc" data-popClose>닫기</button>
        <button type="button" class="mo" data-popclose="">×</button>
        <p>비밀번호 변경</p>
        <input type="password" name="mb_pass" class="required" title="기존 비밀번호" placeholder="기존 비밀번호">
        <input type="password" name="new_pass" class="required" title="변경할 비밀번호" placeholder="변경할 비밀번호">
        <ul>
        <li>ㆍ비밀번호는 8~20자로 사용가능하며 영문, 숫자, 특수문자 모두 포함해야 합니다.</li>
        <li>ㆍ사용가능 특수문자 ~!@#$%^&</li>
        </ul>
        <input type="password" name="new_pass2" class="required" title="비밀번호 확인" placeholder="비밀번호 확인">
        <div>
            <button class="btn-small btn02" type="submit">완료</button>
        </div>
    </div>
    </form>
</div>
