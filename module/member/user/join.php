<?php
/**
 * @file    intranet.php
 * @author  Alpha-Edu
 */

//$_SESSION = array();
//include_once(_PLUGIN_PATH_ . '/nice_cert/checkplus_main.php'); // 나이스 휴대폰 본인인증 설정파일
//include_once(_PLUGIN_PATH_ . '/nice_ipin/ipin_main.php'); // 나이스 아이핀 본인인증 설정파일
use sFramework\Html;
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
                        <p>
※ 알파에듀평생교육원의 온라인교육(사업주훈련 및 국민내일배움카드, 평생교육바우처 포함)에 참여하고자 하는 모든 수강생은 「국민 평생 직업능력 개발법」에 의거한 아래의 “개인정보의 수집 및 제공에 관한 동의서”에 동의합니다.<br>
※ 귀하는 개인정보 수집 ･ 이용에 동의하지 않거나 동의한 것을 철회할 권리가 있으며, 동의를 거부(철회)할 경우에는 해당 서비스를 제공받을 수 없습니다.<br>
<br>
[사업주훈련 개인정보의 수집 및 제공에 관한 동의서]<br>
1. 고용노동부에서는 근로자 직업능력개발훈련 지원제도 운영에 있어 개인을 고유하게 구별하기 위해 부여된 식별정보(주민등록번호 등)를 포함한 개인정보를 다음과 같이 직업능력개발정보망(HRD-Net)에 수집ㆍ이용하고 있습니다.<br>
⚪ 개인정보의 수집ㆍ이용 목적: 훈련비용 지원, 개인별 훈련이력 관리, 정부의 직업능력개발훈련제도실적ㆍ성과 평가, 모니터링(훈련 수강 안내) 등에 활용<br>
⚪ 수집하는 개인정보 항목: 성명, 주민등록번호(필수)/휴대전화번호<br>
⚪ 개인정보의 보유 및 이용기간: 직업능력개발정보망(HRD-Net)에서 수집ㆍ계속 관리<br>
※ 개인정보 수집ㆍ이용에 동의하지 않을 수 있으나, 동의를 거부할 경우에는 해당 서비스를 제공을 받을 수 없습니다.<br>
2. 사업주 직업능력개발훈련 지원제도 운영을 위해서는 개인을 고유하게 구별하기 위해 부여된 식별정보(주민등록번호 등)를 포함한 개인정보가 필요하며, 고용노동부는 「개인정보 보호법」에 따라 훈련생으로부터 제공받는 개인정보를 보호하여야 합니다.<br>
3. 고용노동부는 개인정보를 처리 목적에 필요한 범위에서 적합하게 처리하고 그 목적 외의 용도로 사용하지 않으며 개인정보를 제공한 훈련생은 언제나 자신이 입력한 개인정보의 열람ㆍ수정을 신청할 수 있습니다. 다만, 훈련실시 및 비용지원 등에 관한 정보는 오류가 있는 경우를 제외하고는 수정ㆍ삭제를 요청할 수 없습니다.<br>
4. 본인은 위 1.~ 3.의 내용에 따른 사업주 직업능력개발훈련 지원제도 운영을 위해 개인식별정보(주민등록번호 등)를 제공할 것을 동의합니다.<br>
<br>
[근로시간 외 훈련 동의서]<br>
국민 평생 직업능력 개발법 제9조에 근거하여 근로시간에 위와 같이 직업능력개발훈련을 하는 것에 대하여 동의하며 별도의 수당 등 일체의 조건이 없음을 서약합니다.<br>
※ 국민 평생 직업능력 개발법 제9조(훈련계약과 권리의무)<br>
④ 제1항에 따른 훈련계약을 체결하지 아니한 사업주는 직업능력개발훈련을 「근로기준법」 제50조에 따른 근로시간(이하 “기준근로시간”이라 한다) 내에 실시하되, 해당 근로자와 합의한 경우에는 기준근로시간 외의 시간에 직업능력개발훈련을 실시할 수 있다.<br>
⑤ 기준근로시간 외의 훈련시간에 대하여는 생산시설을 이용하거나 근무장소에서 하는 직업능력개발훈련의 경우를 제외하고는 연장근로와 야간근로에 해당하는 임금을 지급하지 아니할 수 있다.<br>
<br>
[국민내일배움카드 개인정보의 수집 및 제공에 관한 동의서]<br>
알파에듀평생교육원은 「국민 평생 직업능력 개발법」 및 「고용보험법」에 따른 훈련비 등을 지원받을 수 있는 직업능력개발훈련을 실시함에 있어 「국민내일배움카드 운영규정」 제34조제1항에 따라 훈련생의 출결정보를 관리하기 위하여 다음과 같이 개인정보를 수집ㆍ이용합니다.<br>
⚪ 수집항목 : 일반개인정보(성명, 생년월일)<br>
⚪ 수집목적 : 훈련생 출결정보 관리, 훈련비용의 신청 등<br>
⚪ 보유기간 : 훈련종료 후 5년까지<br>
※ 귀하는 개인정보 수집ㆍ이용에 동의하지 않을 권리가 있으며, 동의를 거부할 경우에는 귀하의 출결정보를 관리할 수가 없으므로 훈련과정 수강에 따른 훈련비 등을 지원받지 못할 수 있습니다.<br>
<br>
[평생교육바우처 개인정보의 수집 및 제공에 관한 동의서]<br>
1. 개인정보 수집ㆍ이용 동의<br>
⚪ 항목 : 성명, 집주소, 대상유형, 결과통지방법, 카드번호, 핸드폰(연락처), 전자우편주소, 문자 메시지 또는 전자우편 수신동의 여부<br>
⚪ 수집ㆍ이용 목적 : 평생교육이용권 발급 및 사업 운영 사무<br>
⚪ 보유기간 : 5년<br>
2. 개인정보 수집ㆍ이용 동의<br>
⚪ 항목 : 회원 계정정보(ID,비밀번호), 암호화된 이용자 확인 값(CI), 회원가입동의 여부, 평생학습계좌번호<br>
⚪ 수집ㆍ이용 목적 <br>
- 평생학습계좌제 학습계좌 발급 및 학습이력관리<br>
- 평생교육이용권 홈페이지 회원관리<br>
⚪ 보유기간 : 5년, 회원탈퇴 시<br>
3. 개인정보 제3자 제공 동의<br>
⚪ 제공받는자 : 「평생교육법」 제16조의2제3항에 따른 관계 중앙행정기관의 장 및 지방자치단체의 장, 관계 기관 및 단체의 장, 발급위탁금융기관<br>
⚪ 개인정보 제공목적<br>
- 이용자 선정을 위한 정보제공<br>
- 평생교육이용권 발급 자격확인<br>
- 평생교육이용권 한도금액 부여<br>
⚪ 개인정보 제공 항목 : 성명, 주민등록번호, 평생교육이용권 발급 여부<br>
⚪ 보유 및 이용기간 : 목적달성시 까지<br>
4. 고유식별정보 제공 고지<br>
⚪ 항목 : 주민등록번호<br>
⚪ 개인정보 처리목적 : 평생교육이용권 발급 및 사업 운영 사무<br>
⚪ 보유기간 : 5년<br>
⚪ 수집근거 <br>
- 「평생교육법 시행령」 제77조의3(고유식별정보의 처리)<br>
- 「개인정보 보호법」 제24조(고유식별정보의 처리 제한)<br>
※ 위의 개인정보 수집ㆍ이용에 대한 동의를 거부할 권리가 있으며, 동의 거부 시 평생교육이용권 신청ㆍ선정ㆍ지급에 제한을 받을 수 있습니다.<br>
<br>
※ 알파에듀평생교육원은 「개인정보 보호법」에 따라 정보주체의 사전 동의 없이 개인정보를 제3자에게 제공하지 않으며, 개인정보를 제공한 훈련생은 자신에 관한 개인정보를 열람ㆍ수정하거나 개인정보의 수집ㆍ이용에 대한 철회를 할 수 있습니다.
                        </p>
                    </div>
                </li>
                <li>
                    <label class="join-chk">
                    <input type="checkbox" name="site_terms" id="agree02" onclick="checkAgree('', this)" value="site_terms"/><span>사이트 이용 약관에 동의합니다</span>
                    </label>
                    <div class="terms">
                        <p>
