<?php
/**
 * User > Footer 파일
 * @file    footer.inc.php
 * @author  Alpha-Edu
 */

use sFramework\FooterUser;
use sFramework\PopupUser;
use sFramework\Setting;

if (!defined('_ALPHA_')) {
    exit;
}
$oPopup = new PopupUser();
$oPopup->init();
$pu_list = $oPopup->selectDisplayList();

$oFooter = new FooterUser();
$oFooter->init();
$ft_list = $oFooter->selectDisplayList();

$oSetting = new Setting();
$oSetting->init();
$data = $oSetting->selectDetail(1);

global $layout_uri, $is_main;

if (!$is_main) {
    ?>
    </div>
    </div>
    <?php
}
?>
<style>
.layer_popup_main { display:block; position:absolute; z-index:100; background-color:#fff; margin:-300px 0 0 -300px; padding:10px 10px 10px 10px; }
.layer_popup_main.hide {display:none;}
.layer_content_main { margin-bottom:5px; overflow-y:auto; *zoom:1; }
.layer_popup_main div.popup_footer {text-align:center; font-family:"notoMedium";}
.popup_footer button {font-family:"notoMedium"; font-size:12px}

@media screen and (max-width:415px) {
    .layer_popup_main { display:none;}
}
</style>
<script>
//<![CDATA[
// 레이어팝업
function closeMainPopup(num) {
    $("#popup" + num).addClass('hide');
}

function closeMainPopupToday(num) {
    $.ajax({
        url: "process.html",
        type: "GET",
        dataType: "json",
        data: {
            flag_json: '1',
            mode: 'popup_cookie',
            pu_id: num
        },
        success: function(rs) {
            $("#popup" + num).addClass('hide');
        }
    });
}
//]]>
</script>
<!-- footer -->
<footer id="footer">
    <div class="top">
        <div class="container">
            <ul class="terms">
            <li><a href="#">개인정보처리방침</a></li>
            <li><a href="#">이용약관</a></li>
            <li><a href="#">알림콕이란?</a></li>
            <li class="mobile logo"><img class="of-ct" src="/common/img/user/logo.svg" alt="알림콕"></li>
            <li class="mobile"></li>
            <li class="mobile">
                <div class="family-site dropDown">
                    <p class="dropBtn">패밀리 사이트</p>
                    <ul class="dropCon">
                    <li><a target="_blank" href="https://www.alpha-edu.co.kr/webuser/page/main.html">알파에듀</a></li>
                    <li><a class="color-gray" target="_blank" href="https://aleasy.co.kr/site/main">알리지</a></li>
                    </ul>
                </div>
            </li>

            </ul>
            <div class="family-site dropDown pc">
                <p class="dropBtn">패밀리 사이트</p>
                <ul class="dropCon">
                <li><a target="_blank" href="https://www.alpha-edu.co.kr/webuser/page/main.html">알파에듀</a></li>
                <li><a target="_blank" href="https://aleasy.co.kr/site/main">알리지</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="btt">
        <div class="container">
            <div class="logo pc"><img class="of-ct" src="/common/img/user/logo.svg" alt="알림콕"></div>
            <div class="info">
                <ul>
                <li class="pc"><span>주식회사 : </span>알파에듀</li>
                <li class="pc"><span>대표이사 : </span>장재선</li>
                <li class="add"><span>본사 : </span>경상남도 창원시 의창구 창원대로363번길 22-33 503호</li>
                </ul>
                <ul>
                <li><span>대표전화 : </span>055-255-6364</li>
                <li class="pc"><span>Fax : </span>055-255-6369</li>
                <li class="pc"><span>E-mail : </span>alpha@alpha-edu.co.kr</li>
                </ul>
                <ul>
                <li class="pc"><span>개인정보책임자 : </span>장재선</li>
                <li class="pc"><span>사업자등록번호 : </span>4928100339</li>
                <li class="pc"><span>통신판매신고 : </span>제2017-창원의창-00043호</li>
                </ul>
                <p class="copy">Copyright 2025 ALPHAEDU All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>
<!-- // footer -->

<div id="quick">
    <p class="title pc">QUICK <br>MENU</p>
    <p class="title mobile"><img src="/common/img/user/icon/quick.svg" class="of-ct" alt="대화"><span>Quick menu</span>
    </p>
    <ul>
    <li>
        <a href="/webuser/page/main.html">
            <img src="/common/img/user/icon/quick-01.svg" class="of-ct" alt=""/><img
                src="/common/img/user/icon/quick-01-on.svg" class="of-ct" alt=""/>
            <p>메인</p>
        </a>
    </li>
    <li>
        <a href="/webuser/biz/list.html">
            <img src="/common/img/user/icon/quick-02.svg" class="of-ct" alt=""/><img
                src="/common/img/user/icon/quick-02-on.svg" class="of-ct" alt=""/>
            <p>맞춤 공고</p>
        </a>
    </li>
    <li class="pc">
        <a href="#">
            <img src="/common/img/user/icon/quick-03.svg" class="of-ct" alt=""/><img
                src="/common/img/user/icon/quick-03-on.svg" class="of-ct" alt=""/>
            <p>AI 제안서<br>사용가이드</p>
        </a>
    </li>
    <li class="mobile">
        <a href="#">
            <img src="/common/img/user/icon/quick-03-mo.svg" class="of-ct" alt=""/>
            <p>AI 제안서 사용가이드</p>
        </a>
    </li>
    </ul>
</div>
<?php
if ($is_main) {
    if (is_array($pu_list)) {
        for ($i = 0; $i < count($pu_list); $i++) {
            $ck_name = 'popup_' . $pu_list[$i]['pu_id'];
            $is_cookie = $_COOKIE[$ck_name];
            $file_list = $pu_list[$i]['file_list'];
            if ($is_cookie != 'Y') {
                if ($pu_list[$i]['pu_uri']) {
                    ?>
                    <div class="layer_popup_main" id="popup<?= $pu_list[$i]['pu_id'] ?>"
                         style="top: <?= $pu_list[$i]['pu_position_top'] ?>%; left: <?= $pu_list[$i]['pu_position_left'] ?>%;">
                        <?php
                        if ($file_list[0]['fi_id']) {
                            $file_list[0]['fi_uri'] = str_replace('/home/best1alpha/www/', '//alpha-edu.co.kr/', $file_list[0]['fi_uri']);
                            $infoNew = '//alpha-edu.co.kr/data/upload/popup/9/95/eedf1509c3120b78e15718950fff7ebc.png';
                            if ($file_list[0]['fi_uri'] == $infoNew) $file_list[0]['fi_uri'] = '//alpha-edu.co.kr/data/upload/popup/9/96/d2f137e312a4c066190df44c58efcb73.png';
                            ?>
                            <a href="<?= $pu_list[$i]['pu_uri'] ?>">
                                <div class="layer_content_main" style="">
                                    <img src="<?= $file_list[0]['fi_uri'] ?>" alt="<?= $pu_list[$i]['pu_alt'] ?>"/>
                                </div>
                            </a>
                            <?php
                        } else {
                            ?>
                            <a href="<?= $pu_list[$i]['pu_uri'] ?>">
                                <div class="layer_content_main"
                                     style="width: <?= $pu_list[$i]['pu_size_width'] ?>px; height: <?= $pu_list[$i]['pu_size_height'] ?>px">
                                    <p><?= $pu_list[$i]['pu_content'] ?></p>
                                </div>
                            </a>
                            <?php
                        } ?>
                        <div class="popup_footer">
                    <span style="float: left"><button type="button"
                                                      onclick="closeMainPopupToday(<?= $pu_list[$i]['pu_id'] ?>)">오늘 하루 열지 않기</button></span>
                            <span style="float: right"><button type="button"
                                                               onclick="closeMainPopup(<?= $pu_list[$i]['pu_id'] ?>)">닫기</button></span>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="layer_popup_main" id="popup<?= $pu_list[$i]['pu_id'] ?>"
                         style="top: <?= $pu_list[$i]['pu_position_top'] ?>%; left: <?= $pu_list[$i]['pu_position_left'] ?>%;">
                        <?php
                        if ($file_list[0]['fi_id']) {
                            $file_list[0]['fi_uri'] = str_replace('/home/best1alpha/www/', '//alpha-edu.co.kr/', $file_list[0]['fi_uri']);
                            ?>
                            <div class="layer_content_main" style="">
                                <img src="<?= $file_list[0]['fi_uri'] ?>"/>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="layer_content_main"
                                 style="width: <?= $pu_list[$i]['pu_size_width'] ?>px; height: <?= $pu_list[$i]['pu_size_height'] ?>px">
                                <p><?= $pu_list[$i]['pu_content'] ?></p>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="popup_footer">
                    <span style="float: left"><button type="button"
                                                      onclick="closeMainPopupToday(<?= $pu_list[$i]['pu_id'] ?>)">오늘 하루 열지 않기</button></span>
                            <span style="float: right"><button type="button"
                                                               onclick="closeMainPopup(<?= $pu_list[$i]['pu_id'] ?>)">닫기</button></span>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    }
}
?>

</div>

</body>
</html>
