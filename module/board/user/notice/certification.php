<?php
/**
 * @file    certification.php
 * @author  Alpha-Edu
 */

use sFramework\NoticeUser;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
/* set URI */
if ($_GET['bd_code'] == 'form') {
    $this_uri = '/webuser/form/list.html';
    $doc_title = '서식자료';
} elseif ($_GET['bd_code'] == 'research') {
    $this_uri = '/webuser/research/list.html';
    $doc_title = '연구및교육자료';
}
/* init Class */
$oBoard = new NoticeUser();
$oBoard->init();
$pk = $oBoard->get('pk');
$bd_code = $oBoard->get('bd_code');
$article_class = 'certification';
?>
<script type="text/javascript" src="/webuser/free/expansion.js"></script>
<form name="certification_form" method="post" action="<?= $return_uri ?>"
      onsubmit="return submitCertificationForm(this)">
<input type="hidden" name="mode" value="certificate"/>
<input type="hidden" name="flag_send" value=""/>
<h4>개인정보처리방침</h4>
<div class="agree_area">
    <p class="agree_guide1">
        재단법인 울산문화예술교육지원센터와의 연수·워크숍 진행과 관련하여, 본인은 개인정보보호법에 따라 아래와 같은 개인정보를 수집&middot;이용 및 제3자에게 제공하는 것에 동의합니다.
    </p>
    <dl class="agree_guide3">
    <dt>개인정보 수집&middot;이용&middot;제공 목적</dt>
    <dd>연수·워크숍의 진행(본인확인 및 연락, 서류 및 면접심사 등) 및 관리, 확정 후 발생하는 일련의 절차이행</dd>
    </dl>
    <dl class="agree_guide3">
    <dt>개인정보 수집항목</dt>
    <dd>성명, 생년월일(주민등록번호), 주소, 이메일, 휴대폰 번호 등 연락처, 비고 등 본인이 작성 및 제출한 관련정보 등 제반사항</dd>
    </dl>
    <dl class="agree_guide3">
    <dt>개인정보의 보유 및 이용 기간</dt>
    <dd>위 개인정보는 연수·워크숍 관련업무 이외의 다른 목적으로 사용하지 않으며, 본인의 동의일로부터 3년(본인 요청시 파기) 동안 위 목적을 위하여 보유 및 이용됩니다.</dd>
    </dl>
    <dl class="agree_guide3">
    <dt>동의를 거부할 권리 및 동의를 거부할 경우의 불이익 내용</dt>
    <dd>위 개인정보의 수집&middot;이용&middot;제공에 동의를 거부하실 수 있습니다. 다만, 동의하지 않을 경우 신청 제한 등의 불이익을 받을 수 있습니다.</dd>
    </dl>
</div>
<input type="checkbox" name="check_agree" id="check_agree" class="checkbox required" value="1" title="게시판 운영정책"/>
<label for="check_agree" class="agree_area_chk">본인은 위와 같이 본인의 개인정보를 수집&middot;이용&middot;제공하는 것에 동의합니다.
</label>
<div class="certification_area">
    <dl>
    <dt><label for="bd_writer_name">이름</label></dt>
    <dd><input type="text" name="bd_writer_name" id="bd_writer_name" class="required text" size="30" maxlength="10"
               title="이름"/></dd>
    </dl>
    <dl>
    <dt><label for="bd_writer_tel">휴대폰</label></dt>
    <dd>
        <input type="text" name="bd_writer_tel" id="bd_writer_tel" class="text tel required" size="30" maxlength="15"
               title="휴대폰"/>
        <span class="btn_set large"><button type="button" id="btn_send_auth"
                                            onclick="sendAuthNo()">인증번호 발송</button></span>
    </dd>
    </dl>
    <dl>
    <dt><label for="au_no">인증번호</label></dt>
    <dd>
        <input type="text" name="au_no" id="au_no" class="text readonly" size="30" maxlength="6" title="인증번호"/>
    </dd>
    </dl>
    <!-- button -->
    <div class="button center">
        <span class="btn_set xlarge"><button type="submit" id="btn_validate_auth" class="active">확인</button></span>
        <span class="btn_set xlarge"><button type="button" id="btn_validate_auth" onclick="history.back(-1);">취소</button></span>
    </div>
    <!-- //button -->
</div>
</form>
<form name="auth_form" method="post" action="./process.html">
<input type="hidden" name="flag_json" value="1"/>
<input type="hidden" name="mode" value=""/>
<input type="hidden" name="bd_writer_name" value=""/>
<input type="hidden" name="bd_writer_tel" value=""/>
<input type="hidden" name="au_no" value=""/>
</form>
