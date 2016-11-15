<!-- registercheck.php checks the input from users.php. 
     If all data is valid, the data is stored in the database as a new user. 
     If any fault occures during the way, the user receives an error message. -->

<?php
    session_start();

	include_once "./db_connect.php"; // Database connection.
    $string =NULL;


    function printFault($faultString, $header) {
        // echo "<div class=\"userpage\">";
        // if ($header == "Login") {
        //     echo "<h1>Inloggningen misslyckades</h1>";
        // } else if ($header =="Register") {
        //     echo "<h1>Registreringen misslyckades</h1>";
        // }
        echo "<p>$faultString </p>";
        // if ($header == "Login") {
        //     echo "<a href=\"../index.php\" class=\"btn\">Försök igen</a>";
        // } else if ($header =="Register") {
        //     echo "<a href=\"../pages/register.php\" class=\"btn\">Försök igen</a>";
        // }
        // echo "</div>";
    } // End function printFault()


    // Check if register button is pressed
    if (isset($_POST["register"])):
        //  Check if all input is given
        // If everything is okay, connect to the server and database
        if (!empty($_POST["userName"]) && !empty($_POST["passWord"]) && !empty($_POST["firstName"])&& !empty($_POST["lastName"])&& !empty($_POST["eMail"]) && !empty($_POST["webSite"]) && !empty($_POST["description"])):
            // Remove any html or php from the input strings
            $un = mysql_real_escape_string($_POST["userName"]);
            $up = mysql_real_escape_string($_POST["passWord"]);
            $upHasch = password_hash($up, PASSWORD_DEFAULT); // Generate hashed password, salt included

            $fn = mysql_real_escape_string($_POST["firstName"]);
            $ln = mysql_real_escape_string($_POST["lastName"]);
            $em = mysql_real_escape_string($_POST["eMail"]);
            $ws = mysql_real_escape_string($_POST["webSite"]);
            $desc = mysql_real_escape_string($_POST["description"]);
            $pic = "../userpics/default_avatar.jpg";     // Default avatar as first picture

            
            // Check if username is taken
            // Create a query
            // Select all columns (data) in database named "users" for given user - $un
            // Run the query towards the database
            $query = "SELECT * FROM users WHERE username = '$un'";
            // Check query
            if ($stmt -> prepare($query)):
                $result = mysqli_query($conn, $query);
                // Check if username is free
                if (!$result || mysqli_num_rows($result) == 0):
                    $query = "SELECT * FROM users WHERE eMail = '$em'";
                    // Check query
                    if ($stmt -> prepare($query)):
                        $result = mysqli_query($conn, $query);
                        // Check if e-mail is free
                        if (!$result || mysqli_num_rows($result) == 0):
                            // Make a query with all userdata
                            $query = "INSERT INTO users VALUES (NULL, '0', '$un', '$upHasch', '$fn', '$ln', '$em', '$ws', '$desc', '$pic')";
                            // Check query
                            if ($stmt -> prepare($query)):
                                $stmt->execute();
                                // checkUser($un, $up);
                            else: // query not ok
                                $string ="<p>Gick inte att registrera. </p>";
                            endif; // end if query ok?
                        else: // Email taken
                            $string ="<p>E-mail adressen är upptaget. </p>";
                        endif;
                    else: // query not ok
                        $string ="<p>Något fel! </p>";
                    endif; // end if query ok?
                else: // Username taken
                    $string ="<p>Användarnamnet är upptaget. </p>";
                endif; // end if check user
            else: // query not ok
                $string ="<p>Något fel! </p>";
            endif; // end if query ok?
        else: // Not all input given
            $string ="<p>Du har inte fyllt i all information! </p>";
        endif; // end if all input given
    else: // Button not pressed
            $string ="<p>Du har inte kommit till denna sida på rätt sätt! </p>";
    endif; // end if submit button not pressed
    header("Location: ../admin/users.php? errorMessage=$string");
?>


