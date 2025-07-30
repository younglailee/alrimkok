<?php
// default redirection
$url = 'callback.html?callback_func=' . $_REQUEST["callback_func"];
$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);

// SUCCESSFUL
if (bSuccessUpload) {
    $tmp_name = $_FILES['Filedata']['tmp_name'];
    $name = $_FILES['Filedata']['name'];

    $filename_ext = strtolower(array_pop(explode('.', $name)));
    $allow_file = array("jpg", "png", "bmp", "gif");

    /* Alpha-Edu */
    $name = md5(date('ymhHis') . substr(microtime(), 2, 3) . '_' . strval(rand(0, 99999999)));
    $name .= '.' . $filename_ext;

    if (!in_array($filename_ext, $allow_file)) {
        $url .= '&errstr=' . $name;
    } else {
        /* Alpha-Edu */
        $uploadDir = '../upload/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777);
        }

        /* Alpha-Edu */
        //$newPath = $uploadDir . urlencode($_FILES['Filedata']['name']);
        $newPath = $uploadDir . $name;

        @move_uploaded_file($tmp_name, $newPath);

        $url .= "&bNewLine=true";
        $url .= "&sFileName=" . urlencode(urlencode($name));
        /* Alpha-Edu */
        //$url .= "&sFileURL=upload/" . urlencode(urlencode($name));
        $url .= "&sFileURL=upload/" . urlencode(urlencode($name));
    }
} // FAILED
else {
    $url .= '&errstr=error';
}

header('Location: ' . $url);
?>