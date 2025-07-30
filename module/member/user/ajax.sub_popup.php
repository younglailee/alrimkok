<?php
/**
 * @file    부계정 관리 팝업
 * @author  Alpha-Edu
 */

use sFramework\MemberUser;

if (!defined('_ALPHA_')) {
    exit;
}
global $mb_id, $sub_mode;

$popup_title = "신규등록";
$mode = "sub_insert";
$mb_pw_class = 'class="required" ';
if ($sub_mode == 'modify') {
    $popup_title = "정보수정";
    $mode = "sub_update";
    $mb_pw_class = '';
}
$oMember = new MemberUser();
$oMember->init();

$data = $oMember->selectDetail($mb_id);
//print_r($data);
$mail_arr = explode('@', $data['mb_email']);
//print_r($mail_arr);
$mail_domain_arr = array('naver.com', 'gmail.com', 'daum.net', 'nate.com', 'hanmail.net', 'manual');
global $query_string;
?>
<style>
.pw-help-txt {margin:10px 0 0 145px; height:20px;}
</style>
<div class="bg"></div>
<div class="pop-con">
    <p class="title"><?= $popup_title ?></p>
    <form action="./process.html" method="post" name="write_form" onsubmit="return submitWriteForm(this)">
    <input type="hidden" name="mode" id="mode" value="<?= $mode ?>">
    <input type="hidden" name="query_string" value="<?= $query_string ?>">
    <div class="scroll">
        <div class="join-input b-num">
            <label for="mb_id">사용자 ID</label>
            <?php
            if ($sub_mode == 'modify') {
                echo $data['mb_id'];
                echo '<input type="hidden" name="mb_id" value="' . $data['mb_id'] . '">';
            } else {
                ?>
                <input type="text" name="mb_id" id="mb_id" value="<?= $data['mb_id'] ?>"
                       class="required" placeholder="아이디를 입력해주세요">
                <button type="button" class="check-btn" id="checkDuplicateBtn" onclick="checkId()">중복확인</button>
                <?php
            }
            ?>
        </div>
        <div class="join-input">
            <label for="mb_pw">비밀번호</label>
            <input type="password" name="mb_pw" id="mb_pw"
                   <?= $mb_pw_class ?>placeholder="비밀번호를 입력해주세요">
        </div>
        <div class="pw-help-txt">8~16자 영문, 숫자, 특수문자를 사용하세요.</div>
        <div class="join-input">
            <label for="userName">사용자명</label>
            <input type="text" name="mb_name" id="userName" value="<?= $data['mb_name'] ?>"
                   class="required" placeholder="사용자명을 입력하세요">
        </div>
        <div class="join-input">
            <label for="deptName">부서명</label>
            <input type="text" name="mb_depart" id="deptName" value="<?= $data['mb_depart'] ?>"
                   class="required" placeholder="부서명을 입력해주세요">
        </div>
        <div class="join-input">
            <label for="userPhone">사용자 연락처</label>
            <input type="tel" name="mb_hp" id="userPhone" value="<?= $data['mb_hp'] ?>"
                   class="required" placeholder="연락처를 입력해주세요">
            <p class="alert-txt">올바른 형식을 입력해주세요</p>
        </div>
        <div class="join-input mail-input">
            <label for="userEmailId">사용자 이메일</label>
            <?php
            if ($sub_mode == 'modify') {
                ?>
                <input type="text" name="mb_email" id="userEmailId" value="<?= $data['mb_email'] ?>"
                       class="required" placeholder="이메일을 입력해주세요" style="width:100%">
                <?php
            } else {
                ?>
                <div class="mail-wrap">
                    <input type="text" name="mb_email" id="userEmailId" value="<?= $mail_arr[0] ?>"
                           class="required" placeholder="이메일을 입력해주세요">
                    <span>@</span>
                    <select name="mb_email2" id="userEmailDomain">
                    <option value="">선택하기</option>
                    <?php
                    for ($i = 0; $i < count($mail_domain_arr); $i++) {
                        $val = $mail_domain_arr[$i];
                        if ($val == 'manual') {
                            $text = '직접입력';
                        } else {
                            $text = $val;
                        }
                        ?>
                        <option value="<?= $val ?>"><?= $text ?></option>
                        <?php
                    }
                    ?>
                    </select>
                    <input class="more-input" type="text" name="userEmailCustomDomain" id="userEmailCustomDomain"
                           style="display: none;" placeholder="도메인 입력">
                </div>
                <?php
            }
            ?>
            <p class="alert-txt">이메일 양식을 확인해주세요</p>
        </div>
    </div>
    <div class="btn-wrap">
        <button type="button" class="btn-cancel" onclick="$('.pop-up').hide();">취소</button>
        <button type="submit" class="btn-save">등록</button>
    </div>
    </form>
    <div class="pop-close" onclick="$('.pop-up').hide();"><img src="/common/img/user/icon/close.svg" class="of-ct"
                                                               alt="닫기"></div>
</div>
<script>
$(function() {

});
</script>
