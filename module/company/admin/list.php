<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\CompanyAdmin;
use sFramework\Format;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

/* init Class */
$oCompany = new CompanyAdmin();
$oCompany->init();
$pk = $oCompany->get('pk');

/* check auth */
if (!$oCompany->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* list */
$list = $oCompany->selectList();
//$p_list = $oCompany->selectPartner();
$cnt_total = $oCompany->get('cnt_total');

/* search condition */
$search_like_arr = $oCompany->get('search_like_arr');
$search_date_arr = $oCompany->get('search_date_arr');
$query_string = $oCompany->get('query_string');

/* pagination */
$page = $oCompany->get('page');
$page_arr = $oCompany->getPageArray();

/* code */
$mn_layout_arr = $oCompany->get('mn_layout_arr');
$mn_depth_arr = $oCompany->get('mn_depth_arr');
$year_arr = $oCompany->get('year_arr');
$month_arr = $oCompany->get('month_arr');
$date_arr = $oCompany->get('date_arr');
$flag_auth_arr = $oCompany->get('flag_auth_arr');
$mb_level_arr = $oCompany->get('mb_level_arr');
$mb_no_login_arr = $oCompany->get('mb_no_login_arr');
$cnt_rows_arr = $oCompany->get('cnt_rows_arr');
$order_arr = $oCompany->get('order_arr');
$cp_size_arr = $oCompany->get('cp_size_arr');
$flag_venture_arr = $oCompany->get('flag_venture_arr');
$flag_research_arr = $oCompany->get('flag_research_arr');


$sch_order = $_GET['sch_order'];
$sch_cnt_rows = $_GET['sch_cnt_rows'];
$sch_flag_auth = $_GET['sch_flag_auth'];
$sch_date = $_GET['sch_date'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$sch_mb_level = $_GET['sch_mb_level'];
$sch_flag_book = $_GET['sch_flag_book'];
$sch_flag_live = $_GET['sch_flag_live'];
$sch_partner = $_GET['sch_partner'];

$query_string .= '&sch_partner=' . $sch_partner;
if ($sch_flag_book) {
    $query_string .= '&sch_flag_book=' . $sch_flag_book;
}
if ($sch_flag_live) {
    $query_string .= '&sch_flag_live=' . $sch_flag_live;
}
global $member;
$mb_id = $member['mb_id'];
if ($mb_id == 'alphatest') {
    $list = array();
    $cnt_total = 0;
}
// 테스트 배열
$mb_flag_test = $member['flag_test'];
if ($mb_flag_test == 'Y') {
    $list = array(
        '0' => array(
            'no' => '3',
            'cp_id' => '1502260229',
            'cp_type' => 'priority_support',
            'cp_name' => '(주)알파에듀',
            'cp_number' => '4928100339',
            'cp_tel' => '0552556364',
            'staff_name' => '서혜미',
            'staff_position' => '대리',
            'partner_name' => '장재선'
        ),
        '1' => array(
            'no' => '2',
            'cp_id' => '1602908890',
            'cp_type' => 'priority_support',
            'cp_name' => '혜미컴퍼니',
            'cp_number' => '1234567890',
            'cp_tel' => '055-111-1112',
            'staff_name' => '혜미기업관리자',
            'staff_position' => '',
            'partner_name' => '장재선',
            'flag_live' => 'Y'
        ),
        '2' => array(
            'no' => '1',
            'cp_id' => '1482940026',
            'cp_type' => '',
            'cp_name' => '직업능력심사평가원',
            'cp_number' => '1234567890',
            'cp_tel' => '1644-5113',
            'staff_name' => '김민경',
            'staff_position' => '담당',
            'partner_name' => '',
            'flag_book' => 'Y'
        )
    );
}

//$partners = array_values($p_list);

global $module;
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    $(".cp_pw_reset_btn").click(function(e) {
        e.preventDefault();
        if (confirm('비밀번호를 1234로 초기화하시겠습니까?')) {
            var cp_id = $(this).attr('data-id');
            $.ajax({
                url: "process.html",
                type: "GET",
                dataType: "json",
                data: {
                    flag_json: '1',
                    mode: 'reset_pw',
                    cp_id: cp_id
                },
                success: function(rs) {
                    alert(rs.msg);
                }
            });
        }
    });
    // 테스트 환경 알림
    $('.ready').click(function(e) {
        alert('테스트 환경에서는 이용할 수 없습니다.');
        e.preventDefault();
        return false;
    });
});

function getCheckedValues() {
    var checkboxes = document.querySelectorAll('input[name="list_uid[]"]:checked');
    var values = [];
    checkboxes.forEach(function(checkbox) {
        values.push(checkbox.value);
    });
    return values.join(',');
}

function sendEmails() {
    console.log("sendEmails 함수가 호출되었습니다."); // 디버깅용 로그
    var checkedValues = getCheckedValues();
    var selectedPartner = document.getElementById('sch_partner').value;

    if (selectedPartner) {
        var href = './email_write.html?partner=' + encodeURIComponent(selectedPartner);
    } else {
        if (checkedValues.length === 0) {
            var href = './email_write.html';
        } else {
            var href = './email_write.html?recipients=' + encodeURIComponent(checkedValues);
        }
    }
    console.log("Redirecting to: ", href); // 리디렉션 URL 확인
    window.location.href = href;
}

