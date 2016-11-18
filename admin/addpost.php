<?php
    require_once "../templates/header.php";
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == false) {
        header("Location: ../login.php");
    }

    // This is used for printing out feedback message once post is uploaded.
    $feedbackMessage = "";

    // This is used to stop user from leaving important fields empty.
    $allRequiredFilled = true;

    if (isset($_POST["submit"])) {

        //These variables are used for checking if all fields are filled.
        $allRequiredFilled = true;
        $required_fields = array("publish", "headline", "post-content", "category");

        // This checks if all required fields are filled.
        for ($i = 0; $i < count($required_fields); $i++) {
            $value = $_POST[$required_fields[$i]];

            if (empty($value)) {
                $allRequiredFilled = false;
                break;
            }
        }

        // Escapes special characters in a string for use in an SQL statement
        if ($allRequiredFilled) {
            // TODO: Keep it dry. This needs som attention.
            $title = mysqli_real_escape_string($conn, $_POST["headline"]);
            $content = mysqli_real_escape_string($conn, $_POST["post-content"]);
            $published = mysqli_real_escape_string($conn, $_POST["publish"]);
            $category = mysqli_real_escape_string($conn, $_POST["category"]);

            $query = "INSERT INTO posts VALUES ('', 1, '', '', '', '{$title}', '{$content}', '{$published}', '{$category}')";
            
            // Statements for inserting and updating database values TODO: escape char.
            if ($stmt->prepare($query)) { // 1st query -INSERTS values into db
                $stmt->execute();
                $imageId = $stmt->insert_id; // Catches the current post.id
                $fileName = basename($_FILES["post-img"]["name"]); // The name of the file
                $temporaryFile = $_FILES["post-img"]["tmp_name"]; // The temporary file and path
                $type = pathinfo($fileName, PATHINFO_EXTENSION); // The file type
                $fileError = checkUploadedFile($_FILES["post-img"]); // This checks if there are any file errors
                $targetName = "../uploads/postimg/" . basename("postimg_") . $imageId . ".$type"; // Temporary $targetName for moving and renaming file

                // Move uploaded file to "uploads/postimg/ and update $targetName to be appropiate path for table posts.image"
                if (!$fileError) {
                    move_uploaded_file($temporaryFile, $targetName);
                    $targetName = "uploads/postimg/". basename("postimg_") . $imageId . ".$type";
                    $updateQuery = "UPDATE posts SET image ='{$targetName}' WHERE id ='{$imageId}' ";
                    $stmt->prepare($updateQuery);
                    $stmt->execute();

                    $feedbackMessage = "Inlägget laddades upp i databasen.";
                }

            } else {
                $feedbackMessage = "Du måste fylla i alla fält.";
            }
        }
    }
// TODO: Remove all <br> once CSS is used.
// In input name="publish", value="1" means publish, 0 means draft.
    $query = "SELECT * FROM categories";
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $category);
    }
?>
<h1>Skapa nytt inlägg</h1>
<form method="POST" enctype="multipart/form-data">
    <label for="choose-file">Bild</label><br>
    <input type="file" name="post-img" id="choose-file" required><br>
    <?php
        // Prints information about an error if true.
        if (isset($_POST["submit"]) && $fileError) {
            echo "$fileError<br>";
        }
    ?>
    <input type="radio" name="publish" id="publish" value="1" required>
    <label for="publish">Publicera</label><br>
    <input type="radio" name="publish" id="draft" value="2" required>
    <label for="draft">Spara som utkast</label><br>
    <label for="headline">Rubrik</label><br>
    <input type="text" name="headline" id="headline" placeholder="Rubrik" required><br>
    <label for="post-content">Beskrivning</label><br>
    <textarea name="post-content" id="post-content" rows="10" cols="50" placeholder="Skriv något om bilden" required></textarea><br>
    <div>
        <h3>Kategori</h3>

        <?php while (mysqli_stmt_fetch($stmt)): ?>
        <input type="radio" name="category" value="<?php echo $id; ?>" required>
        <label for="publish"><?php echo ucfirst($category); ?></label><br>
        <?php endwhile; $stmt->close();?>

    </div>
    <button class="button" type="submit" name="submit">Spara</button>
</form>
<?php
    // This checks if there is a feedback message and prints it if true.
    //TODO: NEEDS TO BE IN <p> or something, now loose in html.
    if (isset($_POST["submit"]) && $feedbackMessage) { echo $feedbackMessage; }
?>

<a href="./dashboard.php" class="button"><br>Till huvudmenyn</a>
<?php require_once "../templates/footer.php"; ?>