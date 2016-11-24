<?php
    require_once "../templates/header.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

/*******************************************************************************
   TODO: THIS PAGE NEEDS AN ALL REQUIRED FILLED FUNCTION, JUST LIKE POSTEDITOR.
   TODO: This page need a check if user is a superadmin. Else, redirect to dashboard.
*******************************************************************************/

    // Reset functions for the internal variables
    $addUser = FALSE;
    $errorMessage = NULL;

    // Set key for printing register form
    if (isset ($_POST["add-user"])) { $addUser = TRUE; }

    // If-statement to check if button for removing users is set
    // If button is pressed continue to check through the array and
    // for each category checked, remove it fromm the db
    if (isset ($_POST["remove-user"])):
        if (!empty($_POST["checklist"])):
            foreach ($_POST["checklist"] as $selected):
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

    // Select all rows from the database users
    // TODO: do we need to bind all results?
    // TODO: Add switch statement to change permission from int to string.
    $query = "SELECT * FROM users";
    if ($stmt -> prepare($query)):
        $stmt-> execute();
        $stmt -> bind_result($userId, $permission, $userName, $userPassword, $userEmail, $userWebSite, $userFirstname, $userLastname, $userPic, $userDescription);
    endif;
?>

    <h2>Användare</h2>
    <div class="flexbox-wrapper">
    <form method="post" action="users.php" class="list-wrapper">
        <div class="list">
            <div class="inner-list">
                <?php while (mysqli_stmt_fetch($stmt)): ?>
                <input type="checkbox" name="checklist[]" value="<?php echo $userId; ?>">
                <?php
                    // TODO: Convert if statment on dashboard to a function, use it here.
                    echo "$userName – behörighet: $permission";?><br>
                <?php endwhile; ?>
            </div>
        </div>
        <button type="submit" value="Ta bort användare" name="remove-user" class="button error">Ta bort användare</button>
        <button type="submit" value="Lägg till ny användare" name="add-user" class="button">Lägg till ny användare</button>
    </form>
    <?php if ($addUser == TRUE): ?>
    <form method="post" action="../assets/registercheck.php">
        <fieldset>
            <legend class="hidden">Lägg till ny användare</legend>
            <label for="userName">Användarnamn</label>
            <input type="text" name="userName" id="userName" required>
            <label for="passWord">Lösenord</label>
            <input type="password" name="passWord" id="passWord" required>
            <label for="firstName">Förnamn</label>
            <input type="text" name="firstName" id="firstName" required>
            <label for="lastName">Efternamn</label>
            <input type="text" name="lastName" id="lastName">
            <label for="eMail">E-post</label>
            <input type="email" name="eMail" id="eMail" required>
            <label for="webSite">Eventuell webbplats</label>
            <input type="text" name="webSite" id="webSite">
            <label for="description">Beskrivning</label>
            <textarea cols="25" rows="7" name="description" id="description"></textarea>
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
