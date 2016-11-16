<!--****************************************************************************

    REGISTERCHECK.PHP CHECKS THE INPUT FROM USERS.PHP
    If all data is valid, the data is stored in the database as a new user.
    If errors, the user receives a feedback message.

*****************************************************************************-->

<?php
    session_start();

    include_once "./db_connect.php"; // Database connection
    $feedbackMessage = NULL;

    // Check if register button is pressed
    if (isset($_POST["register"])):
        //  Check if all input is given
        // If everything is okay, connect to the server and database
        if (!empty($_POST["userName"]) && !empty($_POST["passWord"]) && !empty($_POST["firstName"])&& !empty($_POST["lastName"])&& !empty($_POST["eMail"]) && !empty($_POST["webSite"]) && !empty($_POST["description"])):
            // Remove any html or php from the input strings
            $un = mysql_real_escape_string($_POST["userName"]);
            $up = mysql_real_escape_string($_POST["passWord"]);
            $upHash = password_hash($up, PASSWORD_DEFAULT); // Generate hashed password, salt included

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
                // Check if username is available
                if (!$result || mysqli_num_rows($result) == 0):
                    $query = "SELECT * FROM users WHERE eMail = '$em'";
                    // Check query
                    if ($stmt -> prepare($query)):
                        $result = mysqli_query($conn, $query);
                        // Check if e-mail is available
                        if (!$result || mysqli_num_rows($result) == 0):
                            // Make a query with all userdata
                            $query = "INSERT INTO users VALUES (NULL, '0', '$un', '$upHash', '$fn', '$ln', '$em', '$ws', '$desc', '$pic')";
                            // Check query
                            if ($stmt -> prepare($query)):
                                $stmt->execute();
                                // checkUser($un, $up);
                            else: // query not ok
                                $feedbackMessage ="<p>Gick inte att registrera. </p>";
                            endif; // end if query ok?
                        else: // Email already registered
                            $feedbackMessage ="<p>E-mail adressen är upptaget. </p>";
                        endif;
                    else: // query not ok
                        $feedbackMessage ="<p>Något fel! </p>";
                    endif; // end if query ok?
                else: // Username already registered
                    $feedbackMessage ="<p>Användarnamnet är upptaget. </p>";
                endif; // end if check user
            else: // query not ok
                $feedbackMessage ="<p>Något fel! </p>";
            endif; // end if query ok?
        else: // Not all input given
            $feedbackMessage ="<p>Du har inte fyllt i all information! </p>";
        endif; // end if all input given
    else: // Button not pressed
            $feedbackMessage ="<p>Du har inte kommit till denna sida på rätt sätt! </p>";
    endif; // end if submit button not pressed
    header("Location: ../admin/users.php? errorMessage=$feedbackMessage");
?>
