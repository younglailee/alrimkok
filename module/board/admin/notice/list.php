<?php
/**
 * @file    list.php
 * @author  Alpha-Edu
 */

use sFramework\Html;
use sFramework\NoticeAdmin;

if (!defined('_ALPHA_')) {
    exit;
}
global $module;

/* init Class */
$oBoard = new NoticeAdmin();
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
$sch_bd_state = $_GET['sch_bd_state'];
$sch_bd_is_display = $_GET['sch_bd_is_display'];
$sch_like = $_GET['sch_like'];
$sch_text = $_GET['sch_text'];
$sch_year = $_GET['sch_year'];
$sch_month = $_GET['sch_month'];
$sch_s_date = $_GET['sch_s_date'];
$sch_e_date = $_GET['sch_e_date'];
$search_like_arr = $oBoard->get('search_like_arr');
$search_date_arr = $oBoard->get('search_date_arr');
$query_string = $oBoard->get('query_string');
$year_arr = $oBoard->get('year_arr');
$month_arr = $oBoard->get('month_arr');
$date_arr = $oBoard->get('date_arr');

/* pagination */
$page = $oBoard->get('page');
$page_arr = $oBoard->getPageArray();

/* config */
$flag_use_category = $oBoard->get('flag_use_category');
$flag_use_bgn = $oBoard->get('flag_use_bgn');
$flag_use_end = $oBoard->get('flag_use_end');
$flag_use_state = $oBoard->get('flag_use_state');

/* code */
$bd_is_notice_arr = $oBoard->get('bd_is_notice_arr');
$bd_is_display_arr = $oBoard->get('bd_is_display_arr');
$bd_auth_arr = $oBoard->get('bd_auth_arr');

$sch_bd_auth = $_GET['sch_bd_auth'];

