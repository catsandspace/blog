<?php
    require_once(__DIR__."/functions.php");

    // Redirect to index.php and destroy session when user logs out.
    header("Location: ../index.php");
    logout();
?>
