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
     Fetching and displaying total number of posts, comments and generally
     how many comments on each posts.
    *****************************************************************************/

    $NumberOfPosts = NULL;
    $NumberOfComments = NULL;
    $errorMessage = NULL;

    // Fetching post row id from database
    $query = "SELECT id FROM posts WHERE published = 1";

    if ($stmt->prepare($query)) {

        $stmt->execute();
        $stmt->bind_result($id);
    } else {
        $errorMessage = "Något gick fel vid försök att hämta statistik";
    }
    // Counting number of posts id and sums it up
    while (mysqli_stmt_fetch($stmt)) {

        $stmt->store_result();
        $NumberOfPosts++;
    }
    // Fetching comments row id from database
     $query = "SELECT id FROM comments";

    if ($stmt->prepare($query)) {

        $stmt->execute();
        $stmt->bind_result($id);
    } else {
        $errorMessage = "Något gick fel vid försök att hämta statistik";
    }
    // Counting number of comments id and sums it up
    while (mysqli_stmt_fetch($stmt)) {

        $stmt->store_result();
        $NumberOfComments++;
    }
    // Variables with function that devide comments on posts
    $averagePostComments = $NumberOfComments / $NumberOfPosts;
    $roundAverageNumber = number_format($averagePostComments, 2,',', ' ');

?>
<main>
    <div class="flexbox-wrapper">
        <h1 class="center-text margin-bottom-l">Hej @<?php echo $currentUser; ?></h1>
        <?php if (isset($_GET['statistics'])): ?>
        <div class="border-normal padding-normal relative-container relative-container--boxsizing margin-normal center-text ">
            <h2 class="center-text">Statistik</h2>
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
<?php if ($errorMessage) { echo $errorMessage;}
 require_once "../templates/footer.php"; ?>
