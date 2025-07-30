<?php
global $member, $sv;
$li_class1 = '';
$li_class2 = '';
$li_class3 = '';
//echo $sv;
if ($sv == 'apply_list' || $sv == 'progress_list' || $sv == 'selection_list') {
    $li_class1 = ' class="active"';
} elseif ($sv == 'wish_list' || $sv == 'wish_calender') {
    $li_class2 = ' class="active"';
} elseif ($sv == 'modify_info') {
    $li_class3 = ' class="active"';
}
?>
<li<?= $li_class1 ?>><a href="./apply_list.html">지원현황</a></li>
<li<?= $li_class2 ?>><a href="./wish_list.html">찜한 공고</a></li>
<li<?= $li_class3 ?>><a href="./modify_info.html">가입정보 수정</a></li>
<?php
$mb_level = $member['mb_level'];
if ($mb_level == 1) {
    ?>
    <li><a href="./sub_list.html">부계정 관리</a></li>
    <?php
}
