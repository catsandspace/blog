<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] != 1) {
      header("Location: ../login.php");
    }

    $currentUser = $_SESSION["username"];
    $currentUsersPermission = $_SESSION["permission"];

    // This checks current user's permission level.
    $userPermission = convertPermissionToString($currentUsersPermission);
?>

<main class="dark">
    <div class="flexbox-wrapper">
        <h2 class="inverted-text-color">Hej @<?php echo $currentUser; ?></h2>
        <p class="introduction-paragraph inverted-text-color">Behörighet: <?php echo ucfirst($userPermission); ?></p>
        <a href="./posteditor.php" class="button link__button">Skapa nytt inlägg</a>
        <a href="./postlist.php" class="button link__button">Se alla inlägg</a>
        <a href="./comments.php" class="button link__button">Se alla kommentarer</a>
        <?php if ($currentUsersPermission == 1): ?>
        <a href="./categories.php" class="button link__button">Hantera kategorier</a>
        <a href="./users.php" class="button link__button">Hantera användare</a>
        <?php endif; ?>
        <a href="../assets/logout.php" class="button link__button--error" target="_self">Logga ut</a>
    </div>
</main>
<?php require_once "../templates/footer.php"; ?>