㈜알파에듀(이하 회사라 칭합니다)는 회사 홈페이지(http://www.alpha-edu.co.kr)의 이용약관을 다음과 같이 제정합니다.<br>
<br>
제1장 총칙<br>
<br>
제1조(목적)<br>
본 이용약관(이하 약관이라 칭합니다)은 회사와 이용 고객(이하 회원이라 칭합니다)간에 회사가 제공하는 서비스의 가입조건 및 이용에 관한 제반 사항 등을 구체적으로 규정함을 목적으로 합니다.<br>
<br>
제2조(약관의 효력 및 변경)<br>
(1) 이 약관의 내용은 화면에 게시하거나 기타 방법으로 회원에게 공지함으로써 그 효력이 발생합니다.<br>
(2) 회사는 이 약관을 사정에 의거 변경할 수 있습니다.변경된 사항은 전항과 같은 방법으로 공지함으로써 그 효력이 발생합니다.<br>
<br>
제3조(약관 외 준칙)<br>
이 약관에 명시되지 아니한 사항에 대해서는 관계법령의 규정에 따릅니다.<br>
<br>
제2장 회원가입 및 이용제한<br>
<br>
제4조 (회원가입의 성립)<br>
(1) 회원은 회사가 정한 가입 양식에 따라 회원정보를 제공한 후 약관 준수에 동의함으로써 회원가입을 신청합니다.<br>
(2) 회사는 다음 각 호에 해당하지 않는 한 회원으로 등록합니다.<br>
가. 타인의 명의를 사용하여 신청한 경우<br>
나. 등록 내용에 허위, 기재누락, 오기가 있는 경우<br>
다. 악의적인 목적으로 신청한 경우<br>
라. 기타 회사가 정한 회원가입 요건을 충족하지 못한 경우<br>
<br>
제5조 (가입시 신상정보의 변경)<br>
회원은 회원가입시 제공한 정보가 변경되었을 경우,온라인 등을 활용하여 수정해야 합니다.<br>
<br>
제6조 (회원가입 해지 및 이용제한)<br>
(1) 회원은 언제든지 해지를 요청할 수 있으며, 회사는 즉시 해지 처리해야 합니다.<br>
(2) 회원이 다음 각 호의 사유에 해당하는 경우, 회원자격을 제한 및 정지시킬 수 있습니다.<br>
가. 타인의 주민등록번호를 도용한 경우<br>
나. 타인의 ID 및 비밀번호를 도용한 경우<br>
다. 서비스 운영을 고의로 방해한 경우<br>
라. 가입한 이름이 실명이 아닌 경우<br>
마. 공공질서 및 미풍양속에 저해되는 내용을 고의로 유포시키는 경우<br>
바. 범죄적 행위에 관련된 경우<br>
사. 타인의 명예를 손상시키거나 불이익을 주는 경우<br>
아. 기타 관계법령이나 회사가 정한 이용 조건에 위배되는 경우<br>
(3) 전 항에 의하여 이용 제한이 된 회원은 이의 제기 신청을 할 수 있습니다.<br>
(4) 회사가 회원 자격을 상실시키는 경우에는 회원에게 통지 후 강제적으로 해지합니다. 해지 전에는 약 30일간의 이의제기 신청 기간을 정하여 소명할 기회를 제공합니다.<br>
<br>
제3장 서비스의 이용<br>
<br>
제7조 (서비스 이용 및 제한)<br>
(1) 서비스 이용은 연중무휴, 1일 24시간 운영을 원칙으로 합니다.<br>
(2) 회사는 다음 각 호에 해당하는 경우 서비스 제공을 제한할 수 있습니다.<br>
가. 긴급한 시스템 점검,증설 및 교체 등 부득이한 사유로 인하여 중단되는 경우<br>
나. 국가비상사태,정전,서비스 설비의 장애 또는 서비스 이용의 폭주 등이 발생한 경우<br>
다. 전기통신사업법에 규정된 기간통신사업자가 전기통신 서비스를 중단했을 경우<br>
라. 기타 회사가 통제할 수 없는 사유로 인하여 서비스가 중단되는 경우<br>
(3) 전항이 발생한 경우,회사는 홈페이지 내 게시판을 통해 사전 또는 사후에 공지합니다.다만,회사의 판단에 따라 그러하지 아니할 수 있습니다.<br>
<br>
제8조 (서비스 제공)<br>
(1) 회사는 다음과 같은 업무를 수행합니다.<br>
가. 재화 또는 용역에 대한 정보 제공 및 구매계약의 체결<br>
나. 구매계약이 체결된 재화 또는 용역의 배송<br>
다. 기타 회사가 정하는 업무<br>
(2) 회사는 재화의 품절 또는 기술적 사양의 변경 등의 경우에는 장차 체결되는 계약에 의해 제공할 재화, 용역의 내용을 변경할 수 있습니다. 이 경우에는 변경된 재화, 용역의 내용 및 제공일자를 명시하여 현재의 재화, 용역의 내용을 게시한 곳에 즉시 공지합니다.<br>
(3) 회사는 전항으로 인하여 회원이 입은 손해를 배상합니다.다만,회사가 고의 또는 과실이 없는 경우에는 그러하지 아니합니다.<br>
<br>
제9조 (회원ID 관리)<br>
(1) 회원ID와 비밀번호에 관한 모든 관리책임은 회원에게 있습니다.<br>
(2) 자신의 ID가 부정하게 사용된 경우,회원은 반드시 그 사실을 회사에 통보해야 합니다.<br>
(3) 회사는 회원ID에 의하여 제반 회원 관리업무를 수행하므로 회원은 ID를 변경하고자 하는 경우 회사가 인정할 만한 사유가 없는 한 변경을 제한할 수 있습니다.<br>
(4) 회원이 등록한 ID 및 비밀번호에 의하여 발생되는 사용상의 과실 또는 제3자에 의한 부정사용 등에 대한 모든 책임은 해당 회원에게 있습니다.<br>
<br>
제10조 (게시물의 제한)<br>
회사는 다음 각 호에 해당하는 게시물이나 자료를 사전통지 없이 삭제 할 수 있습니다.<br>
가. 타인을 비방하거나 중상모략으로 개인 및 단체의 명예를 손상시키는 내용인 경우<br>
나. 공공질서 및 미풍양속에 위반되는 내용을 유포하거나 링크시키는 경우<br>
다. 불법복제 또는 해킹을 조장하는 내용인 경우<br>
라. 영리를 목적으로 하는 광고일 경우<br>
마. 범죄적 행위에 부합된다고 인정되는 내용인 경우<br>
바. 타인의 저작권 등 기타의 권리를 침해하는 내용인 경우<br>
사. 회사에서 규정한 게시물 원칙에 어긋나거나,게시판 성격에 부합하지 않는 경우<br>
아. 기타 관계법령에 위배된다고 판단되는 경우<br>
<br>
제11조 (게시물에 대한 저작권)<br>
(1) 회원이 서비스 화면 내에 게시한 게시물의 저작권은 게시한 회원에게 귀속됩니다.또한 회사는 게시자의 동의 없이 게시물을 상업적으로 이용할 수 없습니다.다만 비영리 목적인 경우는 그러하지 아니합니다.<br>
(2) 회원은 서비스를 이용하여 취득한 정보를 임의 가공,판매하는 행위 등 서비스에 게재된 자료를 상업적으로 사용할 수 없습니다.<br>
(3) 회사는 회원이 게시하거나 등록하는 서비스 내의 내용물,게시 내용에 대해 제10조 각 호에 해당된다고 판단되는 경우 사전통지 없이 삭제할 수 있습니다.<br>
<br>
제12조 (정보 및 광고의 제공)<br>
(1) 회사는 회원에게 서비스 이용에 필요가 있다고 인정되는 각종 정보에 대해서 문자발신 등의 방법으로 회원에게 제공할 수 있습니다.<br>
(2) 회사는 서비스 개선 및 회원 대상의 서비스 소개 등의 목적으로 회원의 동의 하에 추가적인 개인 정보를 요구할 수 있습니다.<br>
<br>
제4장 의무<br>
<br>
제13조 (회사의 의무)<br>
(1) 회사는 회원이 희망한 서비스 제공 개시일에 특별한 사정이 없는 한 서비스를 이용할 수 있도록 하여야 합니다.<br>
(2) 회사는 본 약관에서 정한 바에 따라 지속적이고 안정적인 서비스를 제공하는데 최선을 다하여야 합니다.<br>
(3) 회사는 본 서비스와 관련한 회원의 신상정보를 어떠한 경우에도 회원의 동의 없이 제3자에게 누설,배포, 양도하지 아니합니다.<br>
(4) 회사는 개인정보 보호를 위해 보안시스템을 갖추어 개인정보 보호정책을 준수합니다.<br>
(5) 회사는 회원으로부터 제기되는 서비스에 대한의견,불만 등을 적절한 절차를 거쳐 처리하며,처리시 일정 기간이 소요될 경우 회원에게 그 사유와 처리 일정을 통보합니다.<br>
<br>
제14조 (회원의 의무)<br>
(1) 회원은 자신의 ID와 비밀번호에 관한 관리 책임을 집니다.<br>
(2) 회원은 회원가입 신청 또는 회원정보 변경 시 실명으로 모든 사항을 사실에 근거하여 작성하여야 하며,허위 또는 타인의 정보를 등록할 경우 일체의 권리를 주장할 수 없습니다.<br>
(3) 회원은 본 약관에서 규정하는 사항과 기타 회사가 정한 제반 규정 및 관계 법령을 준수하여야 하며,기타 회사의 업무에 방해가 되는 행위 또는 회사의 명예를 손상시키는 행위를 해서는 안됩니다.<br>
(4) 회원은 회원가입 시 등록된 정보가 변경되었을 경우 즉시 회사에 알려야 합니다.<br>
(5) 회원은 회사의 명시적 동의가 없는 한 서비스의 이용 권한, 기타 이용 계약 상의 지위를 타인에게 양도,증여할 수 없으며 이를 담보로 제공할 수 없습니다.<br>
(6) 회원은 회사 및 제3자의 지적 재산권을 침해해서는 안됩니다.<br>
(7) 회원은 바이러스 제작 및 배포,해킹 등 범죄와 결부되는 행위를 하여서는 안됩니다.<br>
(8) 회원은 기타 관계 법령에 위배되는 행위를 하여서는 안됩니다.<br>
<br>
제5장 손해배상 및 기타사항<br>
<br>
제15조 손해배상<br>
(1) 회사는 서비스에서 무료로 제공하는 서비스의 이용과 관련하여 개인정보보호정책에서 정하는 내용에 해당하지 않는 사항에 대하여는 어떠한 손해도 책임을 지지 않습니다.<br>
(2) 회사는 콘텐츠의 하자, 이용 중지 또는 장애 등에 의하여 발생한 이용자의 손해에 대하여 자사 환불 및 취소 규정에 따라 처리합니다.<br>
<br>
제16조 면책조항<br>
(1) 회사는 천재지변, 전쟁 및 기타 이에 준하는 불가항력으로 인하여 서비스를 제공할 수 없는 경우에는 서비스 제공에 대한 책임이 면제됩니다.<br>
(2) 회사는 기간통신 사업자가 전기통신 서비스를 중지하거나 정상적으로 제공하지 아니하여 손해가 발생한 경우 책임이 면제됩니다.<br>
(3) 회사는 서비스용 설비의 보수, 교체, 정기점검, 공사 등 부득이한 사유로 발생한 손해에 대한 책임이 면제됩니다.<br>
(4) 회사는 회원의 귀책사유로 인한 서비스 이용의 장애 또는 손해에 대하여 책임을 지지 않습니다.<br>
(5) 회사는 이용자의 컴퓨터 오류에 의해 손해가 발생한 경우, 또는 회원이 신상정보 등을 부실하게 기재하여 손해가 발생한 경우 책임을 지지 않습니다.<br>
(6) 회사는 회원이 서비스를 이용하여 기대하는 수익을 얻지 못하거나 상실한 것에 대하여 책임을 지지 않습니다.<br>
(7) 회사는 회원이 서비스를 이용하면서 얻은 자료로 인한 손해에 대하여 책임을 지지 않습니다. 또한 회사는 회원이 서비스를 이용하며 타 회원으로 인해 입게 되는 정신적 피해에 대하여 보상할 책임을 지지 않습니다.<br>
(8) 회사는 회원이 서비스에 게재한 각종 정보, 자료, 사실의 신뢰도, 정확성 등 내용에 대하여 책임을 지지 않습니다.<br>
(9) 회사는 이용자 상호간 및 이용자와 제 3자 상호 간에 서비스를 매개로 발생한 분쟁에 대해 개입할 의무가 없으며, 이로 인한 손해를 배상할 책임도 없습니다.<br>
(10) 회사에서 회원에게 무료로 제공하는 서비스의 이용과 관련해서는 어떠한 손해도 책임을 지지 않습니다.<br>
<br>
제17조 분쟁해결<br>
(1) 회사는 회원으로부터 제기되는 의견 및 불만사항은 우선적으로 처리합니다.다만,신속한 처리가 어려운 경우에는 회원에게 그 사유와 처리 일정을 통보합니다.<br>
(2) 회원과 회사간에 발생한 분쟁은 전자거래기본법 제28조 및 동 시행령 제15조에 의하여 설치된 전자거래분쟁조정위원회의 조정에 따를 수 있습니다.<br>
<br>
제18조 재판권 및 준거법<br>
(1) 서비스 이용으로 발생한 분쟁에 대해 소송이 제기되는 경우 회사의 본사 소재지를 관할하는 법원을 관할 법원으로 합니다.<br>
(2) 회원과 회사간에 제기된 전자상거래 소송에는 대한민국법을 적용합니다.<br>
<br>
부칙<br>
(1) 본 약관은 2025년 01월 02일부터 적용됩니다.<br>
(2) 2020년 11월 02일부터 시행되던 종전의 약관은 본 약관으로 대체합니다.<br>
변경공고일자 : 2024년 12월 01일<br>
시행일자 : 2025년 01월 02일
                        </p>
                    </div>
                </li>
                <li>
                    <label class="join-chk">
                    <input type="checkbox" name="privacy_policy" id="agree03" onclick="checkAgree('', this)" value="privacy_policy"/><span>개인정보취급방침에 동의합니다</span>
                    </label>
                    <div class="terms">
                        <p>
㈜알파에듀(이하 ‘회사’라 칭합니다)는 회원의 개인정보를 중요시하며, ‘정보통신망 이용 촉진 및 정보보호 등에 관한 법률’ 및 ‘개인정보보호법’ 등 회사가 준수하여야 할 관련 법령상의 개인정보보호 규정을 준수하고 있습니다.<br>
<br>
1. 수집하는 개인정보의 항목<br>
회사는 회원가입, 상담, 서비스 신청 등을 위해 아래와 같은 개인정보를 수집하고 있으며 개인정보 수집 방법에 따라 수집 항목이 추가 또는 삭제될 수 있습니다.<br>
(1) 필수항목<br>
가. 이름, 아이디, 생년월일, 휴대전화번호, SMS 수신여부, 이메일 수신여부<br>
나. (고용노동부 환급 과정) 주민등록번호/외국인등록번호, 회사명, 전화번호, 사업자등록번호, 훈련생구분, 비정규직 구분<br>
다. (내일배움카드 과정) 구분, 신청 정보, 카드번호, 결제 기록<br>
※ 근거법령 : 근로자직업능력개발법 시행령 제52조 제2항 및 시행규칙 제7조 제2항<br>
라. (평생교육바우처 과정) 신청 정보, 카드번호, 결제 기록<br>
(2) 선택항목 : 주소, 이메일, 부서, 직급/직책, 사번, 팩스번호, 마케팅 활용 동의<br>
(3) 기타항목 : 서비스 이용기록, 쿠키, 접속로그, IP 정보, 결제 기록 등<br>
<br>
2. 개인정보의 수집 및 이용목적<br>
(1) 회사는 개인정보를 다음의 방법으로 수집합니다.<br>
가. 회원가입<br>
나. 전화, 팩스, 메일을 통한 회원가입<br>
다. 기타 방법을 통한 회원가입<br>
(2) 회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.<br>
가. 서비스 이용 계약 이행 및 서비스 제공에 따른 요금정산 : 콘텐츠 제공, 구매, 배송 등<br>
나. 교육운영 및 회원관리 : 본인확인, 수강안내, 개인 식별, 성적제공, 민원처리, 공지 전달, 성취도 확인, 사후관리 등<br>
※ 훈련에 참여한 모든 회원을 대상으로 수신여부와 관계없이 학습관련 문자가 발송됩니다.<br>
다. 마케팅 및 광고에 활용 : 신규 서비스(제품) 개발 및 특화, 이벤트 등 광고성 정보 전달<br>
라. 고용보험 환급 관련 업무<br>
마. 평생교육바우처 관련 업무<br>
<br>
3. 수집한 개인정보의 제3자 제공<br>
회사는 회원의 개인정보를 원칙적으로 외부에 제공하지 않습니다. 다만, 아래의 경우에는 예외로 합니다.<br>
(1) 회원이 사전에 동의한 경우<br>
가. 고용노동부, 한국산업인력공단<br>
① 이용목적 및 제공항목<br>
- 고용보험신고 (이름,주민등록번호,휴대전화번호,이메일,소속기업)<br>
- 직업능력개발훈련 모니터링 (이메일,접 속로그(IP. MAC ADDRESS, HARD DISK SERIAL), 학습정보운영체제 종류,브라우저 종류,스크린 해상도,기타 속성 값)<br>
② 보유 및 이용기간 : 3년<br>
나. nice신용평가정보㈜<br>
① 이용목적 및 제공항목<br>
- 실명확인,본인인증 (이름,주민등록번호)<br>
② 보유 및 이용기간 : 서비스 제공 기간<br>
다. 「평생교육법」 제16조의2제3항에 따른 관계 중앙행정기관의 장 및 지방자치단체의 장, 관계 기관 및 단체의장, 발급위탁금융기관<br>
① 이용목적<br>
- 이용자 선정을 위한 정보제공<br>
- 평생교육이용권 발급 자격확인<br>
- 평생교육이용권 한도금액 부여<br>
② 제공항목 <br>
- 성명, 주민등록번호, 평생교육이용권 발급 여부<br>
③ 보유 및 이용기간 : 목적달성시 까지<br>
라. 기타 사전에 동의한 관계 기관<br>
(2) 법령의 규정에 의거하거나, 수사 목적으로 법령에 정해진 절차와 방법에 따라 수사기관의 요구가 있는 경우<br>
<br>
4. ACS(훈련생 수강확인 문자발송 서비스) 안내<br>
한국산업인력공단에서 원격훈련 모니터링과 관련하여 훈련실시 여부 등을 확인하는 문자를 발송합니다.<br>
(1) 문자내용<br>
- 한국산업인력공단 훈련품질향상센터에서 OOO님께 OO년 O월, OO과정의 원격훈련(이러닝) 수강여부를 확인중입니다. 위의 수강내용이 맞으면 숫자 ‘1’, 틀리면 숫자 ‘2’를 OO까지 문자로 회신 주시면 감사하겠습니다. (이하생략)<br>
(2) 관련규정<br>
가. 휴대전화번호 수집 규정<br>
① 국민평생직업능력개발법 제24조<br>
② 사업주직업능력개발훈련 지원규정 제7조 제1항 제1조 별지 제2호 서식<br>
나. 모니터링 대상 규정<br>
① 직업능력개발훈련 모니터링업무지침 제2조<br>
다. 휴대전화번호 모니터링 관련 사용 규정<br>
① 직업능력개발훈련 모니터링에 관한 규정 제2조<br>
<br>
5. 개인정보의 보유 및 이용기간<br>
회사는 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다.<br>
단, 다음의 정보에 대해서는 아래의 이유로 명시한 기간 동안 보존합니다.<br>
(1) 웹사이트 접속 및 이용 기록 : 6개월 (통신비밀보호법)<br>
(2) 계약 또는 청약철회 등에 관한 기록 : 5년 (전자상거래 등에서의 소비자보호에 관한 법률)<br>
(3) 대금결제 및 제화 등의 공급에 관한 기록 : 5년 (전자상거래 등에서의 소비자보호에 관한 법률)<br>
(4) 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년 (전자상거래 등에서의 소비자보호에 관한 법률)<br>
(5) 훈련 실시에 관한 기록 : 3년 (근로자직업능력개발법)<br>
(6) 고용보험 환급 대상 주민등록번호 : 비용지원 종료 후 3년 (근로자직업능력개발법)<br>
(7) 로그인 기록 : 3개월 (통신비밀보호법)<br>
<br>
6. 개인정보 자동 수집 장치의 설치/운영 및 거부에 관한 사항<br>
회사는 회원들에게 맞춤서비스를 제공하기 위해서 회원의 정보를 저장하고 수시로 불러오는 ‘쿠키(Cookie)’를 사용합니다. 쿠키는 웹사이트를 운영하는데 이용되는 서버(HTTP)가 이용자의 컴퓨터 브라우저에게 보내는 소량의 정보이며 회원들의 PC컴퓨터 내의 하드디스크에 저장되기도 합니다.<br>
(1) 쿠키의 사용 목적<br>
회원의 웹사이트 방문 및 이용형태, 보안접속 여부 등을 통하여 최적화된 정보 제공을 위하여 사용합니다.<br>
(2) 쿠키의 설치/운영 및 거부<br>
가. 이용자는 쿠키 설치에 대한 선택권을 가지고 있습니다. 따라서, 이용자는 웹브라우저에서 옵션을 설정함으로써 모든 쿠키를 허용하거나, 쿠키가 저장될 때마다 확인을 거치거나, 아니면 모든 쿠키의 저장을 거부할 수도 있습니다.<br>
나. 쿠키 설정을 거부하는 방법으로는 이용자가 사용하는 웹 브라우저의 옵션을 선택함으로써 모든 쿠키를 허용하거나 쿠키를 저장할 때마다 확인을 거치거나, 모든 쿠키의 저장을 거부할 수 있습니다.<br>
다. 설정방법 예(인터넷 익스플로어의 경우) : 웹 브라우저 상단의 도구 &gt; 인터넷 옵션 &gt; 개인정보<br>
라. 다만, 쿠키의 저장을 거부할 경우에는 로그인이 필요한 일부 서비스는 이용에 어려움이 있을 수 있습니다.<br>
<br>
7. 개인정보의 파기 절차 및 방법<br>
회사는 원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체없이 파기합니다. 파기절차 및 방법은 다음과 같습니다.<br>
(1) 파기절차<br>
회원의 회원가입 등을 위해 입력하신 정보는 목적이 달성된 후 별도의 DB로 옮겨져(종이의 경우 별도의 서류함) 내부 방침 및 기타 관련 법령에 의한 정보보호 사유에 따라(보유 및 이용기간 참조) 일정 기간 저장된 후 파기됩니다. 별도 DB로 옮겨진 개인정보는 법률에 의한 경우가 아니고서는 다른 목적으로 이용되지 않습니다.<br>
(2) 파기방법<br>
가. 전자적 파일형태로 저장된 개인정보는 기록을 재생할 수 없는 기술적 방법을 사용하여 삭제합니다.<br>
나. 종이에 출력된 개인정보는 분쇄기로 분쇄하거나 소각을 통하여 파기합니다.<br>
<br>
8. 개인정보 안전성 확보조치에 관한 사항<br>
회사는 회원의 개인정보를 보호하기 위해 개인정보가 분실, 도난, 누출, 변조 또는 훼손되지 않도록 안전성 확보를 위하여 다음과 같은 대책을 시행하고 있습니다.<br>
(1) 내부관리계획의 수립 및 시행<br>
(2) 개인정보 암호화<br>
(3) 해킹 등을 대비한 방화벽 설치<br>
(4) 개인정보 취급자의 최소화 및 교육<br>
(5) 개인정보처리 시스템 접근제한<br>
<br>
9. 회원 및 법정대리인의 권리와 의무 및 그 행사방법<br>
(1) 회원 및 법정 대리인은 언제든지 등록되어 있는 자신 혹은 당해 만 14세 미만 아동의 개인정보를 조회하거나 수정할 수 있으며, 가입해지를 요청할 수 있습니다.<br>
(2) 회사는 회원 및 법정 대리인의 요청에 의해 해지 또는 삭제된 개인정보는 “회사가 수집하는 개인정보의 보유 및 이용기간”에 명시된 바에 따라 처리하고 그 외의 용도로 열람 또는 이용할 수 없도록 처리하고 있습니다.<br>
<br>
10. 개인정보에 관한 민원 서비스<br>
회사는 고객의 개인정보를 보호하고 개인정보와 관련한 불만을 처리하기 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다.<br>
▶ 개인정보 보호책임자 : 장재선<br>
- 소속/지위 : ㈜알파에듀 대표이사<br>
- 전화번호 : 055-255-6364<br>
- 이메일주소 : alpha@alpha-edu.co.kr<br>
▶ 개인정보 보호담당자 : 이영래<br>
- 소속/지위 : 연구개발팀 책임연구원<br>
- 전화번호 : 070-4941-3825<br>
- 이메일주소 : develop@alpha-edu.co.kr<br>
회사의 서비스를 이용하면서 발생하는 모든 개인정보보호 관련 민원을 개인정보 보호책임자 또는 담당자에게 신고할 수 있습니다. 회사는 신고사항에 대해 신속하게 답변 드릴 것입니다.<br>
▶ 기타 개인정보침해에 대한 신고나 상담이 필요한 경우에는 아래 기관에 문의 바랍니다.<br>
(1) 개인정보 분쟁조정 위원회 : www.kopico.go.kr / 1833-6972<br>
(2) 개인정보침해 신고센터 : privacy.kisa.or.kr / 국번없이 118<br>
(3) 대검찰청 사이버수사과 : www.spo.go.kr / 국번없이 1301<br>
(4) 경찰청 사이버안전국 : cyberbureau.police.go.kr / 국번없이 182<br>
<br>
부칙<br>
(1) 본 방침은 2025년 01월 02일부터 적용됩니다.<br>
(2) 2020년 11월 02일부터 시행되던 종전의 방침은 본 방침으로 대체합니다.<br>
변경공고일자 : 2024년 12월 01일<br>
시행일자 : 2025년 01월 02일
                        </p>
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
                        <p>
개인정보보호법 제22조 제4항에 의해 선택정보 사항에 대해서는 기재하지 않으셔도 서비스를 이용하실 수 있습니다.<br><br>
1. 마케팅 및 광고에의 활용신규 서비스(제품) 개발 및 맞춤 서비스 제공, 이벤트 및 광고성 정보 제공 및 참여기회 제공, 인구통계학적 특성에 따른 서비스 제공 및
광고 게재, 서비스의 유효성 확인, 접속빈도 파악 또는 회원의 서비스 이용에 대한 통계 등을 목적으로 개인정보를 처리합니다.<br><br>
2. (주)알파에듀는 서비스를 운용함에 있어 각종 정보를 서비스 화면, 전화, email, SMS, 우편물, 앱푸시 등의 방법으로 회원에게 제공할 수
있습니다.<br><br>
3. 의무적으로 안내되어야 하는 수강독려, 개강안내 등의 정보성 내용은 수신동의 여부와 무관하게 제공됩니다.
                        </p>
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
