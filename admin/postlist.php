<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == false) {
        header("Location: ../login.php");
    }

/*******************************************************************************
   START OF FEEDBACK MESSAGES AND DATABASE UPDATE
*******************************************************************************/

    $feedbackMessage = NULL;
    $draftMessage = "<p class=\"relative-container__info\">Det här inlägget är inte publicerat</p>";

    if (isset($_POST["edit-post"])) {
        $postToEdit = $_POST["edit-post"];
        header("Location: ./posteditor.php?edit=$postToEdit");
    }

    if (isset($_POST["delete-post"])) {

        $postToDelete = $_POST["delete-post"];
        $query = "DELETE FROM posts WHERE id ='{$postToDelete}'";

        if ($stmt->prepare($query)) {
            $stmt->execute();
            $feedbackMessage = "Du har tagit bort inlägget";
        }
    }

    require_once "../templates/header.php";

/*******************************************************************************
   START OF QUERY AND STMT THAT IS USED TO PRINT POST LIST
*******************************************************************************/

    //  If logged in as super user, show all posts.
    if ($_SESSION["permission"] == 1) {

        $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id ORDER BY created DESC";

    } else {
        $userId = $_SESSION["userid"];
        $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE posts.userid = '{$userId}' ORDER BY created DESC";
    }

    if ($stmt->prepare($query)) {
       $stmt->execute();
       $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
       $rows = $stmt->num_rows;
   } else {
       $feedbackMessage = "Det går inte att ansluta till databasen just nu.";
   }
?>
<main>
    <?php if ($rows == 0): ?>
        <h1 class="center-text">Det finns inga inlägg!</h1>
    <?php else: ?>
    <?php if ($_SESSION["permission"] == 1): ?>
        <h1 class="center-text">Alla inlägg</h1>
    <?php else: ?>
        <h1 class="center-text">Dina inlägg</h1>
    <?php endif; ?>
    <?php if ($feedbackMessage): ?>
        <p class="error-msg error-msg--confirm"><?php echo $feedbackMessage; ?></p>
    <?php endif; ?>
    <form method="POST" action="./postlist.php">
        <table class="table-listing">
            <thead class="hidden">
                <tr>
                    <td>Foto</td>
                    <td>Rubrik</td>
                    <td>Redigera</td>
                    <td>Ta bort</td>
                </tr>
            </thead>
            <tbody>
                <?php while (mysqli_stmt_fetch($stmt)):

                    $draft = FALSE;
                    if ($published == 2) {
                        $draft = TRUE;
                        $modifier = "grayscale";
                    }
                ?>
                <tr class="table-listing__row">
                    <td class="relative-container">
                        <a href="./posteditor.php?edit=<?php echo $id;?>"><img src="../<?php echo $image; ?>" alt="Image of cats and space" class="full-width-img <?php if ($draft) { echo $modifier; } ?>"></a>
                        <?php if ($draft) { echo $draftMessage; } ?>
                    </td>
                    <td class="relative-container">
                        <h2 class="table-listing__title--on-img"><?php echo formatInnerHtml($title); ?></h2>
                    </td>
                    <td class="relative-container">
                        <button type="submit" class="button" name="edit-post" value="<?php echo $id; ?>">Redigera</button>
                    </td>
                    <td class="relative-container">
                        <button type="submit" class="button error" name="delete-post" value="<?php echo $id; ?>">Ta bort</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
<?php endif; ?>
</main>
<?php require_once "../templates/footer.php"; ?>
