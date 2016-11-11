<?php
require_once(__DIR__."/functions.php");

// Check if user has pressed the save button on addpost.php.
if (isset($_POST["save"])) {

    $target_folder = "../uploads/userpics/"; // The folder in which the file should be saved.

    $file_name = basename($_FILES["post-img"]["name"]); //The name of the file
    $temporary_folder = $_FILES["post-img"]["tmp_name"]; // The temp. folder in which file is stored.
    $type = pathinfo($file_name, PATHINFO_EXTENSION); // The file type.
    $file_error = checkUploadedFile($_FILES["post-img"]); // This checks if there are any file errors.

    $target_name = $target_folder . basename("post_headline") . ".$type";

    // FIXME: Se till att det går att flytta filen till rätt mapp.
    // Move file to ../uploads/userpics.
    // if (!$file_error && move_uploaded_file($temporary_folder, $target_name)) {
    //     echo "File upload success.";
    // }
}
?>
