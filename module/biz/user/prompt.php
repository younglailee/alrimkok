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
$bp_data = $oBiz->getDataProposal($bz_id);
if($bp_data['bp_id']){
    Html::alert('이미 제안서 작성을 진행 중입니다.','./plan.html?bz_id='.$bz_id);
}
$fi_data = $oBiz->getCompanyIntroduction();
?>
<script>
    $(document).ready(function() {
        $('#about_company').on('change', function() {
            const file = this.files[0];
            if (!file) return;

            const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            const fileName = file.name.toLowerCase();
            const fileExtension = fileName.split('.').pop();

            if (!allowedExtensions.includes(fileExtension)) {
                alert('허용되지 않은 파일 형식입니다. PDF 또는 이미지 파일(jpg, jpeg, png)만 업로드 가능합니다.');
                $(this).val(''); // 선택된 파일 초기화
            } else {
                // 유효한 파일이면 미리보기 표시 등 추가 작업 가능
                console.log('업로드 가능:', file.name);
            }
            updateFileName('#about_company');
            updateFileName('#companyFile');
        });
    });

    function updateFileName(inputSelector) {
        $(inputSelector).on('change', function () {
            const fileName = $(this).val().split('\\').pop();
            const targetSelector = $(this).data('target');

            if (targetSelector) {
                const $label = $(targetSelector);
                $label.text(fileName || $label.data('placeholder') || '파일을 등록해주세요.');
            }
        });
    }
</script>
<section id="prompt" class="contents">
    <div class="container">
        <h2 class="sec-title">AI 제안서 받아보기</h2>
        <form action="./plan.html" method="post" enctype="multipart/form-data">
            <input type="hidden" name="bz_id" value="<?=$_GET['bz_id']?>"/>
            <div class="proposalForm">
                <fieldset>
                    <legend>AI 제안서 받아보기</legend>
                    <div class="prompt-input file-input">
                        <label for="about_company">1. 기업소개서</label>
                        <div class="upload-box">
                            <?php if (!empty($fi_data['fi_id'])): ?>
                                <input type="hidden" name="fi_id" value="<?= htmlspecialchars($fi_data['fi_id']) ?>">
                            <?php endif; ?>
                            <span class="placeholder-text clamp c1" id="filePlaceholder"><?= $fi_data['fi_id'] ? $fi_data['fi_name'] : "기업소개서를 업로드 해주세요. (PDF 또는 이미지 파일만 업로드 가능합니다.)"?></span>
                            <input id="about_company" type="file" data-target="#filePlaceholder" data-placeholder="<?= $fi_data['fi_id'] ? $fi_data['fi_name'] : "기업소개서를 업로드 해주세요. (PDF 또는 이미지 파일만 업로드 가능합니다.)"?>">
                            <label for="about_company" class="btn-upload">등록하기</label>
                        </div>
                    </div>
                    <div class="prompt-input">
                        <label for="proposal_prompt">2. 제안서 프롬프트</label>
                        <textarea id="proposal_prompt" name="proposal_prompt" rows="5" placeholder="받아보고 싶은 사업 아이템 관련하여 편하게 작성해주세요."></textarea>
                        <p class="txt-count"><span>0</span>자 작성중</p>
                    </div>
                </fieldset>
            </div>

            <div class="btn-wrap">
                <button class="btn-small btn01" type="submit">받아보기</button>
            </div>
        </form>
    </div>
</section>
