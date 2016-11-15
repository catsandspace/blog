<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";

    // This redirects user to login.php if not logged in.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == false) {
        header("Location: ../login.php");
    }

    // SQL statement with LEFT JOIN table -> posts & categories.
    // TODO: Just get the variables you need.
    $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id";

    // Execute query.
    if ($stmt->prepare($query)) {
       $stmt->execute();
       $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
   }

/* -----------------------------------------------------------------------------
   START OF FEEDBACK MESSAGE THAT PRINTS INFO ABOUT THE DATABASE UPDATE
----------------------------------------------------------------------------- */

    $feedbackMessage = "";

    if (isset($_POST["edit-post"])) {
        $feedbackMessage = "Du vill redigera ett inlägg";
    }

    if (isset($_POST["delete-post"])) {
        $feedbackMessage = "Du vill ta bort ett inlägg";
    }

/* -----------------------------------------------------------------------------
   END OF FEEDBACK MESSAGE THAT PRINTS INFO ABOUT THE DATABASE UPDATE
----------------------------------------------------------------------------- */
?>
<h2>Inlägg</h2>
<?php if ($feedbackMessage) { echo $feedbackMessage; } ?>
<form method="POST" action="./postlist.php">
    <table>
        <thead>
            <td>Foto</td>
            <td>Rubrik</td>
            <td>Redigera</td>
            <td>Ta bort</td>
        </thead>
        </tbody>
            <?php while (mysqli_stmt_fetch($stmt)): ?>
            <tr>
                <td><img src="../uploads/postimg/postlist-img.jpg" alt="Image of cats and space" class="postlist-img"></td>
                <td><h3><?php echo $title; ?></h3></td>
                <td><button type="submit" class="button" name="edit-post" value="<?php echo $id; ?>">Redigera</button></td>
                <td><button type="submit" class="button" name="delete-post" value="<?php echo $id; ?>">Ta bort  </button></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</form>
<?php require_once "../templates/footer.php"; ?>
