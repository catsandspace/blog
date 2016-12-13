<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/session.php";
    require_once "../assets/functions.php";

    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");

    } elseif ($_SESSION["permission"] != 1) {
        header("Location: ./dashboard.php");
    }

/*******************************************************************************
   CHECK TO CONFIRM THAT ALL REQUIRED FIELDS ARE FILLED STARTS HERE
*******************************************************************************/

    $fields = array(
        "username" => "",
        "password" => "",
        "firstname" => "",
        "lastname" => "",
        "email" => "",
        "website" => ""
    );

    $allRequiredFilled = TRUE;
    $errors = array();

    // Variables regarding error message.
    $errorInfo = "<p class=\"error-msg\">Ooops, något gick fel!</p>";
    $obligatoryField = "<p class=\"error-msg\">Fältet ovan är obligatoriskt.</p>";
    $obligatoryFieldEmail = "<p class=\"error-msg\">Fältet ovan är obligatoriskt men tomt eller felaktigt ifyllt.<br> Formatera enligt: namn@catsandspace.com</p>";

    // Check if user has pressed button "register".
    if (isset ($_POST["register"])) {

        $requiredFields = array("username", "password", "firstname", "lastname", "email", "website");

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

        // This checks if email is written correctly.
        if ($key = 'email') {
            if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
                $allRequiredFilled = FALSE;
                array_push($errors, $key);
            }
        }

        if ($allRequiredFilled)  {

            $upHash = password_hash($fields["password"], PASSWORD_DEFAULT);

            $query = "INSERT INTO users VALUES (NULL, '0', '{$fields["username"]}', '{$upHash}', '{$fields["email"]}', '{$fields["website"]}', '{$fields["firstname"]}', '{$fields["lastname"]}')";

            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->close();
                header("Location: ./users.php?getusers=$getUser#nav-adduser");

            } else {

                $errorMessage = "Det gick inte att lägga till användare.";
            }
        }
    }

    // This prints out HTML from header.php.
    require_once "../templates/header.php";

/*******************************************************************************
   REMOVE A USER STARTS HERE
*******************************************************************************/

    if (isset ($_POST["remove-user"])):
        if (!empty($_POST["checklist"])):
            foreach ($_POST["checklist"] as $selected):
                $userId = $selected;
                $query = "DELETE FROM users WHERE id=$userId";
                if ($stmt -> prepare($query)):
                    $stmt->execute();
                else:
                    $errorMessage = "Det gick inte att ta bort användare.";
                endif;
            endforeach;
        else:
            $errorMessage = "Det gick inte att radera användare just nu.";
        endif;
    endif;

    $query = "SELECT permission, username, id FROM users";
    if ($stmt -> prepare($query)) {
        $stmt -> execute();
        $stmt -> bind_result($permission, $userName, $userId);
    }
?>
<main>
    <h1 class="center-text margin-bottom-l">Användare</h1>
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
    <?php if (isset ($_POST["add-user"]) || (isset($_POST["register"]) && !$allRequiredFilled)): ?>
    <div id="nav-users-top">
        <form method="post">
            <fieldset>
                <h2>Lägg till ny användare</h2>
                <?php if (!empty($errors)) { echo $errorInfo; } ?>
                <legend class="hidden">Lägg till ny användare</legend>

                <label class="form-field__label" for="username">Användarnamn</label>
                <input class="form-field" type="text" name="username" id="username" required value="<?php echo $fields['username']; ?>">
                <?php if (in_array("username", $errors)) { echo $obligatoryField; } ?>

                <label class="form-field__label" for="password">Lösenord</label>
                <input class="form-field" type="password" name="password" id="password" required value="<?php echo $fields['password']; ?>">
                <?php if (in_array("password", $errors)) { echo $obligatoryField; } ?>

                <label class="form-field__label" for="firstname">Förnamn</label>
                <input class="form-field" type="text" name="firstname" id="firstname" required value="<?php echo $fields['firstname']; ?>">
                <?php if (in_array("firstname", $errors)) { echo $obligatoryField; } ?>

                <label class="form-field__label" for="lastname">Efternamn</label>
                <input class="form-field" type="text" name="lastname" id="lastname" required value="<?php echo $fields['lastname']; ?>">
                <?php if (in_array("lastname", $errors)) { echo $obligatoryField; } ?>

                <label class="form-field__label" for="email">E-post</label>
                <input class="form-field" type="email" name="email" id="email" required value="<?php echo $fields['email']; ?>">
                <?php if (in_array("email", $errors)) { echo $obligatoryFieldEmail; } ?>

                <label class="form-field__label" for="website">Webbplats</label>
                <input class="form-field" type="text" name="website" id="website" required value="<?php echo $fields['website']; ?>">
                <?php if (in_array("website", $errors)) { echo $obligatoryField; } ?>

                <button type="submit" name="register" value="Lägg till" class="button" id="nav-adduser-end">Lägg till</button>
            </fieldset>
        </form>
    </div>
    <?php else: ?>
    <form method="post" action="#nav-users-top">
        <button type="submit" name="add-user" value="true" class="button margin-bottom-l" id="nav-adduser">Lägg till ny användare</button>
    </form>
    <?php endif; ?>
</main>
<?php
    $stmt -> close();
    $conn -> close();
    include_once "../templates/footer.php";
?>
