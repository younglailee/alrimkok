<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\QnaAdmin;

if (!defined('_ALPHA_')) {
    exit;
}

$doc_title = 'Q&A ';

$body_class = 'intranet_list sub';
$doc_title = ''; //페이지 타이틀, menu와 연동
$layout_size = '';

/* init Class */
$oBoard = new QnaAdmin();
$oBoard->init();
$pk = $oBoard->get('pk');
/* check auth */
if (!$oBoard->checkListAuth()) {
    Html::alert('권한이 없습니다.');
}
/* list */
$list = $oBoard->selectList();
$cnt_total = $oBoard->get('cnt_total');
/* search condition */
$search_like_arr = $oBoard->get('search_like_arr');
$search_date_arr = $oBoard->get('search_date_arr');
$year_arr = $oBoard->get('year_arr');
$month_arr = $oBoard->get('month_arr');
$date_arr = $oBoard->get('date_arr');
$query_string = $oBoard->get('query_string');
/* pagination */
$page = $oBoard->get('page');
$page_arr = $oBoard->getPageArray();
/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_state = $oBoard->get('flag_use_state');
/* code */
if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}
/* colspan */
$colspan = 4;
if ($flag_use_category) {
    $colspan++;
}
if ($flag_use_state) {
    $colspan++;
    $bd_state_arr = $oBoard->get('bd_state_arr');
}
/* notice */
$notice_list = $oBoard->selectNoticeList();

