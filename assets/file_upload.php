<?php
require_once(__DIR__."/functions.php");

// Check if user has pressed the submit button on addpost.php.
if (isset($_POST["submit"])) {

    $target_folder = "../uploads/postimg/"; // The folder in which the file should be saved.

    $file_name = basename($_FILES["post-img"]["name"]); //The name of the file
    $temporary_file = $_FILES["post-img"]["tmp_name"]; // The temporary file and path
    $type = pathinfo($file_name, PATHINFO_EXTENSION); // The file type
    $file_error = checkUploadedFile($_FILES["post-img"]); // This checks if there are any file errors

    $target_name = $target_folder . basename("postimg") . ".$type";

    // Move file to "../uploads/postimg/"
    if (!$file_error && move_uploaded_file($temporary_file, $target_name)) {
        echo "File upload success.";
    }
}
?>
