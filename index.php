<?php

    // File to include
    require_once "./templates/header.php"; // Header content.

    // Variables
    $display = NULL; // To avoid "undefined variable".
    $numberOfComments = NULL;

    // SQL statement with LEFT JOIN table -> posts & categories.
    $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1";

    // If GET request "display" is set.
    if (isset($_GET["display"])) {
        $display = $_GET["display"];

        // New SQL statement WHERE categories.category = $display.
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}' AND published = 1";
    }

    // Execute query.
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

    $posts = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($posts, array(
            "id" => $id,
            "image" => $image,
            "title" => $title,
            "content" => $content,
            "categoryId" => $categoryId,
            "categoryName" => $categoryName
        ));
    }

    for ($i=0; $i < count($posts); $i++):
        $post = $posts[$i];
        ?>

    <article class="list">
        <div class="content-wrapper">
            <img src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>">
            <div class="text-wrapper">
                <h2><?php echo $post["title"]; ?></h2>
                <p class="tag">Tags: <a href="?display=<?php echo $post["categoryId"] ?>"><?php echo str_replace(' ', '', $post["categoryName"]); ?></a> </p>
                <p><?php echo $post["content"]; ?></p>

                <div class="post-comments">
                    <div class="comment-wrapper">
                        <?php // START OF COMMENTS

                            $totalNumberOfComments = NULL;
                            $errorMessage = NULL;

                            $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

                            if ($stmt->prepare($query)) {
                                $stmt->execute();
                                $stmt->bind_result($commentId, $userId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $postId);
                            }
                            else {
                                $errorMessage = "Något gick fel.";
                            }

                            while (mysqli_stmt_fetch($stmt)):
                                $stmt->store_result();
                                $numberOfComments = mysqli_stmt_num_rows($stmt);

                                if ($post["id"] == $postId) {
                                    echo "$commentContent<br>";
                                    echo "Skriven av $commentAuthor<br><br>";
                                    $totalNumberOfComments++;
                            } endwhile;
                        ?>
                    </div>
                <?php if ($totalNumberOfComments): ?>
                        <i class="fa fa-comment" aria-hidden="true"></i>
                        <p><?php echo "$totalNumberOfComments" ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article>
<?php endfor; ?>


<?php if($errorMessage) { echo $errorMessage; }
require_once "./templates/footer.php"; ?>
