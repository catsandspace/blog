<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    //TODO: CLEAN UP ARRAYS
    //TODO: ERROR-MESSAGES/404
    //TODO: CHECK ARTICLE ELEMENT SEMANTICS
    //TODO: REQUIRE ON INPUT-FIELDS
    //TODO: REMOVE DEV LINK
    //TODO: CHECK $stmt->close();
    //TODO: FIX CLASSES
    //TODO: NEW COMMENTS NEED EMAIL AND WEBSITE URL.
    //TODO: POST ALSO NEEDS AUTHOR NAME, AND AUTHOR'S WEBSITE URL.


    $post = array(
        "id" => "",
        "userid" => "",
        "created" => "",
        "updated" => "",
        "image" => "",
        "title" => "",
        "content" => "",
        "username" => "",
        "categoryid" => "",
        "categoryname" => ""
    );

    $comment = array(
        //"id" => "",
        //"userid" => "",
        //"created" => "",
        "email" => "",
        "name" => "",
        "content" => ""
        //"postid" => ""
    );

/*******************************************************************************
   GET SELECTED POST WHERE ID = post.php?getpost[id]
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $getPost = $_GET['getpost'];

        $query  =
        "SELECT posts.*,
        categories.name,
        users.username
        FROM posts
        LEFT JOIN categories
        ON posts.categoryid = categories.id
        LEFT JOIN users
        ON posts.userid = users.id
        WHERE published = 1
        AND posts.id = '{$getPost}'";

            if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName, $postUsername);
            $stmt->fetch();
            //$stmt->close();
            //

            $post["id"] = $id;
            $post["userid"] = $userId;
            $post["created"] = $created;
            $post["updated"] = $updated;
            $post["image"] = $image;
            $post["title"] = $title;
            $post["content"] = $content;
            $post["categoryid"] = $categoryId;
            $post["categoryname"] = $categoryName;
            $post["username"] = $postUsername;

            } else {

                // TODO: Replace with 404 page.
                $errorMessage = "Något gick fel.";
            }
    }

/*******************************************************************************
   GET COMMENTS ASSOCIATING WITH POST
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $query = "SELECT * FROM comments WHERE postid = '{$getPost}' ORDER BY date DESC";

        if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($commentId, $commentUserId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $postId);

        } else {

            // TODO: 404?
            $errorMessage = "Något gick fel.";
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
    if (isset($_POST["add-comment"])) {

        // These variables are used for checking if all fields are filled.
        $requiredFields = array("email", "name", "content");

        // This checks if all required fields are filled.
        foreach ($comment as $key => $value) {
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
    }

/*******************************************************************************
   INSERTING VALUES FROM FORM TO DATABASE
*******************************************************************************/

    if ($allRequiredFilled) {

        if (isset($_POST["add-comment"])) {

            $name = mysqli_real_escape_string($conn, $_POST["name"]);
            $email = mysqli_real_escape_string($conn, $_POST["email"]);
            $content = mysqli_real_escape_string($conn, $_POST["content"]);

            $query = "INSERT INTO comments VALUES ('', '', now(), '{$email}', '{$name}', '{$content}', '{$getPost}')";

            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->close();
                header("Location: ./post.php?getpost=$getPost#nav-comment-bottom");

            } else {

                // TODO: 404?
                $errorMessage = "Något gick fel.";
            }

        }

    }

/*******************************************************************************
   ERROR MESSAGE
*******************************************************************************/

    if ($post["id"] == NULL) {
        // TODO: Show 404-page instead?
        $errorMessage = "Vi hittade inget inlägg med angivet id";
    }

/*******************************************************************************
   START OF HTML
*******************************************************************************/
?>
<main>

<?php if ($post["id"] != NULL): ?>
<!-- TODO: Make this semantic -->
    <article class="smaller-font">
        <div class="relative-container">
            <img class="full-width-img" src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>">
            <a class="relative-container__info relative-container__link" href="index.php?display=<?php echo $post["categoryid"] ?>">Kategori: <?php echo str_replace(' ', '', $post["categoryname"]); ?></a>
        </div>
        <p class="saffron-text primary-brand-font">[Uppladdad av: <?php echo $post["username"]; ?>] [Publicerad: <?php echo $post["created"]; ?>] <?php if ($post["created"] != $post["updated"]): ?> [Uppdaterad: <?php echo $post["updated"]; ?>] <?php endif; ?></p>
        <h2><?php echo $title; ?></h2>
        <p><?php echo formatInnerHtml($content); ?></p>
        <?php if (!isset ($_POST["new-comment"])): ?>
        <form method="post" action="#nav-comment-top">
            <button type="submit" name="new-comment" value="true" class="button margin-bottom-l" id="nav-comment-bottom">Kommentera inlägget</button>
        </form>
        <?php elseif (isset ($_POST["new-comment"])): ?>
        <div class="padding-normal margin-normal padding-top-xxl" id="nav-comment-top">
            <h3>Skriv ny kommentar</h3>
            <form method="post">
                <fieldset>
                    <legend class="hidden">Skriv ny kommentar</legend>
                    <!-- TODO: REQUIRE ON INPUTS WHEN FINAL-->
                    <label class="form-field__label" for="content">Kommentar</label>
                    <textarea class="form-field edit-post__textarea margin-bottom-l" name="content" id="content" cols="25" rows="7"></textarea>
                    <?php if (in_array("content", $errors)) { echo $obligatoryField; } ?>
                    <label class="form-field__label" for="name">Ditt namn</label>
                    <input class="form-field" type="text" name="name" id="name">
                    <?php if (in_array("name", $errors)) { echo $obligatoryField; } ?>
                    <label class="form-field__label" for="email">Din e-postadress</label>
                    <input class="form-field" type="email" name="email" id="email">
                    <?php if (in_array("email", $errors)) { echo $obligatoryField; } ?>
                    <button type="submit" class="button margin-bottom-l" name="add-comment" value="Lägg till">Lägg till</button>
                </fieldset>
            </form>
            <!-- FORM END -->
        </div>
        <?php endif; ?>
        <div class="padding-normal border-normal">
            <h3>Kommentarer</h3>
            <?php while (mysqli_stmt_fetch($stmt)): ?>
            <p><?php echo $commentContent; ?></p>
            <p class="saffron-text primary-brand-font comment__border-bottom">[Av: <?php echo $commentAuthor; ?>] [Skriven den: <?php echo $commentCreated; ?>]</p>
            <?php endwhile; ?>
            <?php if ($commentId == NULL): echo "<p class=\"saffron-text primary-brand-font\">Detta inlägg har inga kommentarer ännu.</p>"; endif; ?>
        </div>
    </article>
</main>

<!-- TODO: Remove dev link when final -->
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; echo "<u><a href=\"?getpost=1\">for developers</a></u>"; endif; ?>
<?php require_once "./templates/footer.php"; ?>
