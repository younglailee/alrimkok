<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Format;
use sFramework\Html;
use sFramework\Session;
use sFramework\VisitAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
global $module;


/* init Class */
$oVisit = new VisitAdmin();
$oVisit->init();
$pk = $oVisit->get('pk');

/* check auth */
if (!$oVisit->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}

/* search condition */
$search_like_arr = $oVisit->get('search_like_arr');
$search_date_arr = $oVisit->get('search_date_arr');
$query_string = $oVisit->get('query_string');
$year_arr = $oVisit->get('year_arr');
$month_arr = $oVisit->get('month_arr');
$date_arr = $oVisit->get('date_arr');

$sch_like = $_GET['sch_like'];
$sch_text = $_GET['sch_text'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];

$sch_date = date("Y-m", strtotime("-1 month"));
//echo $sch_date;
$sch_date_arr = explode('-', $sch_date);
if (!$sch_year) {
    $sch_year = $sch_date_arr[0];
}
if (!$sch_month) {
    $sch_month = $sch_date_arr[1];
}
/* list */
$list = $oVisit->selectListIpMonth('total');
$cnt_total = $oVisit->get('cnt_total');

/* pagination */
$page = $oVisit->get('page');
$page_arr = $oVisit->getPageArray();

/* code */
$date_arr = $oVisit->get('date_arr');

