<?php
    require_once "../templates/header.php"; // Header content.
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

    // For superuser print all comments
    if ($_SESSION["permission"]==1):
        // select all comments and username and email from user
        $query  = "SELECT comments.*, users.username, users.email
                    FROM comments
                    LEFT JOIN users
                    ON comments.userid = users.id";
        if ($stmt -> prepare($query)):
            $stmt-> execute();
            $stmt -> bind_result($commentId, $userId, $date, $eMail, $name, $content, $postId, $userName, $userMail);
        else:
            echo "wrong query";
        endif;
    endif;

    // For adminuser print only the comments connected to the posts for that adminuser
    if ($_SESSION["permission"]==0):
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
            $stmt -> bind_result($commentId, $userId, $date, $eMail, $name, $content, $postId, $userName, $userMail);
        else:
            echo "wrong query";
        endif;
    endif;

    // If-statement to check if button for removing comments is set
    if (isset ($_POST["removeComment"])):
        $commentToDelete = $_POST["removeComment"];
        $query = "DELETE FROM comments WHERE id = '{$commentToDelete}'";
        if ($stmt->prepare($query)):
            $stmt->execute();
        else:
            echo "Fel på queryn";
        endif;
    endif;

    function checkName($name, $userName) {
        if ($name == NULL):
            echo $userName;
        else:
            echo $name;
        endif;
    }

    function checkMail($eMail, $userMail) {
    if ($eMail == NULL):
        echo $userMail;
    else:
        echo $eMail;
    endif;
    }
?>

<main>
    <h2>Kommentarer</h2>
    <form method="POST" action="./comments.php">
        <table>
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
                <tr class="comment">
                    <td><div class="comment-scroll"><?php echo $content; ?><div></td>
                    <td class="comment-info"><?php checkName($name, $userName); ?></td>
                    <td class="comment-info"><?php checkMail($eMail, $userMail); ?></td>
                    <td class="comment-info"><?php echo $date; ?></td>
                    <td class="comment-info"><?php echo $postId; ?></td>
                    <td>
                        <button type="submit" class="button error" name="removeComment" value="<?php echo $id; ?>">Ta bort</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
    <br>
    <?php
        if (isset ($_GET["errorMessage"])):
            if ($_GET["errorMessage"] != NULL):
                echo $_GET["errorMessage"];
            endif;
        endif;
    ?>
    </main>
<?php
    require_once "../templates/footer.php";
?>
