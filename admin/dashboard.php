<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] != 1) {
      header("Location: ../login.php");
    }

/*******************************************************************************
    START OF OVERALL BLOG STATISTICS
*******************************************************************************/

    $NumberOfPosts = 0;
    $NumberOfComments = 0;
    $errorMessage = NULL;
    $roundAverageNumber = 0;
    $errorStatistics = "Något gick fel vid försök att hämta statistik";


    // Get postid from all published posts. If superadmin, get all posts.
    if ($_SESSION["permission"] == 1) {
        $query = "SELECT id FROM posts WHERE published = 1";
    } else {
        $userId = $_SESSION["userid"];
        $query = "SELECT id FROM posts WHERE published = 1 AND userid = '{$userId}'";
    }

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id);

    } else {

        $errorMessage = $errorStatistics;
    }

    while (mysqli_stmt_fetch($stmt)) {

        $stmt->store_result();
        $NumberOfPosts++;
    }

    // Get id from all comments. If superadmin, get all comments.
    if ($_SESSION["permission"] == 1) {
        $query = "SELECT id FROM comments";
    } else {
        $userId = $_SESSION["userid"];
        $query = "SELECT comments.postid, posts.userid, posts.id FROM comments LEFT JOIN posts ON comments.postid = posts.id WHERE posts.userid = '{$userId}'";
    }

    if ($stmt->prepare($query)) {
        $stmt->execute();

        if ($_SESSION["permission"] == 1) {
            $stmt->bind_result($id);

        } else {
            $stmt->bind_result($commentsPostid, $userid, $postid);
        }

    } else {

        $errorMessage = $errorStatistics;
    }

    while (mysqli_stmt_fetch($stmt)) {

        $stmt->store_result();
        $NumberOfComments++;
    }

    if ($NumberOfPosts != 0) {
        $averagePostComments = $NumberOfComments / $NumberOfPosts;
        $roundAverageNumber = number_format($averagePostComments, 2,',', ' ');
    }

/*******************************************************************************
    GET HEADER INFO
*******************************************************************************/

    require_once "../templates/header.php";
?>
<main>
    <div class="flexbox-wrapper">
        <h1 class="center-text margin-bottom-l">Hej @<?php echo $_SESSION["username"]; ?></h1>
        <?php if ($errorMessage): ?>
            <p class="error-msg"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['statistics'])): ?>
        <div class="border-normal padding-normal relative-container relative-container--boxsizing margin-normal center-text ">
            <?php if ($currentUserPermission == 1): ?>
                <h2 class="center-text">Övergripande statistik</h2>
            <?php else: ?>
                <h2 class="center-text">Din statistik</h2>
            <?php endif; ?>
            <ul class="list-style-none no-padding">
                <li>Totalt antal publicerade blogginlägg: <?php echo $NumberOfPosts; ?></li>
                <li>Totalt antal kommentarer: <?php echo $NumberOfComments; ?></li>
                <li>Antal kommentarer i snitt på varje inlägg: <?php echo  $roundAverageNumber; ?></li>
            </ul>
        </div>
        <?php endif; ?>
        <a href="./posteditor.php" class="button link__button">Skapa nytt inlägg</a>
        <a href="./postlist.php" class="button link__button">Se alla inlägg</a>
        <a href="./comments.php" class="button link__button">Se alla kommentarer</a>
        <?php if ($currentUserPermission == 1): ?>
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
