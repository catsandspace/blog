<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");
    }

    // Don't print out HTML from "header.php" before login check is done.
    require_once "../templates/header.php";
?>
<main>
    <div class="flexbox-wrapper">
        <h1 class="center-text">Hurra!</h1>
        <?php if (isset($_GET["updated"])): ?>
        <p class="introduction-paragraph">Inlägget är uppdaterat. Vad vill du göra nu?</p>
        <?php else: ?>
        <p class="introduction-paragraph">Ännu ett inlägg skapat! Vad vill du göra nu?</p>
        <?php endif; ?>
        <a href="./posteditor.php" class="button link__button">Skapa nytt inlägg</a>
        <a href="./postlist.php" class="button link__button">Se alla inlägg</a>
        <a href="./dashboard.php" class="button link__button">Gå tillbaka till panelen</a>
        <a href="logout.php" class="button link__button error" target="_self">Logga ut</a>
    </div>
</main>
<?php require_once "../templates/footer.php"; ?>
