<?php

    require_once __DIR__."/config.php";

    $conn = new mysqli($db_hostname, $db_user, $db_password, $db_name);
    $conn->set_charset("utf8");
    $databaseError = $conn->connect_errno;
    $databaseErrorMessage = "Det går inte att ansluta till databasen just nu (felkod: $databaseError).<br>Försök igen senare.";

    $stmt = $conn->stmt_init();

    // TODO: Discuss if this should be a function. If yes, move to functions.php.
    function getStatement() {
        global $conn;
        $stmt = $conn->stmt_init();
        return $stmt;
    }

?>
