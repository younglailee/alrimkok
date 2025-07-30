<?php
/**
 * @file    index.php
 * @author  Alpha-Edu
 */

@$la = $_GET['la'];
$layout_arr = array('user', 'manager', 'admin', 'mobile', 'tutor', 'company', 'partner', 'cyber');
array_push($layout_arr, 'damc', 'grand', 'kepid');

$url = $_SERVER['REQUEST_URI'];
$host = $_SERVER['HTTP_HOST'];
if ($host == 'http://best1alpha.cafe24.com/webuser/page/main.html') {
    $la = 'mobile';
} else {
    $welcome_uri = '';
    if (!$la || !in_array($la, $layout_arr)) {
        $la = 'user';
    }
    $ua = $_SERVER['HTTP_USER_AGENT'];
}
$welcome_uri = '/web' . $la . '/page/main.html';

if ($la == 'tutor') {
    $welcome_uri = '/web' . $la . '/contents/list.html';
} else if ($la == 'partner') {
    //$welcome_uri = '/web' . $la . '/news/list.html';
} else if ($la == 'company') {
    //$welcome_uri = '/web' . $la . '/progress/list.html';
}
//header('HTTP/1.1 301 Moved Permanently');
//header('Location: ' . $welcome_uri);

// HTML 문서 제목 분기문 yllee 210910
$html_title = '알림콕 Alrim-KOK';
$html_description = '당신에게 맞는 사업공고를 찾아드립니다.';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta name="NaverBot" content="All"/>
<meta name="NaverBot" content="index,follow"/>
<meta name="Yeti" content="All"/>
<meta name="Yeti" content="index,follow"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1"/>
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta name="author" content="Alpha-Edu"/>
<meta name="copyright" content="COPYRIGHT &copy; 2022 Alpha-Edu ALL RIGHT RESERVED."/>
<meta name="language" content="ko"/>
<meta name="description" content="<?= $html_description ?>"/>
<meta property="og:description" content="<?= $html_description ?>"/>
<title><?= $html_title ?></title>
<link rel="shortcut icon" href="/favicon.ico"/>
<script type="text/javascript">
//<![CDATA[
<?php
if ($la != 'cyber') {
?>
self.location.href = "<?= $welcome_uri ?>";
<?php
}
?>
//]]>
</script>
</head>
<body>
<?php
if ($la == 'cyber') {
    //echo '<h1>해당 주소는 전국화학노동조합연맹 인터넷연수원입니다.</h1>';
    //echo '<div>법정필수, 산업안전, 고용보험 환급 과정 기업은 아래 [알파에듀 학습관리시스템]에 접속하시면 됩니다.</div>';
    //echo '<div><br/><a href="https://alpha-edu.co.kr">[알파에듀 학습관리시스템 접속하기]</a></div>';
}
?>
</body>
</html>
