<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

    // For superuser print all comments
    if ($_SESSION["permission"] == 1):

        // select all comments and username and email from user
        $query  = "SELECT comments.*, users.username, users.email
                    FROM comments
                    LEFT JOIN users
                    ON comments.userid = users.id";
        if ($stmt -> prepare($query)):
            $stmt-> execute();
            $stmt -> bind_result($commentId, $userId, $date, $email, $name, $content, $postId, $userName, $userMail);
        else:
            echo "wrong query";
        endif;
    endif;

    // If user has permission "admin" only print the comments connected to the posts for that adminuser
    if ($_SESSION["permission"] == 0):
        $userId = $_SESSION["userid"];
        $query  = "SELECT comments.*, users.username, users.email
                    FROM comments
                    LEFT JOIN users
                    ON comments.userid = users.id
                    LEFT JOIN posts
                    ON comments.postid = posts.id
                    WHERE posts.userid = '{$userId}'";
        if ($stmt -> prepare($query)):
            $stmt-> execute();
            $stmt -> bind_result($commentId, $userId, $date, $email, $name, $content, $postId, $userName, $userMail);
        else:
            echo "wrong query";
        endif;
    endif;

    // If-statement to check if button for removing comments is set
    if (isset ($_POST["remove-comment"])):
        $commentToDelete = $_POST["remove-comment"];
        $query = "DELETE FROM comments WHERE id = '{$commentToDelete}'";
        if ($stmt->prepare($query)):
            $stmt->execute();
        else:
            echo "Fel på queryn";
        endif;
    endif;
?>
<main class="dark">
    <h2 class="inverted-text-color">Kommentarer</h2>
    <form method="POST" action="./comments.php">
        <table class="table-listing--inverted">
            <thead class="hidden">
                <td>Kommentar</td>
                <td>Namn</td>
                <td>E-mail</td>
                <td>Datum</td>
                <td>Post</td>
                <td>Ta bort</td>
            </thead>
            <tbody>
                <tr class="table-listing__row">
                    <?php while (mysqli_stmt_fetch($stmt)): ?>
                        <td class="table-listing__td"><?php echo $content; ?></td>
                        <td class="table-listing__td">Skriven av: <?php echo checkExistingOrReturnPredefined($name, $userName); ?></td>
                        <td class="table-listing__td saffron-text primary-brand-font">[<?php echo $date; ?>] [Kommentar på inlägg:
                            <?php
                            // TODO: Change this to post title instead.
                            echo $postId;
                            ?>]</td>
                        <td class="table-listing__td"><?php echo checkExistingOrReturnPredefined($email, $userMail); ?></td>
                        <td>
                            <button type="submit" class="button error margin-bottom-xl" name="remove-comment" value="<?php echo $id; ?>">Ta bort kommentar</button>
                        </td>
                    <?php endwhile; ?>
                </tr>
            </tbody>
        </table>
    </form>
    <?php if ($_GET["errorMessage"]) { echo $_GET["errorMessage"]; } ?>
</main>
<?php require_once "../templates/footer.php"; ?>
