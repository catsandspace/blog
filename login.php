<?php
   require_once "./templates/header.php";
   require_once "./assets/db_connect.php";
   require_once "./assets/functions.php";
   require_once "./assets/session.php";

   $errorMessage = "";

   // Redirect to dashboard.php if there is already an active session.
   if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == true) {
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

                   storeUserInSession($id, $uname, $upass);
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
********************************************************************************
            Form to login user
********************************************************************************
*****************************************************************************-->
<form action="login.php" method="POST">
   <fieldset>
        <legend>Login</legend>
        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username"><br>
        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password"><br>
        <button type="submit" name="login" class="button">Logga in</button>
   </fieldset>
</form>
<?php if ($errorMessage) { echo $errorMessage; } ?>
<?php require_once "./templates/footer.php"; ?>
