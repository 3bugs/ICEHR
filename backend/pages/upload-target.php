<?php

if(isset($_FILES["file"])){
           
$target_path = $_SERVER['DOCUMENT_ROOT'] . "/static/media/" . basename($_POST['file_name']);
// echo "<pre>";
//         print_r($_POST);
//         print_r($_FILES['file']);
//         print_r($target_path);
//         echo "<br>";
//         print_r($mimetype);
// echo "<pre>";
// exit();

if (move_uploaded_file($_FILES["file"]["tmp_name"],$target_path)) {
   $status = 1;
}

}

?>