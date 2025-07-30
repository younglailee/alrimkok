<?php
/**
 * @file    intranet.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\MemberUser;
use sFramework\CompanyUser;
use sFramework\InterestUser;

if (!defined('_ALPHA_')) {
    exit;
}
global $member;
global $layout_uri;
global $layout;
global $bd_code;

$oMember = new MemberUser();
$oMember->init();
$oCompany = new CompanyUser();
$oCompany->init();
$oInterest = new InterestUser();
$oInterest->init();

/* set URI */
$this_uri = '/webuser/member/modify_info.html';
$doc_title = '기업정보 수정 > 마이페이지';

// 로그인 창 이동 후 로그인 시 해당 페이지로 리턴
if (!$member['mb_id']) {
    $move_url = $layout_uri . '/member/login.html?return_uri=' . $this_uri;
    Html::alert('로그인 후 이용 가능합니다.', $move_url);
}
$mb_tel = $member['mb_tel'];
$mb_direct_line = $member['mb_direct_line'];
$beauty_tel = Html::beautifyTel($mb_tel);
$beauty_direct_line = Html::beautifyTel($mb_direct_line);
$txt_tel = explode('-', $beauty_tel);
$txt_direct_line = explode('-', $beauty_direct_line);
$txt_mail = explode('@', $member['mb_email']);
$mode = $_GET['mode'];

global $member;
$mb_id = $member['mb_id'];
$cp_id = $member['cp_id'];
$list = $oMember->selectSubMember($cp_id);
$cnt_total = $oMember->get('cnt_total');
$page = $oMember->get('page');
$page_arr = $oMember->getPageArray();
$query_string = $oMember->get('query_string');

// 검색
$sch_like = $_GET['sch_like'];
$sch_text = $_GET['sch_text'];
?>
<script type="text/javascript" src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
//<![CDATA[
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            var roadAddr = data.roadAddress;
            $("input[name='cp_zip']").val(data.zonecode);
            $("input[name='cp_address']").val(roadAddr);
            $("input[name='cp_address2']").focus();
        }
    }).open();
}
$(function() {
    $("[data-popOpen]").click(function() {
        $(".con").addClass("bgOn");
        $("[data-popup]").show();
    });

    $("[data-popClose]").click(function() {
        $(".con").removeClass("bgOn");
        $("[data-popup]").hide();
    });

    $(".check-all").change(function() {
        let $panel = $(this).closest('.tab-panel');
        let isChecked = $(this).prop('checked'); // true 또는 false
        $panel.find('input[name="it_area[]"]').prop('checked', isChecked);
    });

    $(".resetBtn02").click(function() {
        $('input[name="it_area[]"]').prop('checked', false);
    });
});
// 이메일 주소 직접 입력
$(document).on('change', '#userEmailDomain', function() {
    console.log(this);
    let selectedOption = $(this).find('option:selected');
    //console.log(selectedOption.attr('value'));
    let managerEmailCustomDomain = $('#userEmailCustomDomain');
    if (selectedOption.attr('value') === 'manual') {
        managerEmailCustomDomain.show().focus();
    } else {
        managerEmailCustomDomain.hide().val("");
    }
});

// 사용자 ID 중복 확인 함수
let result_check = false;
function checkId() {
    let mb_id = $("#mb_id");
    let mb_id_val = $("#mb_id").val();

    if (!mb_id_val) {
        alert('아이디를 입력해주세요');
        mb_id.focus();
        return;
    }
    $.ajax({
        url: "process.html",
        type: "GET",
        dataType: "json",
        data: {
            flag_json: '1',
            mode: 'search_id',
            mb_id: mb_id_val
        },
        success: function(result) {
            if (!result.data) {
                alert("사용가능한 아이디입니다.");
                result_check = true;
            } else {
                alert("이미 사용중인 아이디입니다.");
            }
        }
    });
}
//]]>
</script>
<style>
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

