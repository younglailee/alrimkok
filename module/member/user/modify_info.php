<?php
/**
 * @file    intranet.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyUser;
use sFramework\Html;
use sFramework\InterestUser;
use sFramework\MemberUser;

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
$data = $oMember->selectDetail($mb_id);

//기업 정보
$cp_id = $data['cp_id'];
// 첨부파일
$cp_data = $oCompany->selectDetail($cp_id);
$file_list = $cp_data['file_list'];
$cp_count_arr = $oCompany->get('cp_count_arr');
$cp_size_arr = $oCompany->get('cp_size_arr');
$cp_revenue_arr = $oCompany->get('cp_revenue_arr');
$flag_venture_arr = $oCompany->get('flag_venture_arr');
$flag_research_arr = $oCompany->get('flag_research_arr');
$flag_product_arr = $oCompany->get('flag_product_arr');
// 관심 분야
$it_data = $oInterest->selectInterest($mb_id);
$it_area_arr = $oInterest->get('it_area_arr');
$it_type_arr = $oInterest->get('it_type_arr');
$it_info_arr = $oInterest->get('it_info_arr');
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

function nextSection(no) {
    let mpForm = [];
    mpForm[1] = $(".mpForm01");
    mpForm[2] = $(".mpForm02");
    mpForm[3] = $(".mpForm03");
    let index_li = $(".index-ul").children("li");
    //console.log(index_li);
    index_li.eq(0).removeClass("active");
    index_li.eq(1).removeClass("active");
    index_li.eq(2).removeClass("active");
    mpForm[1].hide();
    mpForm[2].hide();
    mpForm[3].hide();
    if (no === 1) {
        index_li.eq(0).addClass("active");
        mpForm[1].show();
    } else if (no === 2) {
        index_li.eq(1).addClass("active");
        mpForm[2].show();
    } else if (no === 3) {
        index_li.eq(2).addClass("active");
        mpForm[3].show();
    }
}

function submitModifyInfo() {
    const form = document.write_form;
    // 유효성 검사 먼저 실행
    if (submitModifyForm(form)) {
        form.submit(); // 통과하면 폼 제출
    }
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
</style>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<section id="mp" class="contents modify">
    <h2 class="sec-title">가입정보 수정</h2>
    <div class="container">
        <p class="path">마이페이지</p>
        <div class="con-wrap">
            <ul class="side-menu">
            <?php
            include_once _MODULE_PATH_ . '/member/user/include.side_menu.php';
            ?>
            </ul>
            <ul class="index-ul mobile">
            <li class="active">기본정보</li>
            <li>기업정보</li>
            <li>관심분야</li>
            </ul>
            <div class="con">
                <form name="write_form" method="post" action="./process.html" enctype="multipart/form-data"
                      onsubmit="return submitModifyForm(this)">
                <input type="hidden" name="mode" value="update_member_info">
                <input type="hidden" name="mb_id" value="<?= $mb_id ?>"/>
                <input type="hidden" name="cp_id" value="<?= $cp_id ?>"/>
                <!-- 기본정보 -->
                <div class="mpForm mpForm01">
                    <fieldset>
                    <legend>기본정보</legend>
                    <p class="help">필수정보</p>
                    <div class="mp-input required">
                        <label for="cp_name">사업장명</label>
                        <input type="text" name="cp_name" id="cp_name" placeholder="사업장명을 입력하세요"
                               class="required" value="<?= $data['cp_name'] ?>">
                    </div>
                    <div class="mp-input right">
                        <label for="mb_id">사업자등록번호</label>
                        <span><?= $mb_id ?></span>
                    </div>
                    <div class="mp-input required">
                        <label for="mb_email">담당자 이메일</label>
                        <input type="email" name="mb_email" id="email" placeholder="담당자이메일을 입력해주세요"
                               class="required" value="<?= $data['mb_email'] ?>">
                    </div>
                    <div class="mp-input right required">
                        <label for="mb_hp">담당자 휴대폰번호</label>
                        <input type="tel" name="mb_hp" id="mb_hp" placeholder="(&quot;-&quot;)없이 입력해주세요"
                               class="required" value="<?= $data['mb_hp'] ?>">
                    </div>
                    <div class="mp-input password-field required">
                        <label for="mb_pw">비밀번호</label>
                        <input type="password" name="mb_pw" id="mb_pw" placeholder="변경 버튼을 누르세요"
                               value="" readonly>
                        <button type="button" class="mp-btn" data-popOpen>변경</button>
                    </div>
                    <div class="mp-input right">
                        <label for="mb_depart">담당자 부서</label>
                        <input type="text" name="mb_depart" id="mb_depart" placeholder="부서명을 입력해주세요"
                               class="required" value="<?= $data['mb_depart'] ?>">
                    </div>
                    <div class="mp-input required">
                        <label for="mb_name">담당자명</label>
                        <input type="text" name="mb_name" id="mb_name" placeholder="담당자명을 입력해주세요"
                               class="required" value="<?= $data['mb_name'] ?>">
                    </div>
                    <a href="#" onclick="nextSection(2)" class="btn-next btn02 mobile">다음</a>
                    </fieldset>
                </div>
                <?php
                //print_r($cp_data);
                //print_r($file_list);
                if ($data['mb_level'] != '2') {
                    ?>
                    <div class="mpForm mpForm02">
                        <fieldset>
                        <legend>기업정보</legend>

                        <div class="mp-input required">
                            <label for="employeeCount">종업원수</label>
                            <select id="employeeCount" name="cp_count" required="">
                            <option value="" disabled="" selected="" hidden="">종업원수를 선택해주세요</option>
                            <?php
                            $cp_count = $cp_data['cp_count'];
                            foreach ($cp_count_arr as $key => $val) {
                                $selected = ($key == $cp_count) ? ' selected' : '';
                                ?>
                                <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mp-input right required build-field">
                            <label for="buildDate">설립일자</label>
                            <input id="buildDate" class="date-input flatpickr-input" placeholder="시작일" type="text"
                                   name="cp_date" value="<?= $cp_data['cp_date'] ?>" readonly="readonly">
                        </div>

                        <div class="mp-input required">
                            <label for="companySize">기업규모</label>
                            <select id="companySize" name="cp_size" required="">
                            <option value="" disabled="" selected="" hidden="">기업규모를 선택해주세요</option>
                            <?php
                            $cp_size = $cp_data['cp_size'];
                            foreach ($cp_size_arr as $key => $val) {
                                $selected = ($key == $cp_size) ? ' selected' : '';
                                ?>
                                <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mp-input right required revenue-input">
                            <label for="revenue">매출액규모<span>(백만원단위)</span></label>
                            <select id="revenue" name="cp_revenue" required="">
                            <option value="" disabled="" selected="" hidden="">매출액규모를 선택해주세요</option>
                            <?php
                            $cp_revenue = $cp_data['cp_revenue'];
                            foreach ($cp_revenue_arr as $key => $val) {
                                $selected = ($key == $cp_revenue) ? ' selected' : '';
                                ?>
                                <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mp-input file-input">
                            <label for="companyFile">기업소개서</label>
                            <span class="ph-text clamp c1" id="filePlaceholder">파일을 등록해주세요.</span>
                            <input type="hidden" name="fi_type[]" value="company"/>
                            <input id="companyFile" name="atch_file[]" type="file" data-target="#filePlaceholder"
                                   data-placeholder="파일을 등록해주세요.">
                            <label for="companyFile" class="btn-upload mp-btn">등록</label>
                            <?php
                            if ($file_list[0]['fi_id']) {
                                ?>
                                <p>
                                    <a href="../company/download.html?fi_id=<?= $file_list[0]['fi_id'] ?>"
                                       class="btn_download" target="_blank" title="새창 다운로드">
                                        <strong><?= $file_list[0]['fi_name'] ?></strong>
                                    </a>
                                </p>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="mp-input right required">
                            <label>벤처기업 유/무</label>
                            <?php
                            $flag_venture = $cp_data['flag_venture'];
                            foreach ($flag_venture_arr as $key => $val) {
                                $checked = ($key == $flag_venture) ? ' checked' : '';
                                if ($key == 'N' && $flag_venture == '') {
                                    $checked = ' checked';
                                }
                                ?>
                                <div class="rdo_btn">
                                    <input type="radio" name="flag_venture" id="venture<?= $key ?>"
                                           value="<?= $key ?>"<?= $checked ?>>
                                    <label for="venture<?= $key ?>"><?= $val ?></label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="mp-input required">
                            <label for="research">연구소 보유</label>
                            <select id="research" name="flag_research" required="">
                            <option value="">연구소 유/무 선택해주세요</option>
                            <?php
                            $flag_research = $cp_data['flag_research'];
                            foreach ($flag_research_arr as $key => $val) {
                                $selected = ($key == $flag_research) ? ' selected' : '';
                                ?>
                                <option value="<?= $key ?>"<?= $selected ?>><?= $val ?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </div>

                        <div class="mp-input right required">
                            <label>완제품 생산 유/무</label>
                            <?php
                            $flag_product = $cp_data['flag_product'];
                            foreach ($flag_product_arr as $key => $val) {
                                $checked = ($key == $flag_product) ? ' checked' : '';
                                if ($key == 'N' && $flag_product == '') {
                                    $checked = ' checked';
                                }
                                ?>
                                <div class="rdo_btn">
                                    <input type="radio" name="flag_product" id="product<?= $key ?>"
                                           value="<?= $key ?>"<?= $checked ?>>
                                    <label for="product<?= $key ?>"><?= $val ?></label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="mp-input required add-input">
                            <p class="input-title">업장소재지<span>(주소기입)</span></p>
                            <div class="add-wrap">
                                <input type="text" id="zipcode" name="cp_zip" value="<?= $cp_data['cp_zip'] ?>"
                                       placeholder="우편번호" readonly="">
                                <button type="button" id="addBtn" onclick="execDaumPostcode()">우편번호 검색</button>
                                <input type="text" id="address1" name="cp_address" value="<?= $cp_data['cp_address'] ?>"
                                       class="full-width"
                                       placeholder="기본주소" readonly="">
                                <input type="text" id="address2" name="cp_address2"
                                       value="<?= $cp_data['cp_address2'] ?>"
                                       class="full-width" placeholder="나머지 주소를 입력해주세요">
                            </div>
                        </div>
                        <div class="btn-wrap mobile">
                            <a href="#" onclick="nextSection(1)" class="btn-prev">이전</a>
                            <a href="#" onclick="nextSection(3)" class="btn-next">다음</a>
                        </div>
                        </fieldset>
                    </div>
                    <!-- // 기업정보 -->
                    <?php
                }
                //print_r($data);
                //print_r($it_data);
                ?>
                <!-- 관심분야 -->
                <div class="mpForm mpForm03">
                    <fieldset>
                    <div class="top">
                        <legend>관심분야</legend>
                        <p>* 관심분야를 등록하시면, 등록한 내용을 기준으로 추천공고를 확인 할 수 있습니다.</p>
                    </div>

                    <div class="mp-input mp-chk required">
                        <p class="input-title">주력분야 사업</p>
                        <div class="tab-group">
                            <ul class="tab">
                            <li class="active">제조</li>
                            <li class="">서비스</li>
                            <li class="">IT신산업</li>
                            <li class="">건설</li>
                            </ul>
                            <div class="tab-panel active" id="tab_a">
                                <label class="ws-all"><input type="checkbox" class="check-all"><span>전체선택</span></label>
                                <?php
                                //print_r($it_area_arr);
                                $selected_area_str = $it_data['it_area'];
                                $selected_area_arr = explode('|', $selected_area_str);
                                foreach ($it_area_arr as $key => $val) {
                                    if (str_starts_with($key, 'a')) {
                                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                                        ?>
                                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="tab-panel" id="tab_b">
                                <label class="ws-all"><input type="checkbox" class="check-all"><span>전체선택</span></label>
                                <?php
                                foreach ($it_area_arr as $key => $val) {
                                    if (str_starts_with($key, 'b')) {
                                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                                        ?>
                                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="tab-panel" id="tab_c">
                                <label class="ws-all"><input type="checkbox" class="check-all"><span>전체선택</span></label>
                                <?php
                                foreach ($it_area_arr as $key => $val) {
                                    if (str_starts_with($key, 'c')) {
                                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                                        ?>
                                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <div class="tab-panel" id="tab_d">
                                <label class="ws-all"><input type="checkbox" class="check-all"><span>전체선택</span></label>
                                <?php
                                foreach ($it_area_arr as $key => $val) {
                                    if (str_starts_with($key, 'd')) {
                                        $checked = in_array($key, $selected_area_arr) ? ' checked' : '';
                                        ?>
                                        <label class="ws-check"><input type="checkbox" name="it_area[]" <?= $checked ?>
                                                                       value="<?= $key ?>"><span><?= $val ?></span></label>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <ul class="choice"></ul>
                            <p class="resetBtn02">선택초기화</p>
                        </div>
                    </div>

                    <div class="mp-input required type-input">
                        <p class="input-title">유형</p>
                        <div class="tab-group">
                            <div class="tab-panel active">
                                <?php
                                //print_r($it_type_arr);
                                $selected_type_str = $it_data['it_type'];
                                $selected_type_arr = explode('|', $selected_type_str);
                                foreach ($it_type_arr as $key => $val) {
                                    $checked = in_array($key, $selected_type_arr) ? ' checked' : '';
                                    ?>
                                    <label class="ws-check"><input type="checkbox" name="it_type[]" <?= $checked ?>
                                                                   value="<?= $key ?>"><span><?= $val ?></span></label>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="mp-input required info-input">
                        <p class="input-title">주요 관심정보</p>
                        <div class="tab-group">
                            <div class="tab-panel active">
                                <?php
                                //print_r($it_info_arr);
                                $selected_info_str = $it_data['it_info'];
                                $selected_info_arr = explode('|', $selected_info_str);
                                foreach ($it_info_arr as $key => $val) {
                                    $checked = in_array($key, $selected_info_arr) ? ' checked' : '';
                                    ?>
                                    <label class="ws-check"><input type="checkbox" name="it_info[]" <?= $checked ?>
                                                                   value="<?= $key ?>"><span><?= $val ?></span></label>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $flag_notice = $data['flag_notice'];
                    $checked_notice = ($flag_notice == 'Y') ? ' checked' : '';
                    ?>
                    <!-- 알림 수신 동의 여부 -->
                    <div class="alarm-input">
                        <label class="alarm-check"><input type="checkbox" name="flag_notice"<?= $checked_notice ?>
                                                          value="Y"><span>알림수신 동의 여부</span></label>
                        <p class="txt">*추천공고 및 관심공고 알림에 대한 SNS 메시지 전달 동의 여부</p>
                    </div>
                    <div class="btn-wrap mobile">
                        <a href="#" onclick="nextSection(2)" class="btn-prev" type="button">이전</a>
                        <a href="#" onclick="submitModifyInfo()" class="btn-next" type="button">수정</a>
                    </div>
                    </fieldset>
                </div>
                <!-- // 관심분야 -->

                <div class="btn-wrap pc">
                    <button class="btn-small btn02" type="submit">수정</button>
                </div>
                </form>
                <form action="./process.html" method="post" name="pass_modi_form" onsubmit="return validateForm(this)">
                <div class="popup" data-popup style="display:none;">
                    <input type="hidden" name="mode" value="update_password">
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
        </div>
    </div>
</section>
<script src="/common/js/user/choice_info.js"></script>
<!-- 일정 날짜 선택 -->
<script src="//cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="//cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ko.js"></script>
<script>
const startPicker = flatpickr("#buildDate", {
    locale: "ko",
    dateFormat: "Y-m-d",
    //defaultDate: new Date(),
    onChange: function(selectedDates, dateStr) {
        endPicker.set('minDate', dateStr);
    }
});
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById("companyFile");
    const placeholder = document.querySelector(fileInput.dataset.target);

    fileInput.addEventListener("change", function() {
        if (fileInput.files.length > 0) {
            placeholder.textContent = fileInput.files[0].name;
        } else {
            placeholder.textContent = fileInput.dataset.placeholder;
        }
    });
});
</script>
