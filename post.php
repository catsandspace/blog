<?php
    require_once "./assets/db_connect.php";
    require_once "./assets/session.php";
    require_once "./assets/functions.php";

/*******************************************************************************
   GET SELECTED POST WHERE ID = post.php?getpost[id]
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $getPost = $_GET['getpost'];

        $query  =
        "SELECT posts.*,
        categories.name,
        users.id, users.username, users.email, users.website
        FROM posts
        LEFT JOIN categories
        ON posts.categoryid = categories.id
        LEFT JOIN users
        ON posts.userid = users.id
        WHERE published = 1
        AND posts.id = '{$getPost}'";

        if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($postId, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName, $postUserId, $authorName, $authorEmail, $authorWebsite);
            $stmt->fetch();

        } else {
            $errorMessage = "Något gick fel när sidan skulle hämtas.";
        }
    }

    if ($postId == NULL) {
        // TODO: Show 404-page instead?
        $errorMessage = "Vi hittade inget inlägg med angivet id";
    }

/*******************************************************************************
   START OF CHECK TO CONFIRM THAT ALL REQUIRED FIELDS ARE FILLED.
*******************************************************************************/

    $fields = array(
        "content" => "",
        "name" => "",
        "email" => "",
        "website" => ""
    );

    $allRequiredFilled = TRUE;
    $errors = array();

    $errorInfo = "<p class=\"error-msg\">Ooops, något gick fel! Se felmeddelanden nedan.</p>";
    $obligatoryField = "<p class=\"error-msg\">Fältet ovan är obligatoriskt.</p>";
    $obligatoryFieldEmail = "<p class=\"error-msg\">Fältet ovan är obligatoriskt men tomt eller felaktigt ifyllt.<br> Formatera enligt: namn@catsandspace.com</p>";

// End of variables regarding error messages ***********************************

    if (isset($_POST["add-comment"])) {

        // If user is logged in, the user only need to provide comment content.
        if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE) {
            $requiredFields = array("content");

        } else {
            $requiredFields = array("content", "email", "name", "website");
        }

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

        // Check if email is correctly formatted.
        if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == FALSE) {

            if ($key = 'email') {
                    if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
                        $allRequiredFilled = FALSE;
                        array_push($errors, $key);
                    }
                }
            }

        if ($allRequiredFilled)  {

            if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE) {
                $uid = $_SESSION["userid"];
                $commentContent = $_POST["content"];
                $query = "INSERT INTO comments VALUES ('', '{$uid}', now(), '', '', '{$commentContent}', '', '{$getPost}')";

                if ($stmt->prepare($query)) {
                    $stmt->execute();
                    $stmt->close();
                    header("Location: ./post.php?getpost=$getPost#nav-comment-bottom");

                } else {

                    // TODO: 404?
                    $errorMessage = "Det gick inte att lägga till kommentaren.";
                }
            } else {

                $query = "INSERT INTO comments VALUES ('', '', now(), '{$fields["email"]}', '{$fields["name"]}', '{$fields["content"]}', '{$fields["website"]}', '{$getPost}')";

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
    }

    require_once "./templates/header.php";

/*******************************************************************************
   GET COMMENTS ASSOCIATING WITH POST
*******************************************************************************/

    if (isset($_GET['getpost'])) {
        $query  =
        "SELECT
        comments.*,
        users.username,
        users.email,
        users.website
        FROM comments
        LEFT JOIN users
        ON comments.userid = users.id
        WHERE postid = '{$getPost}'
        ORDER BY date DESC";

        if ($stmt -> prepare($query)):
            $stmt-> execute();
            $stmt -> bind_result($commentId, $commentUserId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $commentWebsite, $postId, $userName, $userMail, $userWebsite);

        else:
            $errorMessage = "Något gick fel när kommentarerna skulle hämtas.";
        endif;
    }

