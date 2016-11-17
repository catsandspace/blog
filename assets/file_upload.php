<?php
require_once(__DIR__."/functions.php");

// Check if user has pressed the submit button on addpost.php.
if (isset($_POST["submit"])) {

    $targetFolder = "../uploads/postimg/"; // The folder in which the file should be saved.

    $fileName = basename($_FILES["post-img"]["name"]); //The name of the file
    $temporaryFile = $_FILES["post-img"]["tmp_name"]; // The temporary file and path
    $type = pathinfo($fileName, PATHINFO_EXTENSION); // The file type
    $fileError = checkUploadedFile($_FILES["post-img"]); // This checks if there are any file errors

    // TODO: This URL differs from the URL's used on index.php. Don't forget to sort this out.
    $targetName = $targetFolder . basename("postimg") . ".$type";

    // Move file to "uploads/postimg/"
    if (!$fileError) {
        move_uploaded_file($temporaryFile, $targetName);

        $targetName = "uploads/postimg/". basename("postimg") . ".$type";
    }
}
?>
