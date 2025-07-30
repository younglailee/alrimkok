<?php
/**
 * Admin > Footer 파일
 * @file    footer.inc.php
 * @author  Alpha-Edu
 */
if (!defined('_ALPHA_')) {
    exit;
}
?>
        </div>
		<!-- //content -->

    </div>
	<!-- container -->

	<!-- footer -->
    <div id="footer">
		<h2 class="hidden">하단영역</h2>
    	<p>Copyright ⓒ <strong><?=_HOMEPAGE_TITLE_?></strong> All Rights Reserved.</p>
        <p class="top"><a href="#container"><i class="xi-arrow-up"></i> TOP</a></p>
    </div>
    <!-- //footer -->

</div>
<!-- //wrap -->

<!-- layer popup -->
<div id="layer_back"></div>
<div id="layer_popup">
    <div id="layer_header">
        <h1>레이어팝업</h1>
        <button type="button" onclick="closeLayerPopup()" title="닫기"><i class="xi-close-square"></i></button>
    </div>

    <div id="layer_content">
        레이어팝업 내용
    </div>
</div>
<div id="layer_loading">
    <p>
        <i class="xi-spinner-4 xi-spin"></i>
        <br />
        <strong id="loading_state">잠시만 기다려주세요.</strong>
    </p>
</div>
<!-- //layer popup -->
</body>
</html>