if ($flag_use_category) {
    $bd_category_arr = $oBoard->get('bd_category_arr');
}
if ($flag_use_state) {
    $bd_state_arr = $oBoard->get('bd_state_arr');
}
/* colspan */
$colspan = 7;
$bd_code = $_GET['bd_code'];
if ($bd_code == 'article') {
    $colspan = 8;
}
if ($flag_use_category) {
    $colspan++;
}
if ($flag_use_bgn) {
    $colspan++;
}
if ($flag_use_state) {
    $colspan++;
}
if ($bd_code == 'safe' || $bd_code == 'snotice') {
    $colspan = 7;
} elseif ($bd_code == 'cloud') {
    $colspan = 7;
}
?>
<script type="text/javascript">
//<![CDATA[
$(function() {
});
//]]>
</script>
<div id="<?= $module ?>">
    <div class="search">
        <form name="search_form" action="./list.html" method="get" onsubmit="return submitSearchForm(this)">
        <fieldset>
        <legend><i class="xi-search"></i> 검색조건</legend>
        <table class="search_table" border="1">
        <caption>검색조건</caption>
        <colgroup>
        <col width="90"/>
        <col width="*"/>
        <col width="90"/>
        <col width="*"/>
        </colgroup>
        <tbody>
        <?php
        if ($flag_use_state) {
            ?>
            <tr>
                <th>상태</th>
                <td>
                    <input type="radio" name="sch_bd_state" id="sch_bd_state_all" value=""
                           class="radio" <?= (!$sch_bd_state) ? 'checked="checked"' : null ?> title="전체"/>
                    <label for="sch_bd_state_all">전체</label>
                    <?= Html::makeRadio('sch_bd_state', $bd_state_arr, $sch_bd_state, 1) ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <th><label for="sch_text">검색어</label></th>
            <td>
                <select name="sch_like" class="select" title="검색컬럼">
                <?= Html::makeSelectOptions($search_like_arr, $sch_like, 1) ?>
                </select>
                <input type="text" name="sch_text" id="sch_text" value="<?= $sch_text ?>" class="text"
                       size="30" maxlength="30" title="검색어"/>
            </td>
            <?php
            if ($bd_code == 'notice') {
                ?>
                <th><label for="sch_bd_is_display">출력여부</label></th>
                <td>
                    <select name="sch_bd_is_display" id="sch_bd_is_display" class="select" title="출력여부">
                    <option value="">전체</option>
                    <?= Html::makeSelectOptions($bd_is_display_arr, $sch_bd_is_display, 1) ?>
                    </select>
                </td>
                <?php
            } elseif ($bd_code == 'news') {
                ?>
                <th><label for="sch_bd_auth">읽기권한</label></th>
                <td>
                    <select name="sch_bd_auth" id="sch_bd_auth" class="select" title="읽기권한">
                    <option value="">전체</option>
                    <?= Html::makeSelectOptions($bd_auth_arr, $sch_bd_auth, 1) ?>
                    </select>
                </td>
                <?php
            }
            ?>
        </tr>
        <tr>
            <th><label for="sch_s_date">작성일</label></th>
            <td colspan="3">
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
            </div>
        </div>

        <form name="list_form" method="post" action="./process.html" onsubmit="return submitListForm(this)">
        <fieldset>
        <legend>검색관련</legend>
        <input type="hidden" name="query_string" value="<?= $query_string ?>"/>
        <input type="hidden" name="page" value="<?= $page ?>"/>
        </fieldset>

        <fieldset>
        <legend>자료목록</legend>
        <input type="hidden" name="mode" value="delete"/>
        <table class="list_table border odd" border="1">
        <colgroup>
        <col width="30"/><!-- 체크박스 -->
        <col width="60"/><!-- 번호 -->
        <?= ($bd_code == 'reference') ? '<col width="240" />' : '' ?><!-- 과정명 -->
        <col width="*"/><!-- 제목 -->
        <?php
        if ($bd_code == 'article') {
            ?>
            <col width="100"/>
            <col width="260"/>
            <?php
        }
        ?>
        <col width="90"/><!-- 첨부파일 -->
        <?php
        if ($bd_code == 'notice') {
            ?>
            <col width="90"/><!-- 출력여부 -->
            <?php
        }
        if ($bd_code == 'news') {
            ?>
            <col width="120"/><!-- 권한 -->
            <col width="80"/><!-- 작성자 -->
            <?php
        } else {
            ?>
            <col width="120"/><!-- 작성자 -->
            <?php
        }
        ?>
        <col width="90"/><!-- 작성일 -->
        <?= ($flag_use_bgn) ? '<col width="160" />' : '' ?><!-- 진행기간 -->
        <?= ($flag_use_state) ? '<col width="60" />' : '' ?><!-- 상태 -->
        <?php
        if ($bd_code != 'article') {
            ?>
            <col width="60"/><!-- 조회수 -->
            <?php
        }
        ?>
        </colgroup>
        <thead>
        <tr>
            <th><input type="checkbox" id="all_checkbox" title="전체선택"/></th>
            <th>No</th>
            <?= ($bd_code == 'reference') ? '<th>과정명</th>' : '' ?>
            <th>제목</th>
            <?php
            if ($bd_code == 'article') {
                ?>
                <th>언론사</th>
                <th>링크주소</th>
                <?php
            }
            ?>
            <th>첨부파일</th>
            <?php
            if ($bd_code == 'notice') {
                ?>
                <th>출력여부</th>
                <?php
            }
            if ($bd_code == 'news') {
                ?>
                <th>읽기권한</th>
                <?php
            }
            ?>
            <th>작성자</th>
            <th>작성일</th>
            <?= ($flag_use_bgn) ? '<th>진행기간</th>' : '' ?>
            <?= ($flag_use_state) ? '<th>상태</th>' : '' ?>
            <?php
            if ($bd_code != 'article') {
                ?>
                <th>조회수</th>
                <?php
            } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (is_array($list)) {
            for ($i = 0; $i < count($list); $i++) {
                $file_list = $list[$i]['file_list'];
                ?>
                <tr class="list_tr_<?= $list[$i]['odd'] ?>">
                    <td class="checkbox"><input type="checkbox" name="list_uid[]" value="<?= $list[$i][$pk] ?>"
                                                class="list_checkbox" title="선택/해제"/></td>
                    <td><?= ($list[$i]['bd_is_notice'] == 'Y') ? '공지' : $list[$i]['no'] ?></td>
                    <?php
                    if ($bd_code == 'reference') {
                        ?>
                        <td><?= $list[$i]['bd_etc1'] ?></td>
                        <?php
                    }
                    ?>
                    <?php
                    if ($bd_code == 'article') {
                        ?>
                        <td class="">
                            <a href="./write.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['bd_subject'] ?></a>
                            <?= ($list[$i]['cnt_file']) ? '<i class="xi-save file"></i>' : '' ?>
                            <?= ($list[$i]['is_new']) ? '<i class="xi-new new"></i>' : '' ?>
                        </td>
                        <td><?= $list[$i]['bd_etc1'] ?></td>
                        <td class="overflow"><?= $list[$i]['bd_content'] ?></td>
                        <?php
                    } else {
                        ?>
                        <td class="">
                            <a href="./view.html?<?= $pk ?>=<?= $list[$i][$pk] ?>&page=<?= $page ?><?= $query_string ?>"><?= $list[$i]['bd_subject'] ?></a>
                            <?= ($list[$i]['cnt_file']) ? '<i class="xi-save file"></i>' : '' ?>
                            <?= ($list[$i]['is_new']) ? '<i class="xi-new new"></i>' : '' ?>
                        </td>
                        <?php
                    }
                    ?>
                    <td>
                        <?php
                        if ($file_list[0]) {
                            ?>
                            <a href="./download.html?fi_id=<?= $file_list[0]['fi_id'] ?>" class="btn_download"
                               target="_blank" title="새창 다운로드">
                                <img src="/common/img/exam/lms_download_icon.png"/>
                            </a>
                            <?php
                        }
                        ?>
                    </td>
                    <?php
                    if ($bd_code == 'notice') {
                        ?>
                        <td><?= $bd_is_display_arr[$list[$i]['bd_is_display']] ?></td><!-- 출력여부 -->
                        <?php
                    }
                    if ($bd_code == 'news') {
                        $txt_auth = '';
                        if ($list[$i]['bd_etc3'] == 'Y') {
                            $txt_auth .= '파트너';
                            $txt_auth .= '<br/>';
                        }
                        if ($list[$i]['bd_etc6'] == 'Y') {
                            $txt_auth .= '튜터';
                            $txt_auth .= '<br/>';
                        }
                        if ($list[$i]['bd_etc9'] == 'Y') {
                            $txt_auth .= '기업담당자';
                        }
                        if (!$txt_auth) {
                            $txt_auth = '관리자';
                        }
                        ?>
                        <td><?= $txt_auth ?></td><!-- 출력여부 -->
                        <?php
                    }
                    ?>
                    <td><?= $list[$i]['bd_writer_name'] ?></td>
                    <td><?= $list[$i]['bt_reg_date'] ?></td>
                    <?= ($flag_use_bgn) ? '<td>' . $list[$i]['bt_bd_bgn_date'] . (($flag_use_end) ? ' ~ ' . $list[$i]['bt_bd_end_date'] : '') . '</td>' : '' ?>
                    <?= ($flag_use_state) ? '<td><strong class="' . $list[$i]['state_class'] . '">' . $list[$i]['txt_bd_state'] . '</strong></td>' : '' ?>
                    <?php
                    if ($bd_code != 'article') {
                        ?>
                        <td><?= number_format($list[$i]['bd_hit']) ?></td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
            }
            echo (!count($list)) ? Html::makeNoTd($colspan) : null;
        }
        ?>
        </tbody>
        </table>
        <div class="list_footer">
            <div class="left">
                <button type="submit" class="sButton small">선택삭제</button>
            </div>
            <div class="right">
                <a href="./write.html?page=<?= $page ?><?= $query_string ?>" class="sButton small primary"
                   title="글쓰기">글쓰기</a>
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
