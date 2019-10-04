<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js"></script>
    <link rel="stylesheet" href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css">
</head>
<body>

<?php
$imageFileName = null;
if (isset($_FILES['file'])) {
    if (!moveUploadedFile('file', '../uploads/intro_assets/', $imageFileName)) {
        // case error
        return;
    }
}

function moveUploadedFile($key, $dest, &$randomFileName, $index = -1)
{
    $clientName = $index === -1 ? $_FILES[$key]['name'] : $_FILES[$key]['name'][$index];
    /*$response['name'] = $clientName;
    $response['type'] = $index === -1 ? $_FILES[$key]['type'] : $_FILES[$key]['type'][$index];
    $response['size'] = $index === -1 ? $_FILES[$key]['size'] : $_FILES[$key]['size'][$index];
    $response['tmp_name'] = $index === -1 ? $_FILES[$key]['tmp_name'] : $_FILES[$key]['tmp_name'][$index];*/

    $src = $index === -1 ? $_FILES[$key]['tmp_name'] : $_FILES[$key]['tmp_name'][$index];
    /*$response['upload_src'] = $src;
    $response['upload_dest'] = $dest;*/

    //$date = date('Y-m-d H:i:s');
    //$timestamp = time();
    $timestamp = round(microtime(true) * 1000);
    $randomFileName = "{$timestamp}-{$clientName}";
    return move_uploaded_file($src, "{$dest}{$randomFileName}");
}

?>

<h3>Test Dropzone</h3>
<form class="dropzone"
      action="<?= $_SERVER['PHP_SELF']; ?>">

    <button name="submit" type="submit" value="1">Send</button>
</form>

</body>
</html>