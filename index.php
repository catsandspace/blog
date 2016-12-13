<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";
    require_once "./assets/session.php";

/*******************************************************************************
    START OF VARIABLES USED ON PAGE
*******************************************************************************/

    $display = NULL;
    $errorMessage = NULL;
    $numberOfComments = NULL;
    $postsPerPage = 5;
    $pagenum = 1; // main page
    $paginationCtrls = '';
    $queryFailed = "Det blev något fel. Försök igen senare.";

/*******************************************************************************
    START OF PAGINATION AND QUERY TO CHECK NUMBER OF ROWS IN DATABASE TABLE "POSTS"
*******************************************************************************/

    $query = "SELECT id FROM posts WHERE published = 1";

    if (isset($_GET["display"])) {

        $display = $_GET["display"];
        $query = "SELECT posts.id, categories.id FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}' AND published = 1";
    }

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        $rows = $stmt->num_rows;

    } else {
        $errorMessage = $queryFailed;
    }

    $last = ceil($rows / $postsPerPage); // Round up

    // If less posts than number on each page
    if ($last < 1) {
        $last = 1;
    }

    if (isset($_GET['pn'])) {
        $pagenum = $_GET['pn'];
    }

    if ($pagenum < 1) {
        $pagenum = 1;

    } elseif ($pagenum > $last) {
        $pagenum = $last;
    }

    // TODO: This could use some explaining! :-)
    $limit = 'LIMIT ' .($pagenum - 1) * $postsPerPage .',' .$postsPerPage;

/*******************************************************************************
    START OF QUERIES TO PRINT OUT VARIABLES ON PAGE
*******************************************************************************/

    $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC $limit";

    if (isset($_GET["display"])) {
        $display = $_GET["display"];

        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}' AND published = 1 ORDER BY created DESC $limit";
    }

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

/*******************************************************************************
    START OF PAGINATION NAVIGATION
*******************************************************************************/

    if ($last != 1) {

        if ($pagenum > 1) {
            $previous = $pagenum - 1;
            $first = 1;

            // This string is used if user is filtering by category.
            if ($display) {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$first.'&display='.$display.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;</a> ';
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'&display='.$display.'"><i class="fa fa-angle-left" aria-hidden="true"></i> Föregående &nbsp;</a> ';

            // This string is used if user is not filtering by category.
            } else {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$first.'"><i class="fa fa-angle-double-left" aria-hidden="true"></i>&nbsp;</a> ';
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$previous.'"><i class="fa fa-angle-left" aria-hidden="true"></i> Föregående &nbsp;</a> ';
            }
        }

        // Check if current page is last page
        if ($pagenum != $last) {
            $next = $pagenum + 1;

            // This string is used if user is filtering by category.
            if ($display) {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'&display='.$display.'"> Nästa <i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;</a> ';

                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$last.'&display='.$display.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a> ';

            // This string is used if user is not filtering by category.
            } else {
                $paginationCtrls .= '<a class="pagination-wrapper__text" href="'.$_SERVER['PHP_SELF'].'?pn='.$next.'"> Nästa <i class="fa fa-angle-right" aria-hidden="true"></i>&nbsp;</a> ';

                $paginationCtrls .= '<a class="pagination-wrapper__text" href="' .$_SERVER['PHP_SELF'].'?pn='.$last.'"><i class="fa fa-angle-double-right" aria-hidden="true"></i></a> ';
            }
        }
    }

/*******************************************************************************
    START ARRAY THAT IS USED WHEN PRINTING OUT POSTS ON PAGE
*******************************************************************************/

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
    }

/*******************************************************************************
    START OF HTML
*******************************************************************************/
?>
<main class="blogpost">
    <div class="pagination-wrapper">
        <?php echo $paginationCtrls; ?>
    </div>
    <?php if (count($posts) < 1):
        if ($display != NULL): ?>
            <h1 class="center-text">Det finns inga inlägg i vald kategori.</h1>
        <?php else: ?>
            <h1 class="center-text">Det finns inga inlägg.</h1>
        <?php endif; ?>
    <?php else: ?>

    <div class="blogpost__flex-list">
        <?php for ($i = 0; $i < count($posts); $i++):
            $post = $posts[$i];
            $id = $post["id"];
            $totalNumberOfComments = 0;
            $errorMessage = NULL;

            $query = "SELECT id FROM comments WHERE postid = '{$id}'";

            if ($stmt->prepare($query)) {
                $stmt->execute();
                $stmt->store_result();
                $totalNumberOfComments = $stmt->num_rows;
            } else {
                $errorMessage = $queryFailed;
            }

            // Choose class on comment bubble depending on number of comments.
            if ($totalNumberOfComments < 10) {
                $bubbleClass = "comment-bubble__number-one";
            } elseif ($totalNumberOfComments < 100) {
                $bubbleClass = "comment-bubble__number-two";
            } else {
                $bubbleClass = "comment-bubble__number-three";
            }
        ?>
        <article class="blogpost__article">
            <div class="blogpost-wrapper">
                <a href="post.php?getpost=<?php echo $post["id"] ?>"><div class="blogpost-wrapper__img-container"><img src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>" class="blogpost-wrapper__img"></div></a>
                <div class="comment-bubble">
                    <a href="post.php?getpost=<?php echo $post["id"] ?>"><i class="fa fa-comment comment-bubble__offset-text" aria-hidden="true"></i>
                    <p class="comment-bubble__number <?php echo $bubbleClass; ?>"><?php echo "$totalNumberOfComments" ?></p></a>
                </div>
                <div class="blogpost-wrapper__text">
                    <h1 class="blogpost-wrapper__headline"><a href="post.php?getpost=<?php echo $post["id"] ?>" class="blogpost__link"><?php echo formatInnerHtml($post["title"]); ?></a></h1>
                    <p class="blogpost-wrapper__tags">[ Tags: <a href="?display=<?php echo $post["categoryId"] ?>" class="blogpost-wrapper__links"><?php echo str_replace(' ', '', $post["categoryName"]); ?> ]</a> [ Publicerad: <?php echo formatDate($post["created" ]); ?> ]</p>
                </div>
            </div>
        </article>
        <?php endfor; ?>
    </div>
    <div class="pagination-wrapper">
        <div class="pagination-wrapper__text pagination-wrapper__text_bottom">
            <?php echo $paginationCtrls; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($errorMessage) { echo $errorMessage; } ?>
</main>
<?php require_once "./templates/footer.php"; ?>
