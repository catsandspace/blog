<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
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
?>
<main>
    <h1 class="center-text margin-bottom-l">Kommentarer</h1>
    <form method="POST" action="./comments.php">
        <table class="table-listing__centered-content">
            <thead class="hidden">
                <td>Kommentar</td>
                <td>Namn</td>
                <td>E-mail</td>
                <td>Datum</td>
                <td>Post</td>
                <td>Ta bort</td>
            </thead>
            <tbody>
                    <?php while (mysqli_stmt_fetch($stmt)): ?>
                        <tr>
                            <td class="inline-block"><?php echo $content; ?></td>
                            <td class="inline-block">Skriven av: <?php echo checkExistingOrReturnPredefined($name, $userName); ?></td>
                            <td class="inline-block">E-postadress: <a href="mailto:<?php echo checkExistingOrReturnPredefined($email, $userMail); ?>"><?php echo checkExistingOrReturnPredefined($email, $userMail); ?></a></td>
                            <td class="inline-block saffron-text primary-brand-font">[<?php echo $date; ?>] [Kommentar på inlägg:
                                <?php
                                // TODO: Change this to post title instead.
                                echo $postId;
                                ?>]</td>
                            <td class="inline-block">
                                <button type="submit" class="button error margin-bottom-xl" name="remove-comment" value="<?php echo $commentId; ?>">Ta bort kommentar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
            </tbody>
        </table>
    </form>
    <?php if (!empty($_GET["errorMessage"])) { echo $_GET["errorMessage"]; } ?>
</main>
<?php require_once "../templates/footer.php"; ?>
