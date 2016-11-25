<?php
    require_once "../templates/header.php";
    require_once "../assets/functions.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");

    // Redirect to .dashboard.php if user is not a superadmin.
    } elseif ($_SESSION["permission"] != 1) {
        header("Location: ./dashboard.php");
    }

/*******************************************************************************
   TODO: THIS PAGE NEEDS AN ALL REQUIRED FILLED FUNCTION, JUST LIKE POSTEDITOR.
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

    $query = "SELECT permission, username FROM users";
    if ($stmt -> prepare($query)) {
        $stmt-> execute();
        $stmt -> bind_result($permission, $userName);
    }

    // This checks current user's permission level.
    $userPermission = strtolower(convertPermissionToString($permission));
?>
<main>
    <h2>Användare</h2>
    <div class="flexbox-wrapper">
    <form method="post" action="users.php" class="list-wrapper">
        <div class="list">
            <div class="inner-list">
                <?php while (mysqli_stmt_fetch($stmt)): ?>
                <input type="checkbox" name="checklist[]" value="<?php echo $userId; ?>">
                <?php
                    // TODO: Convert if statment on dashboard to a function, use it here.
                    echo "$userName – behörighet: $userPermission";?><br>
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
            <label class="form-field__label" for="userName">Användarnamn</label>
            <input class="form-field" type="text" name="userName" id="userName" required>
            <label class="form-field__label" for="passWord">Lösenord</label>
            <input class="form-field" type="password" name="passWord" id="passWord" required>
            <label class="form-field__label" for="firstName">Förnamn</label>
            <input class="form-field" type="text" name="firstName" id="firstName" required>
            <label class="form-field__label" for="lastName">Efternamn</label>
            <input class="form-field" type="text" name="lastName" id="lastName">
            <label class="form-field__label" for="eMail">E-post</label>
            <input class="form-field" type="email" name="eMail" id="eMail" required>
            <label class="form-field__label" for="website">Webbplats</label>
            <input class="form-field" type="text" name="website" id="website">
            <label class="form-field__label" for="description">Beskrivning</label>
            <textarea class="form-field" cols="25" rows="7" name="description" id="description"></textarea>
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
