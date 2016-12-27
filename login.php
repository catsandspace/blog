<?php
   require_once "./assets/db_connect.php";
   require_once "./assets/functions.php";
   require_once "./assets/session.php";

   $errorMessage = NULL;

   if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE) {

       header("Location: ./admin/dashboard.php");
    }

   if (isset($_POST["login"]) ) {

        if (!empty($_POST["username"]) && !empty($_POST["password"]) ) {

            $user = mysqli_real_escape_string($conn, $_POST["username"]);
            $password = mysqli_real_escape_string($conn, $_POST["password"]);

            if ($stmt->prepare("SELECT id, permission, username, password FROM users WHERE username = '{$user}' ") ) {

                $stmt->execute();
                $stmt->bind_result($id, $permission, $userName, $userPassword);
                $stmt->fetch();

                if (password_verify($password, $userPassword)) {

                    storeUserInSession($id, $permission, $userName, $userPassword);
                    header("Location: ./admin/dashboard.php");

                } else {
                    $errorMessage = "Felaktigt användarnamn eller lösenord";
                }
            }

        } else {
            $errorMessage = "Misslyckades att logga in!";
        }
    }

    require_once "./templates/header.php";
?>
<main>
    <h1>Logga in</h1>
    <form action="./login.php" method="POST">
       <fieldset class="no-padding">
            <legend class="hidden">Login</legend>
            <label class="form-field__label" for="username">Användarnamn</label>
            <input class="form-field" type="text" name="username" id="username" required>
            <label class="form-field__label" for="password">Lösenord</label>
            <input class="form-field" type="password" name="password" id="password">
            <button type="submit" name="login" class="button">Logga in</button>
       </fieldset>
    </form>
    <?php if ($errorMessage): ?>
        <p class="error-msg"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
</main>
<?php require_once "./templates/footer.php"; ?>