/* -------------------- 비밀번호 변경 -------------------- */
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
#search_form {width:100%;}
.no_data { width:100%; }
</style>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<section id="mp" class="contents apply sub">
    <h2 class="sec-title">부계정 관리</h2>

    <div class="container">
        <p class="path">마이페이지</p>

        <div class="con-wrap">
            <ul class="side-menu">
            <li><a href="/page/mypage/apply-write.html">지원현황</a></li>
            <li><a href="/page/mypage/wish-list.html">찜한 공고</a></li>
            <li><a href="./modify_info.html">가입정보 수정</a></li>
            <li class="active"><a href="/page/mypage/sub-list.html">부계정 관리</a></li>
            </ul>
            <?php
            //print_r($member);
            //print_r($list);
            ?>
            <div class="con">
                <form name="search_form" id="search_form" action="./sub_list.html" method="get"
                      onsubmit="return submitSearchForm(this)">
                <div class="sub-list-top">
                    <select name="sch_like" id="department">
                    <option value="all">전체</option>
                    <?php
                    $selected1 = '';
                    $selected2 = '';
                    $selected3 = '';
                    $selected4 = '';
                    if ($sch_like == 'mb_depart') {
                        $selected1 = ' selected';
                    } elseif ($sch_like == 'mb_id') {
                        $selected2 = ' selected';
                    } elseif ($sch_like == 'mb_name') {
                        $selected3 = ' selected';
                    } elseif ($sch_like == 'mb_hp') {
                        $selected4 = ' selected';
                    }
                    ?>
                    <option value="mb_depart"<?= $selected1 ?>>부서명</option>
                    <option value="mb_id"<?= $selected2 ?>>사용자ID</option>
                    <option value="mb_name"<?= $selected3 ?>>사용자명</option>
                    <option value="mb_hp"<?= $selected4 ?>>연락처</option>
                    </select>
                    <div class="search">
                        <input type="search" name="sch_text" id="sch_text" value="<?= $sch_text ?>">
                        <button class="btn_search">검색</button>
                    </div>
                </div>
                </form>

                <p class="total">총 <span><?= $cnt_total ?></span>건</p>
                <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
                <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
                <input type="hidden" name="page" value="<?= $page ?>"/>
                <input type="hidden" name="mode" value="delete"/>
                <table class="sub-list-table">
                <thead>
                <tr>
                    <th class="t-chk"><label class="tb-check"><input type="checkbox" id="listCheckAll" name="" value=""><span></span></label>
                    </th>
                    <th class="t-dpt pc">부서명</th>
                    <th class="t-id">사용자 ID</th>
                    <th class="t-name">사용자명</th>
                    <th class="t-tel">사용자 연락처</th>
                    <th class="t-mail pc">사용자 이메일</th>
                    <th class="t-drop dropDown mobile"></th>
                </tr>
                </thead>
                <tbody>
                <!-- 사용자 -->
                <?php
                if (is_array($list)) {
                    for ($i = 0; $i < count($list); $i++) {
                        ?>
                        <tr class="dropDown">
                            <td class="t-chk">
                                <label class="tb-check">
                                    <input type="checkbox" class="list-check list_checkbox" name="list_uid[]"
                                           value="<?= $list[$i]['mb_id'] ?>"><span></span>
                                </label>
                            </td>
                            <td class="t-pop t-dpt pc"><?= $list[$i]['mb_depart'] ?></td>
                            <td class="t-pop t-id"><?= $list[$i]['mb_id'] ?></td>
                            <td class="t-pop t-name"><?= $list[$i]['mb_name'] ?></td>
                            <td class="t-pop t-tel"><?= $list[$i]['mb_hp'] ?></td>
                            <td class="t-pop t-mail pc"><?= $list[$i]['mb_email'] ?></td>
                            <td class="t-drop mobile">
                                <div class="dropBtn"></div>
                                <div class="mo-detail dropCon">
                                    <div class="tit"><span class="d-dpt">부서명</span><span class="d-mail">사용자 이메일</span>
                                    </div>
                                    <div class="t-pop txt"><span class="d-dpt"><?= $list[$i]['mb_depart'] ?></span><span
                                            class="d-mail"><?= $list[$i]['mb_email'] ?></span></div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    echo !count($list) ? Html::makeNoTd(7) : null;
                } else {
                    echo Html::makeNoTd(7);
                }
                ?>
                </tbody>
                </table>
                <div class="btn-box">
                    <button type="submit" class="">삭제</button>
                    <div class="btn_add">신규등록</div>
                </div>
                </form>

                <ul class="pg-num">
                <?= Html::makePagination($page_arr, $query_string); ?>
                </ul>

            </div>
        </div>
    </div>
</section>
<div id="sub_popup" class="new pop-up" style="display: none;">
</div>
