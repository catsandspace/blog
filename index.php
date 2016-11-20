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
?>
<?php while (mysqli_stmt_fetch($stmt)): ?>
    <article class="list">
        <div class="content-wrapper">
            <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
            <div class="text-wrapper">
                <h2><?php echo $title; ?></h2>
                <p class="tag">Tags: <a href="?display=<?php echo $categoryId ?>"><?php echo str_replace(' ', '', $categoryName); ?></a> </p>
                <p><?php echo $content; ?></p>
                <div class="post-comments">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                    <p>8</p>
                </div>
            </div>
        </div>
    </article>
<?php endwhile; ?>

<?php
/*******************************************************************************
   START OF COMMENTS
*******************************************************************************/

    $totalNumberOfComments = NULL;
    $errorMessage = NULL;

    $stmtComments = $conn->stmt_init();
    $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

    if ($stmtComments->prepare($query)) {
        $stmtComments->execute();
        $stmtComments->bind_result($commentId, $userId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $postId);
    }
    else {
        $errorMessage = "Något gick fel.";
    }


    while (mysqli_stmt_fetch($stmtComments)):
    $stmtComments->store_result();
    $numberOfComments = mysqli_stmt_num_rows($stmtComments);


    // TODO: this if statement will be used once this is working properly.
    // if ($id == $postId) {
    echo "$commentContent<br>";
    echo "Skriven av $commentAuthor<br><br>";
    $totalNumberOfComments++;
    // }

    endwhile;
    echo "Total number of comments: $totalNumberOfComments<br>";

/*******************************************************************************
   END OF COMMENTS
*******************************************************************************/
?>

<?php if($errorMessage) { echo $errorMessage; }
require_once "./templates/footer.php"; ?>