/*******************************************************************************
   START OF HTML
*******************************************************************************/
?>
<main>
<?php if ($postId != NULL): ?>
    <article>
        <div class="relative-container">
            <img class="full-width-img" src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
            <a class="relative-container__info relative-container__link" href="index.php?display=<?php echo $categoryId ?>">Kategori: <?php echo str_replace(' ', '', $categoryName); ?></a>
        </div>
        <p class="author-info">[ Publicerad: <?php echo formatDate($created); ?> ]</p>
        <p class="author-info"><?php if (formatDate($created) != formatDate($updated)): ?>[ Uppdaterad: <?php echo formatDate($updated); ?> ]</p>
            <?php endif; ?>
        <p class="author-info">[ Uppladdad av: <?php echo $authorName; ?> ]</p>
        <p class="author-info">[ <a class="author-info__links" href="mailto:<?php echo $authorEmail; ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Skicka e-post</a> ]</p>
        <p class="author-info">[ <a class="author-info__links" href="<?php echo $authorWebsite; ?>"><i class="fa fa-globe" aria-hidden="true"></i> Besök webbplats</a> ]</p>
        <h1 class="padding-top-l"><?php echo formatInnerHtml($title); ?></h1>
        <p><?php echo formatInnerHtml($content); ?></p>
        <?php if (isset($_POST["new-comment"]) || (isset($_POST["add-comment"]) && !$allRequiredFilled)): ?>
        <div class="comment-container margin-bottom-l" id="nav-comment-top">
            <h2>Skriv ny kommentar</h2>
            <?php if (!empty($errors)) { echo $errorInfo; } ?>
            <?php if (isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE): ?>
            <p class="author-info">Du kommenterar som: @<?php echo $_SESSION["username"]; ?></p>
            <?php endif; ?>
            <form method="post" novalidate>
                <fieldset>
                    <legend class="hidden">Skriv ny kommentar</legend>
                    <label class="form-field__label" for="content">Kommentar</label>
                    <textarea class="form-field edit-post__textarea margin-bottom-l" name="content" id="content" cols="25" rows="7" required><?php echo $fields['content']; ?></textarea>
                    <?php if (in_array("content", $errors)) { echo $obligatoryField; } ?>
                    <!-- Only ask visitors that are not logged in to provide info -->
                    <?php  if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] == FALSE): ?>
                    <label class="form-field__label" for="name">Ditt namn</label>
                    <input class="form-field" type="text" name="name" id="name" required value="<?php echo $fields['name']; ?>">
                    <?php if (in_array("name", $errors)) { echo $obligatoryField; } ?>
                    <label class="form-field__label" for="email">Din e-postadress</label>
                    <input class="form-field" type="email" name="email" id="email" required value="<?php echo $fields['email']; ?>">
                    <?php if (in_array("email", $errors)) { echo $obligatoryFieldEmail; } ?>
                    <label class="form-field__label" for="website">Din webbplats</label>
                    <input class="form-field" type="url" name="website" id="website" required value="<?php echo $fields['website']; ?>">
                    <?php if (in_array("website", $errors)) { echo $obligatoryField; } ?>
                    <?php endif; ?>
                    <button type="submit" class="button margin-bottom-l" name="add-comment">Lägg till</button>
                </fieldset>
            </form>
        </div>
        <?php else: ?>
        <form method="post" action="#nav-comment-top">
            <button type="submit" name="new-comment" value="true" class="button margin-bottom-l" id="nav-comment-bottom">Kommentera inlägget</button>
        </form>
        <?php endif; ?>
        <div class="comment-container">
            <h2>Kommentarer</h2>
            <?php while (mysqli_stmt_fetch($stmt)):
            if ($commentUserId != NULL):
                $commentEmail = $userMail;
                $commentAuthor = $userName;
                $commentWebsite = $userWebsite;
            endif; ?>
            <p class="comment-container__text"><?php echo formatInnerHtml($commentContent); ?></p>
            <div class="comment__border-bottom">
                <p class="author-info">[ Av: <?php echo $commentAuthor; if ($commentUserId != NULL) { echo " (administratör)";}; ?> ]</p>
                <p class="author-info">[ Skriven: <?php echo formatDate($commentCreated); ?> ]</p>
                <p class="author-info">[ <a class="author-info__links" href="mailto:<?php echo $commentEmail; ?>"><i class="fa fa-envelope" aria-hidden="true"></i> Skicka e-post</a> ]</p>
                <p class="author-info"> [ <a class="author-info__links" href="<?php echo $commentWebsite; ?>"><i class="fa fa-globe" aria-hidden="true"></i> Besök webbplats</a> ]</p>
            </div>
            <?php endwhile; ?>
            <?php if ($commentId == NULL): echo "<p class=\"saffron-text primary-brand-font\">Detta inlägg har inga kommentarer ännu.</p>"; endif; ?>
        </div>
    </article>
</main>
<!-- TODO: Remove dev link when final -->
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; echo "<u><a href=\"?getpost=1\">for developers</a></u>"; endif; ?>
<?php require_once "./templates/footer.php"; ?>
