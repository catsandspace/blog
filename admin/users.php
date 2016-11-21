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
    if (isset ($_POST["addUser"])):
        $addUser = TRUE;
    endif;

    // If-statement to check if button for removing users is set
    // If button is pressed continue to check through the array and
    // for each category checked, remove it fromm the db
    if (isset ($_POST["removeUser"])):
        if (!empty($_POST["checkList"])):
            foreach ($_POST['checkList'] as $selected):
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
<main>
<h2>Användare</h2>

    <!-- Form that prints all categories from the db with checkboxes -->
    <!-- If change category is ordered an input field is printed -->
    <div class="flexboxWrapper">

    <form method="post" action="users.php" class="listWrapper">
        <div class="list">
            <div class="inner-list">
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
        <button type="submit" value="Ta bort användare" name="removeUser"class="button error">Ta bort användare</button>
    </form>
    <br>
    <form method="post" action="users.php">
        <button type="submit" value="Lägg till ny användare" name="addUser" class="button">Lägg till ny användare</button>
    </form>
    <br>
    <?php
        // if registration is ordered print registration form
        if ($addUser == TRUE):
    ?>
            <form method="post" action="../assets/registercheck.php">

                <fieldset>
                    <legend>Lägg till ny användare</legend>
                    <label for="userName">Användarnamn:</label> <br>
                    <input type="text" name="userName" id="userName" required> <br>
                    <label for="passWord">Lösenord: </label> <br>
                    <input type="password" name="passWord" id="passWord" required> <br>
                    <label for="firstName">Förnamn:</label> <br>
                    <input type="text" name="firstName" id="firstName" required> <br>
                    <label for="lastName">Efternamn:</label> <br>
                    <input type="text" name="lastName" id="lastName"> <br>
                    <label for="eMail">E-post:</label> <br>
                    <input type="email" name="eMail" id="eMail" required> <br>
                    <label for="webSite">Web-sida:</label> <br>
                    <input type="text" name="webSite" id="webSite"> <br>
                    <label for="description">Beskrivning:</label> <br>
                    <textarea cols="25" rows="7" name="description" id="description"></textarea> <br>
                    <button id="button" type="submit" name="register" value="Lägg till" class="button">Lägg till</button>
                </fieldset>
            </form>
        </div>
    </main>
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
