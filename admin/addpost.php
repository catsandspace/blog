<?php
    // require_once "../templates/header.php";
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");
    }

/*******************************************************************************
   START OF CHECK TO CONFIRM THAT ALL REQUIRED FIELDS ARE FILLED.
*******************************************************************************/

    // This is used to stop user from leaving important fields empty.
    $allRequiredFilled = TRUE;

    // If a required field is left empty, info the key will be inserted in $errors
    // $obligatoryField is used to print out error message to user
    $errors = array();
    $obligatoryField = "<p class=\"error\">Obligatoriskt fält</p><br>";

    $fields = array(
        "publish" => "",
        "headline" => "",
        "post-content" => "",
        "category" => ""
    );

    if (isset($_POST["submit"])) {

        // These variables are used for checking if all fields are filled.
        $allRequiredFilled = TRUE;
        $requiredFields = array("publish", "headline", "post-content", "category");
        $uploadedFile = $_FILES["post-img"]["size"];

        // This checks if all required fields are filled.
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

        // Check if file has a file size. If not, push key to $errors.
        if (empty($uploadedFile)) {
            $allRequiredFilled = FALSE;
            array_push($errors, "file");
        }

/*******************************************************************************
   START OF DATABASE INSERTION SINCE ALL REQUIRED FIELDS ARE FILLED
*******************************************************************************/

        if ($allRequiredFilled) {

            $userid = $_SESSION["userid"];

            $publish = mysqli_real_escape_string($conn, $fields["publish"]);
            $headline = mysqli_real_escape_string($conn, $fields["headline"]);
            $content = mysqli_real_escape_string($conn, $fields["post-content"]);
            $category = mysqli_real_escape_string($conn, $fields["category"]);

            $query = "INSERT INTO posts VALUES ('', {$userid}, now(), '', '', '{$headline}', '{$content}', '{$publish}', '{$category}')";

            // Insert and update database values
            if ($stmt->prepare($query)) {
                $stmt->execute();
                $imageId = $stmt->insert_id; // Catches the created post.id for later use

                // Now lets start working with the uploaded file
                $fileName = basename($_FILES["post-img"]["name"]); // The name of the file
                $temporaryFile = $_FILES["post-img"]["tmp_name"]; // The temporary file path
                $type = pathinfo($fileName, PATHINFO_EXTENSION); // The file type
                $fileError = checkUploadedFile($_FILES["post-img"]); // A function to check file errors
                $targetName = "../uploads/postimg/" . basename("postimg_") . $imageId . ".$type"; // The new file path connected with post.id column

                // Move uploaded file to "uploads/postimg/ and update $targetName to a appropiate path in table posts.image
                if (!$fileError) {
                    move_uploaded_file($temporaryFile, $targetName); // Move file from temp to new file path
                    $targetName = "uploads/postimg/". basename("postimg_") . $imageId . ".$type"; // Renames the file path
                    $updateQuery = "UPDATE posts SET image ='{$targetName}' WHERE id ='{$imageId}' "; // Inserts correct file path into db column posts.image
                    $stmt->prepare($updateQuery); // Prepares 2nd query to UPDATE posts.image with new value.
                    $stmt->execute();

                    // Redirect to confirmation.php
                    header("Location: ./confirmation.php");
                }
            } else {
                // If problem occurs, create variable $databaseError
                $databaseError = "<p class=\"error\">Det gick inte att lägga upp inlägget i databasen. Försök igen.</p>";
            }
        }
    }
// TODO: Remove all <br> once CSS is used.
    $query = "SELECT * FROM categories";
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $category);
    }
?>
<h2>Skapa nytt inlägg</h2>

<form method="POST" enctype="multipart/form-data">
    <label for="choose-file">Bild</label><br>
    <input type="file" name="post-img" id="choose-file" required><br>
    <?php if (in_array("file", $errors)) { echo $obligatoryField; } ?>
    <?php if (!empty($fileError)) { echo "$fileError<br>"; } ?>

    <input type="radio" name="publish" id="publish" value="1" required <?php if ($fields["publish"] == 1) { echo "checked"; } ?> >
    <label for="publish">Publicera</label><br>
    <input type="radio" name="publish" id="draft" value="2" required <?php if ($fields["publish"] == 2) { echo "checked"; } ?>>
    <label for="draft">Spara som utkast</label><br>
    <?php if (in_array("publish", $errors)) { echo $obligatoryField; } ?>

    <label for="headline">Rubrik</label><br>
    <input type="text" name="headline" id="headline" placeholder="Rubrik"
    value="<?php echo $fields["headline"]; ?>" required><br>
    <?php if (in_array("headline", $errors)) { echo $obligatoryField; } ?>

    <label for="post-content">Beskrivning</label><br>
    <textarea name="post-content" id="post-content" rows="10" cols="50" placeholder="Skriv något om bilden" required><?php echo $fields["post-content"]; ?></textarea><br>
    <?php if (in_array("post-content", $errors)) { echo $obligatoryField; } ?>

    <div>
        <h3>Kategori</h3>

        <?php while (mysqli_stmt_fetch($stmt)): ?>
        <input type="radio" name="category" value="<?php echo $id; ?>" required <?php if ($fields["category"] == $id) { echo "checked"; } ?>>
        <label for="publish"><?php echo ucfirst($category); ?></label><br>
        <?php endwhile; $stmt->close();?>
        <?php if (in_array("category", $errors)) { echo $obligatoryField; } ?>

    </div>
    <button class="button" type="submit" name="submit">Spara</button>
</form>
<a href="./dashboard.php" class="button error"><br>Till huvudmenyn</a>
<?php require_once "../templates/footer.php"; ?>
