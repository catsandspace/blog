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

        //$query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1, id = '{$getPost}'";
        //TODO: ^ FIX Problem with $getPost won't work with LEFT JOIN, we also need to join posts.userid w. users.username ^

        $query = "SELECT * FROM posts WHERE id = '{$getPost}' AND published = 1";

            if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
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
            //$post["categoryname"] = $categoryName;

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
    <article class="">
        <div class="post-test">
            <img src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>">
            <div class="post-test__flex">
                <p>Skapad: <?php echo $post["created"]; ?></p>

                <?php if ($post["created"] != $post["updated"]): ?>
                <p>Uppdaterad: <?php echo $post["updated"]; ?></p>
                <?php endif; ?>
                </div>

                <p>Av: <?php echo $post["userid"]; ?></p>
                <p class="tag">Kategori: <a href="index.php?display=<?php echo $post["categoryid"] ?>"><?php echo str_replace(' ', '', $post["categoryid"]); ?></a></p>
                <h2>Titel: <?php echo $post["title"]; ?></h2>
                <p>Text: <?php echo $post["content"]; ?></p>


            <div class="post-test__comments">
                <h3>Kommentarer:</h3>
                <!-- TODO: Loop these out.. -->
                <?php if ($comment["id"] != NULL): ?>
                <p class="commentAuthor">By: <?php echo $comment["name"]; ?></p>
                <p><?php echo $comment["created"]; ?></p><br>
                <p><?php echo $comment["content"]; ?></p>
                <?php else: echo "<p>Detta inlägg har inga kommentarer.</p>"; endif; ?>

            </div>

            <div class="post-test__comments">
                <h3>Kommentera inlägg:</h3>
                <!-- FORM START -->
                <form>

                </form>
                <!-- FORM END -->

            </div>
        </div>
    </article>

</main>
<!-- TODO: Remove dev link when final -->
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; echo "<u><a href=\"?getpost=1\">for developers</a></u>"; endif; ?>