$sch_bd_category = $_GET['sch_bd_category'];
$sch_bd_state = $_GET['sch_bd_state'];
?>
<div id="intranet" class="main">
    <div id="board">
        <!-- search -->
        <div class="search">
            <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
            <fieldset>
            <legend><i class="xi-search"></i> 검색조건</legend>

            <table class="search_table" border="1">
            <caption>검색조건</caption>
            <colgroup>
            <col width="90"/>
            <col width="370"/>
            <col width="80"/>
            <col width="*"/>
            <col width="90"/>
            <col width="*"/>
            </colgroup>
            <tbody>
            <tr>
                <th><label for="sch_text">검색어</label></th>
                <td>
                    <select name="sch_like" class="select" title="검색컬럼">
                    <option value="all">통합검색</option>
                    <option value="bd_subject">제목</option>
                    <option value="bd_content">내용</option>
                    <option value="bd_writer_name">작성자명</option>
                    <option value="reg_id">아이디</option>
                    </select>
                    <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text serchicon"
                           size="30"
                           maxlength="30" title="검색어"/>
                </td>
                <th>상태</th>
                <td>
                    <select name="sch_bd_state" id="sch_bd_state" class="select" title="검색컬럼">
                    <option value="">전체</option>
                    <?= Html::makeSelectOptions($bd_state_arr, $sch_bd_state, 1) ?>
                    </select>
                </td>
                <th>카테고리</th>
                <td>
                    <select name="sch_bd_category" id="sch_bd_category" class="select" title="검색컬럼">
                    <option value="">전체</option>
                    <?= Html::makeSelectOptions($bd_category_arr, $sch_bd_category, 1) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="sch_s_date">기간</label></th>
                <td colspan="5">
                    <select name="sch_date" class="select" title="기간컬럼">
                    <?= Html::makeSelectOptions($search_date_arr, $sch_date, 1) ?>
                    </select>

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
                <a href="./list.html" class="sButton" title="초기화">초기화</a>
                <a href="./qna_list_excel.html?page=<?= $page ?><?= $query_string ?>"
                   class="sButton" title="엑셀출력">엑셀출력</a>
            </div>
            </form>
        </div>
        <!-- //search -->

        <!-- list -->
        <div class="list">
            <!-- list_header -->
            <div class="list_header">
                <div class="left">
                    <i class="xi-file-text-o"></i> 총 <strong><?= number_format($cnt_total) ?></strong>개의 게시글이 있습니다.
                </div>

                <div class="right">

                </div>
            </div>
            <!-- //list_header -->
            <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
            <fieldset>
            <legend>검색관련</legend>
            <input type="hidden" name="query_string" value=""/>
            <input type="hidden" name="page" value="1"/>
            </fieldset>

            <fieldset>
            <legend>자료목록</legend>
            <input type="hidden" name="mode" value="delete"/>

            <!-- list_table -->
            <table class="list_table border odd" border="1">
            <colgroup>
            <col width="30"/><!-- 체크박스 -->
            <col width="60"/><!-- 번호 -->
            <col width="120"/><!-- 카테고리 -->
            <col width="*"/><!-- 제목 -->
            <col width="120"/><!-- 작성자 -->
            <col width="90"/><!-- 작성일 -->
            <col width="80"/><!-- 상태 -->
            <col width="60"/><!-- 조회수 -->
            </colgroup>
            <thead>
            <tr>
                <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
                <th>번호</th>
                <th>카테고리</th>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일시</th>
                <th>상태</th>
                <th>조회수</th>
            </tr>
            </thead>
            <tbody>

            <!--리스트 for-->
            <?php
            for ($i = 0; $i < count($list); $i++) {
                $us_id = $list[$i]['reg_id'];
                $crm_url = '../user/crm.html?mb_id=' . $us_id;
                ?>

                <tr class="list_tr_0">
                    <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                                class="list_checkbox" title="선택/해제"/></td>
                    <td><?= $list[$i]['no'] ?></td>
                    <td><?= $bd_category_arr[$list[$i]['bd_category']] ?></td>
                    <td class="">
                        <a href="/webadmin/qna/view.html?bd_id=<?= $list[$i]['bd_id'] ?>">
                            <?= $list[$i]['bd_subject'] ?>
                        </a>
                    </td>
                    <td><a href="<?= $crm_url ?>"
                           target="_blank"><?= $list[$i]['bd_writer_name'] ?></a><br/>(<?= $list[$i]['reg_id'] ?>)
                    </td>
                    <td>
                        <?php
                        $data_date = str_replace('-', '.', $list[$i]['bd_reg_date']);
                        ?>
                        <?= $list[$i]['reg_time'] ?>
                    </td>
                    <td><?= $list[$i]['txt_bd_state'] ?></td>
                    <td><?= $list[$i]['bd_hit'] ?></td>
                </tr>
            <?php
             }
            echo (!count($list)) ?
                '<tr class="">
                            <td colspan="8" class="no_data">등록된 질문이 없습니다.</td>
                        </tr>' : '';
            ?>
            </tbody>
            </table>
            <!-- //list_table -->

            <!-- list_footer -->
            <div class="list_footer">
                <div class="left">
                    <button type="submit" class="sButton small">선택삭제</button>
                </div>
                <div class="right">

                </div>
            </div>
            <!-- //list_footer -->

            </form>

            <!-- pagination -->
            <div class="pagination">
                <ul>
                <?= Html::makePagination($page_arr, $query_string); ?>
                </ul>
            </div>
            <!-- //pagination -->

        </div>
        <!-- //list -->
    </div>
</div>

<div id="layer_popup" style="">
    <div id="layer_header">
        <h1>레이어팝업</h1>
        <button type="button" onclick="closeLayerPopup()" title="닫기"><i class="xi-close-square"></i></button>
    </div>

    <div id="layer_content">
        <div class="whoread group">
            <div class="left">
                <ul>
                <li class="tit"><strong>읽음</strong></li>
                <li><strong>김읽음</strong> {0000.00.00}</li>
                <li><strong>이읽음</strong> {0000.00.00}</li>
                <li><strong>박읽음</strong> {0000.00.00}</li>
                <li><strong>김읽음</strong> {0000.00.00}</li>
                <li><strong>이읽음</strong> {0000.00.00}</li>
                <li><strong>박읽음</strong> {0000.00.00}</li>

                </ul>
            </div>
            <div class="right">
                <ul>
                <li class="tit"><strong>안읽음</strong></li>
                <li><strong>노읽음</strong> {0000.00.00}</li>
                <li><strong>안읽음</strong> {0000.00.00}</li>
                <li><strong>몬읽음</strong> {0000.00.00}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

