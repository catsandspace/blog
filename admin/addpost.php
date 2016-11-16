<?php
    require_once "../templates/header.php";
    require_once "../assets/db_connect.php";
    require_once "../assets/file_upload.php";
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
            $image = mysqli_real_escape_string($conn, $_FILES["post-img"]["tmp_name"]);
            $title = mysqli_real_escape_string($conn, $_POST["headline"]);
            $content = mysqli_real_escape_string($conn, $_POST["post-content"]);
            $published = mysqli_real_escape_string($conn, $_POST["publish"]);
            $category = mysqli_real_escape_string($conn, $_POST["category"]);

            $query = "INSERT INTO posts VALUES ('', 1, '', '', '{$image}', '{$title}', '{$content}', '{$published}', '{$category}')";

            if ($stmt->prepare($query)) {
        		$stmt->execute();
                $stmt->close();
                $feedbackMessage = "Inlägget laddades upp i databasen.";
            } else {
                $feedbackMessage = "Du måste fylla i alla fält.";
            }
        }
    }
// TODO: Remove all <br> once CSS is used.
// In input name="publish", value="1" means publish, 0 means draft.
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
    <input type="radio" name="publish" id="draft" value="0" required>
    <label for="draft">Spara som utkast</label><br>
    <label for="headline">Rubrik</label><br>
    <input type="text" name="headline" id="headline" placeholder="Rubrik" required><br>
    <label for="post-content">Beskrivning</label><br>
    <textarea name="post-content" id="post-content" rows="10" cols="50" placeholder="Skriv något om bilden" required></textarea><br>
    <div>
        <h3>Kategori</h3>
        <!-- TODO: Loopa ut från kategorierna. -->
        <input type="radio" name="category" value="1" required>
        <label for="publish">Cats</label><br>
        <input type="radio" name="category" value="2" required>
        <label for="draft">Space</label><br>
        <input type="radio" name="category" value="3" required>
        <label for="draft">Cats and Space</label><br>
    </div>
    <button class="button" type="submit" name="submit">Spara</button>
</form>
<?php
    // This checks if there is a feedback message and prints it if true.
    if (isset($_POST["submit"]) && $feedbackMessage) { echo $feedbackMessage; }
?>

<a href="./dashboard.php" class="button"><br>Till huvudmenyn</a>
<?php require_once "../templates/footer.php"; ?>
