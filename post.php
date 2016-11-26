<?php
    require_once "./templates/header.php";

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
        //"categoryname" => ""
    );

    $comment = array(
        "id" => "",
        "userid" => "",
        "created" => "",
        "email" => "",
        "name" => "",
        "content" => "",
        "postid" => ""
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

        //TODO: join posts.userid w. users.username

            if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName, $postUsername);
            $stmt->fetch();
            //$stmt->close();

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

            //var_dump($post);

            } else {
                $errorMessage = "Något gick fel.";
            }

    }

/*******************************************************************************
   GET COMMENTS ASSOCIATING WITH POST
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $query = "SELECT * FROM comments WHERE postid = '{$getPost}'";

        if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($commentId, $commentUserId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $postId);
            $stmt->fetch();

            $comment["id"] = $commentId;
            $comment["userid"] = $commentUserId;
            $comment["created"] = $commentCreated;
            $comment["email"] = $commentEmail;
            $comment["name"] = $commentAuthor;
            $comment["content"] = $commentContent;
            $comment["postid"] = $postId;

        } else {
            $errorMessage = "Något gick fel.";
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
    <article class="post-test">
        <img class="post-test__img" src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>">
        <div class="post-test__flex">
            <p>Skapad: <?php echo $post["created"]; ?></p>

            <?php if ($post["created"] != $post["updated"]): ?>
            <p>Uppdaterad: <?php echo $post["updated"]; ?></p>
            <?php endif; ?>
            </div>

            <p>Uppladdad av: <span class="post-text__name"><?php echo $post["username"]; ?></span></p>
            <p class="tag">Kategori: <a href="index.php?display=<?php echo $post["categoryid"] ?>"><?php echo str_replace(' ', '', $post["categoryname"]); ?></a></p>
            <h2 class="post-text__title">Titel: <?php echo $post["title"]; ?></h2>
            <p>Text: <?php echo $post["content"]; ?></p>


        <div class="post-test__comments">
            <h3>Kommentarer:</h3>
            <!-- TODO: Loop these out.. -->
            <?php if ($comment["id"] != NULL): ?>
            <p>Av: <span class="post-text__name"><?php echo $comment["name"]; ?></span></p>
            <p><?php echo $comment["created"]; ?></p>
            <p><?php echo $comment["content"]; ?></p>
            <?php else: echo "<p>Detta inlägg har inga kommentarer.</p>"; endif; ?>

        </div>

        <div class="post-test__comments">
            <h3>Kommentera inlägg:</h3>
            <!-- FORM START -->
            <form method="post" action="" class="">
                <fieldset>
                    <legend class="hidden">Skriv ny kommentar</legend>

                    <!-- TODO: Checka så att dessa är ifyllda.. -->

                    <!-- NAME START -->
                    <label class="form-field__label" for="userName">Namn:</label>
                    <input class="form-field" type="text" name="name" id="name" required>
                    <!-- NAME END -->

                    <!-- EMAIL START -->
                    <label class="form-field__label" for="userName">Email:</label>
                    <input class="form-field" type="email" name="email" id="email" required>
                    <!-- EMAIL END -->

                    <!-- TEXTFIELD START -->
                    <label class="form-field__label" for="comment">Kommentar:</label>
                    <textarea class="" name="comment" id="comment" required></textarea>
                    <!-- TEXTFIELD END -->

                </fieldset>
            </form>
            <!-- FORM END -->

        </div>
    </article>

</main>
<!-- TODO: Remove dev link when final -->
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; echo "<u><a href=\"?getpost=1\">for developers</a></u>"; endif; ?>
