<?php
    // File to include
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    // Variables
    $display = NULL; // To avoid "undefined variable".
    $numberOfComments = NULL;
    $errorMessage = NULL;


    // Pagination, display 5 posts per page
    $postPage = 5;

    // Query to check number of rows in table posts
    $query = "SELECT id FROM posts WHERE published = 1";

    // If GET request "display" is set.
    if (isset($_GET["display"])) {
        $display = $_GET["display"];
        // New SQL statement WHERE categories.category = $display.
        $query = "SELECT posts.id, categories.id FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}' AND published = 1";
    }

    // Execute query.
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $rows = $stmt->num_rows; // Number of rows in posts
    }
    else {
        $errorMessage = "Fel på query.";
    }
    $last = ceil($rows/$postPage); // Round up
    // If less posts than number on each page
    if ($last < 1) {
        $last = 1;
    }

    $pagenum = 1; // First page
    if(isset($_GET['pn'])){
        $pagenum = $_GET['pn'];
    }
    if ($pagenum < 1) {
        $pagenum = 1;
    } else if ($pagenum > $last) {
        $pagenum = $last;
    }
    $limit = 'LIMIT ' .($pagenum - 1) * $postPage .',' .$postPage; //  => LIMIT 0, 5 or

    // SQL statement with LEFT JOIN table -> posts & categories. Latest post is shown first.
    $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC $limit";

    // If GET request "display" is set.
    if (isset($_GET["display"])) {
        $display = $_GET["display"];

        // New SQL statement WHERE categories.category = $display.
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}' AND published = 1 ORDER BY created DESC $limit";
    }

    // Execute query.
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

    $paginationCtrls = '';
    if($last != 1){
	/* If page one then we don't need a link to the previous page.
        Otherwise generate link to previous page. */
        if ($pagenum > 1) {
            $previous = $pagenum - 1;
            $first = 1;
            // Diffent strings depending on if categories set
            if ($display) {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$first.'&display='.$display.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;</a> ';
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'&display='.$display.'"><i class="fa fa-angle-left" aria-hidden="true"></i> Föregående &nbsp;</a> ';
            } else {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$first.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;</a> ';
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'"><i class="fa fa-angle-left" aria-hidden="true"></i> Föregående &nbsp;</a> ';
            }
        }

        // This does the same as above, only checking if we are on the last page
        if ($pagenum != $last) {
            $next = $pagenum + 1;
            // Diffent strings depending on if categories set
            if ($display) {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'&display='.$display.'"> Nästa <i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;</a> ';
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$last.'&display='.$display.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a> ';
            } else {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'"> Nästa <i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;</a> ';
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$last.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a> ';
            }
        }
    }

    $posts = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($posts, array(
            "id" => $id,
            "image" => $image,
            "title" => $title,
            "content" => $content,
            "categoryId" => $categoryId,
            "created" => $created,
            "categoryName" => $categoryName
        ));
    } ?>
    <!-- TODO: un-comment this one when responsive is OK -->
    <!-- <div class="content-slides-in"> -->
    <main class="blogpost">
        <div class="pagination-wrapper">
            <?php
                echo $paginationCtrls;
            ?>
        </div>
        <?php if (count($posts)<1){
                if ($display != NULL) {
                    echo "<p class='blogpost__message'>Det finns inga inlägg i vald kategori!</p>";
                } else {
                    echo "<p class='blogpost__message'>Det finns inga inlägg!</p>";
                }
            } else {
        ?>
        <div class="blogpost__flex-list">
    <?php for ($i=0; $i < count($posts); $i++):
        $post = $posts[$i];
    ?>
            <article class="blogpost__article">
                <div class="blogpost-wrapper">
                    <a href="post.php?getpost=<?php echo $post["id"] ?>"><img src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>" class="blogpost-wrapper__img"></a>
                    <div class="blogpost-wrapper__text">
                        <h1><a href="post.php?getpost=<?php echo $post["id"] ?>"><?php echo formatInnerHtml($post["title"]); ?></a></h1>
                        <p class="blogpost-wrapper__tags">[ Tags: <a href="?display=<?php echo $post["categoryId"] ?>" class="blogpost-wrapper__links"><?php echo str_replace(' ', '', $post["categoryName"]); ?> ]</a> [ Publicerad: <?php echo formatDate($post["created" ]); ?> ]</p>
                        <div class="comment-bubble">
                                <?php // START OF COMMENTS

                                // TODO: Right now, this div is not used. Delete if we don't want it.

                                $totalNumberOfComments = 0;
                                $errorMessage = NULL;

                                $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

                                if ($stmt->prepare($query)) {
                                    $stmt->execute();
                                    $stmt->bind_result($commentId, $userId, $commentCreated, $commentEmail, $commentAuthor, $commentContent, $commentWebsite, $postId);
                                } else {
                                    $errorMessage = "Något gick fel.";
                                }

                                while (mysqli_stmt_fetch($stmt)):
                                    $stmt->store_result();
                                    $numberOfComments = mysqli_stmt_num_rows($stmt);

                                    if ($post["id"] == $postId) {
                                        $totalNumberOfComments++;
                                    }

                                endwhile;
                                ?>
                            <a href="post.php?getpost=<?php echo $post["id"] ?>"><i class="fa fa-comment comment-bubble__offset-text" aria-hidden="true"></i>
                            <p class="comment-bubble__number"><?php echo "$totalNumberOfComments" ?></p></a>
                        </div>
                    </div>
                </div>
            </article>
    <?php endfor; ?>
        </div>

        <!-- </div> -->
        <div class="pagination-wrapper">
    <div class="pagination-wrapper__text pagination-wrapper__text_bottom">
<?php
    echo $paginationCtrls;
?>
    </div>
</div>
<?php
    }
?>
    </main>

<?php
    if ($errorMessage) { echo $errorMessage; }
?>

<?php
    require_once "./templates/footer.php";
?>
