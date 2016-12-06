<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC";

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

    // TODO: Seperating in different months needs to be handled by PHP and SQL.
    // TODO: Get the styling right on buttons, select and svg.

?>
<main>
    <h1 class="margin-bottom-l">Arkiv</h1>
    <form method="GET" action="">
        <label for="sort">Sortera arkivet</label>
        <div class="select-arrows">
          <select class="form-field form-field__select" name="sort" id="sort">
              <option value="desc">Senast publicerad först</option>
              <option value="asc">Tidigast publicerad först</option>
              <option value="name">Sortera efter bokstavsordning (A-Z)</option>
          </select>
          <button class="button button--small border-radius margin-bottom-l" type="submit">Sortera</button>
          <!-- <svg class="icon select-arrows">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
          </svg> -->
        </div>
    </form>
    <div class="list-wrapper">
        <h2>November 2016</h2>
        <ul class="no-padding">
        <?php while (mysqli_stmt_fetch($stmt)): ?>
            <li class="list-style-none"><span class="saffron-text primary-brand-font">[<?php echo formatDate($created); ?>]</span><a href="post.php?getpost=<?php echo $id ?>"><?php echo $title; ?></a></li>
        <?php endwhile; ?>
        </ul>
    </div>
</main>
<?php
var_dump($_GET);
?>
<?php require_once "./templates/footer.php"; ?>

