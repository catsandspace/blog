<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    //TODO: CLEAN UP ARRAYS
    //TODO: ERROR-MESSAGES/404
    //TODO: CHECK ARTICLE ELEMENT SEMANTICS
    //TODO: REQUIRE ON INPUT-FIELDS
    //TODO: REMOVE DEV LINK
    //TODO: CHECK $stmt->close();
    //TODO: FIX ALL REQUIRED FILLED

    // $post = array(
    //     "id" => "",
    //     "userid" => "",
    //     "created" => "",
    //     "updated" => "",
    //     "image" => "",
    //     "title" => "",
    //     "content" => "",
    //     "username" => "",
    //     "categoryid" => "",
    //     "categoryname" => ""
    // );

    $comment = array(
        "content" => "",
        "name" => "",
        "email" => "",
        "website" => ""
    );

/*******************************************************************************
   GET SELECTED POST WHERE ID = post.php?getpost[id]
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $getPost = $_GET['getpost'];

        $query  =
        "SELECT posts.*,
        categories.name,
        users.*
        FROM posts
        LEFT JOIN categories
        ON posts.categoryid = categories.id
        LEFT JOIN users
        ON posts.userid = users.id
        WHERE published = 1
        AND posts.id = '{$getPost}'";

            if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($postId, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName, $postUserId, $authorPermission, $authorName, $authorPassword, $authorEmail, $authorWebsite, $authorFirstname, $authorLastname, $authorimg, $authorDescription);
            $stmt->fetch();


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
            $stmt->bind_result($commentId, $commentUserId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $commentWebsite, $postId);

        } else {

            // TODO: 404?
            $errorMessage = "Något gick fel.";
        }
    }

/*******************************************************************************
   START OF CHECK TO CONFIRM THAT ALL REQUIRED FIELDS ARE FILLED.
*******************************************************************************/

    // FIXME: This does not seem to be working properly.
    // This is used to stop user from leaving important fields empty.
    $allRequiredFilled = TRUE;

    // $obligatoryField is used to print out error message to user
    $errors = array();
    $obligatoryField = "<p class=\"error-msg\">Obligatoriskt fält</p><br>";
    if (isset($_POST["add-comment"])) {

        $requiredFields = array(
            "comment",
            "email",
            "name",
            "website"
        );

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

            $content = mysqli_real_escape_string($conn, $_POST["content"]);
            $name = mysqli_real_escape_string($conn, $_POST["name"]);
            $email = mysqli_real_escape_string($conn, $_POST["email"]);
            $website = mysqli_real_escape_string($conn, $_POST["website"]);

            $query = "INSERT INTO comments VALUES ('', '', now(), '{$email}', '{$name}', '{$content}', '{$website}', '{$getPost}')";

            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->close();
                header("Location: ./post.php?getpost=$getPost#nav-comment-bottom");

            } else {

                // TODO: 404?
                $errorMessage = "Det gick inte att lägga till kommentaren.";
            }

        }

    }

/*******************************************************************************
   ERROR MESSAGE
*******************************************************************************/

    if ($postId == NULL) {
        // TODO: Show 404-page instead?
        $errorMessage = "Vi hittade inget inlägg med angivet id";
    }

/*******************************************************************************
   START OF HTML
*******************************************************************************/
?>
<main>
<?php if ($postId != NULL): ?>
    <article class="smaller-font">
        <div class="relative-container">
            <img class="full-width-img" src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
            <a class="relative-container__info relative-container__link" href="index.php?display=<?php echo $categoryId ?>">Kategori: <?php echo str_replace(' ', '', $categoryName); ?></a>
        </div>
        <p class="author-info">[ Publicerad: <?php echo formatDate($created); ?> ]
            <?php if (formatDate($created) != formatDate($updated)): ?>
                [ Uppdaterad: <?php echo formatDate($updated); ?> ]
            <?php endif; ?>
            [ Uppladdad av: <?php echo $authorName; ?> ]
            [ <a class="author-info__links" href="mailto:<?php echo $authorEmail; ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Skicka e-post</a> ]
            [ <a class="author-info__links" href="<?php echo $authorWebsite; ?>"><i class="fa fa-globe" aria-hidden="true"></i> Besök webbplats</a> ]
        </p>
        <h1><?php echo $title; ?></h1>
        <p><?php echo formatInnerHtml($content); ?></p>
        <?php if (!isset ($_POST["new-comment"])): ?>
        <form method="post" action="#nav-comment-top">
            <button type="submit" name="new-comment" value="true" class="button margin-bottom-l" id="nav-comment-bottom">Kommentera inlägget</button>
        </form>
        <?php elseif (isset ($_POST["new-comment"])): ?>
        <div class="comment-container comment-container--xl-margin" id="nav-comment-top">
            <h2>Skriv ny kommentar</h2>
            <form method="post">
                <fieldset>
                    <legend class="hidden">Skriv ny kommentar</legend>
                    <label class="form-field__label" for="content">Kommentar</label>
                    <textarea class="form-field edit-post__textarea margin-bottom-l" name="content" id="content" cols="25" rows="7" required></textarea>
                    <?php if (in_array("content", $errors)) { echo $obligatoryField; } ?>
                    <label class="form-field__label" for="name">Ditt namn</label>
                    <input class="form-field" type="text" name="name" id="name" required>
                    <?php if (in_array("name", $errors)) { echo $obligatoryField; } ?>
                    <label class="form-field__label" for="email">Din e-postadress</label>
                    <input class="form-field" type="email" name="email" id="email" required>
                    <?php if (in_array("email", $errors)) { echo $obligatoryField; } ?>
                    <label class="form-field__label" for="website">Din webbplats</label>
                    <input class="form-field" type="url" name="website" id="website" required>
                    <?php if (in_array("website", $errors)) { echo $obligatoryField; } ?>
                    <button type="submit" class="button margin-bottom-l" name="add-comment" value="Lägg till">Lägg till</button>
                </fieldset>
            </form>
        </div>
        <?php endif; ?>
        <div class="comment-container">
            <h2>Kommentarer</h2>
            <?php while (mysqli_stmt_fetch($stmt)): ?>
            <p><?php echo $commentContent; ?></p>
            <p class="author-info author-info--border">[ Skriven: <?php
            echo formatDate($commentCreated); ?> ] [ Av: <?php echo $commentAuthor; ?>]<br> [
            <a class="author-info__links" href="mailto:<?php echo $commentEmail; ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Skicka e-post</a> ] [
            <a class="author-info__links" href="<?php echo $commentWebsite; ?>"><i class="fa fa-globe" aria-hidden="true"></i> Besök webbplats</a> ]</p>
            <?php endwhile; ?>
            <?php if ($commentId == NULL): echo "<p class=\"saffron-text primary-brand-font\">Detta inlägg har inga kommentarer ännu.</p>"; endif; ?>
        </div>
    </article>
</main>

<!-- TODO: Remove dev link when final -->
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; echo "<u><a href=\"?getpost=1\">for developers</a></u>"; endif; ?>
<?php require_once "./templates/footer.php"; ?>
