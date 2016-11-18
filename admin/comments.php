<?php
    require_once "../templates/header.php"; // Header content.
    require_once "../assets/session.php";





    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

    // If-statement to check if button for removing users is set
    // If button is pressed continue to check through the array and
    // for each category checked, remove it fromm the db
    if (isset ($_GET["removeComment"])):
        if (!empty($_GET["checkList"])):
            foreach ($_GET['checkList'] as $selected):
                $userId = $selected;
                $query = "DELETE FROM comments WHERE id=$commentId";
                if ($stmt -> prepare($query)):
                    $stmt->execute();
                else:
                    echo "fel";
                endif;
            endforeach;
        else:
            echo "fellist";
        endif;
    endif;



    // Select all rows from the database comments
    $query = "SELECT * FROM comments";
    if ($stmt -> prepare($query)):
        $stmt-> execute();
        $stmt -> bind_result($commentId, $userId, $date, $eMail, $name, $content, $postId);
    endif;



?>

<main>
<h2>Användare</h2>
    <form method="POST" action="./comments.php">
        <table>
            <thead>
                <th>Ta bort</th>
                <th>Kommentar</th>
            </thead>
            <tbody>
                <?php while (mysqli_stmt_fetch($stmt)): ?>
                <tr>
                    <td><input type="checkbox" name="checkList[]" value="<?php echo $commentId; ?>"></td>
                    <td><?php echo $content; ?></td>
                </tr>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $eMail; ?></td>
                    <td><?php echo $date; ?></td>
                </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
                        <button type="submit" value="Ta bort användare" name="removeComment"class="button red">Ta bort kommentarer</button>

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
