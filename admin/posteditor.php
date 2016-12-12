<?php
    require_once "../templates/header.php";
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");
    }

    // This is used to populate input fields.
    $fields = array(
        "publish" => "",
        "headline" => "",
        "post-content" => "",
        "category" => ""
    );

/*******************************************************************************
   START TO CHECK IF EXISTING POST IS TO BE EDITED
*******************************************************************************/

    if (isset($_GET['edit'])) {
        $postIdToEdit = $_GET['edit'];

        $query = "SELECT * FROM posts WHERE id = '{$postIdToEdit}'";

        // Insert and update database values
        if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
            $stmt->fetch();

            //Populate fields array with values from database
            $fields["publish"] = $published;
            $fields["headline"] = $title;
            $fields["post-content"] = $content;
            $fields["category"] = $categoryId;

        }
    }

/*******************************************************************************
   START OF CHECK TO CONFIRM THAT ALL REQUIRED FIELDS ARE FILLED.
*******************************************************************************/

    // This is used to stop user from leaving important fields empty.
    $allRequiredFilled = TRUE;

    // If a required field is left empty, info about the key will be inserted in $errors
    // $obligatoryField is used to print out error message to user
    $errors = array();
    $obligatoryField = "<p class=\"error-msg\">Obligatoriskt fält</p><br>";
    if (isset($_POST["submit"])) {

        // These variables are used for checking if all fields are filled.
        $requiredFields = array("publish", "headline", "post-content", "category");

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
        if (!isset($_GET["edit"])) {
            $uploadedFile = $_FILES["post-img"]["size"];
            if (empty($uploadedFile)) {
                $allRequiredFilled = FALSE;
                array_push($errors, "file");
            }
        }

/*******************************************************************************
   START OF DATABASE INSERTION SINCE ALL REQUIRED FIELDS ARE FILLED
*******************************************************************************/

        if ($allRequiredFilled) {

            $userid = $_SESSION["userid"];
            $publish = $fields["publish"];
            $headline = $fields["headline"];
            $content = $fields["post-content"];
            $category = $fields["category"];

            $query = "INSERT INTO posts VALUES ('', '{$userid}', now(), '', '', '{$headline}', '{$content}', '{$publish}', '{$category}')";

            if (isset($_GET["edit"])) {

                 $query = "UPDATE posts SET title ='{$headline}', content ='{$content}', published ='{$publish}', categoryid = '{$category}' WHERE id ='{$postIdToEdit}'";
                if ($stmt->prepare($query)) {
                    $stmt->execute();
                } else {
                    // If problem occurs, create variable $databaseError
                    $databaseError = "<p class=\"error-msg\">Det gick inte att uppdatera inlägget i databasen. Försök igen.</p>";
                }

                // Redirect to confirmation.php?edit
                header("Location: ./confirmation.php?updated=true");
            }

            if (!isset($_GET["edit"])) {
                // Insert and update database values
                if ($stmt->prepare($query)) {
                    $stmt->execute();
                    $imageId = $stmt->insert_id; // Catches the created post.id for later use

                    // Working with the uploaded file
                    $fileName = basename($_FILES["post-img"]["name"]);
                    $temporaryFile = $_FILES["post-img"]["tmp_name"]; // The temporary file path
                    $type = pathinfo($fileName, PATHINFO_EXTENSION);
                    $fileError = checkUploadedFile($_FILES["post-img"]); // A function to check file errors
                    $targetName = "../uploads/postimg/" . basename("postimg_") . $imageId . ".$type"; // The new file path connected with post.id column

                    // Move uploaded file to "uploads/postimg/ and update $targetName to a appropiate path in table posts.image
                    if (!$fileError) {
                        move_uploaded_file($temporaryFile, $targetName);
                        $targetName = "uploads/postimg/". basename("postimg_") . $imageId . ".$type"; // Renames the file path
                        $updateQuery = "UPDATE posts SET image ='{$targetName}' WHERE id ='{$imageId}' "; // Inserts correct file path into db column posts.image

                        // Prepares 2nd query to UPDATE posts.image with new value.
                        if ($stmt->prepare($updateQuery)) {
                            $stmt->execute();
                        } else {
                            $databaseError = "<p class=\"error-msg\">Det gick inte att lägga upp inlägget i databasen. Försök igen.</p>";
                        }
                        // Redirect to confirmation.php
                        header("Location: ./confirmation.php");
                    }
                } else {

                    $databaseError = "<p class=\"error-msg\">Det gick inte att lägga upp inlägget i databasen. Försök igen.</p>";
                }
            }
        }
    }

