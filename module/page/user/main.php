<?php
/**
 * @file    main.php
 * @author  Alpha-Edu
 */

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/

use sFramework\BizUser;
use sFramework\Html;
use sFramework\Session;

if (!defined('_ALPHA_')) {
    exit;
}
global $is_mobile;
global $member;
//if (!$is_mobile) {
$request_ip = $_SERVER['REMOTE_ADDR'];

// http 사용자를 https 로 redirect 처리
Html::httpsRedirect();

$is_main = true;
$body_class = 'main';

$oBiz = new BizUser();
$oBiz->init();

$list = $oBiz->selectListRegion();
$bz_region_arr = $oBiz->get('bz_region_arr');
$hits_list = $oBiz->selectListHits();

global $js_uri;
$ck_save_id = Session::getCookie('ck_save_id_user');
?>
<script type="text/javascript">
    //<![CDATA[
    //]]>
</script>

<script src="/common/js/user/main.js"></script>

<div id="main" class="contents">
    <section class="map-list">
        <div class="bg"></div>
        <div class="box container">
            <h2>당신에게 맞는 사업공고를 찾아드릴께요</h2>
            <div class="con">
                <div class="map-wrap">
                    <div class="dropDown map-dropDown">
                        <div class="dropBtn">전국지도선택</div>
                        <ul class="dropCon">
                            <li>서울</li>
                            <li>부산</li>
                            <li>대구</li>
                            <li>인천</li>
                            <li>광주</li>
                            <li>대전</li>
                            <li>울산</li>
                            <li>경기도</li>
                            <li>강원</li>
                            <li>충남</li>
                            <li>충북</li>
                            <li>경남</li>
                            <li>경북</li>
                            <li>전남</li>
                            <li>전북</li>
                            <li>제주</li>
                            <li>세종</li>
                        </ul>
                    </div>
                    <div id="map">지도</div>
                </div>
                <div class="list">
                    <p class="total">검색결과 <span><?= count($list) ?></span>건</p>
                    <div class="scrollwrap">
                        <ul>
                            <?php
                            for ($i = 0; $i < count($list); $i++) {
                                $bz_e_datetime = $list[$i]['bz_apply_e_datetime'];
                                $bz_e_date = substr($bz_e_datetime, 0, 10);
                                $today = new DateTime(date('Y-m-d'));
                                $endDate = new DateTime($bz_e_date);
                                $interval = $today->diff($endDate);
                                $daysLeft = (int)$interval->format('%r%a'); // 음수도 포함해서 정수로 변환
                                $d_day = "D-" . $daysLeft;

                                $region = $bz_region_arr[$list[$i]['bz_region']];
                                ?>
                                <li class="<?= $region ?>">
                                    <p><span class="badge-main-export badge"><?= $list[$i]['bz_category'] ?></span><span
                                                class="badge-display badge"><?= $list[$i]['bz_field'] ?></span><span
                                                class="badge-day badge"><?= $d_day ?></span></p>
                                    <a href="/webuser/biz/view.html?bz_id=<?= $list[$i]['bz_id'] ?>"
                                       class="title clamp"><?= $list[$i]['bz_title'] ?></a>
                                </li>
                            <?php }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="popular-list">
        <div class="container">
            <div class="left">
                <h2>조회수 많은 공고</h2>
                <p>실시간 공고를 확인해보세요</p>
                <div class="pl-btn">
                    <div class="swiper-button-prev pl-prev"></div>
                    <div class="swiper-button-next pl-next"></div>
                    <a class="more" href="/webuser/biz/list.html">더보기</a>
                </div>
            </div>
            <div class="right">
                <div class="swiper mySwiper pl-sw">
                    <div class="swiper-wrapper">
                        <input type="hidden" name="mb_id" value="<?=$member['mb_id']?>"/>
                        <?php
                        for ($i = 0; $i < count($hits_list); $i++) {
                            $bz_e_datetime = $hits_list[$i]['bz_apply_e_datetime'];
                            $bz_e_date = substr($bz_e_datetime, 0, 10);
                            $today = new DateTime(date('Y-m-d'));
                            $endDate = new DateTime($bz_e_date);
                            $interval = $today->diff($endDate);
                            $daysLeft = (int)$interval->format('%r%a'); // 음수도 포함해서 정수로 변환
                            $d_day = "D-" . $daysLeft;
                            ?>
                            <div class="swiper-slide">
                                <p>
                                    <span class="badge-main-startup badge"><?= $hits_list[$i]['bz_category'] ?></span><span
                                            class="badge-display badge"><?= $hits_list[$i]['bz_field'] ?></span><span
                                            class="badge-day badge"><?= $d_day ?></span>
                                </p>
                                <a href="/webuser/biz/view.html?bz_id=<?=$hits_list[$i]['bz_id']?>" class="title clamp c2"><?= $hits_list[$i]['bz_title'] ?></a>
                                <p class="s-txt clamp c2"><?= $hits_list[$i]['bz_title'] ?></p>
                                <div class="info">
                                    <p class="where"><?= $hits_list[$i]['bz_institution'] ?></p>
                                    <p class="wish <?= $hits_list[$i]['is_like'] == 1 ? 'on' : '' ?>" data-bz="<?=$hits_list[$i]['bz_id']?>"><?=$hits_list[$i]['cnt_like']?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="main-banner">
        <div class="container">
            <div class="mb-btn">
                <div class="swiper-button-prev mb-prev"></div>
                <div class="swiper-button-next mb-next"></div>
            </div>
            <div class="swiper mySwiper mb-sw">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <a href="#"><img src="/common/img/user/img/main-banner-01.jpg" class="of-ct" alt=""/><img
                                    src="/common/img/user/img/main-banner-01-mo.jpg" class="of-ct" alt=""/></a>
                    </div>
                    <div class="swiper-slide">
                        <a href="#"><img src="/common/img/user/img/main-banner-01.jpg" class="of-ct" alt=""/><img
                                    src="/common/img/user/img/main-banner-01-mo.jpg" class="of-ct" alt=""/></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    window.onload = function () {
        $('.gyeongnam').trigger('click');
    };
</script>
