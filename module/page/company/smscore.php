<?php
/**
 * @file    main.php
 * @author  Alpha-Edu
 */
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

$flag_used_head = false;
$flag_used_header = false;
$flag_used_footer = false;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="Generator" content="EditPlus®">
<meta name="Author" content="">
<meta name="Keywords" content="">
<meta name="Description" content="">
<title>Document</title>
</head>
<body>
<form name="form" method="post" action="http://www.smscore.co.kr/webmicro/member/process.html">
<input type="hidden" name="mode" value="login_by_api_key" />
<input type="hidden" name="login_id" value="usarte" />
<input type="hidden" name="api_key" value="TL10K3BC6F01FJ7QY3IC" />
<input type="hidden" name="return_uri" value="/webmicro/message/send.html" />
</form>
<script type="text/javascript">
    document.form.submit();
</script>
</body>
</html>
