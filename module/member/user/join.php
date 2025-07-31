<?php
/**
 * @file    intranet.php
 * @author  Alpha-Edu
 */

//$_SESSION = array();
//include_once(_PLUGIN_PATH_ . '/nice_cert/checkplus_main.php'); // 나이스 휴대폰 본인인증 설정파일
//include_once(_PLUGIN_PATH_ . '/nice_ipin/ipin_main.php'); // 나이스 아이핀 본인인증 설정파일
use sFramework\MemberUser;

if (!defined('_ALPHA_')) {
    exit;
}
//print_r($_POST);
$oMember = new MemberUser();
$oMember->init();

$cp_list = $oMember->selectCompanyList();
//print_r($_POST);
$step = ($_GET['step']) ?: $_POST['step'];
if (!$step) {
    $step = 1;
}
$class_step1 = '';
$class_step2 = '';
$class_step3 = '';
if ($step == 1) {
    $class_step1 = ' class="now"';
} else if ($step == 2) {
    $class_step2 = ' class="now"';
} else if ($step == 3) {
    $class_step3 = ' class="now"';
}
// 다음 우편번호 서비스 yllee 190306
?>
<script type="text/javascript" src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
//<![CDATA[
let result_check = false;

function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            console.log(data);
            var roadAddr = data.roadAddress;
            $("input[name='mb_zip']").val(data.zonecode);
            $("input[name='mb_addr']").val(roadAddr);
            $("input[name='mb_addr2']").focus();
        }
    }).open();
}

$(function() {

    $("#mb_id").change(function() {
        result_check = false;
    });

    $("#join_submit").click(function() {
        if (!result_check) {
            alert('중복확인 후 가입이 가능합니다.');
            $("#id_check_btn").focus();
        } else {
            $("#join_form").submit();
        }
    });

    $("#mail_selector").change(function() {
        var mail = $("#mail_selector option:selected").text();
        if (mail === '직접입력') {
            mail = '';
        }
        $("#mb_mail2").val(mail);
    })

    $("#addr_cancel_join").click(function() {
        $("#address01").val('');
        $("#mb_addr").val('');
        $("#mb_addr2").val('');
    })

    $("#mail_selector_user").change(function() {
        var mail2 = $(this).val();
        var target = $("#mb_email2");

        if (mail2 === '직접입력') {
            target.val('');
            target.attr('readonly', false);
        } else {
            target.val(mail2);
            target.attr('readonly', 'readonly');
        }
    });

    // 이메일 주소 직접 입력
    $('#mb_email2').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        //console.log(selectedOption.attr('value'));
        let managerEmailCustomDomain = $('#managerEmailCustomDomain');
        if (selectedOption.attr('value') === 'manual') {
            managerEmailCustomDomain.show().focus();
        } else {
            managerEmailCustomDomain.hide().val("");
        }
    });
});

function checkAgree(mode, obj) {
    var all01 = $("#all01");
    var all02 = $("#all02");
    var agree01 = $("#agree01");
    var agree02 = $("#agree02");
    var agree03 = $("#agree03");
    var agree04 = $("#agree04");
    if (mode === 'all') {
        if ($(obj).attr("checked")) {
            all01.prop('checked', true);
            all02.prop('checked', true);
            agree01.prop('checked', true);
            agree02.prop('checked', true);
            agree03.prop('checked', true);
            agree04.prop('checked', true);
        } else {
            all01.prop('checked', false);
            all02.prop('checked', false);
            agree01.prop('checked', false);
            agree02.prop('checked', false);
            agree03.prop('checked', false);
            agree04.prop('checked', false);
        }
    } else {
        if (!$(obj).attr("checked")) {
            all01.prop('checked', false);
            all02.prop('checked', false);
        }
    }
}

function nextStep() {
    if ($("#agree01").is(":checked") == false || $("#agree02").is(":checked") == false || $("#agree03").is(":checked") == false) {
        alert('필수동의 약관에 모두 체크해주세요.');
    } else {
        $(".mmb_join01").submit();
    }
    return false;
}

function joinMember() {
    //alert($("#cp_name").val());
    let f = document.write_form;
    f.submit();
    return false;
}

function goMain() {
    location.href = "../member/join.html";
}

function fregister_submit(f) {
    if (document.getElementById('register_agree')) {
        if ($("#register_agree").val() != 1) {
            alert('본인인증 후 진행이 가능합니다.');
            return false;
        }
    } else {
        alert('본인인증 후 진행이 가능합니다.');
        return false;
    }
    f.action = "./join.html";
    //f.action = "./register2_cert.php";
    f.submit();
}
window.name = "Parent_window";

