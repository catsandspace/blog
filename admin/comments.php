<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

    require_once "../templates/header.php";

/*******************************************************************************
    START OF VARIABLES USED ON PAGE
*******************************************************************************/

    $errorMessage = NULL;
    $queryFailed = "Det blev något fel. Försök igen senare.";

/*******************************************************************************
    START OF QUERY TO REMOVE COMMENT
*******************************************************************************/

    if (isset ($_POST["remove-comment"])):

        $commentToDelete = $_POST["remove-comment"];
        $query = "DELETE FROM comments WHERE id = '{$commentToDelete}'";

        if ($stmt->prepare($query)):
            $stmt->execute();

        else:
            $errorMessage = $queryFailed;

        endif;

    endif;

/*******************************************************************************
    START OF QUERY TO PRINT ALL COMMENTS (ONLY FOR SUPERUSERS)
*******************************************************************************/

    if ($_SESSION["permission"] == 1):

        $query = "SELECT comments.*, users.username, users.email, posts.title
                    FROM comments
                    LEFT JOIN users
                    ON comments.userid = users.id
                    LEFT JOIN posts
                    ON comments.postid = posts.id";

        if ($stmt -> prepare($query)):
            $stmt-> execute();
            $stmt -> bind_result($commentId, $userId, $date, $email, $name, $content, $website, $postId, $userName, $userMail, $postTitle);

        else:
            $errorMessage = $queryFailed;

        endif;

    endif;

/*******************************************************************************
    START OF QUERY TO PRINT COMMENTS CONNECTED TO CURRENT USER
*******************************************************************************/

    if ($_SESSION["permission"] == 0):

        $userId = $_SESSION["userid"];

        $query  = "SELECT comments.*, users.username, users.email, posts.title
                    FROM comments
                    LEFT JOIN users
                    ON comments.userid = users.id
                    LEFT JOIN posts
                    ON comments.postid = posts.id
                    WHERE posts.userid = '{$userId}'";

        if ($stmt -> prepare($query)):
            $stmt-> execute();
            $stmt -> bind_result($commentId, $userId, $date, $email, $name, $content, $website, $postId, $userName, $userMail, $postTitle);

        else:
            $errorMessage = $queryFailed;

        endif;

    endif;
?>
<main>
    <?php if ($_SESSION["permission"] == 1): ?>
        <h1 class="center-text margin-bottom-l">Alla kommentarer</h1>
    <?php else: ?>
        <h1 class="center-text margin-bottom-l">Kommentarer på dina inlägg</h1>
    <?php endif; ?>
    <form method="POST" action="./comments.php">
        <table class="table-listing__centered-content">
            <thead class="hidden">
                <tr>
                    <td>Kommentar</td>
                    <td>Namn</td>
                    <td>E-mail</td>
                    <td>Datum</td>
                    <td>Post</td>
                    <td>Ta bort</td>
                </tr>
            </thead>
            <tbody>
                <?php while (mysqli_stmt_fetch($stmt)): ?>
                    <tr>
                        <td class="inline-block"><?php echo $content; ?></td>
                        <td class="inline-block">Skriven av: <?php echo checkExistingOrReturnPredefined($name, $userName); ?></td>
                        <td class="inline-block">E-postadress: <a href="mailto:<?php echo checkExistingOrReturnPredefined($email, $userMail); ?>"><?php echo checkExistingOrReturnPredefined($email, $userMail); ?></a></td>

                        <td class="author-info">[<?php echo $date; ?>]</td>
                        <td class="author-info">
                        [Kommentar på inlägg: <a class="author-info__links" href="../post.php?getpost=<?php echo $postId; ?>"><?php echo $postTitle; ?></a>]
                        </td>

                        <td class="inline-block">
                            <button type="submit" class="button error margin-bottom-xl" name="remove-comment" value="<?php echo $commentId; ?>">Ta bort kommentar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
    <?php if ($errorMessage): ?>
        <p class="error-msg"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
</main>
<?php require_once "../templates/footer.php"; ?>
