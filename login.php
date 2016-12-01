<?php
   require_once "./templates/header.php";
   require_once "./assets/db_connect.php";
   require_once "./assets/functions.php";
   require_once "./assets/session.php";

   $errorMessage = NULL;

   // Redirect to dashboard.php if there is already an active session.
   if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE) {

       // session.php is used to start the session.
       header("Location: ./admin/dashboard.php");
    }

   // if statement that checks if user has filled in username and password
   if (isset($_POST["login"]) ) {

        if (!empty($_POST["username"]) && !empty($_POST["password"]) ) {

            $user = mysqli_real_escape_string($conn, $_POST["username"]);
            $pass = mysqli_real_escape_string($conn, $_POST["password"]);

            if ($stmt->prepare("SELECT * FROM users WHERE username = '{$user}' ") ) {

                $stmt->execute();
                $stmt->bind_result($id, $permission, $uname, $upass, $email, $website, $fname, $lname, $pic, $desc);
                $stmt->fetch();

                if ($pass == $upass) {
                    // function, stores users id, username, userpassword in session variables - functions.php
                    storeUserInSession($id, $permission, $uname, $upass);
                    header("Location: ./admin/dashboard.php");
                } else {
               $errorMessage = "Felaktigt användarnamn eller lösenord";
                }
            }
        } else {
            $errorMessage = "Misslyckades att logga in!";
        }
    }
?>
<!--****************************************************************************
    FORM TO LOGIN USER
*****************************************************************************-->
<main>
    <h2>Logga in</h2>
    <form action="./login.php" method="POST">
       <fieldset>
            <legend class="hidden">Login</legend>
            <label class="form-field__label" for="username">Användarnamn</label><br>
            <input class="form-field" type="text" name="username" id="username"><br>
            <label class="form-field__label" for="password">Lösenord</label><br>
            <input class="form-field" type="password" name="password" id="password"><br>
            <button type="submit" name="login" class="button">Logga in</button>
            <?php if ($errorMessage) { echo "<p class='error-msg'>".$errorMessage."</p>"; } ?>
       </fieldset>
    </form>
    
</main>
<?php require_once "./templates/footer.php"; ?>
