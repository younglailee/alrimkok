<?php

use sFramework\BizUser;
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

global $member;
global $is_mobile;

/* set URI */
global $layout, $module;

$this_uri = '/web' . $layout . '/' . $module . '/list.html';

/* init Class */
$oBiz = new BizUser();
$oBiz->init();
$pk = $oBiz->get('pk');

$is_root = true;

$bz_id = $_GET['bz_id'];
//$oBiz->updateHits($bz_id);
$data = $oBiz->selectDetail($bz_id);
$file_list = $data['file_list'];
$oBiz->updateHits($bz_id);
$oBiz->updateRecent($bz_id);
?>
<script>
    $(function () {
        $('.ai-page').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const href = $(this).attr('href');
            const mb_id = $('input[name="mb_id"]').val();

            if (!mb_id) {
                alert('로그인이 필요합니다.');
            } else {
                location.href = href;
            }
        })
    })
</script>
<section id="noti-view" class="contents">
    <input type="hidden" name="mb_id" value="<?= $member['mb_id'] ?>"/>
    <input type="hidden" name="bz_id" value="<?= $bz_id ?>"/>
    <a class="top-banner ai-page" href="./prompt.html?bz_id=<?= $bz_id ?>">지금 보고 있는 공고! AI 제안서 받아보기</a>
    <div class="container">
        <h2 class="sec-title">모집중</h2>

        <div class="title-box">
            <h3><?= $data['bz_title'] ?></h3>
            <ul class="btnWrap">
                <li class="btn copy">
                    <div class="ico-wrap"><img class="of-ct" src="/common/img/user/icon/url-copy.svg" alt="URL복사"/><img
                                class="of-ct" src="/common/img/user/icon/url-copy-on.svg" alt="URL복사"/></div>
                    <p>URL복사</p>
                </li>
                <li class="btn print">
                    <div class="ico-wrap"><img class="of-ct" src="/common/img/user/icon/print.svg" alt="인쇄하기"/><img
                                class="of-ct" src="/common/img/user/icon/print-on.svg" alt="인쇄하기"/></div>
                    <p>인쇄하기</p>
                </li>
                <?php if ($data['is_like'] == 1) { ?>
                    <li class="btn wish wish02 on" data-bz="<?= $data['bz_id'] ?>">
                        <div class="ico-wrap"><img class="of-ct" src="/common/img/user/icon/wish.svg" alt="찜하기"/><img
                                    class="of-ct" src="/common/img/user/icon/wish-on.svg" alt="찜하기"/></div>
                        <p>찜하기</p>
                    </li>
                <?php } else { ?>
                    <li class="btn wish wish02" data-bz="<?= $data['bz_id'] ?>">
                        <div class="ico-wrap"><img class="of-ct" src="/common/img/user/icon/wish.svg" alt="찜하기"/><img
                                    class="of-ct" src="/common/img/user/icon/wish-line.svg" alt="찜하기"/></div>
                        <p>찜하기</p>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="text-content">
            <h4>신청방법 및 대상</h4>
            <p>
                <span class="title">신청기간</span><span><?= $oBiz->formatApplyDateRange($data['bz_apply_s_datetime'], $data['bz_apply_e_datetime']) ?></span>
            </p>
            <p><span class="title">신청방법</span><span><?= $data['bz_apply_rule'] ?></span></p>
            <p><span class="title">신청대상</span><span><?= $data['bz_apply_target'] ?></span></p>
        </div>

        <div class="text-content">
            <h4>지원내용</h4>
            <p><?= $oBiz->formatNumberedListWithBr($data['bz_support_content']) ?></p>
        </div>

        <div class="text-content">
            <h4>신청절차 및 평가방법</h4>
            <p><?= $oBiz->formatNumberedListWithBr($data['bz_apply_process']) ?></p>
        </div>

        <div class="text-content">
            <h4>제출서류</h4>
            <p><?= $oBiz->formatNumberedListWithBr($data['bz_submit_docs']) ?></p>
        </div>

        <div class="text-content cs">
            <h4>문의처</h4>
            <p><?= str_replace(',', '<br/>', $data['bz_contact_info']) ?></p>
        </div>

        <a class="ai-btn pc ai-page" href="./prompt.html?bz_id=<?= $bz_id ?>"><p>AI 제안서 받아보기</p></a>

        <div class="file-wrap text-content02">
            <h4>첨부파일</h4>
            <?php for ($i = 0; $i < count($file_list); $i++) { ?>
                <div class="file-box">
                    <a class="name" href="./download.html?fi_id=<?= $file_list[$i]['fi_id'] ?>" target="_blank"
                       download><?= $file_list[$i]['fi_name'] ?></a>
                    <div class="file-btn">
                        <a class="view-btn" href="./download.html?fi_id=<?= $file_list[$i]['fi_id'] ?>" target="_blank"
                           download>다운로드</a>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="memo text-content02 <?=$data['bm_content'] ? 'memo-on' : ''?>">
            <h4>메모장</h4>
            <div class="memo-box memo-btn" data-bz="<?=$data['bz_id']?>">
                <?php if ($data['bm_content']) { ?>
                    <p><?=$data['bm_content']?></p>
                <?php } else { ?>
                    <p>해당 공고와 관련된 중요한 내용을 메모해보세요. <span>Ex) 나중에 지원서 작성하기</span></p>
                <?php } ?>
            </div>
        </div>

        <a class="ai-btn mobile ai-page" href="./prompt.html?bz_id=<?= $bz_id ?>"><p>AI 제안서 받아보기</p></a>

        <a class="list-btn" href="./list.html"><p>목록</p></a>

    </div>
</section>


<div id="" class="pop-memo pop-up">
    <div class="bg"></div>
    <div class="pop-con">
        <p class="title">메모</p>
        <p class="txt">공고가 삭제될 경우, 작성된 메모도 삭제됩니다.</p>

        <form id="memoForm">
            <div class="scroll">
                <?php if ($data['bm_content']) { ?>
                    <textarea id="memo_txt" name="memo_txt" ><?= $data['bm_content'] ?></textarea>
                <?php } else { ?>
                    <textarea id="memo_txt" name="memo_txt"
                              placeholder="찜한 공고에 관련한 중요한 내용을 메모해보세요."></textarea>
                <?php } ?>
            </div>
            <div class="btn-wrap">
                <button type="button" class="btn-cancel">취소</button>
                <button type="button" class="btn-save" data-bz="">저장</button>
            </div>
        </form>

        <div class="pop-close"><img src="/common/img/user/icon/close.svg" class="of-ct" alt="닫기"/></div>
    </div>
</div>
