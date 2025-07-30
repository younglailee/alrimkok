<?php
/**
 * @file    login.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
global $is_member, $layout_uri, $layout, $module;
$return_uri = $_GET['return_uri'];

if ($is_member) {
    Html::alert('현재 로그인 중입니다.', $layout_uri . '/page/main.html');
}
$flag_use_header = false;
$flag_use_footer = false;
$html_title = '로그인 :: ' . _HOMEPAGE_TITLE_;
$ck_save_id = Session::getCookie('ck_save_id_' . $layout);
//echo Format::encryptString('a1234');
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $("#login_id").focus();
});
//]]>
</script>
</head>
<body>
<div id="<?= $module ?>">
    <div id="login_box">
        <h3><a href="./login.html"><img src="./img/h3_login.gif" alt="Administrator LOGIN"/></a></h3>
        <p class="comment">
            로그인 후 이용하실 수 있습니다.<br/>
            발급받은 아이디/패스워드를 입력하신 후 <strong>로그인 버튼</strong>을 클릭해주세요.
        </p>
        <div class="form_box">
            <form name="login_form" method="post" action="./process.html" onsubmit="return submitLoginForm(this)">
            <input type="hidden" name="mode" value="login"/>
            <input type="hidden" name="return_uri" value="<?= $return_uri ?>"/>
            <ul>
            <li class="login_id">
                <input type="text" name="login_id" id="login_id" value="<?= $ck_save_id ?>"
                       class="text required placeholder" size="20" maxlength="20" title="아이디"/>
            </li>
            <li class="login_pw">
                <input type="password" name="login_pw" id="login_pw" class="text required placeholder" size="20"
                       maxlength="20" title="비밀번호"/>
            </li>
            </ul>
            <div class="btn_login">
                <p>
                    <input type="image" src="./img/btn_login.gif" alt="LOGIN" title="Login"/>
                </p>
            </div>
            <div class="auto_login">
                <input type="checkbox" name="flag_save_id" id="flag_save_id" value="1"
                        <?= ($ck_save_id) ? 'checked="checked"' : '' ?>
                <label for="flag_save_id">아이디 저장</label>
            </div>
            </form>
        </div>
        <div class="copyright">
            <p>Copyright © <strong><?= _HOMEPAGE_TITLE_ ?></strong> All Rights Reserved.</p>
        </div>
    </div>
</div>
</body>
</html>