/*******************************************************************************
            QUERY THAT PRINTS CATEGORIES
*******************************************************************************/

    $query = "SELECT * FROM categories";
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $category);
    }
?>
<main>
    <?php if (isset($_GET["edit"])): ?>
    <h1 class="center-text">Redigera inlägg</h1>
    <?php else: ?>
    <h1 class="center-text">Skapa nytt inlägg</h1>
    <?php endif; ?>
    <?php if (!empty($errors)) { echo "<p class=\"error-msg\">Ooops, något gick fel!</p>"; } ?>
    <form method="POST" enctype="multipart/form-data" novalidate>
        <?php if (!isset($_GET["edit"])): ?>
        <div class="edit-post-div">
            <input class="choose-file__input button" type="file" name="post-img" id="choose-file" required>
            <label class="choose-file__label button" for="choose-file"></label><br>
        </div>
        <?php else: ?>
        <div class="edit-post-div">
            <img src="../<?php echo $image; ?>" alt="Inläggets bild" class="border-xl full-width-img">
        </div>
        <?php endif; ?>
        <?php if (in_array("file", $errors)) { echo $obligatoryField; } ?>
        <?php if (!empty($fileError)) { echo "$fileError<br>"; } ?>
        <div class="edit-post-div">
            <label class="form-field__label" for="headline">Rubrik</label><br>
            <input type="text" class="form-field edit-post__input" name="headline" id="headline" value="<?php echo formatInnerHtml($fields["headline"]); ?>" required><br>
            <?php if (in_array("headline", $errors)) { echo $obligatoryField; } ?>
        </div>
        <div class="edit-post-div">
            <label class="form-field__label" for="post-content">Beskrivning</label><br>
            <textarea class="form-field edit-post__textarea" name="post-content" id="post-content" rows="10" cols="50" required><?php echo replaceSpecialCharacters($fields["post-content"]); ?></textarea><br>
            <?php if (in_array("post-content", $errors)) { echo $obligatoryField; } ?>
        </div>
        <div class="edit-post-div">
            <h2>Kategori</h2>
            <?php while (mysqli_stmt_fetch($stmt)): ?>
            <label class="radiobutton-wrapper">
                <input class="radiobutton-wrapper__button" type="radio" name="category" value="<?php echo $id; ?>" required <?php if ($fields["category"] == $id) { echo "checked"; } ?> >
                <i class="radiobutton-wrapper__icon"></i>
                <?php echo ucfirst($category); ?>
            </label>
            <?php endwhile; $stmt->close();?>
            <?php if (in_array("category", $errors)) { echo $obligatoryField; } ?>
        </div>
        <div class="edit-post-div">
            <h2>Ska inlägget publiceras?</h2>
            <label class="radiobutton-wrapper">
                <input class="radiobutton-wrapper__button" type="radio" name="publish" value="1" required <?php if ($fields["publish"] == 1) { echo "checked"; } ?> >
                <i class="radiobutton-wrapper__icon"></i>
                Publicera
            </label>
            <label class="radiobutton-wrapper">
                <input class="radiobutton-wrapper__button" type="radio" name="publish" value="2" required <?php if ($fields["publish"] == 2) { echo "checked"; } ?>>
                <i class="radiobutton-wrapper__icon"></i>
                Spara som utkast
            </label>
            <?php if (in_array("publish", $errors)) { echo $obligatoryField; } ?>
        </div>
        <div class="flexbox-wrapper">
            <button class="button" type="submit" name="submit">Spara</button>
            <a href="./dashboard.php" class="button error"><br>Till kontrollpanelen</a>
        </div>
    </form>
</main>
<?php require_once "../templates/footer.php"; ?>
