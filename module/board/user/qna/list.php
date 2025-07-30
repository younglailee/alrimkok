<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Format;
use sFramework\Html;
use sFramework\QnaUser;

if (!defined('_ALPHA_')) {
    exit;
}
global $member;
global $layout_uri;
global $layout;
// XSS 필터링 적용 yllee 220811
$sch_text = Format::filterXss($_GET['sch_text']);

$bd_code = ($_GET['bd_code']) ? $_GET['bd_code'] : $_POST['bd_code'];
$sch_text = ($_GET['sch_text']) ? $_GET['sch_text'] : $_POST['sch_text'];

/* set URI */
$this_uri = '/web' . $layout . '/' . $bd_code . '/list.html';
$doc_title = '1:1문의';

// 로그인 창 이동 후 로그인 시 해당 페이지로 리턴
if (!$member['mb_id']) {
    $move_url = $layout_uri . '/member/login.html?return_uri=' . $this_uri;
    Html::alert('로그인 후 이용 가능합니다.', $move_url);
}
/* init Class */
$oBoard = new QnaUser();
$oBoard->init();
$pk = $oBoard->get('pk');

/* list */
$list = $oBoard->selectList();
$cnt_total = $oBoard->get('cnt_total');

/* search condition */
$search_like_arr = $oBoard->get('search_like_arr');
$search_date_arr = $oBoard->get('search_date_arr');
$query_string = $oBoard->get('query_string');

/* pagination */
$page = $oBoard->get('page');
$page_arr = $oBoard->getPageArray();

/* code */
$bd_category_arr = $oBoard->get('bd_category_arr');
$bd_state_arr = $oBoard->get('bd_state_arr');

$colspan = 5;
?>
<style>
.no_data { width:100%; }
</style>

<section id="inquiry" class="contents">
    <div class="container">
        <h2 class="sec-title">1:1 문의</h2>

        <div class="search">
            <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
            <input type="hidden" name="sch_like" value="bd_subject"/>
            <input type="search" name="sch_text" value="<?= $sch_text ?>" placeholder="검색어를 입력하세요.">
            <button class="btn_search">검색</button>
            </form>
        </div>

        <p class="list-total">총 <span><?= $cnt_total ?></span>건</p>

        <table class="list-table">
        <thead>
        <tr>
            <th class="num" scope="col">번호</th>
            <th class="title" scope="col">제목</th>
            <th class="file" scope="col">첨부파일</th>
            <th class="date" scope="col">작성일</th>
            <th class="answer" scope="col">답변상태</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                $bd_state = $list[$i]['bd_state'];
                ?>
                <tr>
                    <td class="num"><?= $list[$i]['no'] ?></td>
                    <td class="title">
                        <a class="clamp c1"
                           href="./view.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['bd_subject'] ?></a>
                    </td>
                    <td class="file">
                        <?php
                        $file_list = $list[$i]['file_list'];
                        if ($file_list) {
                            for ($j = 0; $j < count($file_list); $j++) {
                                ?>
                                <a href="./download.html?fi_id=<?= $file_list[$j]['fi_id'] ?>" download="">
                                    <img class="of-ct" src="/common/img/user/icon/file-down.svg" alt="첨부파일">
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </td>
                    <td class="date"><?= $list[$i]['bt_reg_date'] ?></td>
                    <td class="answer"><?= $bd_state_arr[$bd_state] ?></td>
                </tr>
                <?php
            }
            echo (!count($list)) ? Html::makeNoTd($colspan) : '';
        } else {
            echo Html::makeNoTd($colspan);
        }
        ?>
        </tbody>
        </table>

        <a class="writeBtn" href="./write.html">문의하기</a>

        <ul class="pg-num">
        <?= Html::makePagination($page_arr, $query_string); ?>
        </ul>
    </div>
</section>