//]]>
</script>
<div id="<?= $module ?>" style="width: 1400px">
    <div class="search">
        <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_order" value="<?= $sch_order ?>"/>
        <input type="hidden" name="sch_cnt_rows" value="<?= $sch_cnt_rows ?>"/>
        <table class="search_table">
        <caption>검색조건</caption>
        <colgroup>
        <col style="width:90px"/>
        <col style="width:400px"/>
        <col style="width:90px"/>
        <col style="width:250px"/>
        <col style="width:70px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text"
                       size="26"
                       maxlength="30" title="검색어"/>
            </td>
            <th><label for="sch_cp_type">기업규모</label></th>
            <td>
                <select name="sch_cp_type" id="sch_cp_type" class="select" title="기업구분">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($cp_size_arr, $sch_cp_size, 1) ?>
                </select>
            </td>
            <th><label for="sch_flag_book"></label></th>
            <td>
                <select name="sch_flag_book" id="sch_flag_book" class="select" title="북러닝">
                <option value="">전체</option>
                <?= Html::makeSelectOptions($flag_book_arr, $sch_flag_book, 1) ?>
                </select>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>
        <div class="button">
            <button type="submit" class="sButton info" title="검색">검 색</button>
            <a href="./list.html" class="sButton" title="초기화">초기화</a>
        </div>
        </form>
    </div>
    <div class="list">
        <div class="list_header">
            <div class="left">
                <i class="xi-file-text-o"></i> Total : <strong><?= number_format($cnt_total) ?></strong> 건, 현재 :
                <strong><?= number_format($page) ?></strong> 페이지
            </div>
            <div class="right">
                <select name="sch_order" class="select change_order" title="정렬순서">
                <?= Html::makeSelectOptions($order_arr, $sch_order, 1) ?>
                </select>
                <select name="sch_cnt_rows" class="select change_order" title="출력갯수">
                <?= Html::makeSelectOptions($cnt_rows_arr, $sch_cnt_rows, 1) ?>
                </select>
            </div>
        </div>
        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        <input type="hidden" name="mode" value="delete"/>
        <fieldset>
        <legend>자료목록</legend>
        <table class="list_table border odd">
        <colgroup>
        <col style="width:30px"/>
        <col style="width:50px"/>
        <col style="width:100px"/>
        <col style="width:90px"/>
        <col/>
        <col style="width:120px"/>
        <col style="width:110px"/>
        <col style="width:80px"/>
        <col style="width:100px"/>
        <col style="width:80px"/>
        <col style="width:80px"/>
        <col style="width:120px"/>
        <col style="width:100px"/>
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
            <th>No</th>
            <th>기업코드</th>
            <th>기업규모</th>
            <th>사업장명</th>
            <th>사업자등록번호</th>
            <th>대표번호</th>
            <th>담당자명</th>
            <th>부서</th>
            <th>설립일자</th>
            <th>벤처기업</th>
            <th>연구소</th>
            <th>관리</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i = 0; $i < count($list); $i++) {
            // 회원 구분
            $mb_level = $list[$i]['mb_level'];
            $txt_level = $mb_level_arr[$mb_level];
            // 본인인증
            $flag_auth = $list[$i]['flag_auth'];
            $txt_auth = $flag_auth_arr[$flag_auth];
            //echo $list[$i]['cp_tel'];
            ?>
            <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                            class="list_checkbox" title="선택/해제"/></td>
                <td><?= $list[$i]['no'] ?></td>
                <td><?= $list[$i]['cp_id'] ?></td>
                <td><?= $cp_size_arr[$list[$i]['cp_size']] ?></td>
                <td class="">
                    <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['cp_name'] ?></a>
                </td>
                <td><?= $list[$i]['cp_number'] ?></td>
                <td><?= Html::beautifyTel($list[$i]['cp_tel']) ?></td>
                <td><?= Format::getWithoutNull($list[$i]['staff_name']) ?></td>
                <td><?= Format::getWithoutNull($list[$i]['mb_depart']) ?></td>
                <td><?= Format::getWithoutNull($list[$i]['cp_date']) ?></td>
                <td><?= ($flag_venture_arr[$list[$i]['flag_venture']]) ?: $flag_venture_arr['N'] ?></td>
                <td><?= $flag_research_arr[$list[$i]['flag_research']] ?></td>
                <td class="button">
                    <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"
                       class="sButton tiny" title="수정">수정</a>
                    <?php
                    if ($mb_flag_test == 'Y') {
                        echo '<button type="button" class="sButton tiny ready">초기화</button>';
                    } else {
                        ?>
                        <a href="#" data-id="<?= $list[$i]['cp_id'] ?>"
                           class="sButton tiny cp_pw_reset_btn" title="초기화">초기화</a>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        echo !count($list) ? Html::makeNoTd(13) : null;
        ?>
        </tbody>
        </table>
        <div class="list_footer">
            <div class="left">
                <?php
                if ($mb_flag_test == 'Y') {
                    echo '<button type="button" class="sButton small ready">선택삭제</button>';
                } else {
                    ?>
                    <button type="submit" class="sButton small">선택삭제</button>
                    <?php
                }
                ?>
            </div>
            <div class="right">
                <a href="./write.html?page=<?= $page ?><?= $query_string ?>" class="sButton small primary"
                   title="등록">등록</a>
            </div>
        </div>
        </form>

        <div class="pagination">
            <ul>
            <?= Html::makePagination($page_arr, $query_string); ?>
            </ul>
        </div>
    </div>
</div>