//$layout_size = 'large';
/*
$us_name = array('홍길동', '이영래', '박금삼', '임꺽정', '이순신', '관리자', '최고관리자', '개발관리자', '부서관리자', '담당자');
$us_id = array('hong', 'lucas', 'sliva', 'limkkj', 'leess', 'admin', 'root', 'dept', 'part', 'charge');
$cp_name = array('알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀', '알파에듀');
*/
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
    choosePeriodYear();
    choosePeriodMonth();
    //document.search_form.submit();
});
//]]>
</script>
<div id="<?= $module ?>">
    <div class="search">
        <form name="search_form" action="./list.month.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <input type="hidden" name="sch_date" value="reg_time"/>

        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text" size="30"
                       maxlength="30" title="검색어"/>
            </td>
        </tr>
        <tr>
            <th><label for="sch_s_date">접속일시</label></th>
            <td>
                <select name="sch_year" id="sch_year" class="select" title="년">
                <option value="">년</option>
                <?= Html::makeSelectOptions($year_arr, $sch_year, 1) ?>
                </select>
                <select name="sch_month" id="sch_month" class="select" title="월">
                <option value="">월</option>
                <?= Html::makeSelectOptions($month_arr, $sch_month, 1) ?>
                </select>
                <input type="text" name="sch_s_date" value="<?= $sch_s_date ?>" class="text date" size="10"
                       maxlength="10" title="시작일"/>
                ~
                <input type="text" name="sch_e_date" value="<?= $sch_e_date ?>" class="text date" size="10"
                       maxlength="10" title="종료일"/>

                <?= Html::makePeriodAnchor($date_arr, $sch_s_date, $sch_e_date) ?>
            </td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <div class="button">
            <button type="submit" class="sButton info" title="검색">검 색</button>
            <a href="./list.month.html" class="sButton" title="초기화">초기화</a>
        </div>
        </form>
    </div>
    <?php
    $arr_company = array();
    $arr_user = array();
    $arr_ip = array();
    $arr_ip_check = array();
    for ($i = 0; $i < count($list); $i++) {
        $cp_name = $list[$i]['cp_name'];
        if ($cp_name) {
            $arr_company[$cp_name] = '';
        }
        $cp_id = $list[$i]['cp_id'];
        if ($cp_id) {
            $arr_cp[$cp_id] = '';
        }
        $reg_id = $list[$i]['reg_id'];
        if ($reg_id) {
            $arr_user[$reg_id] = '';
        }
        $vs_ip = $list[$i]['vs_ip'];
        $cp_id = $list[$i]['cp_id'];
        $reg_id = $list[$i]['reg_id'];
        if ($vs_ip && $cp_id && $reg_id) {
            $arr_ip[$vs_ip][$cp_id][$reg_id] = '';
            $arr_ip_check[$vs_ip] = $vs_ip;
        }
    }
    //print_r($arr_company);
    //print_r($arr_ip);

    // 타기업 접속 IP 수
    $count_company = 0;
    $count_ip = 0;
    $duplicate_ip = '';
    $duplicate_us = '';
    foreach ($arr_ip as $key => $val) {
        $count_cp = count($val);
        if ($count_cp > 1) {
            $count_company++;
            if ($duplicate_ip) {
                $duplicate_ip .= '|' . $arr_ip_check[$key];
            } else {
                $duplicate_ip = $arr_ip_check[$key];
            }
        }
        foreach ($val as $key2 => $val2) {
            $count_vs_ip = count($val2);
            if ($count_vs_ip > 1) {
                $count_ip++;
                if ($duplicate_us) {
                    $duplicate_us .= '|' . $arr_ip_check[$key];
                } else {
                    $duplicate_us = $arr_ip_check[$key];
                }
            }
        }
        /*
        echo $key;
        print_r($val);
        echo '<br/>';
        */
    }
    //echo $duplicate_ip;
    $count_arr_ip = number_format(count($arr_ip));
    $count_arr_ip = number_format($oVisit->countTotalIpMonth('ip'));

    $sch_list_mode = 'day';
    $oVisit->set('list_mode', $sch_list_mode);
    $result = $oVisit->selectListByDate();

    $view_url_cp = './view.company.html?list_mode=company' . $query_string;
    $view_url_ip = './view.ip.html?list_mode=ip' . $query_string;
    $view_url_ip_check = './view.ip.html?list_mode=ip_check' . $query_string;
    $view_url_ip_check .= '&ip_arr=' . $duplicate_ip;
    $view_url_us_check = './view.ip.html?list_mode=ip_check' . $query_string;
    Session::setSession('duplicate_us', $duplicate_us);
    ?>
    <div class="write">
        <fieldset style="margin-bottom:30px;">
        <legend>월별 접속 IP 현황</legend>
        <table class="write_table">
        <caption>월별 접속 IP 현황</caption>
        <colgroup>
        <col style="width:160px"/>
        <col style="width:160px"/>
        <col/>
        </colgroup>
        <tbody>
        <tr>
            <th>통계 연월</th>
            <td colspan="2"><?= $sch_date_arr[0] ?>년 <?= number_format($sch_date_arr[1]) ?>월</td>
        </tr>
        <tr>
            <th>접속 기업 수</th>
            <td><?= number_format(count($arr_cp)) ?>개</td>
            <td><a href="<?= $view_url_cp ?>" class="sButton tiny">접속 기업 현황</a></td>
        </tr>
        <tr>
            <th>접속 IP 수</th>
            <td><?= $count_arr_ip ?>개</td>
            <td><a href="<?= $view_url_ip ?>" class="sButton tiny">접속 IP 현황</a></td>
        </tr>
        <tr>
            <th>타기업 접속 IP 수</th>
            <td><?= number_format($count_company) ?>개</td>
            <td><a href="<?= $view_url_ip_check ?>" class="sButton tiny">타기업 접속 IP 현황</a></td>
        </tr>
        <tr>
            <th>중복 IP 수</th>
            <td><?= number_format($count_ip) ?>개</td>
            <td><a href="<?= $view_url_us_check ?>" class="sButton tiny">중복 IP 현황</a></td>
        </tr>
        <tr>
            <th>접속 수강생 수</th>
            <td><?= number_format(count($arr_user)) ?>명</td>
            <td><a href="<?= $view_url ?>" class="sButton tiny">접속 수강생 현황</a></td>
        </tr>
        <tr>
            <th>전체 방문자 수</th>
            <td><?= Format::beautifyNumber($result['total']) ?>명</td>
            <td><a href="<?= $view_url ?>" class="sButton tiny">전체 방문자 현황</a></td>
        </tr>
        </tbody>
        </table>
        </fieldset>

        <table class="stats_table">
        <colgroup>
        <col width="200">
        <col width="*">
        <col width="100">
        </colgroup>
        <thead>
        <tr>
            <th>기간</th>
            <th>방문자수</th>
            <th>합계</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($result['list'])) {
            foreach ($result['list'] as $key => $arr) {
                ?>
                <tr>
                    <td class="sub_th"><?= $arr['txt'] ?></td>
                    <td>
                        <div class="visit_bar">
                            <span style="width:<?= $arr['per'] ?>%"></span>
                            <span class="percent"
                                  style="left:<?= $arr['per'] ?>%"><?= Format::beautifyNumber($arr['cnt']) ?>명</span>
                        </div>
                    </td>
                    <td class="number"><?= Format::beautifyNumber($arr['cnt']) ?>명</td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
        <tfoot>
        <tr class="sum">
            <th class="sub_th">합계</th>
            <td class="number"></td>
            <td class="number"><?= Format::beautifyNumber($result['total']) ?>명</td>
        </tr>
        </tfoot>
        </table>
    </div>
</div>
