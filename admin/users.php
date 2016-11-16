<?php
    include_once "../templates/header.php"; // Header content.
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

    // Reset functions for the internal variables
    $addUser = FALSE;
    $errorMessage = NULL;

    // Set key for printing register form
    if (isset ($_GET["addUser"])):
        $addUser = TRUE;
    endif;

    // If-statement to check if button for removing users is set
    // If button is pressed continue to check through the array and
    // for each category checked, remove it fromm the db
    if (isset ($_GET["removeUser"])):
        if (!empty($_GET["checkList"])):
            foreach ($_GET['checkList'] as $selected):
                $userId = $selected;
                $query = "DELETE FROM users WHERE id=$userId";
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

    // Function converting permission to textstring
    function permission($permission) {
        if ($permission == 1):
            echo "Superadmin";
        else:
            echo "Admin";
        endif;
    }

    // Select all rows from the database users
    $query = "SELECT * FROM users";
    if ($stmt -> prepare($query)):
        $stmt-> execute();
        $stmt -> bind_result($userId, $permission, $uName, $uPass, $uMail, $uWebSite, $ufName, $ulName, $uPic, $uDesc);
    endif;
?>

<h2>Användare</h2>

    <!-- Form that prints all categories from the db with checkboxes -->
    <!-- If change category is ordered an input field is printed -->
    <div class="flexboxWrapper">

    <form method="get" action="users.php" class="listWrapper">
        <div class="categoryList">
            <div class="innerCatList">
<?php
    while (mysqli_stmt_fetch($stmt)):
        ?>
        <input type="checkbox" name="checkList[]" value="<?php echo $userId; ?>"> <?php echo "$uName "; permission($permission);?>
        <br>
<?php
    endwhile;
?>
            </div>
        </div>
        <input type="submit" value="Ta bort användare" name="removeUser"class="button red">
    </form>
    <br>
    <form method="get" action="users.php">
        <input type="submit" value="Lägg till ny användare" name="addUser" class="button">
    </form>
    <br>
    <?php
        // if registration is ordered print registration form
        if ($addUser == TRUE):
    ?>
            <form method="POST" action="../assets/registercheck.php">

                <fieldset>
                    <legend>Lägg till ny användare</legend>
                    <label>Användarnamn:</label> <br>
                    <input type="text" name="userName"> <br>
                    <label>Lösenord: </label> <br>
                    <input type="password" name="passWord"> <br>
                    <label>Förnamn:</label> <br>
                    <input type="text" name="firstName"> <br>
                    <label>Efternamn:</label> <br>
                    <input type="text" name="lastName"> <br>
                    <label>E-post:</label> <br>
                    <input type="email" name="eMail"> <br>
                    <label>Web-sida:</label> <br>
                    <input type="text" name="webSite"> <br>
                    <label>Beskrivning:</label> <br>
                    <textarea cols="25" rows="7" name="description"></textarea> <br>
                    <input id="button" type="submit" name="register" value="Lägg till" class="button">
                </fieldset>
            </form>
        </div>
    <?php
        endif;

        // Printing error message

        if (isset ($_GET["errorMessage"])):
            if ($_GET["errorMessage"] != NULL):
                echo $_GET["errorMessage"];
            endif;
        endif;
        include_once "../templates/footer.php"; // Footer.
    ?>
