<?php

use sFramework\BizUser;

$flag_use_head = false;
$flag_use_header = false;
$flag_use_footer = false;

/*
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
*/
$bz_id = $_GET['bz_id'];
/* init Class */
$oBiz = new BizUser();
$oBiz->init();

$data = $oBiz->getDataProposal($bz_id);
$bz_data = $oBiz->selectDetail($bz_id);

require_once _PLUGIN_PATH_ . '/tcpdf/tcpdf_import.php';

// ✅ 1. 커스텀 Footer 추가용 TCPDF 확장 클래스
class MYPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('nanumbarungothic', '', 9);

        $pageText = $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
        $pageWidth = $this->getPageWidth();
        $textWidth = $this->GetStringWidth($pageText);

        $this->SetX(($pageWidth - $textWidth) / 2);
        $this->Cell($textWidth, 10, $pageText, 0, 0, 'L');
    }
}

// ✅ 2. PDF 객체 생성 및 설정
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AlphaEdu');
$pdf->SetTitle($bz_data['bz_title']);
$pdf->SetSubject('사업 제안서');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->SetFont('nanumbarungothic', '', 11);

// ✅ 3. 커버 페이지
$pdf->AddPage();
$pdf->SetFont('nanumbarungothic', 'B', 22);
$pdf->MultiCell(0, 20, $bz_data['bz_title'], 0, 'C', false, 1, '', '', true, 0, false, true, 40, 'M');

$pdf->SetFont('nanumbarungothic', '', 16);
$pdf->Ln(10);
$pdf->MultiCell(0, 10, '사업 제안서', 0, 'C');
$pdf->Ln(40);
$pdf->SetFont('nanumbarungothic', '', 12);
$pdf->MultiCell(0, 10, '제출일: ' . date('Y년 m월 d일'), 0, 'C');

// ✅ 4. 본문 페이지 시작
$pdf->AddPage();

// ✅ 5. 콘텐츠 작성 함수 정의
function renderSection($pdf, $title, $contents) {
    $pdf->Ln(6);
    $pdf->SetFont('nanumbarungothic', 'B', 14);
    $pdf->MultiCell(0, 8, $title, 0, 'L');
    $pdf->Ln(2);
    $pdf->SetFont('nanumbarungothic', '', 11);
    $pdf->MultiCell(0, 6, $contents ?: '-', 0, 'L');
}

// ✅ 6. 전체 섹션 렌더링
renderSection($pdf, '1. 제안 목적', $data['bp_purpose']);
renderSection($pdf, '2. 수행 범위', $data['bp_scope']);
renderSection($pdf, '3. 제안의 특징 및 장점', $data['bp_advantages']);

$pdf->AddPage();
$pdf->SetFont('nanumbarungothic', 'B', 16);
$pdf->MultiCell(0, 10, 'Ⅱ. 제안사 일반', 0, 'L');
renderSection($pdf, '1. 일반 현황', $data['bp_status']);
renderSection($pdf, '2. 조직 및 인원', $data['bp_organization']);

$pdf->AddPage();
$pdf->SetFont('nanumbarungothic', 'B', 16);
$pdf->MultiCell(0, 10, 'Ⅲ. 사업수행부문', 0, 'L');
renderSection($pdf, '1. 개요', $data['bp_status']);
renderSection($pdf, '2. 추진목표 및 전략', $data['bp_organization']);
renderSection($pdf, '3. 주요 사업내용', $data['bp_organization']);
renderSection($pdf, '4. 세부과제별 추진방안', $data['bp_organization']);

$pdf->AddPage();
$pdf->SetFont('nanumbarungothic', 'B', 16);
$pdf->MultiCell(0, 10, 'Ⅳ. 사업관리부문', 0, 'L');
renderSection($pdf, '1. 추진일정 계획', $data['bp_status']);
renderSection($pdf, '2. 업무보고 및 검토계획', $data['bp_organization']);
renderSection($pdf, '3. 수행조직 및 업무분장', $data['bp_organization']);
renderSection($pdf, '4. 참여인력 및 이력사항', $data['bp_organization']);

$pdf->AddPage();
$pdf->SetFont('nanumbarungothic', 'B', 16);
$pdf->MultiCell(0, 10, 'Ⅴ. 성과관리', 0, 'L');
renderSection($pdf, '1. 사업관리', $data['bp_status']);
renderSection($pdf, '2. 산출물 관리', $data['bp_organization']);
renderSection($pdf, '3. 예산계획', $data['bp_organization']);

// ✅ 7. 파일 출력
$filename = 'alrimkok_proposal_' . date('Ymd_His') . '.pdf';
$encoded_filename = rawurlencode($filename);

header('Content-Type: application/pdf');
header("Content-Disposition: inline; filename*=UTF-8''{$encoded_filename}");
header('Cache-Control: private');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

$pdf->Output($filename, 'I');
exit;