<?php
    require_once "../templates/header.php";
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");
    }
?>
<main>
    <div class="flexbox-wrapper">
        <h2>Hurra!</h2>
        <p>Ännu ett inlägg skapat! Vad vill du göra nu?</p>
        <a href="./posteditor.php" class="button">Skapa nytt inlägg</a>
        <a href="./postlist.php" class="button">Se alla inlägg</a>
        <a href="./dashboard.php" class="button">Gå tillbaka till panelen</a>
        <a href="logout.php" class="button error" target="_self">Logga ut</a>
    </div>
</main>
<?php require_once "../templates/footer.php"; ?>
