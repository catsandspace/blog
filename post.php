<?php 
    require_once "./templates/header.php";

    //Remove these just to avoid header from hiding errors.
    require_once __DIR__."./assets/db_connect.php";
    require_once __DIR__."./assets/session.php";

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


/*******************************************************************************
   GET SELECTED POST WHERE ID = post.php?getpost[id]
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $getPost = $_GET['getpost'];

        //$query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 AND id = '{$getPost}'";

        // ^ Problem with $getPost won't work with LEFT JOIN ??? ^

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

        $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

        if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($commentId, $commentUserId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $postId);
            $stmt->fetch();

        } else {
            $errorMessage = "Något gick fel.";       
        }
    }

/*******************************************************************************
   ERROR MESSAGE
*******************************************************************************/

    if ($post["id"] == NULL) {
        // TODO: Show 404-page instead?
        $errorMessage = "Vi hittade inget inlägg med id: $getPost";
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
                <p>Created: <?php echo $post["created"]; ?></p>

                <?php if ($post["created"] != $post["updated"]): ?>
                <p>Updated: <?php echo $post["updated"]; ?></p>
                <?php endif; ?>
                </div>

                <p>Author: <?php echo $post["userid"]; ?></p>
                <p class="tag">Tags: <a href="index.php?display=<?php echo $post["categoryid"] ?>"><?php echo str_replace(' ', '', $post["categoryid"]); ?></a> </p>
                <h2>Title: <?php echo $post["title"]; ?></h2>
                <p>Content: <?php echo $post["content"]; ?></p>
            

            <div class="post-test__comments">
                <h3>Comments:</h3> 
                <!-- TODO: Loop these out.. -->
                <p class="commentAuthor">By: <?php echo $commentAuthor; ?></p>
                <p><?php echo $commentCreated; ?></p><br>
                <p><?php echo $commentContent; ?></p>

            </div>

            <div class="post-test__comments">
                <h3>Add comment:</h3>

                <form>
                    
                </form>

            </div>
        </div>
    </article>

</main>
<!-- TODO: Remove dev link when final -->
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; echo "<u><a href=\"?getpost=1\">for developers</a></u>"; endif; ?>