<?php
/**
 * @file    download.php
 * @author  Alpha-Edu
 */
use sFramework\QnaAdmin;
use sFramework\Html;
use sFramework\Session;
use sFramework\Db;

if (!defined('_ALPHA_')) {
    exit;
}

/* variable */
$bd_code = $_GET['bd_code'];
$fi_id = $_GET['fi_id'];

/* board code name */
$bd_name_arr = array(
    'form' => '서식자료',
    'research' => '연구및교육자료'
);

/* init Class */
$oBoard = new QnaAdmin();
$oBoard->init();

/* check auth */
// 서식자료, 연구및교육자료 게시판 파일 다운로드 시 작동 yllee 180906
if ($bd_code == 'form' || $bd_code == 'research') {
    if (!$oBoard->checkDownloadSession()) {
        Html::movePage('./certification.html?return_uri=' . urlencode($_SERVER['REQUEST_URI']));
    }
    // 휴대폰 인증 후 다운로드 내역 기록 yllee 180906
    $ss_bd_writer_name = Session::getSession('ss_bd_writer_name');
    $ss_bd_writer_tel = Session::getSession('ss_bd_writer_tel');

    $file_table = $oBoard->get('file_table');
    $file_pk = $oBoard->get('file_pk');
    $fi_data = Db::selectOnce($file_table, "fi_name, fi_uid", "WHERE $file_pk = '$fi_id'", "");
    $fi_name = $fi_data['fi_name'];
    $fi_uid = $fi_data['fi_uid'];
    $bd_data = Db::selectOnce('tbl_board', "bd_code", "WHERE bd_id = '$fi_uid'", "");
    $bd_code = $bd_data['bd_code'];
    $bd_category = $bd_name_arr[$bd_code];

    $data = array();
    $data['us_name'] = $ss_bd_writer_name;
    $data['us_tel'] = $ss_bd_writer_tel;
    $data['fi_id'] = $fi_id;
    $data['fi_name'] = $fi_name;
    $data['bd_category'] = $bd_category;

    $result = $oBoard->insetDownload($data);
}

/* Download File */
$oBoard->downloadFile($fi_id);