function fnPopup() {
    window.open('', 'popupChk', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
    document.form_chk.action = "/plugin/nice_cert/checkplus_main.php";
    document.form_chk.target = "popupChk";
    document.form_chk.submit();
}

function fnPopup2() {
    window.open('', 'popupIPIN2', 'width=450, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
    document.form_ipin.target = "popupIPIN2";
    document.form_ipin.action = "/plugin/nice_ipin/ipin_main.php";
    document.form_ipin.submit();
}

function checkPass() {
    var mb_pw = $("#mb_pw").val();
    var mb_pw2 = $("#mb_pw2").val();

    if (mb_pw != mb_pw2) {
        $("#state_mb_pw2").text('비밀번호가 틀립니다.');
        $("#state_mb_pw2").removeClass('on');
    } else {
        $("#state_mb_pw2").text('확인되었습니다.');
        $("#state_mb_pw2").addClass('on');
    }
}

function ReceiveMessage(evt) {
    let data;
    if (!evt.data) {
        console.log("Unsupported browser.");
    } else {
        data = evt.data;
        console.log(data);
        if (data.alpha === "cert") {
            $("input[name=cert_type]").val(data.cert_type);
            $("input[name=mb_name]").val(data.mb_name);
            $("input[name=mb_hp]").val(data.mb_hp);
            $("input[name=cert_no]").val(data.cert_no);
            $("input[name=mb_sex]").val(data.mb_sex);
            $("input[name=mb_birth]").val(data.mb_birth);
            $("input[name=register_agree]").val(data.register_agree);
            let form1 = $("#form1");
            form1.submit();
        }
    }
}
// postMessage 리스너
if (!window['postMessage'])
    console.log("Unsupported browser.");
else {
    if (window.addEventListener) {
        window.addEventListener("message", ReceiveMessage, false);
    } else {
        window.attachEvent("onmessage", ReceiveMessage);
    }
}
//]]>
</script>
<section id="join" class="contents">
    <div class="container">
        <h2 class="sec-title">회원가입</h2>
        <ul id="join-tab">
        <li<?= $class_step1 ?>><p href="/page/join/terms.html">약관동의</p></li>
        <li<?= $class_step2 ?>><p href="/page/join/info.html">정보입력</p></li>
        <li<?= $class_step3 ?>><p href="/page/join/end.html">가입완료</p></li>
        </ul>
        <?php
        if ($step == 1) {
            $st_data = $oMember->selectSetting(1);
            //print_r($st_data);
        ?>
        <form class="mmb_join01" action="./join.html" method="post">
        <input type="hidden" name="step" id="step" value="2"/>
        <div class="termsForm">
            <fieldset>
            <legend>약관동의</legend>
            <div class="required-terms">
                <div class="join-top">
                    <p class="title">이용약관동의(필수)</p>
                    <label class="agree-all join-chk">
                    <input type="checkbox" name="agree_all" id="all01" onclick="checkAgree('all', this)" value="agree_all"><span>전체동의</span>
                    </label>
                </div>
                <ul class="terms-wrap">
                <li>
                    <label class="join-chk">
                    <input type="checkbox" name="required_terms" id="agree01" onclick="checkAgree('', this)" value="required_terms"><span>필수 약관에 동의합니다</span>
                    </label>
                    <div class="terms">
                        <p><?= nl2br($st_data['necessary']) ?></p>
                    </div>
                </li>
                <li>
                    <label class="join-chk">
                    <input type="checkbox" name="site_terms" id="agree02" onclick="checkAgree('', this)" value="site_terms"/><span>사이트 이용 약관에 동의합니다</span>
                    </label>
                    <div class="terms">
                        <p><?= nl2br($st_data['terms']) ?></p>
                    </div>
                </li>
                <li>
                    <label class="join-chk">
                    <input type="checkbox" name="privacy_policy" id="agree03" onclick="checkAgree('', this)" value="privacy_policy"/><span>개인정보취급방침에 동의합니다</span>
                    </label>
                    <div class="terms">
                        <p><?= nl2br($st_data['privacy']) ?></p>
                    </div>
                </li>
                </ul>
            </div>
            <div class="optional-terms">
                <div class="join-top">
                    <p class="title">선택동의 약관</p>
                </div>
                <ul class="terms-wrap">
                <li>
                    <label class="join-chk">
                    <input type="checkbox" name="information_terms" id="agree04" onclick="checkAgree('', this)" value="Y"><span>마케팅 정보 수신에 동의합니다.</span>
                    </label>
                    <div class="terms">
                        <p><?= nl2br($st_data['selection']) ?></p>
                    </div>
                </li>
                </ul>
            </div>
            </fieldset>
        </div>
        <div class="btn-wrap">
            <button type="button" class="submit-btn" onclick="nextStep()">다음</button>
        </div>
        </form>
        <?php
        } else if ($step == 2) {
            // 마케팅 동의 체크
            $information_terms = $_POST['information_terms'];
            ?>
            <form name="write_form" id="join_form" method="post" action="./process.html" onsubmit="return submitWriteForm(this)">
            <input type="hidden" name="mode" value="join" />
            <input type="hidden" name="information_terms" value="<?= $information_terms ?>" />
            <div class="joinForm">
                <fieldset>
                <legend>정보입력</legend>

                <div class="join-input">
                    <label for="cp_name">사업장명</label>
                    <input type="text" name="cp_name" id="cp_name" class="required" placeholder="사업장명을 입력하세요">
                </div>

                <div class="join-input b-num">
                    <label for="mb_id">사업자등록번호 <span>(로그인 ID로 사용)</span></label>
                    <input type="text" name="mb_id" id="mb_id" class="required" placeholder="(&quot;-&quot;)없이 입력해주세요">
                    <button type="button" class="check-btn" onclick="checkId()">중복확인</button>
                </div>

                <div class="join-input">
                    <label for="mb_pw">비밀번호</label>
                    <input type="password" name="mb_pw" id="mb_pw" class="required" placeholder="비밀번호를 입력하세요">
                    <p class="help-txt">8~16자 영문, 숫자, 특수문자를 사용하세요.</p>
                    <input type="hidden" name="flag_mb_pw" value="0"/>
                </div>

                <div class="join-input">
                    <label for="mb_pw2">비밀번호 확인</label>
                    <input type="password" name="mb_pw2" id="mb_pw2" class="required" placeholder="비밀번호를 입력하세요">
                    <p class="alert-txt" id="state_mb_pw">동일한 비밀번호를 입력해주세요</p>
                </div>

                <div class="join-input">
                    <label for="mb_name">담당자명</label>
                    <input type="text" name="mb_name" id="mb_name" class="required" placeholder="담당자명을 입력해주세요">
                </div>

                <div class="join-input">
                    <label for="mb_depart">담당자 부서</label>
                    <input type="text" name="mb_depart" id="mb_depart" class="required" placeholder="부서명을 입력해주세요">
                </div>

                <div class="join-input">
                    <label for="mb_hp">담당자 휴대폰번호</label>
                    <input type="tel" name="mb_hp" id="mb_hp" class="required" placeholder="(&quot;-&quot;)없이 입력해주세요">
                    <p class="alert-txt">올바른 형식을 입력해주세요</p>
                </div>

                <div class="join-input mail-input">
                    <label for="mb_email">담당자 이메일</label>
                    <div class="mail-wrap">
                        <input type="text" name="mb_email" id="mb_email" class="required" placeholder="담당자이메일을 입력해주세요">
                        <span>@</span>
                        <select name="mb_email2" id="mb_email2" class="required" title="이메일 도메인">
                        <option value="">선택하기</option>
                        <option value="naver.com">naver.com</option>
                        <option value="gmail.com">gmail.com</option>
                        <option value="daum.net">daum.net</option>
                        <option value="nate.com">nate.com</option>
                        <option value="hanmail.net">hanmail.net</option>
                        <option value="manual">직접입력</option>
                        </select>
                        <input class="more-input" type="text" name="managerEmailCustomDomain" id="managerEmailCustomDomain" style="display: none;" placeholder="도메인 입력">
                    </div>
                    <p class="alert-txt">이메일 양식을 확인해주세요</p>
                </div>
                </fieldset>
            </div>

            <div class="btn-wrap btn-wrap02">
                <a href="./join.html" class="cancel-btn">이전</a>
                <button type="button" class="submit-btn" id="join_submit">가입하기</button>
            </div>
            </form>
            <form name="check_form" method="post" action="./process.html">
            <input type="hidden" name="flag_json" value="1"/>
            <input type="hidden" name="mode" value=""/>
            <input type="hidden" name="mb_id" value=""/>
            <input type="hidden" name="mb_pw" value=""/>
            </form>
            <?php
        } else if ($step == 3) {
            ?>
            <div class="join-end">
                <div class="img-wrap"><img src="/common/img/user/img/clap.png" alt="박수" class="of-ct"></div>
                <p class="b-txt">알림콕 회원가입이 완료되었습니다.</p>
                <p class="s-txt">로그인 후, 가입 정보를 수정하시면 <br class="mobile">맞춤 공고를 제공해 드립니다</p>
            </div>
            <div class="join-end-btn">
                <a href="../page/main.html" class="home-btn">홈으로</a>
                <a href="./login.html" class="login-btn">로그인</a>
            </div>
            <?php
        }
        ?>
    </div>
</section>
