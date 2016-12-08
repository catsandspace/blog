<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC";

    //Determine if a variable is set and is not NULL
     $sort = "";
    if(isset($_GET["sort"]) ) { //Avoids error message
        $sort = $_GET["sort"];
        $month = $_GET["month"];
    }
    // Sort post by name
    if($sort == "name") {
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY title ASC";
    }
    // Sort post by lastest entry
    if($sort == "asc") {
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created ASC";
    }
    // Sort post by the last one
    if($sort == "desc") {
        $query = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 ORDER BY created DESC";
    }

    if($month == "11") {
        $query = "SELECT posts.*,
        categories.name
        FROM posts
        LEFT JOIN categories
        ON posts.categoryid = categories.id
        WHERE published = 1
        AND EXTRACT(MONTH FROM created) = 11
        ORDER BY created DESC";
    }

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
    }

    // TODO: Seperating in different months needs to be handled by PHP and SQL.
    // TODO: Get the styling right on buttons, select and svg.

?>
<main>
    <h1 class="margin-bottom-l">Arkiv</h1>
    <form method="GET" action="archive.php">
        <label for="sort">Sortera arkivet</label>
        <div class="select-arrows">
          <select class="form-field form-field__select" name="sort" id="sort">
              <option value="desc">Senast publicerad</option>
              <option value="asc">Tidigast publicerad</option>
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
<?php require_once "./templates/footer.php"; ?>
