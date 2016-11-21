<?php
    require_once "../templates/header.php";
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");
    }

    // This is used to stop user from leaving important fields empty.
    $allRequiredFilled = TRUE;
    $obligatoryField = "<p class=\"error\">Det här fältet är obligatoriskt</p><br>";
    $publish = "";
    $headline = "";
    $postContent = "";
    $postCategory = "";
    $errors = array();
    // TODO: Add array for info stored in $_FILES.

    if (isset($_POST["submit"])) {

        //These variables are used for checking if all fields are filled.
        $allRequiredFilled = TRUE;
        $required_fields = array("publish", "headline", "post-content", "category");
        $uploadedFile = $_FILES["post-img"]["size"];

        // This checks if all required fields are filled.
        for ($i = 0; $i < count($required_fields); $i++) {
            $key = $required_fields[$i];

            // TODO: Sort this out, ATM shown as "undefined index".
            $value = $_POST[$key];

            if (empty($value)) {
                $allRequiredFilled = FALSE;
                array_push($errors, $key);
            }
        }

        // This checks if the file has a file size
        if (empty($uploadedFile)) {
            $allRequiredFilled = FALSE;
        }

        // These are printed if user already filled the fields but forgot to fill all.
        // TODO: convert to function.
        // TODO: Keep it dry. This needs attention.
        $publish = mysqli_real_escape_string($conn, $_POST["publish"]);
        $headline = mysqli_real_escape_string($conn, $_POST["headline"]);
        $postContent = mysqli_real_escape_string($conn, $_POST["post-content"]);
        $postCategory = mysqli_real_escape_string($conn, $_POST["category"]);

        // Escapes special characters in a string for use in an SQL statement
        if ($allRequiredFilled) {

            $userid = $_SESSION["userid"];
            $query = "INSERT INTO posts VALUES ('', {$userid}, now(), '', '', '{$headline}', '{$postContent}', '{$publish}', '{$postCategory}')";

            // Lets insert and update database values.
            if ($stmt->prepare($query)) { // Prepares 1st query INSERTS first query values into db
                $stmt->execute();
                $imageId = $stmt->insert_id; // Catches the created post.id for later use

                // NOW lets start working with the uploaded file
                $fileName = basename($_FILES["post-img"]["name"]); // The name of the file
                $temporaryFile = $_FILES["post-img"]["tmp_name"]; // The temporary file path
                $type = pathinfo($fileName, PATHINFO_EXTENSION); // The file type
                $fileError = checkUploadedFile($_FILES["post-img"]); // This checks if there are any file errors
                $targetName = "../uploads/postimg/" . basename("postimg_") . $imageId . ".$type"; // The new file path connected with post.id column

                // Move uploaded file to "uploads/postimg/ and update $targetName to a appropiate path in table posts.image
                if (!$fileError) {
                    move_uploaded_file($temporaryFile, $targetName); // Move file from temp to new file path
                    $targetName = "uploads/postimg/". basename("postimg_") . $imageId . ".$type"; // Renames the file path
                    $updateQuery = "UPDATE posts SET image ='{$targetName}' WHERE id ='{$imageId}' "; // Inserts correct file path into db column posts.image
                    $stmt->prepare($updateQuery); // Prepares 2nd query to UPDATE posts.image with new value.
                    $stmt->execute();

                    // Filename should now be postimg_[post.id].[type]
                    die(header("Location: ./addpost.php?message=success"));

                // When file error occurs save new post as draft [value: 2]
                } else if ($fileError) {
                    $updateQuery = "UPDATE posts SET published ='2' WHERE id ='{$imageId}' ";
                    $stmt->prepare($updateQuery);
                    $stmt->execute();
                    die(header("Location: ./addpost.php?message=fileerror"));
                }

            } else {
                die(header("Location: ./addpost.php?message=failed"));
            }
        }


    }
// TODO: Remove all <br> once CSS is used.
    $query = "SELECT * FROM categories";
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $postCategory);
    }
?>
<h2>Skapa nytt inlägg</h2>


<form method="POST" enctype="multipart/form-data">
    <label for="choose-file">Bild</label><br>
    <input type="file" name="post-img" id="choose-file" required><br>

    <input type="radio" name="publish" id="publish" value="1" required <?php if ($publish == 1) { echo "checked"; } ?> >
    <label for="publish">Publicera</label><br>
    <input type="radio" name="publish" id="draft" value="2" required <?php if ($publish == 2) { echo "checked"; } ?>>
    <label for="draft">Spara som utkast</label><br>
    <?php if (in_array("publish", $errors)) { echo $obligatoryField; } ?>

    <label for="headline">Rubrik</label><br>
    <input type="text" name="headline" id="headline" placeholder="Rubrik"
    value="<?php echo $headline; ?>" required><br>
    <?php if (in_array("headline", $errors)) { echo $obligatoryField; } ?>

    <label for="post-content">Beskrivning</label><br>
    <textarea name="post-content" id="post-content" rows="10" cols="50" placeholder="Skriv något om bilden" required><?php echo $postContent; ?></textarea><br>
    <?php if (in_array("post-content", $errors)) { echo $obligatoryField; } ?>

    <div>
        <h3>Kategori</h3>

        <?php while (mysqli_stmt_fetch($stmt)): ?>
        <input type="radio" name="category" value="<?php echo $id; ?>" required <?php if ($postCategory == $id) { echo "checked"; } ?>>
        <label for="publish"><?php echo ucfirst($postCategory); ?></label><br>
        <?php endwhile; $stmt->close();?>
        <?php if (in_array("category", $errors)) { echo $obligatoryField; } ?>

    </div>
    <button class="button" type="submit" name="submit">Spara</button>
</form>
<p class="upload-message">
<?php
    // Message if POST = succeed/failed.
    switch ((isset($_GET["message"]) ? $_GET["message"]: "" )) {
        case "success":
            echo "Inlägget laddades upp i databasen.";
            break;
        case "fileerror":
            echo "Ej tillåtet filformat.<br>Tillåtna filformat: [tillåtna filformat]<br>Inlägg sparat som utkast.";
            break;
        case "failed":
            echo "Du måste fylla i alla fält.";
            break;
    }
?>
</p>
<a href="./dashboard.php" class="button"><br>Till huvudmenyn</a>
<?php require_once "../templates/footer.php"; ?>
