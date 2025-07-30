<?php
/**
 * Admin > Head 파일
 * @file    head.inc.php
 * @author  Alpha-Edu
 */

use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}
// http 사용자를 https 로 redirect 처리
Html::httpsRedirect();

global $html_title;
global $layout_uri;
global $js_uri;
global $layout;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<meta name="robots" content="noindex"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="imagetoolbar" content="no"/>
<meta name="author" content="Alpha-Edu(alpha@alpha-edu.co.kr)"/>
<meta name="copyright" content="COPYRIGHT &copy; 2016 alpha-edu.co.kr ALL RIGHT RESERVED."/>
<meta name="language" content="ko"/>
<title><?= $html_title ?></title>
<link rel="stylesheet" type="text/css" href="<?= $layout_uri ?>/layout.css"/>
<link rel="stylesheet" type="text/css" href="<?= $js_uri ?>/jquery-ui-1.11.4/jquery-ui.min.css"/>
<link rel="stylesheet" type="text/css" href="<?= $js_uri ?>/uniform-2.1.2/alpha.css"/>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/xeicon/2/xeicon.min.css"/>
<script type="text/javascript" src="<?= $js_uri ?>/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/uniform-2.1.2/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/jquery.smenu-0.2.1.min.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.util.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.ajax.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.validate.js"></script>
<script type="text/javascript" src="<?= $js_uri ?>/alpha.common.js"></script>
<script type="text/javascript" src="<?= $layout_uri ?>/layout.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    initContent(document);

});
var layout = "<?=$layout?>";
var base_uri = "<?=_BASE_URI_?>";
var layout_uri = "<?=$layout_uri?>";
//]]>
</script>
