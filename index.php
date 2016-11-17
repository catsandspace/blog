<?php

    // File to include
    require_once "./templates/header.php"; // Header content.

    // Variables
    $display = NULL; // To avoid "undefined variable".

    // SQL statement with LEFT JOIN table -> posts & categories.
    $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id";

    // If GET request "display" is set.
    if (isset($_GET["display"])) {
        $display = $_GET["display"];

        // New SQL statement WHERE categories.category = $display.
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}'";
    }

    // Execute query.
    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

?>
<section>

    <?php while (mysqli_stmt_fetch($stmt)): ?>
    <article class="list">
        <div class="post">
            <h2 class="post-header"><?php echo $title; ?></h2>
            <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>"class="post-img">
            <div class="post-text">
                <p><?php echo $content; ?></p>
                <p>Tags: <a href="?display=<?php echo $categoryId ?>"><?php echo str_replace(' ', '', $categoryName); ?></a> </p>
            </div>
        </div>
    </article>
    <?php endwhile; ?>

</section>

<?php require_once "./templates/footer.php"; ?>
