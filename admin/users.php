<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");

    // Redirect to .dashboard.php if user is not a superadmin.
    } elseif ($_SESSION["permission"] != 1) {
        header("Location: ./dashboard.php");
    }

    // Reset functions for the internal variables
    $addUser = FALSE;
    //$errorMessage = NULL;

/*******************************************************************************
   START OF CHECK TO CONFIRM THAT ALL REQUIRED FIELDS ARE FILLED.
*******************************************************************************/

    $fields = array(
        "userName" => "",
        "passWord" => "",
        "firstName" => "",
        "lastName" => "",
        "eMail" => "",
        "website" => ""
    );

    $allRequiredFilled = TRUE;
    $errors = array();

    // Variables regarding error messages ******************************************
    $errorInfo = "<p class=\"error-msg\">Ooops, något gick fel! Se felmeddelanden nedan.</p>";

    $obligatoryField = "<p class=\"error-msg\">Fältet ovan är obligatoriskt.</p>";

    $obligatoryFieldEmail = "<p class=\"error-msg\">Fältet ovan är obligatoriskt men tomt eller felaktigt ifyllt.<br> Formatera enligt: namn@catsandspace.com</p>";

    $obligatoryFieldWebsite = "<p class=\"error-msg\">Fältet ovan är obligatoriskt men tomt eller felaktigt ifyllt. Formatera enligt: <br>
    https://www.catsandspace.com/ eller http://www.catsandspace.com/</p>";


    // Set key for printing register form
    if (isset ($_POST["add-user"])) {
        $addUser = TRUE;
    }

    // Button pressed for sending register form
    if (isset ($_POST["register"])) {
        $requiredFields = array("userName", "passWord", "firstName", "lastName", "eMail", "website");

        // Check to see that all required fields are filled
        // Information is stored in an array
        foreach ($fields as $key => $value) {
            $isRequired = in_array($key, $requiredFields);

            if (!array_key_exists($key, $_POST) || empty($_POST[$key])) {
                if ($isRequired) {
                    $allRequiredFilled = FALSE;
                    array_push($errors, $key);
                }
            } else {
                $fields[$key] = mysqli_real_escape_string($conn, $_POST[$key]);
            }
        }

        // This checks if email is written correctly. If not, return an error message.
        if ($key = 'eMail') {
            if (!filter_var($fields['eMail'], FILTER_VALIDATE_EMAIL)) {
                $allRequiredFilled = FALSE;
                array_push($errors, $key);
            }
        }

        // This checks if website is written correctly. If not, return an error message.
        if ($key = 'website') {
            if (!filter_var($fields['website'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                $allRequiredFilled = FALSE;
                array_push($errors, $key);
            }
        }

        // If all requied fields are filled, continue to store information in database
        if ($allRequiredFilled)  {

            // Generate hashed password, salt included
            $upHash = password_hash($fields["passWord"], PASSWORD_DEFAULT);

            $query = "INSERT INTO users VALUES (NULL, '0', '{$fields["userName"]}', '{$upHash}', '{$fields["eMail"]}', '{$fields["website"]}', '{$fields["firstName"]}', '{$fields["lastName"]}')";

            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->close();
                header("Location: ./users.php?getusers=$getUser#nav-adduser");


            } else {

                // TODO: 404?
                $errorMessage = "Det gick inte att lägga till användare.";
            }
        }
    }

    // This prints out HTML from header.php.
    require_once "../templates/header.php";

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

    $query = "SELECT permission, username, id FROM users";
    if ($stmt -> prepare($query)) {
        $stmt-> execute();
        $stmt -> bind_result($permission, $userName, $userId);
    }


/*******************************************************************************
   START OF HTML
*******************************************************************************/
?>
<main>
    <h1 class="center-text margin-bottom-l">Användare</h1>
    <!-- Form prints all registered users an gives the possibility to remove user -->
    <form method="post" action="#nav-adduser">
        <div class="flex-list">
            <?php while (mysqli_stmt_fetch($stmt)): ?>
            <div class="flex-list__item">
                <p><?php echo "Användarnamn: $userName"; ?></p>
                <?php
                    // This checks current user's permission level.
                    $userPermission = strtolower(convertPermissionToString($permission));
                    $userPermission = utf8_encode($userPermission);
                ?>
                <p class="saffron-text primary-brand-font"><?php echo "Behörighet: $userPermission"; ?></p>
                <label class="checkbox-wrapper">
                    <input class="checkbox-wrapper__checkbox" type="checkbox" name="checklist[]" value="<?php echo $userId; ?>">
                    <i class="checkbox-wrapper__icon"></i>
                    Radera
                </label>
            </div>
            <?php endwhile; ?>
        </div>
        <button type="submit" value="Ta bort användare" name="remove-user" class="button error">Ta bort användare</button>
    </form>

    <!-- If button for adding a new user or if someone is trying to submit a new user but not
        all required fields are filled, print form for registration.
        If any error is noted an error message is printed for that input field.-->
    <?php if ($addUser == TRUE || (isset($_POST["register"]) && !$allRequiredFilled)): ?>
    <div id="nav-users-top">
        <form method="post" novalidate>
            <fieldset>
                <h2>Lägg till ny användare</h2>
                <?php if (!empty($errors)) { echo "<p class=\"error-msg\">Ooops, något gick fel!</p>"; } ?>

                <legend class="hidden">Lägg till ny användare</legend>
                <label class="form-field__label" for="userName">Användarnamn</label>
                <input class="form-field" type="text" name="userName" id="userName" required value="<?php echo $fields['userName']; ?>">
                <?php if (in_array("userName", $errors)) { echo $obligatoryField; } ?>
                <label class="form-field__label" for="passWord">Lösenord</label>
                <input class="form-field" type="password" name="passWord" id="passWord" required value="<?php echo $fields['passWord']; ?>">
                <?php if (in_array("passWord", $errors)) { echo $obligatoryField; } ?>
                <label class="form-field__label" for="firstName">Förnamn</label>
                <input class="form-field" type="text" name="firstName" id="firstName" required value="<?php echo $fields['firstName']; ?>">
                <?php if (in_array("firstName", $errors)) { echo $obligatoryField; } ?>
                <label class="form-field__label" for="lastName">Efternamn</label>
                <input class="form-field" type="text" name="lastName" id="lastName" required value="<?php echo $fields['lastName']; ?>">
                <?php if (in_array("lastName", $errors)) { echo $obligatoryField; } ?>
                <label class="form-field__label" for="eMail">E-post</label>
                <input class="form-field" type="email" name="eMail" id="eMail" required value="<?php echo $fields['eMail']; ?>">
                <?php if (in_array("eMail", $errors)) { echo $obligatoryFieldEmail; } ?>
                <label class="form-field__label" for="website">Webbplats</label>
                <input class="form-field" type="text" name="website" id="website" required value="<?php echo $fields['website']; ?>">
                <?php if (in_array("website", $errors)) { echo $obligatoryFieldWebsite; } ?>

                <button type="submit" name="register" value="Lägg till" class="button" id="nav-adduser-end">Lägg till</button>
            </fieldset>
        </form>
    </div>
    <?php else: ?>
    <!-- Form to add new users -->
    <form method="post" action="#nav-users-top">
        <button type="submit" name="add-user" value="true" class="button margin-bottom-l" id="nav-adduser">Lägg till ny användare</button>
    </form>
    <?php endif; ?>
</main>
<?php
    include_once "../templates/footer.php"; // Footer.
?>
