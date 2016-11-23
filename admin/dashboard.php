<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] != 1) {
      header("Location: ../login.php");
    }

    $currentUser = $_SESSION["username"];
    $currentUsersPermission = $_SESSION["permission"];

    // This checks current user's permission level.
    if ($currentUsersPermission == 0) {
        $user_permission = "redaktör";
    } elseif ($currentUsersPermission == 1) {
        $user_permission = "superadministratör";
    }
?>

<main>
    <div class="flexboxWrapper">
        <h2>Hej @<?php echo $currentUser; ?></h2>
        <p>Din behörighet är <?php echo $user_permission; ?></p>
        <a href="./posteditor.php" class="button">Skapa nytt inlägg</a>
        <a href="./postlist.php" class="button">Se alla inlägg</a>
        <a href="./comments.php" class="button">Se alla kommentarer</a>
        <?php if ($currentUsersPermission == 1): ?>
        <a href="./categories.php" class="button">Hantera kategorier</a>
        <a href="./users.php" class="button">Hantera användare</a>
        <a href="../assets/logout.php" class="button error" target="_self">Logga ut</a>
        <?php endif; ?>
    </div>
</main>
<?php require_once "../templates/footer.php"; ?>
