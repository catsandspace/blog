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

    /*****************************************************************************
    Trying to retreive data and display it - work in progress / Anders
    *****************************************************************************/

    // $conn = NEW mysqli("localhost", "root", "", "catsandspace");

    $resultSet = $conn->query("SELECT id FROM posts");

    if ($resultSet->num_rows != 0){
        while($rows = $resultSet->fetch_assoc()) {
            $post_id = $rows['id'];


            echo $post_id;

        }
    }
    $numberOfPosts = NULL;
    /***************************************************************************/
?>

<main class="dark">
    <div class="flexbox-wrapper">
        <h2 class="inverted-text-color">Hej @<?php echo $currentUser; ?></h2>
        <?php if (isset($_GET['statistics'])): ?>
        <div class="border-normal padding-normal relative-container relative-container--boxsizing margin-normal center-text ">
            <h3 class="center-text">Statistik</h3>
            <ul class="list-style-none">
                <li>Du har totalt x blogginlägg</li>
                <li>Du har totalt x antal kommentarer</li>
                <li>Du har x antal kommentarer i snitt på varje inlägg</li>
            </ul>
        </div>
        <?php endif; ?>
        <a href="./posteditor.php" class="button link__button">Skapa nytt inlägg</a>
        <a href="./postlist.php" class="button link__button">Se alla inlägg</a>
        <a href="./comments.php" class="button link__button">Se alla kommentarer</a>
        <?php if ($currentUsersPermission == 1): ?>
        <a href="./categories.php" class="button link__button">Hantera kategorier</a>
        <a href="./users.php" class="button link__button">Hantera användare</a>
        <?php endif; ?>
        <form method="get">
            <button type="submit" name="statistics" value="true" class="button">Se statistik </button>

        </form>
        <a href="../assets/logout.php" class="button link__button--error" target="_self">Logga ut</a>
    </div>
</main>
<?php require_once "../templates/footer.php"; ?>
