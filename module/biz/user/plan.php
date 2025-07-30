<?php

use sFramework\BizUser;

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
$bz_id = $_POST['bz_id'] ?? $_GET['bz_id'];
$bp_data = $oBiz->getDataProposal($bz_id);
$plan_result = array(
    'proposal_purpose' => $bp_data['bp_purpose'],
    'execution_scope' => $bp_data['bp_scope'],
    'advantages' => $bp_data['bp_advantages'],
    'company_status' => $bp_data['bp_status'],
    'organization_and_staff' => $bp_data['bp_organization'],
    'project_summary' => $bp_data['bp_summary'],
    'strategy' => $bp_data['bp_strategy'],
    'main_content' => $bp_data['bp_content'],
    'detailed_plan' => $bp_data['bp_plan'],
    'schedule' => $bp_data['bp_schedule'],
    'reporting_plan' => $bp_data['bp_reporting'],
    'task_assignment' => $bp_data['bp_task'],
    'personnel_info' => $bp_data['bp_personnel'],
    'admin_management' => $bp_data['bp_management'],
    'output_management' => $bp_data['bp_output'],
    'budget_plan' => $bp_data['bp_budget'],
);

if (!$bp_data['bp_id']) {
    $plan_result = $oBiz->makePlan();
}
?>

<section id="answer" class="contents">
    <div class="container tab-group">
        <h2 class="sec-title">AI 제안서 받아보기</h2>

        <ul class="tab">
            <li class="active">제안개요</li>
            <li>제안사 일반</li>
            <li>사업수행부문</li>
            <li>사업관리부문</li>
            <li>성과관리</li>
        </ul>
        <form action="./process.html" method="post">
            <input type="hidden" name="bz_id" value="<?= $bz_id ?>"/>
            <input type="hidden" name="mode" value="update_plan"/>
            <div class="tab-content proposalForm">
                <button id="saveBtn" type="button" class="btn-save">
                    <div class="ico-wrap"><img class="of-ct" src="/common/img/user/icon/save.svg" alt="저장"/></div>
                    <p>제안서 저장</p>
                </button>
                <!-- 제안개요 -->
                <div class="tab-panel active">
                    <fieldset>
                        <legend>제안개요</legend>
                        <div class="prompt-input">
                            <label for="purpose">1. 제안목적</label>
                            <textarea id="purpose"
                                      name="proposal_purpose"><?= $plan_result['proposal_purpose'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="scope">2. 수행범위</label>
                            <textarea id="scope"
                                      name="execution_scope"><?= $plan_result['execution_scope'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="features">3. 제안의 특징 및 장점</label>
                            <textarea id="features" name="advantages"><?= $plan_result['advantages'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>
                    </fieldset>
                </div>

                <!-- 제안사 일반 -->
                <div class="tab-panel">
                    <fieldset>
                        <legend>제안사 일반</legend>

                        <div class="prompt-input">
                            <label for="general_status">1. 일반현황</label>
                            <textarea id="general_status"
                                      name="company_status"><?= $plan_result['company_status'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="organization">2. 조직 및 인원</label>
                            <textarea id="organization"
                                      name="organization_and_staff"><?= $plan_result['organization_and_staff'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>
                    </fieldset>
                </div>

                <!-- 사업수행부문 -->
                <div class="tab-panel">
                    <fieldset>
                        <legend>사업수행부문</legend>

                        <div class="prompt-input">
                            <label for="exec_summary">1. 개요</label>
                            <textarea id="exec_summary"
                                      name="project_summary"><?= $plan_result['project_summary'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="strategy">2. 추진목표 및 전략</label>
                            <textarea id="strategy" name="strategy"><?= $plan_result['strategy'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="main_content">3. 주요 사업내용</label>
                            <textarea id="main_content"
                                      name="main_content"><?= $plan_result['main_content'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="detail_plan">4. 세부과제별 추진방안</label>
                            <textarea id="detail_plan"
                                      name="detailed_plan"><?= $plan_result['detailed_plan'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>
                    </fieldset>
                </div>

                <!-- 사업관리부문 -->
                <div class="tab-panel">
                    <fieldset>
                        <legend>사업관리부문</legend>

                        <div class="prompt-input">
                            <label for="schedule_plan">1. 추진일정 계획</label>
                            <textarea id="schedule_plan" name="schedule"><?= $plan_result['schedule'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="review_plan">2. 업무보고 및 검토계획</label>
                            <textarea id="review_plan"
                                      name="reporting_plan"><?= $plan_result['reporting_plan'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="org_roles">3. 수행조직 및 업무분장</label>
                            <textarea id="org_roles"
                                      name="task_assignment"><?= $plan_result['task_assignment'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="participants">4. 참여인력 및 이력사항</label>
                            <textarea id="participants"
                                      name="personnel_info"><?= $plan_result['personnel_info'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>
                    </fieldset>
                </div>

                <!-- 성과관리 -->
                <div class="tab-panel">
                    <fieldset>
                        <legend>성과관리</legend>

                        <div class="prompt-input">
                            <label for="project_manage">1. 사업관리</label>
                            <textarea id="project_manage"
                                      name="admin_management"><?= $plan_result['admin_management'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="output_manage">2. 산출물 관리</label>
                            <textarea id="output_manage"
                                      name="output_management"><?= $plan_result['output_management'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>

                        <div class="prompt-input">
                            <label for="budget_plan">3. 예산계획</label>
                            <textarea id="budget_plan" name="budget_plan"><?= $plan_result['budget_plan'] ?></textarea>
                            <p class="txt-count"><span>0</span>자 작성중</p>
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="chk-01">
                <label class="chk">
                    <input type="checkbox" name="apply" id="apply"
                           value="Y" <?= $bp_data['ba_state'] == 'P' || $bp_data['ba_state'] == 'S' ? 'checked' : '' ?>/>
                    해당 공고에 지원할 예정입니다.
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="btn-wrap">
                <a href="#" class="btn-small btn01" onclick="goBack()">이전</a>
                <button class="btn-small btn02" type="submit">저장</button>
            </div>
        </form>
    </div>
</section>

<script>
    function goBack() {
        window.history.back();
    }

    $('#saveBtn').on('click', function () {
        const bz_id = $("input[name='bz_id']").val();
        window.open("./pdf.plan.html?bz_id=" + bz_id, '_blank');
    });

</script>
